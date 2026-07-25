import React, { useEffect, useMemo, useRef, useState } from "react";
import ReactDOM from "react-dom/client";
import "html-midi-player";
import * as mm from "@magenta/music/esm/core.js";

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
const BLACK_KEY_OFFSETS = {
    1: 1,
    3: 2,
    6: 4,
    8: 5,
    10: 6,
};

const isBlackPitch = (pitch) => BLACK_KEY_CLASSES.has(((pitch % 12) + 12) % 12);

const pitchLabel = (pitch) => {
    const name = PITCH_NAMES[((pitch % 12) + 12) % 12];
    const octave = Math.floor(pitch / 12) - 1;

    return `${name}${octave}`;
};

const getActivePitches = (sequence, time) => {
    if (!sequence?.notes?.length) return new Set();

    return new Set(
        sequence.notes
            .filter((note) => (note.startTime || 0) <= time + 0.02 && (note.endTime || 0) >= time)
            .map((note) => note.pitch),
    );
};

const getSequencePitchRange = (sequence) => {
    const pitches = (sequence?.notes || []).map((note) => note.pitch).filter(Number.isFinite);

    return {
        minNote: pitches.length ? Math.min(...pitches) : 48,
        maxNote: pitches.length ? Math.max(...pitches) : 72,
    };
};

const getPlaybackState = (player) => player?.player?.getPlayState?.() || (player?.playing ? "started" : "stopped");
const transposeOptions = Array.from({ length: 25 }, (_, index) => index - 12);

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

