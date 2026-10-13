#!/usr/bin/env python3
"""Render single-note ear-training audio from a General MIDI soundfont.

Usage:
    python3 tools/quiz-audio/generate.py --instrument piano --from C3 --to C4

Each note is written as public/uploads/audio/generated/<instrument>/<name>.mp3
plus a manifest.json. After rendering, the pitch of every file is measured and
compared with the note it should be, so a wrong note is caught without listening.

Requires: fluidsynth and lame (brew install fluidsynth lame).
"""
import argparse
import array
import json
import math
import os
import shutil
import struct
import subprocess
import sys
import tempfile
import wave

REPO = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
DEFAULT_SOUNDFONT = "/Applications/MuseScore 4.app/Contents/Resources/sound/MS Basic.sf3"
OUT_ROOT = os.path.join(REPO, "public", "uploads", "audio", "generated")

# General MIDI program numbers (0-based), so every instrument comes from the same soundfont.
INSTRUMENTS = {
    "piano": 0,       # Acoustic Grand Piano
    "xylophone": 13,
    "organ": 19,      # Church Organ
    "guitar": 24,     # Nylon guitar
    "violin": 40,
    "strings": 48,    # String Ensemble
    "choir": 52,      # Choir Aahs
    "voice": 53,      # Voice Oohs
    "saxophone": 65,  # Alto Sax
}

SAMPLE_RATE = 44100
NOTE_SECONDS = 2.0      # how long the key is held
TAIL_SECONDS = 1.5      # release tail after the key is lifted
PEAK_TARGET = 0.70      # about -3 dBFS, so every note has the same loudness ceiling

NAMES = ["C", "C#", "D", "D#", "E", "F", "F#", "G", "G#", "A", "A#", "B"]
FILE_NAMES = ["c", "csharp", "d", "dsharp", "e", "f", "fsharp", "g", "gsharp", "a", "asharp", "b"]


def parse_note(text):
    """'C3' / 'F#4' -> MIDI number, with C4 = 60 (middle C)."""
    text = text.strip().upper()
    letter = text[0]
    sharp = "#" in text
    octave = int(text.replace("#", "")[1:])
    pitch = NAMES.index(letter + ("#" if sharp else ""))
    return (octave + 1) * 12 + pitch


def label(midi):
    return f"{NAMES[midi % 12]}{midi // 12 - 1}"


def file_stem(midi):
    return f"{FILE_NAMES[midi % 12]}{midi // 12 - 1}"


def frequency(midi):
    return 440.0 * 2 ** ((midi - 69) / 12)


def vlq(value):
    out = [value & 0x7F]
    value >>= 7
    while value:
        out.append((value & 0x7F) | 0x80)
        value >>= 7
    return bytes(reversed(out))


def write_midi(path, program, midi_note, strikes, tail, velocity=96):
    """strikes = [(start_seconds, held_seconds), ...] - the same key is struck once per entry."""
    ppq = 480                       # ticks per beat; tempo below makes one beat 0.5 s
    to_ticks = lambda seconds: int(round(seconds * 2 * ppq))
    events = []
    for start, held in strikes:
        events.append((to_ticks(start), bytes([0x90, midi_note, velocity])))
        events.append((to_ticks(start + held), bytes([0x80, midi_note, 0])))
    events.sort(key=lambda e: e[0])
    end_tick = events[-1][0] + to_ticks(tail)
    track = vlq(0) + b"\xFF\x51\x03" + (500000).to_bytes(3, "big")   # 120 bpm
    track += vlq(0) + bytes([0xC0, program])
    now = 0
    for tick, message in events:
        track += vlq(tick - now) + message
        now = tick
    track += vlq(end_tick - now) + b"\xFF\x2F\x00"
    header = b"MThd" + struct.pack(">IHHH", 6, 0, 1, ppq)
    with open(path, "wb") as handle:
        handle.write(header + b"MTrk" + struct.pack(">I", len(track)) + track)


def render_wav(soundfont, midi_path, wav_path):
    # Reverb and chorus off: a dry note is the same every time and easier to judge by ear.
    subprocess.run(
        ["fluidsynth", "-ni", "-q", "-r", str(SAMPLE_RATE), "-R", "0", "-C", "0",
         "-F", wav_path, soundfont, midi_path],
        check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)


