import React, { useEffect, useMemo, useRef, useState } from "react";
import ReactDOM from "react-dom/client";
import * as mm from "@magenta/music/esm/core.js";

// The shared Tone.js instance magenta's Player is built on (Player.tone =
// Tone is set as a static property in @magenta/music's own source), used to
// drive playback (Transport) and muting (Destination) directly.
const Tone = mm.Player.tone;

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

const formatTime = (seconds) => {
    if (!Number.isFinite(seconds) || seconds < 0) return "0:00";

    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60).toString().padStart(2, "0");

    return `${mins}:${secs}`;
};

const rangeFill = (percent, color = "#2563eb") => ({
    background: `linear-gradient(to right, ${color} 0%, ${color} ${percent}%, #dbe3ef ${percent}%, #dbe3ef 100%)`,
});

const PITCH_NAMES = ["C", "C#", "D", "D#", "E", "F", "F#", "G", "G#", "A", "A#", "B"];
const BLACK_KEY_CLASSES = new Set([1, 3, 6, 8, 10]);
// Each black key is centered on the boundary between the two white keys it
// sits between (in white-key-width units from the start of its octave).
// This keeps the two-key/three-key groupings visually distinct: C#/D# stay
// close together, F#/G#/A# stay close together, and the E-F / B-C gaps
// (which have no black key at all) read as clearly wider.
const BLACK_KEY_OFFSETS = {
    1: 1,
    3: 2,
    6: 4,
    8: 5,
    10: 6,
};
// Grid-column subdivisions per white key. Every black key spans exactly
// BLACK_KEY_SPAN of these columns, so the grid layout guarantees identical
// pixel widths for every black key (see the comment in the `keyboard`
// useMemo for why this replaces percentage-based positioning).
const WHITE_KEY_SUBDIVISIONS = 10;
const BLACK_KEY_SPAN = 6;
// How many white keys precede each white pitch class within its own octave
// (C=0 ... B=6). Combined with the octave number this gives an absolute
// "white key ordinal" for any pitch, measured from a fixed C reference —
// unlike an octave-relative index, this stays correct even when the visible
// range starts on a non-C pitch.
const WHITE_KEY_INDEX_IN_OCTAVE = { 0: 0, 2: 1, 4: 2, 5: 3, 7: 4, 9: 5, 11: 6 };
const whiteKeyUnits = (pitch) => Math.floor(pitch / 12) * 7 + WHITE_KEY_INDEX_IN_OCTAVE[((pitch % 12) + 12) % 12];
const blackKeyBoundaryUnits = (pitch) => Math.floor(pitch / 12) * 7 + BLACK_KEY_OFFSETS[((pitch % 12) + 12) % 12];
// Sampled Salamander Grand Piano tone (publicly hosted by Magenta), used
// instead of html-midi-player's default bare oscillator synth.
const PIANO_SOUNDFONT_URL = "https://storage.googleapis.com/magentadata/js/soundfonts/salamander";
// Fixed C0-C7 range shown in the fullscreen "full view".
const FULL_VIEW_MIN_PITCH = 24; // C0
const FULL_VIEW_MAX_PITCH = 108; // C7
// Mobile full view (landscape) uses a narrower 5-octave range instead of
// the full 88 keys — the full range doesn't fit comfortably on a phone
// screen even rotated, so this trades range for a more usable key size.
const MOBILE_FULL_VIEW_MIN_PITCH = 24; // C0
const MOBILE_FULL_VIEW_MAX_PITCH = 84; // C5
// Default embedded player (desktop, not full view) shows a fixed C1-C6
// range instead of the note-driven dynamic window, matching the fixed
// ranges used by the other two view modes.
const DEFAULT_VIEW_MIN_PITCH = 36; // C1
const DEFAULT_VIEW_MAX_PITCH = 96; // C6
// Active-key highlight color split: below this pitch highlights blue, this
// pitch and above highlights green.
const KEY_RANGE_SPLIT_PITCH = 48; // C2

const isBlackPitch = (pitch) => BLACK_KEY_CLASSES.has(((pitch % 12) + 12) % 12);

const pitchLabel = (pitch) => {
    const name = PITCH_NAMES[((pitch % 12) + 12) % 12];
    const octave = Math.floor(pitch / 12) - 2;

    return `${name}${octave}`;
};

// Note name (no octave) shown under a key while it's actively playing in
// full view — e.g. "C#", "D", "G#".
const noteName = (pitch) => PITCH_NAMES[((pitch % 12) + 12) % 12];

// Real performance MIDI often has a note's note-off land a few milliseconds
// after the next note's note-on (a natural release/attack crossfade), not
// an intentional held chord. Below this threshold, an older note that's
// already about to end anyway is cut the instant something new starts,
// instead of visibly flickering both at once. A note genuinely still
// sustaining well past a new onset (a real held note under moving voices)
// keeps showing normally — this only suppresses the brief tail end.
const NOTE_RELEASE_OVERLAP_THRESHOLD = 0.05;

// `sequence` here has already been time-stretched by transformSequence when
// tempo != baseTempo (see the `sequence` useMemo), so a fixed threshold
// would no longer match a ~30ms crossfade once it's been stretched to ~60ms
// at half speed. `tempoRatio` (baseTempo / tempo, same ratio transformSequence
// used) scales the threshold back up/down to match, so the suppression
// stays correct at every playback speed.
const getActivePitches = (sequence, time, tempoRatio = 1) => {
    if (!sequence?.notes?.length) return new Set();

    const overlapThreshold = NOTE_RELEASE_OVERLAP_THRESHOLD * tempoRatio;

    let latestOnset = -Infinity;

    for (const note of sequence.notes) {
        const startTime = note.startTime || 0;

        if (startTime <= time && startTime > latestOnset) {
            latestOnset = startTime;
        }
    }

    return new Set(
        sequence.notes
            .filter((note) => {
                const startTime = note.startTime || 0;
                const endTime = note.endTime || 0;

                if (!(startTime <= time + 0.02 && endTime >= time)) return false;

                const isOlderNote = startTime < latestOnset - 0.001;

                if (isOlderNote && endTime - latestOnset <= overlapThreshold) {
                    return false;
                }

                return true;
            })
            .map((note) => note.pitch),
    );
};

const cloneSequence = (sequence) => {
    if (mm.sequences?.clone) {
        return mm.sequences.clone(sequence);
    }

    return JSON.parse(JSON.stringify(sequence));
};