const MidiPracticePlayer = ({ data }) => {
    const files = data.files || [];
    const playerRef = useRef(null);

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

    const selectedFile = useMemo(
        () => files.find((file) => String(file.id) === selectedFileId) || null,
        [files, selectedFileId],
    );

    const sequence = useMemo(() => {
        if (!sourceSequence) return null;

        return transformSequence(sourceSequence, transpose, tempo);
    }, [sourceSequence, tempo, transpose]);

    const keyboard = useMemo(() => {
        const { minNote, maxNote } = getSequencePitchRange(sequence);
        let minPitch = clamp(Math.floor(minNote / 12) * 12, 36, 60);
        let maxPitch = minPitch + 47;

        if (maxNote > maxPitch) {
            minPitch = clamp(Math.floor((maxNote - 47) / 12) * 12, 36, 60);
            maxPitch = minPitch + 47;
        }

        if (minNote < minPitch) {
            minPitch = clamp(Math.floor(minNote / 12) * 12, 36, 60);
            maxPitch = minPitch + 47;
        }

        const pitches = Array.from(
            { length: Math.max(maxPitch - minPitch + 1, 0) },
            (_, index) => minPitch + index,
        );
        const whitePitches = pitches.filter((pitch) => !isBlackPitch(pitch));
        const octaveCount = Math.max(Math.round((maxPitch - minPitch) / 12), 1);
        const blackPitches = pitches
            .filter(isBlackPitch)
            .map((pitch) => ({
                pitch,
                left: (((Math.floor((pitch - minPitch) / 12) * 7) + BLACK_KEY_OFFSETS[pitch % 12]) / (octaveCount * 7)) * 100,
            }));

        return { pitches, whitePitches, blackPitches, octaveCount };
    }, [sequence]);

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
                playerRef.current?.stop?.();

                const loadedSequence = await mm.urlToNoteSequence(selectedFile.midiUrl);

                if (cancelled) return;

                const qpm = Math.round(loadedSequence.tempos?.[0]?.qpm || 100);
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
        if (!sequence || !playerRef.current) return;

        const player = playerRef.current;

        player.stop?.();
        player.noteSequence = sequence;
        player.currentTime = 0;
        player.soundFont = null;
        player.loop = false;

        setIsPlaying(false);
        setTimeInfo({ current: 0, duration: sequence.totalTime || 0 });
        setActivePitches(new Set());
    }, [sequence]);

    useEffect(() => {
        const player = playerRef.current;
        if (!player) return undefined;

        const syncTime = () => {
            const current = Number(player.currentTime || 0);
            const playbackState = getPlaybackState(player);
            const isStarted = playbackState === "started";

            setIsPlaying(isStarted);
            setTimeInfo({
                current,
                duration: Number(player.duration || sequence?.totalTime || 0),
            });
            setActivePitches(isStarted ? getActivePitches(sequence, current) : new Set());
        };

        const interval = window.setInterval(syncTime, 250);
        player.addEventListener("load", syncTime);
        player.addEventListener("start", syncTime);
        player.addEventListener("stop", syncTime);
        player.addEventListener("note", syncTime);

        return () => {
            window.clearInterval(interval);
            player.removeEventListener("load", syncTime);
            player.removeEventListener("start", syncTime);
            player.removeEventListener("stop", syncTime);
            player.removeEventListener("note", syncTime);
        };
    }, [sequence]);

    const canPlay = loadState === "ready" && sequence;
    const progressPercent = timeInfo.duration ? (timeInfo.current / timeInfo.duration) * 100 : 0;
    const tempoPercent = ((tempo - 40) / 180) * 100;
    const handlePlay = () => {
        if (!canPlay) return;
        playerRef.current?.start?.();
        setIsPlaying(true);
    };

    const handlePause = () => {
        const magentaPlayer = playerRef.current?.player;

        if (magentaPlayer?.pause && magentaPlayer?.isPlaying?.()) {
            magentaPlayer.pause();
            setIsPlaying(false);
        } else {
            playerRef.current?.stop?.();
            setIsPlaying(false);
        }
    };

    const handleStop = () => {
        playerRef.current?.stop?.();
        if (playerRef.current) {
            playerRef.current.currentTime = 0;
        }
        setIsPlaying(false);
        setTimeInfo((current) => ({ ...current, current: 0 }));
        setActivePitches(new Set());
    };

    const handleSeek = (event) => {
        const nextTime = Number(event.target.value);

        if (playerRef.current) {
            playerRef.current.currentTime = nextTime;
        }

        setTimeInfo((current) => ({ ...current, current: nextTime }));
        setActivePitches(getActivePitches(sequence, nextTime));
    };

    const handleTempoChange = (value) => {
        const nextTempo = clamp(Math.round(Number(value) || baseTempo), 40, 220);
        setTempo(nextTempo);
    };

    return (
        <section className="kk-midi-shell overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <style>{`
                .kk-midi-shell {
                    --kk-midi-blue: #2563eb;
                    --kk-midi-amber: #f59e0b;
                    --kk-midi-track: #dbe3ef;
                }

                .kk-midi-engine {
                    position: absolute;
                    width: 1px;
                    height: 1px;
                    overflow: hidden;
                    clip: rect(0 0 0 0);
                    white-space: nowrap;
                }

                .kk-midi-keyboard-scroll {
                    overflow-x: auto;
                    overflow-y: hidden;
                    padding-bottom: 2px;
                }

                .kk-midi-keyboard {
                    --white-key-width: calc(100% / var(--white-key-count));
                    --black-key-width: calc(var(--white-key-width) * 0.58);
                    position: relative;
                    height: 178px;
                    min-width: 100%;
                    border: 1px solid #cbd5e1;
                    border-radius: 8px;
                    background: #f8fafc;
                    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.1);
                }

                .kk-midi-white-row {
                    display: grid;
                    height: 100%;
                    padding-top: 0;
                }

                .kk-midi-white-key {
                    position: relative;
                    height: 100%;
                    border-right: 1px solid #cbd5e1;
                    border-bottom: 1px solid #cbd5e1;
                    background: linear-gradient(180deg, #ffffff 0%, #ffffff 55%, #f3f6fb 100%);
                    box-shadow: inset 0 -8px 12px rgba(148, 163, 184, 0.12);
                }

                .kk-midi-white-key:first-child {
                    border-top-left-radius: 8px;
                    border-bottom-left-radius: 8px;
                }

                .kk-midi-white-key:last-child {
                    border-right: 0;
                    border-top-right-radius: 8px;
                    border-bottom-right-radius: 8px;
                }

                .kk-midi-white-key.is-active {
                    background: linear-gradient(180deg, #dbeafe 0%, #93c5fd 100%);
                    box-shadow: inset 0 0 0 2px rgba(37, 99, 235, 0.38);
                }

                .kk-midi-black-key {
                    position: absolute;
                    top: 0;
                    z-index: 2;
                    width: var(--black-key-width);
                    height: 62%;
                    transform: translateX(-50%);
                    border: 1px solid #111827;
                    border-top: 0;
                    border-radius: 0 0 4px 4px;
                    background: linear-gradient(180deg, #172033 0%, #020617 100%);
                    box-shadow: 0 7px 12px rgba(15, 23, 42, 0.28);
                }

                .kk-midi-black-key.is-active {
                    background: linear-gradient(180deg, #fbbf24 0%, #d97706 100%);
                    border-color: #92400e;
                    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.28), 0 8px 14px rgba(15, 23, 42, 0.25);
                }

                .kk-midi-key-label {
                    position: absolute;
                    right: 5px;
                    bottom: 5px;
                    color: #94a3b8;
                    font-size: 11px;
                    font-weight: 800;
                    line-height: 1;
                    pointer-events: none;
                }

                .kk-midi-control-section {
                    position: relative;
                }

                .kk-midi-controls {
                    display: grid;
                    grid-template-columns: minmax(0, 1fr);
                    gap: 12px;
                }

                .kk-midi-tempo-section {
                    min-width: 0;
                }

                @media (min-width: 900px) {
                    .kk-midi-controls {
                        grid-template-columns: auto minmax(220px, 1fr);
                    }

                    .kk-midi-tempo-section {
                        grid-column: 1 / -1;
                    }
                }

                @media (min-width: 1280px) {
                    .kk-midi-controls {
                        grid-template-columns: auto minmax(220px, 360px) minmax(380px, 1fr);
                    }

                    .kk-midi-tempo-section {
                        grid-column: auto;
                    }

                    .kk-midi-control-section + .kk-midi-control-section {
                        padding-left: 18px;
                    }

                    .kk-midi-control-section + .kk-midi-control-section::before {
                        content: "";
                        position: absolute;
                        left: 0;
                        top: 6px;
                        bottom: 6px;
                        width: 1px;
                        background: #dbe3ef;
                    }
                }

                .kk-midi-range {
                    -webkit-appearance: none;
                    appearance: none;
                    height: 6px;
                    border: 0;
                    border-radius: 999px;
                    outline: none;
                }

                .kk-midi-range::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    appearance: none;
                    width: 18px;
                    height: 18px;
                    border-radius: 999px;
                    border: 3px solid #ffffff;
                    background: var(--kk-midi-blue);
                    box-shadow: 0 2px 7px rgba(15, 23, 42, 0.22);
                }

                .kk-midi-range::-moz-range-thumb {
                    width: 18px;
                    height: 18px;
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
                        height: 136px;
                        min-width: 780px;
                    }
                }

                @media (max-width: 900px) and (orientation: landscape) {
                    .kk-midi-keyboard {
                        height: 124px;
                    }
                }
            `}</style>

            <div className="grid gap-3 border-b border-slate-200 px-4 py-3 lg:grid-cols-[minmax(0,1fr)_minmax(260px,520px)] lg:items-center lg:px-5">
                <div className="min-w-0 lg:flex lg:items-center lg:gap-3">
                    <p className="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400 lg:shrink-0">
                        MIDI Practice
                    </p>
                    <h3 className="mt-1 truncate text-base font-bold leading-tight text-slate-950 lg:mt-0">
                        {selectedFile?.name || data.title}
                    </h3>
                </div>

                <div className="min-w-0 space-y-2">
                    {files.length > 1 && (
                        <select
                            value={selectedFileId}
                            onChange={(event) => setSelectedFileId(event.target.value)}
                            className="h-11 w-full min-w-0 rounded-md border border-slate-300 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">Choose MIDI file</option>
                            {files.map((file) => (
                                <option key={file.id} value={file.id}>
                                    {file.name}
                                </option>
                            ))}
                        </select>
                    )}
                </div>
            </div>

            <div className="space-y-4 bg-slate-50 px-4 py-4 lg:px-5">
                <div className="rounded-lg border border-slate-200 bg-white p-3">
                    {loadState === "loading" && (
                        <div className="flex h-64 items-center justify-center text-sm font-semibold text-slate-500">
                            Loading MIDI...
                        </div>
                    )}

                    {loadState === "empty" && (
                        <div className="flex h-64 items-center justify-center px-6 text-center text-sm font-semibold text-slate-500">
                            No MIDI files have been uploaded yet.
                        </div>
                    )}

                    {loadState === "choose" && (
                        <div className="flex h-64 items-center justify-center px-6 text-center text-sm font-semibold text-slate-500">
                            Choose a MIDI file to begin.
                        </div>
                    )}

                    {loadState === "error" && (
                        <div className="flex h-64 items-center justify-center px-6 text-center text-sm font-semibold text-red-600">
                            {loadError}
                        </div>
                    )}

                    {canPlay && (
                        <div className="kk-midi-keyboard-scroll">
                            <div
                                className="kk-midi-keyboard"
                                aria-label="MIDI piano keyboard"
                                role="img"
                                style={{ "--white-key-count": keyboard.whitePitches.length }}
                            >
                                <div
                                    className="kk-midi-white-row"
                                    style={{
                                        gridTemplateColumns: `repeat(${keyboard.whitePitches.length}, minmax(0, 1fr))`,
                                    }}
                                >
                                    {keyboard.whitePitches.map((pitch) => (
                                        <div
                                            key={pitch}
                                            className={`kk-midi-white-key${activePitches.has(pitch) ? " is-active" : ""}`}
                                        >
                                            {pitch % 12 === 0 && (
                                                <span className="kk-midi-key-label">
                                                    {pitchLabel(pitch)}
                                                </span>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                {keyboard.blackPitches.map((pitch) => {
                                    return (
                                        <div
                                            key={pitch.pitch}
                                            className={`kk-midi-black-key${activePitches.has(pitch.pitch) ? " is-active" : ""}`}
                                            style={{
                                                left: `${pitch.left}%`,
                                            }}
                                            aria-hidden="true"
                                        />
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>

                <div className="kk-midi-engine" aria-hidden="true">
                    <midi-player ref={playerRef} />
                </div>

                <label className="grid grid-cols-[44px_minmax(0,1fr)_44px] items-center gap-3 text-sm font-bold text-slate-500">
                    <span>{formatTime(timeInfo.current)}</span>
                    <input
                        type="range"
                        min="0"
                        max={timeInfo.duration || 0}
                        step="0.01"
                        value={timeInfo.current}
                        onChange={handleSeek}
                        disabled={!canPlay}
                        className="kk-midi-range w-full cursor-pointer disabled:cursor-not-allowed disabled:opacity-40"
                        style={rangeFill(progressPercent)}
                        aria-label="MIDI progress"
                    />
                    <span className="text-right">{formatTime(timeInfo.duration)}</span>
                </label>

                <div className="kk-midi-controls min-w-0 items-center rounded-lg border border-slate-200 bg-white p-3">
                    <div className="kk-midi-control-section flex min-w-0 flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={handlePlay}
                            disabled={!canPlay || isPlaying}
                            className="inline-flex h-10 min-w-[88px] items-center justify-center gap-2 rounded-md bg-indigo-600 px-3 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                        >
                            <i className="fa fa-play text-xs"></i>
                            Play
                        </button>
                        <button
                            type="button"
                            onClick={handlePause}
                            disabled={!canPlay || !isPlaying}
                            className="inline-flex h-10 min-w-[88px] items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45"
                        >
                            <i className="fa fa-pause text-xs"></i>
                            Pause
                        </button>
                        <button
                            type="button"
                            onClick={handleStop}
                            disabled={!canPlay}
                            className="inline-flex h-10 min-w-[82px] items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45"
                        >
                            <i className="fa fa-stop text-xs"></i>
                            Stop
                        </button>
                    </div>

                    <label className="kk-midi-control-section flex min-w-0 items-center gap-2">
                        <span className="shrink-0 text-xs font-bold uppercase tracking-wide text-slate-500">
                            Key
                        </span>
                        <select
                            value={transpose}
                            onChange={(event) => setTranspose(Number(event.target.value))}
                            disabled={!canPlay}
                            className="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm font-bold text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:opacity-45"
                        >
                            {transposeOptions.map((option) => (
                                <option key={option} value={option}>
                                    {option > 0 ? `+${option}` : option} st
                                </option>
                            ))}
                        </select>
                    </label>

                    <label className="kk-midi-control-section kk-midi-tempo-section flex min-w-0 items-center gap-2">
                        <span className="shrink-0 text-xs font-bold uppercase tracking-wide text-slate-500">
                            Tempo
                        </span>
                        <div className="grid min-w-0 flex-1 grid-cols-[72px_minmax(0,1fr)_64px] items-center gap-2">
                            <input
                                type="number"
                                min="40"
                                max="220"
                                step="1"
                                value={tempo}
                                onChange={(event) => handleTempoChange(event.target.value)}
                                disabled={!canPlay}
                                className="h-10 w-full rounded-md border border-slate-300 bg-white px-2 text-sm font-bold text-slate-700 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:opacity-45"
                            />
                            <input
                                type="range"
                                min="40"
                                max="220"
                                step="1"
                                value={tempo}
                                onChange={(event) => handleTempoChange(event.target.value)}
                                disabled={!canPlay}
                                className="kk-midi-range min-w-0 w-full disabled:opacity-40"
                                style={rangeFill(tempoPercent)}
                                aria-label="MIDI tempo"
                            />
                            <button
                                type="button"
                                onClick={() => setTempo(baseTempo)}
                                disabled={!canPlay || tempo === baseTempo}
                                className="h-10 w-16 rounded-md px-2 text-xs font-bold text-indigo-600 transition hover:bg-indigo-50 hover:text-indigo-700 disabled:text-slate-300 disabled:hover:bg-transparent"
                            >
                                Reset
                            </button>
                        </div>
                    </label>
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