def load_mono(path):
    with wave.open(path, "rb") as handle:
        channels, width, rate = handle.getnchannels(), handle.getsampwidth(), handle.getframerate()
        frames = handle.readframes(handle.getnframes())
    if width != 2:
        sys.exit(f"expected 16-bit audio, got {width * 8}-bit")
    samples = array.array("h")
    samples.frombytes(frames)
    if channels == 2:
        samples = [(samples[i] + samples[i + 1]) / 2 for i in range(0, len(samples), 2)]
    else:
        samples = list(samples)
    return samples, rate


def normalise(samples, rate, total_seconds):
    # Drop the near-silent lead-in, then scale to a fixed peak and fade the last 80 ms
    # so the file never ends on a click.
    start = next((i for i, s in enumerate(samples) if abs(s) > 40), 0)
    samples = samples[max(0, start - int(rate * 0.005)):]
    samples = samples[:int(rate * total_seconds)]   # fluidsynth renders extra silence after the last note
    peak = max(abs(s) for s in samples) or 1
    scale = PEAK_TARGET * 32767 / peak
    out = [s * scale for s in samples]
    fade = int(rate * 0.08)
    for i in range(fade):
        out[-1 - i] *= i / fade
    return out


def write_wav(path, samples, rate):
    with wave.open(path, "wb") as handle:
        handle.setnchannels(1)
        handle.setsampwidth(2)
        handle.setframerate(rate)
        handle.writeframes(array.array("h", [max(-32768, min(32767, int(s))) for s in samples]).tobytes())


def detect_pitch(samples, rate, expected_hz):
    """Find the strongest spectral peak within 1.5 semitones of the expected pitch.

    Goertzel energy at fine frequency steps over the steady part of the note. This
    replaced an autocorrelation check that mis-read B3 by 44 cents on an in-tune note.
    """
    seg = samples[int(rate * 0.2):int(rate * 1.0)]
    if len(seg) < rate * 0.5:
        return 0.0
    n = len(seg)
    seg = [v * (0.5 - 0.5 * math.cos(2 * math.pi * i / n)) for i, v in enumerate(seg)]   # Hann window
    step = 0.25
    lo, hi = expected_hz * 2 ** (-1.5 / 12), expected_hz * 2 ** (1.5 / 12)
    freqs, energy = [], []
    f = lo
    while f <= hi:
        k = 2 * math.cos(2 * math.pi * f / rate)
        s1 = s2 = 0.0
        for v in seg:
            s0 = v + k * s1 - s2
            s2, s1 = s1, s0
        freqs.append(f)
        energy.append(s1 * s1 + s2 * s2 - k * s1 * s2)
        f += step
    i = max(range(len(energy)), key=energy.__getitem__)
    if 0 < i < len(energy) - 1:     # parabolic refinement between the bins
        a, b, c = energy[i - 1], energy[i], energy[i + 1]
        denom = a - 2 * b + c
        if denom:
            return freqs[i] + 0.5 * (a - c) / denom * step
    return freqs[i]