const transformSequence = (sequence, transpose, targetTempo) => {
    const nextSequence = cloneSequence(sequence);
    const sourceTempo = nextSequence.tempos?.[0]?.qpm || 100;
    const ratio = sourceTempo / targetTempo;

    nextSequence.notes = (nextSequence.notes || []).map((note) => ({
        ...note,
        pitch: clamp((note.pitch || 0) + transpose, 0, 127),
        startTime: (note.startTime || 0) * ratio,
        endTime: (note.endTime || 0) * ratio,
    }));

    if (nextSequence.tempos?.length) {
        nextSequence.tempos = nextSequence.tempos.map((tempo, index) => ({
            ...tempo,
            time: (tempo.time || 0) * ratio,
            qpm: index === 0 ? targetTempo : tempo.qpm,
        }));
    } else {
        nextSequence.tempos = [{ time: 0, qpm: targetTempo }];
    }

    nextSequence.totalTime = Math.max(
        ...nextSequence.notes.map((note) => note.endTime || 0),
        (nextSequence.totalTime || 0) * ratio,
    );

    return nextSequence;
};

const TEMPO_PERCENT_OPTIONS = [25, 50, 75, 90, 100];

const MidiPracticePlayer = ({ data }) => {
    const files = data.files || [];
    // A single mm.SoundFontPlayer, created once per loaded file and reused
    // across every tempo/transpose change (see the effect that creates it).
    // html-midi-player's <midi-player> element used to own this instead,
    // but assigning it a new noteSequence — which happens on every tempo
    // change, since retiming requires a new sequence object — made it
    // silently rebuild its entire engine from scratch, including a brand
    // new SoundFont with an empty, per-instance sample cache. That's what
    // was producing both the multi-second delay after changing tempo (every
    // piano sample re-fetched from the network) and the inconsistent seek
    // behavior (the rebuild's own internal position reset racing with
    // whatever the user just did). Owning the player instance ourselves and
    // never recreating it means samples are fetched once, ever.
    const soundPlayerRef = useRef(null);
    const shellRef = useRef(null);
    // The user's actual last explicit seek, tracked separately from
    // timeInfo.current, which is continuously overwritten by the 30ms sync
    // poll — see handlePlay for why that distinction matters. This ref is
    // only ever written by handleSeek.
    const desiredSeekRef = useRef(null);
    // Read by the persistent player's note-onset callback and the polling
    // loop, both of which are set up once (effects with no/near-empty
    // dependency arrays) and must not close over stale render values.
    const sequenceRef = useRef(null);
    const tempoRatioRef = useRef(1);

    const [selectedFileId, setSelectedFileId] = useState(data.selectedFileId ? String(data.selectedFileId) : "");
    const [sourceSequence, setSourceSequence] = useState(null);
    const [loadState, setLoadState] = useState(files.length ? "choose" : "empty");
    const [loadError, setLoadError] = useState("");
    const [baseTempo, setBaseTempo] = useState(100);
    const [tempo, setTempo] = useState(100);
    const [transpose, setTranspose] = useState(0);
    const [isPlaying, setIsPlaying] = useState(false);
    const [timeInfo, setTimeInfo] = useState({ current: 0, duration: 0 });
    const [activePitches, setActivePitches] = useState(new Set());
    const [isMuted, setIsMuted] = useState(false);
    const [isFullscreen, setIsFullscreen] = useState(false);
    const [isNarrowPortrait, setIsNarrowPortrait] = useState(
        () => typeof window !== "undefined" && window.innerWidth <= 900 && window.innerHeight > window.innerWidth,
    );
    // Raw viewport size, used to size the rotated inner wrapper (see
    // rotateInnerStyle) with exact pixel values instead of vh/vw units.
    const [windowSize, setWindowSize] = useState(
        () => typeof window !== "undefined" ? { width: window.innerWidth, height: window.innerHeight } : { width: 0, height: 0 },
    );

    // Computed directly from window dimensions rather than the CSS
    // `orientation` media feature — that feature has real inconsistencies
    // across browsers and devtools device-emulation modes (it didn't fire
    // reliably in testing), so this drives the full-view rotation via an
    // inline style instead of a media query, for a result that's actually
    // reliable everywhere.
    useEffect(() => {
        const updateOrientation = () => {
            setIsNarrowPortrait(window.innerWidth <= 900 && window.innerHeight > window.innerWidth);
            setWindowSize({ width: window.innerWidth, height: window.innerHeight });
        };

        updateOrientation();
        window.addEventListener("resize", updateOrientation);
        window.addEventListener("orientationchange", updateOrientation);

        return () => {
            window.removeEventListener("resize", updateOrientation);
            window.removeEventListener("orientationchange", updateOrientation);
        };
    }, []);

    const selectedFile = useMemo(
        () => files.find((file) => String(file.id) === selectedFileId) || null,
        [files, selectedFileId],
    );

    const sequence = useMemo(() => {
        if (!sourceSequence) return null;

        // At default settings (no transpose, tempo unchanged from the
        // file), use the parsed sequence exactly as read — no cloning or
        // recomputed timing, so playback is byte-faithful to the source
        // MIDI. transformSequence only runs once the user actually asks
        // for a transpose or tempo change.
        if (transpose === 0 && tempo === baseTempo) {
            return sourceSequence;
        }

        return transformSequence(sourceSequence, transpose, tempo);
    }, [sourceSequence, tempo, transpose, baseTempo]);

    useEffect(() => {
        sequenceRef.current = sequence;
    }, [sequence]);

    useEffect(() => {
        tempoRatioRef.current = baseTempo / tempo;
    }, [baseTempo, tempo]);

    const keyboard = useMemo(() => {
        let minPitch;
        let maxPitch;

        if (isFullscreen) {
            // Full view (desktop or mobile landscape) shows a fixed C0-C7
            // range instead of the note-driven window, regardless of what
            // the loaded sequence actually uses.
            minPitch = FULL_VIEW_MIN_PITCH;
            maxPitch = FULL_VIEW_MAX_PITCH;
        } else if (isNarrowPortrait) {
            // Mobile default embedded player (not full view): fixed C0-C5,
            // 5 octaves, instead of the note-driven dynamic window — keeps
            // key size consistent and predictable on a phone screen rather
            // than depending on whatever range the loaded sequence uses.
            minPitch = MOBILE_FULL_VIEW_MIN_PITCH;
            maxPitch = MOBILE_FULL_VIEW_MAX_PITCH;
        } else {
            // Default embedded player (desktop, not full view): fixed C1-C6
            // instead of the note-driven dynamic window, regardless of what
            // the loaded sequence actually uses.
            minPitch = DEFAULT_VIEW_MIN_PITCH;
            maxPitch = DEFAULT_VIEW_MAX_PITCH;
        }

        const pitches = Array.from(
            { length: Math.max(maxPitch - minPitch + 1, 0) },
            (_, index) => minPitch + index,
        );
        const whitePitches = pitches.filter((pitch) => !isBlackPitch(pitch));
        const octaveCount = Math.max(Math.round((maxPitch - minPitch) / 12), 1);

        // Every white key is given the same integer number of grid columns
        // (WHITE_KEY_SUBDIVISIONS), and every black key spans the same
        // integer number of columns (BLACK_KEY_SPAN), centered on the grid
        // line between the two white keys it sits over. Because the grid
        // track-sizing algorithm allocates pixels for all columns in a
        // single pass, every black key ends up pixel-identical in width —
        // unlike independent percentage-based "left" offsets, which round
        // to the nearest pixel separately per element and can drift by a
        // pixel from key to key once keys get this thin.
        //
        // Positions are measured in "white key units" from a fixed C
        // reference (whiteKeyUnits / blackKeyBoundaryUnits), not relative to
        // minPitch's own octave — this keeps the grouping correct even when
        // the visible range starts on a non-C pitch.
        const origin = whiteKeyUnits(minPitch);
        const totalColumns = whitePitches.length * WHITE_KEY_SUBDIVISIONS;
        const blackPitches = pitches
            .filter(isBlackPitch)
            .map((pitch) => {
                const boundaryLine = (blackKeyBoundaryUnits(pitch) - origin) * WHITE_KEY_SUBDIVISIONS + 1;

                return {
                    pitch,
                    colStart: boundaryLine - BLACK_KEY_SPAN / 2,
                    colEnd: boundaryLine + BLACK_KEY_SPAN / 2,
                };
            });

        return { pitches, whitePitches, blackPitches, octaveCount, totalColumns };
    }, [sequence, isFullscreen, isNarrowPortrait]);

    useEffect(() => {
        if (!selectedFile?.midiUrl) {
            setSourceSequence(null);
            setLoadState(files.length ? "choose" : "empty");
            setTimeInfo({ current: 0, duration: 0 });
            setActivePitches(new Set());
            return;
        }

        let cancelled = false;

        const loadSequence = async () => {
            try {
                setLoadState("loading");
                setLoadError("");
                try {
                    soundPlayerRef.current?.stop?.();
                } catch {
                    // Wasn't playing — nothing to stop.
                }

                const loadedSequence = await mm.urlToNoteSequence(selectedFile.midiUrl);

                if (cancelled) return;

                // Keep the exact tempo from the file (not rounded) — tempo
                // and transpose are both compared/ratioed against this in
                // transformSequence, and rounding it here would make even
                // "100%" default playback silently drift the note timing
                // away from the source file over the length of the piece.
                const qpm = loadedSequence.tempos?.[0]?.qpm || 100;
                setBaseTempo(qpm);
                setTempo(qpm);
                setTranspose(0);
                setSourceSequence(loadedSequence);
                setTimeInfo({ current: 0, duration: loadedSequence.totalTime || 0 });
                setActivePitches(new Set());
                setLoadState("ready");
            } catch (error) {
                if (cancelled) return;
                setSourceSequence(null);
                setLoadError(error.message || "MIDI file could not be loaded.");
                setActivePitches(new Set());
                setLoadState("error");
            }
        };

        loadSequence();

        return () => {
            cancelled = true;
        };
    }, [selectedFile?.midiUrl, files.length]);

    useEffect(() => {
        // A tempo/transpose change rescales or reshapes the timeline, so
        // just reset our own displayed position — the sound engine itself
        // (soundPlayerRef) is untouched here; see the effect below for why
        // it's never recreated on this kind of change.
        if (!sequence) return;

        try {
            soundPlayerRef.current?.stop?.();
        } catch {
            // Wasn't playing — nothing to stop.
        }

        setIsPlaying(false);
        setTimeInfo({ current: 0, duration: sequence.totalTime || 0 });
        setActivePitches(new Set());
        desiredSeekRef.current = null;
    }, [sequence]);

    useEffect(() => {
        if (!sourceSequence) return undefined;

        let cancelled = false;
        // callbackObject.run fires per note-onset, scheduled at the exact
        // audio time via Tone.Draw — reading sequenceRef/tempoRatioRef
        // (rather than closing over sequence/tempo) keeps this correct
        // across tempo changes without needing to recreate the player.
        const callbackObject = {
            run: (note) => {
                if (cancelled) return;
                setActivePitches(getActivePitches(sequenceRef.current, note.startTime, tempoRatioRef.current));
            },
            stop: () => {
                if (cancelled) return;
                setIsPlaying(false);
            },
        };

        const player = new mm.SoundFontPlayer(
            PIANO_SOUNDFONT_URL,
            undefined,
            undefined,
            undefined,
            callbackObject,
        );

        soundPlayerRef.current = player;
        player.loadSamples(sourceSequence).catch(() => {});

        return () => {
            cancelled = true;
            try {
                player.stop();
            } catch {
                // Wasn't playing — nothing to stop.
            }
            if (soundPlayerRef.current === player) {
                soundPlayerRef.current = null;
            }
        };
    }, [sourceSequence]);

    useEffect(() => {
        const interval = window.setInterval(() => {
            const player = soundPlayerRef.current;

            if (!player) return;

            const magentaIsPlaying = player.isPlaying?.();
            const isStarted = magentaIsPlaying && Tone.Transport.state === "started";

            setIsPlaying(isStarted);

            if (!magentaIsPlaying) return;

            // Keep showing the chord at the current position when paused,
            // instead of clearing the keyboard — only an explicit Stop
            // resets it (see handleStop). isPlaying() stays true while
            // paused (only Stop clears it), so this still runs then.
            const current = Tone.Transport.seconds;

            setTimeInfo((prev) => ({ ...prev, current }));
            setActivePitches(getActivePitches(sequenceRef.current, current, tempoRatioRef.current));
        }, 30);

        return () => window.clearInterval(interval);
    }, []);

    useEffect(() => {
        // SoundFontPlayer has no per-instance volume/mute API — its
        // synths/samples all connect straight to Tone.Destination, so
        // that's the only place playback can actually be muted from.
        Tone.Destination.mute = isMuted;
    }, [isMuted]);

    useEffect(() => {
        // "Full view" is our own state now (toggled directly in
        // handleFullscreenToggle), not derived from the native Fullscreen
        // API — that API is unreliable in Chrome DevTools' device-emulation
        // mode and unsupported for arbitrary elements on iOS Safari
        // entirely. This listener only needs to catch the case where a
        // *real* fullscreen session was actually engaged and the user backs
        // out of it some other way (Escape key, browser UI), so our fake
        // state doesn't get left on.
        const syncFullscreen = () => {
            if (!document.fullscreenElement) setIsFullscreen(false);
        };

        document.addEventListener("fullscreenchange", syncFullscreen);

        return () => document.removeEventListener("fullscreenchange", syncFullscreen);
    }, []);

    const canPlay = loadState === "ready" && sequence;
    const progressPercent = timeInfo.duration ? (timeInfo.current / timeInfo.duration) * 100 : 0;
    const tempoPercentValue = clamp(Math.round((tempo / baseTempo) * 100), 25, 200);
    const handlePlay = () => {
        if (!canPlay) return;

        const player = soundPlayerRef.current;

        if (!player) return;

        if (player.getPlayState?.() === "paused") {
            // Resuming: any seek made while paused was already applied
            // directly via seekTo() in handleSeek (Tone allows moving the
            // transport position while paused), so just continue from
            // there — no need to restart from scratch.
            player.resume();
            setIsPlaying(true);
            desiredSeekRef.current = null;
            return;
        }

        // Use the user's actual last explicit seek (desiredSeekRef), not
        // timeInfo.current — see the comment where desiredSeekRef is
        // declared for why timeInfo.current can't be trusted here.
        const targetTime = desiredSeekRef.current ?? timeInfo.current;

        // Player.start(seq, qpm, offset) tells the audio Part "treat
        // yourself as `offset` seconds in already" (so it skips scheduling
        // anything earlier), but it never moves Tone.js's own global
        // Transport clock to that same position — Transport just resumes
        // from wherever it was last left. A note at note.startTime=X
        // actually fires at transport time `transportStart + (X - offset)`,
        // so unless Transport's own clock starts at exactly `offset` too,
        // the audible content and the displayed/actual elapsed time
        // diverge. Setting Transport to the same offset the Part is asked
        // to use keeps both in lockstep, so a note fires at transport time
        // = X, matching its real position.
        Tone.Transport.stop();
        Tone.Transport.seconds = targetTime;

        player.start(sequence, undefined, targetTime).catch(() => {});
        setIsPlaying(true);
        desiredSeekRef.current = null;
    };

    const handlePause = () => {
        const player = soundPlayerRef.current;

        if (player?.isPlaying?.()) {
            try {
                player.pause();
            } catch {
                // Already stopped/paused — nothing to do.
            }
        }

        setIsPlaying(false);
    };

    const handleStop = () => {
        try {
            soundPlayerRef.current?.stop?.();
        } catch {
            // Wasn't playing — nothing to stop.
        }

        setIsPlaying(false);
        setTimeInfo((current) => ({ ...current, current: 0 }));
        setActivePitches(new Set());
        desiredSeekRef.current = null;
    };

    useEffect(() => {
        if (!isFullscreen) return undefined;

        const handleKeyDown = (event) => {
            if (event.code !== "Space" && event.key !== " ") return;

            // Don't hijack Space while it's meant for a focused control
            // (e.g. the tempo <select>, a button, or a text field).
            const target = event.target;
            const tag = target?.tagName;
            if (tag === "SELECT" || tag === "INPUT" || tag === "TEXTAREA" || target?.isContentEditable) return;

            event.preventDefault();

            if (isPlaying) {
                handlePause();
            } else if (canPlay) {
                handlePlay();
            }
        };

        document.addEventListener("keydown", handleKeyDown);

        return () => document.removeEventListener("keydown", handleKeyDown);
    }, [isFullscreen, isPlaying, canPlay]);

    const handleSeek = (event) => {
        const nextTime = Number(event.target.value);

        desiredSeekRef.current = nextTime;

        const player = soundPlayerRef.current;

        if (player?.isPlaying?.()) {
            // Covers both "actually playing" and "paused" (isPlaying()
            // stays true until an explicit Stop) — Tone allows moving the
            // transport position directly in both cases.
            try {
                player.seekTo(nextTime);
            } catch {
                // Transitioned to fully stopped between the check and this
                // call — handlePlay will pick up desiredSeekRef instead.
            }
        }

        setTimeInfo((current) => ({ ...current, current: nextTime }));
        setActivePitches(getActivePitches(sequence, nextTime, baseTempo / tempo));
    };

    const handleTempoPercentChange = (percent) => {
        const nextTempo = clamp(Math.round(baseTempo * (Number(percent) / 100)), 20, 220);
        setTempo(nextTempo);
    };

    const handleTransposeStep = (delta) => {
        setTranspose((current) => clamp(current + delta, -12, 12));
    };

    const handleMuteToggle = () => setIsMuted((current) => !current);

    const handleFullscreenToggle = () => {
        if (!shellRef.current) return;

        const next = !isFullscreen;

        // Full view itself is driven by this state directly, not by
        // whether the native Fullscreen API call below actually succeeds —
        // that API is flaky in devtools device emulation and unsupported
        // for non-video elements on iOS Safari. The CSS fake-fullscreen
        // overlay (.kk-midi-shell[data-fullscreen]) is what actually makes
        // full view appear everywhere. requestFullscreen + orientation lock
        // are attempted as a best-effort enhancement on top (mainly
        // Android Chrome, where they give a truly immersive, rotated view).
        setIsFullscreen(next);

        if (next) {
            Promise.resolve(shellRef.current.requestFullscreen?.())
                .then(() => screen.orientation?.lock?.("landscape"))
                .catch(() => {});
        } else {
            screen.orientation?.unlock?.();
            if (document.fullscreenElement) {
                Promise.resolve(document.exitFullscreen?.()).catch(() => {});
            }
        }
    };

    const isRotated = isFullscreen && isNarrowPortrait;
    const shellClassName = `kk-midi-shell${isRotated ? " kk-midi-rotate-landscape" : ""}`;
    // Combining `position: fixed` with a `transform` on the *same* element
    // proved unreliable under devtools device emulation in testing (the
    // outer fixed overlay rendered fine, but the transform on it never
    // visibly took effect). So the outer .kk-midi-shell stays plain
    // position:fixed with no transform, and the rotation instead applies to
    // this separate inner wrapper — swapped width/height (pre-rotation) so
    // its rotated visual footprint exactly matches the screen, centered via
    // alignSelf since the outer shell already centers its single child.
    const rotateInnerStyle = isRotated
        ? {
              width: `${windowSize.height}px`,
              height: `${windowSize.width}px`,
              transform: "rotate(90deg)",
              alignSelf: "center",
              flexShrink: 0,
              padding: "10px 16px",
              display: "flex",
              flexDirection: "column",
              justifyContent: "center",
          }
        : undefined;

    return (
        <section
            className={shellClassName}
            ref={shellRef}
            data-fullscreen={isFullscreen ? "true" : "false"}
        >
            <style>{`
                .kk-midi-shell {
                    --kk-midi-blue: #2f80ff;
                    --kk-midi-amber: #f59e0b;
                    --kk-midi-track: #485365;
                    --kk-midi-panel: #101827;
                    --kk-midi-panel-soft: #172033;
                }

                /* Full view is a CSS overlay driven by our own React state
                   (isFullscreen), not the native Fullscreen API — see the
                   comment in handleFullscreenToggle for why. This is what
                   actually makes it appear at all in devtools device
                   emulation and on iOS Safari. */
                .kk-midi-shell[data-fullscreen="true"] {
                    position: fixed;
                    inset: 0;
                    z-index: 2147483000;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    padding: 20px;
                    background: #05070c;
                    overflow: auto;
                }

                /* On a narrow portrait viewport, additionally rotate the
                   whole shell 90deg so it displays in landscape — swap its
                   box to viewport-height x viewport-width before rotating
                   so it still exactly fills the screen. Applied via
                   isNarrowPortrait (computed from window dimensions in JS)
                   rather than an orientation media query — that media
                   feature proved unreliable across browsers/devtools
                   device-emulation in testing. Real device rotation via the
                   Screen Orientation lock (attempted in
                   handleFullscreenToggle) covers Android Chrome; this
                   covers everything else, iOS Safari included.
                */
                /* Rotation itself is now applied to a separate inner
                   wrapper (see rotateInnerStyle in the component), not this
                   outer shell — see the comment on rotateInnerStyle for
                   why. This class is kept only as a state marker. */
                .kk-midi-shell[data-fullscreen="true"].kk-midi-rotate-landscape {
                    padding: 16px;
                    align-items: center;
                }

                .kk-midi-keyboard-frame {
                    border-radius: 12px;
                    background: linear-gradient(180deg, #1c2333 0%, #0b0f1a 100%);
                    padding: 10px 10px 8px;
                    box-shadow:
                        inset 0 1px 0 rgba(255, 255, 255, 0.06),
                        inset 0 0 0 1px rgba(255, 255, 255, 0.04),
                        0 18px 34px rgba(2, 6, 23, 0.35);
                }

                .kk-midi-keyboard-scroll {
                    overflow-x: auto;
                    overflow-y: hidden;
                    padding-bottom: 2px;
                    border-radius: 6px;
                }

                .kk-midi-keyboard-scroll::-webkit-scrollbar {
                    height: 6px;
                }

                .kk-midi-keyboard-scroll::-webkit-scrollbar-track {
                    background: transparent;
                }

                .kk-midi-keyboard-scroll::-webkit-scrollbar-thumb {
                    background: rgba(148, 163, 184, 0.35);
                    border-radius: 999px;
                }

                .kk-midi-keyboard {
                    position: relative;
                    display: grid;
                    grid-template-rows: 60% 40%;
                    width: 100%;
                    aspect-ratio: calc(var(--white-key-count) * 1) / 8.5;
                    max-height: 108px;
                    border-radius: 6px;
                    background: #060a13;
                    box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.55);
                    overflow: hidden;
                }

                .kk-midi-white-key {
                    position: relative;
                    grid-row: 1 / 3;
                    border-right: 1px solid rgba(15, 23, 42, 0.28);
                    border-radius: 0 0 2px 2px;
                    background: linear-gradient(180deg, #ffffff 0%, #ffffff 78%, #f2f4f7 93%, #e6e9ee 100%);
                    box-shadow: inset 0 -6px 5px rgba(15, 23, 42, 0.05);
                    transition: background 90ms ease, box-shadow 90ms ease;
                }

                .kk-midi-white-key.wk-first {
                    border-top-left-radius: 3px;
                    border-bottom-left-radius: 3px;
                }

                .kk-midi-white-key.wk-last {
                    border-right: 0;
                    border-top-right-radius: 3px;
                    border-bottom-right-radius: 3px;
                }

                .kk-midi-white-key.is-active.range-blue {
                    background: linear-gradient(180deg, #6ba4ff 0%, #2563eb 100%);
                    box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.9), inset 0 -4px 10px rgba(29, 78, 216, 0.5), 0 0 14px rgba(47, 128, 255, 0.5);
                }

                .kk-midi-white-key.is-active.range-green {
                    background: linear-gradient(180deg, #6ee7a8 0%, #16a34a 100%);
                    box-shadow: inset 0 0 0 1px rgba(22, 163, 74, 0.9), inset 0 -4px 10px rgba(21, 128, 61, 0.5), 0 0 14px rgba(74, 222, 128, 0.5);
                }

                .kk-midi-black-key {
                    position: relative;
                    grid-row: 1 / 2;
                    z-index: 2;
                    border-radius: 0 0 2px 2px;
                    background: linear-gradient(180deg, #1a1d24 0%, #050608 8%, #000000 45%, #000000 100%);
                    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22), 0 1px 1px rgba(0, 0, 0, 0.8);
                    transition: background 90ms ease, box-shadow 90ms ease;
                }

                .kk-midi-black-key.is-active.range-blue {
                    background: linear-gradient(180deg, #6ba4ff 0%, #2563eb 100%);
                    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), inset 0 0 0 1px rgba(96, 165, 250, 0.7), 0 0 4px rgba(47, 128, 255, 0.55);
                }

                .kk-midi-black-key.is-active.range-green {
                    background: linear-gradient(180deg, #6ee7a8 0%, #16a34a 100%);
                    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), inset 0 0 0 1px rgba(74, 222, 128, 0.7), 0 0 4px rgba(34, 197, 94, 0.55);
                }

                .kk-midi-key-label {
                    position: absolute;
                    left: 50%;
                    bottom: 7px;
                    transform: translateX(-50%);
                    color: #9aa5b5;
                    font-size: 9px;
                    font-weight: 700;
                    letter-spacing: 0.02em;
                    line-height: 1;
                    pointer-events: none;
                }

                /* Mobile only: the C1/C2/.../C5 octave labels clutter the
                   already-small keys, so hide them there — desktop keeps
                   them. */
                @media (max-width: 900px) {
                    .kk-midi-key-label {
                        display: none;
                    }
                }

                .kk-midi-note-label {
                    position: absolute;
                    left: 50%;
                    bottom: 6px;
                    transform: translateX(-50%);
                    z-index: 3;
                    color: #ffffff;
                    font-size: 10px;
                    font-weight: 800;
                    letter-spacing: 0.02em;
                    line-height: 1;
                    white-space: nowrap;
                    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.55);
                    pointer-events: none;
                }

                .kk-midi-note-label--black {
                    bottom: 5px;
                    font-size: 8px;
                }

                .kk-midi-control-section {
                    position: relative;
                }

                .kk-midi-display-title {
                    margin: 0 0 14px;
                    color: #1f2937;
                    font-size: 19px;
                    font-weight: 800;
                    letter-spacing: 0;
                }

                .kk-midi-display-panel {
                    overflow: hidden;
                    border-radius: 10px;
                    background: var(--kk-midi-panel);
                    padding: 14px 16px 18px;
                    box-shadow: 0 18px 38px rgba(15, 23, 42, 0.14);
                }

                /* Mobile full-view landscape: the heading is hidden and
                   there's a lot more width to work with than height, so
                   trim the outer padding and let the keyboard grow taller
                   to actually use the space instead of sitting small in
                   the middle of the screen. */
                .kk-midi-display-panel--rotated {
                    padding: 8px 10px 10px;
                }

                /* No max-height here: aspect-ratio + max-height + width:100%
                   together are fragile — whichever constraint binds can
                   override the others, and a max-height tuned for the
                   6-octave default view fights the fuller 88-key layout
                   used here, shrinking it narrower than its container
                   instead of filling it. Landscape has plenty of vertical
                   room, so just let height follow width via aspect-ratio
                   with no ceiling. */
                .kk-midi-display-panel--rotated .kk-midi-keyboard {
                    max-height: none;
                }

                .kk-midi-display-panel--rotated .kk-midi-toolbar {
                    padding-bottom: 8px;
                }

                .kk-midi-display-panel--rotated .kk-midi-progress {
                    padding: 8px 0;
                }

                .kk-midi-toolbar {
                    display: grid;
                    grid-template-columns: minmax(0, 1fr);
                    gap: 8px;
                    align-items: center;
                    padding-bottom: 14px;
                    border-bottom: 1px solid rgba(148, 163, 184, 0.14);
                }

                .kk-midi-file-title {
                    display: flex;
                    min-width: 0;
                    align-items: center;
                    gap: 8px;
                    color: #e2e8f0;
                    font-size: 13px;
                    font-weight: 700;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .kk-midi-file-title i {
                    flex: 0 0 auto;
                    color: #94a3b8;
                    font-size: 12px;
                }

                .kk-midi-transport {
                    display: flex;
                    flex-wrap: nowrap;
                    align-items: center;
                    gap: 6px;
                    min-width: max-content;
                }

                .kk-midi-button {
                    display: inline-flex;
                    flex-shrink: 0;
                    height: 34px;
                    min-width: 60px;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                    border: 0;
                    border-radius: 4px;
                    padding: 0 10px;
                    color: #e2e8f0;
                    background: rgba(255, 255, 255, 0.09);
                    font-size: 12px;
                    font-weight: 800;
                    transition: background 150ms ease, color 150ms ease;
                }

                .kk-midi-button:hover:not(:disabled) {
                    background: rgba(255, 255, 255, 0.14);
                }

                .kk-midi-button-primary {
                    color: #ffffff;
                    background: var(--kk-midi-blue);
                }

                .kk-midi-button-primary:hover:not(:disabled) {
                    background: #1d6df2;
                }

                .kk-midi-button:disabled {
                    cursor: not-allowed;
                    color: #64748b;
                    background: rgba(100, 116, 139, 0.16);
                }

                .kk-midi-field {
                    display: flex;
                    min-width: 0;
                    align-items: center;
                    gap: 9px;
                    color: #cbd5e1;
                    font-size: 12px;
                    font-weight: 700;
                }

                .kk-midi-field-label {
                    flex: 0 0 auto;
                }

                .kk-midi-select {
                    height: 34px;
                    min-width: 0;
                    border: 1px solid rgba(148, 163, 184, 0.2);
                    border-radius: 4px;
                    background: rgba(255, 255, 255, 0.06);
                    color: #f8fafc;
                    font-size: 12px;
                    font-weight: 800;
                    outline: none;
                }

                .kk-midi-select {
                    width: 74px;
                    padding: 0 8px;
                }

                .kk-midi-select option {
                    color: #111827;
                    background: #ffffff;
                }

                .kk-midi-stepper {
                    display: inline-flex;
                    flex-shrink: 0;
                    align-items: center;
                    height: 34px;
                    border: 1px solid rgba(148, 163, 184, 0.2);
                    border-radius: 4px;
                    background: rgba(255, 255, 255, 0.06);
                    overflow: hidden;
                }

                .kk-midi-stepper-btn {
                    display: inline-flex;
                    flex-shrink: 0;
                    width: 28px;
                    height: 100%;
                    align-items: center;
                    justify-content: center;
                    border: 0;
                    background: transparent;
                    color: #cbd5e1;
                    font-size: 14px;
                    font-weight: 800;
                    line-height: 1;
                    transition: background 150ms ease, color 150ms ease;
                }

                .kk-midi-stepper-btn:hover:not(:disabled) {
                    background: rgba(255, 255, 255, 0.1);
                    color: #f8fafc;
                }

                .kk-midi-stepper-btn:disabled {
                    color: #475569;
                    cursor: not-allowed;
                }

                .kk-midi-stepper-value {
                    flex-shrink: 0;
                    min-width: 26px;
                    text-align: center;
                    color: #f8fafc;
                    font-size: 12px;
                    font-weight: 800;
                }

                .kk-midi-select-wrap {
                    position: relative;
                    display: inline-flex;
                    flex-shrink: 0;
                    align-items: center;
                }

                .kk-midi-select--tempo {
                    width: 84px;
                    padding: 0 26px 0 10px;
                    appearance: none;
                    -webkit-appearance: none;
                }

                .kk-midi-select-chevron {
                    position: absolute;
                    right: 10px;
                    color: #94a3b8;
                    font-size: 10px;
                    pointer-events: none;
                }

                .kk-midi-icon-group {
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                    gap: 6px;
                }

                .kk-midi-icon-btn {
                    display: inline-flex;
                    flex-shrink: 0;
                    width: 32px;
                    height: 32px;
                    align-items: center;
                    justify-content: center;
                    border: 0;
                    border-radius: 4px;
                    background: transparent;
                    color: #94a3b8;
                    font-size: 13px;
                    transition: background 150ms ease, color 150ms ease;
                }

                .kk-midi-icon-btn:hover:not(.kk-midi-icon-static) {
                    background: rgba(255, 255, 255, 0.1);
                    color: #f8fafc;
                }

                .kk-midi-icon-static {
                    color: #34d399;
                    cursor: default;
                }

                .kk-midi-progress {
                    display: grid;
                    grid-template-columns: 44px minmax(0, 1fr) 44px;
                    align-items: center;
                    gap: 12px;
                    padding: 15px 0;
                    color: #cbd5e1;
                    font-size: 12px;
                    font-weight: 800;
                }

                @media (min-width: 1180px) {
                    .kk-midi-toolbar {
                        grid-template-columns: minmax(70px, 1fr) max-content max-content max-content max-content;
                    }

                    .kk-midi-control-section + .kk-midi-control-section {
                        padding-left: 10px;
                        border-left: 1px solid rgba(148, 163, 184, 0.18);
                    }
                }

                @media (max-width: 1179px) {
                    .kk-midi-toolbar {
                        display: flex;
                        flex-wrap: nowrap;
                        align-items: center;
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                        padding-bottom: 10px;
                    }

                    .kk-midi-control-section {
                        flex-shrink: 0;
                    }

                    .kk-midi-control-section + .kk-midi-control-section {
                        padding-left: 10px;
                        border-left: 1px solid rgba(148, 163, 184, 0.18);
                    }

                    .kk-midi-file-title {
                        display: none;
                    }

                    .kk-midi-select {
                        width: auto;
                    }

                    .kk-midi-icon-group {
                        justify-content: flex-start;
                    }

                    .kk-midi-btn-label {
                        display: none;
                    }

                    .kk-midi-transport .kk-midi-button {
                        min-width: 34px;
                        padding: 0;
                    }
                }

                .kk-midi-range {
                    -webkit-appearance: none;
                    appearance: none;
                    height: 4px;
                    border: 0;
                    border-radius: 999px;
                    outline: none;
                }

                .kk-midi-range::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    appearance: none;
                    width: 14px;
                    height: 14px;
                    border-radius: 999px;
                    border: 3px solid #ffffff;
                    background: var(--kk-midi-blue);
                    box-shadow: 0 2px 7px rgba(15, 23, 42, 0.22);
                }

                .kk-midi-range::-moz-range-thumb {
                    width: 14px;
                    height: 14px;
                    border-radius: 999px;
                    border: 3px solid #ffffff;
                    background: var(--kk-midi-blue);
                    box-shadow: 0 2px 7px rgba(15, 23, 42, 0.22);
                }

                .kk-midi-range--amber::-webkit-slider-thumb {
                    background: var(--kk-midi-amber);
                }

                .kk-midi-range--amber::-moz-range-thumb {
                    background: var(--kk-midi-amber);
                }

                @media (max-width: 640px) {
                    .kk-midi-keyboard {
                        max-height: 84px;
                    }
                }

                @media (max-width: 900px) and (orientation: landscape) {
                    .kk-midi-keyboard {
                        max-height: 84px;
                    }
                }
            `}</style>

            <div style={rotateInnerStyle}>
            {!isRotated && (
                <h2 className="kk-midi-display-title">
                    MIDI Virtual Display
                </h2>
            )}

            <div className={`kk-midi-display-panel${isRotated ? " kk-midi-display-panel--rotated" : ""}`}>
                <div className="kk-midi-toolbar">
                    <div className="kk-midi-control-section">
                        <div className="kk-midi-file-title">
                            <i className="fa fa-music"></i>
                            <span className="truncate">{selectedFile?.name || data.title}</span>
                        </div>
                    </div>

                    <div className="kk-midi-control-section kk-midi-transport">
                        <button
                            type="button"
                            onClick={handlePlay}
                            disabled={!canPlay || isPlaying}
                            className="kk-midi-button kk-midi-button-primary"
                            aria-label="Play"
                        >
                            <i className="fa fa-play text-[10px]"></i>
                            <span className="kk-midi-btn-label">Play</span>
                        </button>
                        <button
                            type="button"
                            onClick={handlePause}
                            disabled={!canPlay || !isPlaying}
                            className="kk-midi-button"
                            aria-label="Pause"
                        >
                            <i className="fa fa-pause text-[10px]"></i>
                            <span className="kk-midi-btn-label">Pause</span>
                        </button>
                        <button
                            type="button"
                            onClick={handleStop}
                            disabled={!canPlay}
                            className="kk-midi-button"
                            aria-label="Stop"
                        >
                            <i className="fa fa-stop text-[10px]"></i>
                            <span className="kk-midi-btn-label">Stop</span>
                        </button>
                    </div>

                    <div className="kk-midi-control-section kk-midi-field">
                        <span className="kk-midi-field-label">
                            Transpose
                        </span>
                        <div className="kk-midi-stepper">
                            <button
                                type="button"
                                onClick={() => handleTransposeStep(-1)}
                                disabled={!canPlay || transpose <= -12}
                                className="kk-midi-stepper-btn"
                                aria-label="Transpose down"
                            >
                                &minus;
                            </button>
                            <span className="kk-midi-stepper-value">{transpose}</span>
                            <button
                                type="button"
                                onClick={() => handleTransposeStep(1)}
                                disabled={!canPlay || transpose >= 12}
                                className="kk-midi-stepper-btn"
                                aria-label="Transpose up"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <label className="kk-midi-control-section kk-midi-field">
                        <span className="kk-midi-field-label">
                            Tempo
                        </span>
                        <div className="kk-midi-select-wrap">
                            <select
                                value={tempoPercentValue}
                                onChange={(event) => handleTempoPercentChange(event.target.value)}
                                disabled={!canPlay}
                                className="kk-midi-select kk-midi-select--tempo"
                            >
                                {(TEMPO_PERCENT_OPTIONS.includes(tempoPercentValue)
                                    ? TEMPO_PERCENT_OPTIONS
                                    : [...TEMPO_PERCENT_OPTIONS, tempoPercentValue].sort((a, b) => a - b)
                                ).map((percent) => (
                                    <option key={percent} value={percent}>
                                        {percent}%
                                    </option>
                                ))}
                            </select>
                            <i className="fa-solid fa-chevron-down kk-midi-select-chevron" aria-hidden="true"></i>
                        </div>
                    </label>

                    <div className="kk-midi-control-section kk-midi-icon-group">
                        <button
                            type="button"
                            onClick={handleFullscreenToggle}
                            className="kk-midi-icon-btn"
                            aria-label={isFullscreen ? "Exit fullscreen" : "Enter fullscreen"}
                        >
                            <i className={`fa-solid ${isFullscreen ? "fa-compress" : "fa-expand"}`}></i>
                        </button>
                        <button
                            type="button"
                            onClick={handleMuteToggle}
                            className="kk-midi-icon-btn"
                            aria-label={isMuted ? "Unmute" : "Mute"}
                        >
                            <i className={`fa-solid ${isMuted ? "fa-volume-xmark" : "fa-volume-high"}`}></i>
                        </button>
                        <span
                            className="kk-midi-icon-btn kk-midi-icon-static"
                            title="Audio engine ready"
                            aria-hidden="true"
                        >
                            <i className="fa-solid fa-plug-circle-check"></i>
                        </span>
                    </div>
                </div>

                <label className="kk-midi-progress">
                    <span>{formatTime(timeInfo.current)}</span>
                    <input
                        type="range"
                        min="0"
                        max={timeInfo.duration || 0}
                        step="0.01"
                        value={timeInfo.current}
                        onChange={handleSeek}
                        disabled={!canPlay}
                        className="kk-midi-range"
                        style={rangeFill(progressPercent)}
                        aria-label="MIDI progress"
                    />
                    <span className="text-right">{formatTime(timeInfo.duration)}</span>
                </label>

                <div>
                    {loadState === "loading" && (
                        <div className="flex h-40 items-center justify-center text-sm font-semibold text-slate-400">
                            Loading MIDI...
                        </div>
                    )}

                    {loadState === "empty" && (
                        <div className="flex h-40 items-center justify-center px-6 text-center text-sm font-semibold text-slate-400">
                            No MIDI files have been uploaded yet.
                        </div>
                    )}

                    {loadState === "choose" && (
                        <div className="flex h-40 items-center justify-center px-6 text-center text-sm font-semibold text-slate-400">
                            Choose a MIDI file to begin.
                        </div>
                    )}

                    {loadState === "error" && (
                        <div className="flex h-40 items-center justify-center px-6 text-center text-sm font-semibold text-red-300">
                            {loadError}
                        </div>
                    )}

                    {canPlay && (
                        <div className="kk-midi-keyboard-frame">
                            <div className="kk-midi-keyboard-scroll">
                                <div
                                    className="kk-midi-keyboard"
                                    aria-label="MIDI piano keyboard"
                                    role="img"
                                    style={{
                                        "--white-key-count": keyboard.whitePitches.length,
                                        gridTemplateColumns: `repeat(${keyboard.totalColumns}, 1fr)`,
                                    }}
                                >
                                    {keyboard.whitePitches.map((pitch, idx) => {
                                        const colStart = idx * WHITE_KEY_SUBDIVISIONS + 1;
                                        const edgeClass = idx === 0
                                            ? " wk-first"
                                            : idx === keyboard.whitePitches.length - 1
                                                ? " wk-last"
                                                : "";

                                        const rangeClass = pitch < KEY_RANGE_SPLIT_PITCH ? " range-blue" : " range-green";

                                        return (
                                            <div
                                                key={pitch}
                                                className={`kk-midi-white-key${edgeClass}${rangeClass}${activePitches.has(pitch) ? " is-active" : ""}`}
                                                style={{
                                                    gridColumn: `${colStart} / ${colStart + WHITE_KEY_SUBDIVISIONS}`,
                                                }}
                                            >
                                                {pitch % 12 === 0 && (
                                                    <span className="kk-midi-key-label">
                                                        {pitchLabel(pitch)}
                                                    </span>
                                                )}
                                                {isFullscreen && activePitches.has(pitch) && (
                                                    <span className="kk-midi-note-label">
                                                        {noteName(pitch)}
                                                    </span>
                                                )}
                                            </div>
                                        );
                                    })}

                                    {keyboard.blackPitches.map((pitch) => {
                                        const rangeClass = pitch.pitch < KEY_RANGE_SPLIT_PITCH ? " range-blue" : " range-green";

                                        return (
                                            <div
                                                key={pitch.pitch}
                                                className={`kk-midi-black-key${rangeClass}${activePitches.has(pitch.pitch) ? " is-active" : ""}`}
                                                style={{
                                                    gridColumn: `${pitch.colStart} / ${pitch.colEnd}`,
                                                }}
                                                aria-hidden="true"
                                            >
                                                {isFullscreen && activePitches.has(pitch.pitch) && (
                                                    <span className="kk-midi-note-label kk-midi-note-label--black">
                                                        {noteName(pitch.pitch)}
                                                    </span>
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
            </div>
        </section>
    );
};

document.querySelectorAll(".midi-practice-player").forEach((element) => {
    const rawData = element.dataset.midiPractice;
    const data = rawData ? JSON.parse(rawData) : {};

    ReactDOM.createRoot(element).render(
        <React.StrictMode>
            <MidiPracticePlayer data={data} />
        </React.StrictMode>,
    );
});