def check_strikes(samples, rate, starts):
    """Each strike after the first must be a clear jump in level versus just before it."""
    def level(a, b):
        part = samples[int(rate * a):int(rate * b)]
        return math.sqrt(sum(v * v for v in part) / max(1, len(part)))
    jumps = []
    for t in starts[1:]:
        before, after = level(t - 0.12, t - 0.02), level(t + 0.02, t + 0.12)
        jumps.append(after / before if before else float("inf"))
    return jumps


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--instrument", default="piano", choices=sorted(INSTRUMENTS))
    parser.add_argument("--from", dest="low", default="C3")
    parser.add_argument("--to", dest="high", default="C4")
    parser.add_argument("--repeat", type=int, default=1, help="strike the note this many times in one clip")
    parser.add_argument("--cycle", type=float, default=2.0, help="seconds from one strike to the next (repeat > 1)")
    parser.add_argument("--hold", type=float, default=1.3, help="seconds each strike is held before release (repeat > 1)")
    parser.add_argument("--soundfont", default=os.environ.get("SOUNDFONT", DEFAULT_SOUNDFONT))
    args = parser.parse_args()

    for tool in ("fluidsynth", "lame"):
        if not shutil.which(tool):
            sys.exit(f"{tool} not found - run: brew install fluidsynth lame")
    if not os.path.exists(args.soundfont):
        sys.exit(f"soundfont not found: {args.soundfont}")

    low, high = parse_note(args.low), parse_note(args.high)
    set_name = args.instrument if args.repeat == 1 else f"{args.instrument}-x{args.repeat}"
    out_dir = os.path.join(OUT_ROOT, set_name)
    if args.repeat == 1:
        strikes, tail = [(0.0, NOTE_SECONDS)], TAIL_SECONDS
    else:
        strikes, tail = [(i * args.cycle, args.hold) for i in range(args.repeat)], TAIL_SECONDS - 0.3
    total_seconds = strikes[-1][0] + strikes[-1][1] + tail
    os.makedirs(out_dir, exist_ok=True)
    program = INSTRUMENTS[args.instrument]
    manifest, problems = [], 0

    print(f"{set_name} (GM program {program}), {label(low)} to {label(high)}, {args.repeat} strike(s) per clip, {total_seconds:.1f} s each, soundfont: {os.path.basename(args.soundfont)}")
    print(f"{'note':<5} {'file':<14} {'expected':>9} {'measured':>9} {'off (cents)':>12}  {'attack jumps':<14} size")
    with tempfile.TemporaryDirectory() as work:
        for midi in range(low, high + 1):
            stem = file_stem(midi)
            mid_path, raw_path, wav_path = (os.path.join(work, f"{stem}.{ext}") for ext in ("mid", "raw.wav", "wav"))
            mp3_path = os.path.join(out_dir, f"{stem}.mp3")
            write_midi(mid_path, program, midi, strikes, tail)
            render_wav(args.soundfont, mid_path, raw_path)
            samples, rate = load_mono(raw_path)
            samples = normalise(samples, rate, total_seconds)
            write_wav(wav_path, samples, rate)
            subprocess.run(["lame", "--quiet", "-m", "m", "-b", "128", wav_path, mp3_path], check=True)

            expected = frequency(midi)
            measured = detect_pitch(samples, rate, expected)
            cents = 1200 * math.log2(measured / expected) if measured else float("nan")
            jumps = check_strikes(samples, rate, [t for t, _ in strikes])
            ok = measured and abs(cents) < 35 and all(j > 3 for j in jumps)
            problems += 0 if ok else 1
            print(f"{label(midi):<5} {stem + '.mp3':<14} {expected:>8.1f}Hz {measured:>8.1f}Hz {cents:>+11.1f}  {('x' + ' x'.join(f'{j:.0f}' for j in jumps)) if jumps else '-':<14} {os.path.getsize(mp3_path) // 1024} KB{'' if ok else '   <-- CHECK'}")
            manifest.append({"note": label(midi), "midi": midi, "hz": round(expected, 2),
                             "path": f"/uploads/audio/generated/{set_name}/{stem}.mp3",
                             "seconds": round(len(samples) / rate, 2)})

    with open(os.path.join(out_dir, "manifest.json"), "w") as handle:
        json.dump({"instrument": args.instrument, "set": set_name, "strikes_per_clip": args.repeat, "strike_times": [t for t, _ in strikes], "soundfont": os.path.basename(args.soundfont), "notes": manifest}, handle, indent=2)
    sets = sorted(d for d in os.listdir(OUT_ROOT) if os.path.exists(os.path.join(OUT_ROOT, d, "manifest.json")))
    with open(os.path.join(OUT_ROOT, "sets.json"), "w") as handle:
        json.dump(sets, handle)
    with open(os.path.join(OUT_ROOT, "NOTICE.txt"), "w") as handle:
        handle.write("Audio in this folder was rendered with the MuseScore General SoundFont (MIT license).\n"
                     "FluidR3 Copyright (c) 2000-2002, 2008 Frank Wen; FluidR3Mono (c) 2014-17 Michael Cowgill;\n"
                     "MuseScore_General adaptation (c) 2018-19 S. Christian Collins.\n"
                     "Permission is granted under the MIT license; these notices must be kept with derivative works.\n")
    print(f"\n{len(manifest)} files in {out_dir}")
    print("all pitches verified" if not problems else f"{problems} file(s) flagged - listen to those")


if __name__ == "__main__":
    main()
