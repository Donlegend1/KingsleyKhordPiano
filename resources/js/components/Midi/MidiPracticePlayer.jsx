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
const KEYBOARD_OCTAVE_COUNT = 8;
const KEYBOARD_PITCH_COUNT = KEYBOARD_OCTAVE_COUNT * 12;
const KEYBOARD_LOWEST_START_PITCH = 12;
const KEYBOARD_HIGHEST_START_PITCH = 24;

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
        let minPitch = clamp(
            Math.floor((minNote - 12) / 12) * 12,
            KEYBOARD_LOWEST_START_PITCH,
            KEYBOARD_HIGHEST_START_PITCH,
        );
        let maxPitch = minPitch + KEYBOARD_PITCH_COUNT - 1;

        if (maxNote > maxPitch) {
            minPitch = clamp(
                Math.ceil((maxNote - KEYBOARD_PITCH_COUNT + 1) / 12) * 12,
                KEYBOARD_LOWEST_START_PITCH,
                KEYBOARD_HIGHEST_START_PITCH,
            );
            maxPitch = minPitch + KEYBOARD_PITCH_COUNT - 1;
        }

        if (minNote < minPitch) {
            minPitch = clamp(
                Math.floor(minNote / 12) * 12,
                KEYBOARD_LOWEST_START_PITCH,
                KEYBOARD_HIGHEST_START_PITCH,
            );
            maxPitch = minPitch + KEYBOARD_PITCH_COUNT - 1;
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
        <section className="kk-midi-shell">
            <style>{`
                .kk-midi-shell {
                    --kk-midi-blue: #2f80ff;
                    --kk-midi-amber: #f59e0b;
                    --kk-midi-track: #485365;
                    --kk-midi-panel: #101827;
                    --kk-midi-panel-soft: #172033;
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

                .kk-midi-keyboard-scroll::-webkit-scrollbar {
                    height: 8px;
                }

                .kk-midi-keyboard-scroll::-webkit-scrollbar-track {
                    background: rgba(148, 163, 184, 0.12);
                    border-radius: 999px;
                }

                .kk-midi-keyboard-scroll::-webkit-scrollbar-thumb {
                    background: rgba(148, 163, 184, 0.46);
                    border-radius: 999px;
                }

                .kk-midi-keyboard {
                    --white-key-width: calc(100% / var(--white-key-count));
                    --black-key-width: calc(var(--white-key-width) * 0.58);
                    position: relative;
                    height: 118px;
                    min-width: max(100%, 1568px);
                    border-radius: 4px;
                    background: #0f172a;
                    box-shadow: 0 16px 32px rgba(2, 6, 23, 0.22);
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
                    border-bottom: 1px solid #94a3b8;
                    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 63%, #e5e7eb 100%);
                    box-shadow: inset 0 -9px 11px rgba(15, 23, 42, 0.14);
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
                    background: linear-gradient(180deg, #dbeafe 0%, #60a5fa 100%);
                    box-shadow: inset 0 0 0 2px rgba(37, 99, 235, 0.58), 0 0 16px rgba(47, 128, 255, 0.42);
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
                    border-radius: 0 0 3px 3px;
                    background: linear-gradient(180deg, #111827 0%, #020617 100%);
                    box-shadow: 0 8px 14px rgba(2, 6, 23, 0.48);
                }

                .kk-midi-black-key.is-active {
                    background: linear-gradient(180deg, #60a5fa 0%, #2563eb 100%);
                    border-color: #1d4ed8;
                    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.24), 0 8px 14px rgba(2, 6, 23, 0.34);
                }

                .kk-midi-key-label {
                    position: absolute;
                    right: 5px;
                    bottom: 5px;
                    color: #64748b;
                    font-size: 10px;
                    font-weight: 800;
                    line-height: 1;
                    pointer-events: none;
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

                .kk-midi-toolbar {
                    display: grid;
                    grid-template-columns: minmax(0, 1fr);
                    gap: 12px;
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
                    gap: 8px;
                    min-width: max-content;
                }

                .kk-midi-button {
                    display: inline-flex;
                    height: 34px;
                    min-width: 70px;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    border: 0;
                    border-radius: 4px;
                    padding: 0 13px;
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

                .kk-midi-select,
                .kk-midi-number {
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

                .kk-midi-tempo-control {
                    display: grid;
                    grid-template-columns: 64px minmax(90px, 1fr) 50px;
                    align-items: center;
                    gap: 8px;
                    min-width: 0;
                    flex: 1 1 auto;
                }

                .kk-midi-number {
                    width: 64px;
                    padding: 0 8px;
                }

                .kk-midi-reset {
                    height: 34px;
                    border: 0;
                    border-radius: 4px;
                    background: transparent;
                    color: #93c5fd;
                    font-size: 12px;
                    font-weight: 800;
                    transition: background 150ms ease, color 150ms ease;
                }

                .kk-midi-reset:hover:not(:disabled) {
                    background: rgba(255, 255, 255, 0.1);
                    color: #bfdbfe;
                }

                .kk-midi-reset:disabled {
                    color: #475569;
                    cursor: not-allowed;
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

                .kk-midi-tempo-section {
                    min-width: 0;
                }

                @media (min-width: 920px) {
                    .kk-midi-toolbar {
                        grid-template-columns: minmax(140px, 1fr) auto minmax(128px, auto) minmax(260px, 320px);
                    }

                    .kk-midi-control-section + .kk-midi-control-section {
                        padding-left: 14px;
                        border-left: 1px solid rgba(148, 163, 184, 0.18);
                    }
                }

                @media (max-width: 919px) {
                    .kk-midi-toolbar {
                        align-items: stretch;
                    }

                    .kk-midi-transport {
                        min-width: 0;
                        flex-wrap: wrap;
                    }

                    .kk-midi-field {
                        align-items: flex-start;
                        flex-direction: column;
                    }

                    .kk-midi-select {
                        width: 100%;
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
                        height: 104px;
                    }
                }

                @media (max-width: 900px) and (orientation: landscape) {
                    .kk-midi-keyboard {
                        height: 104px;
                    }
                }
            `}</style>

            <h2 className="kk-midi-display-title">
                MIDI Virtual Display
            </h2>

            <div className="kk-midi-display-panel">
                <div className="kk-midi-engine" aria-hidden="true">
                    <midi-player ref={playerRef} />
                </div>

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
                        >
                            <i className="fa fa-play text-[10px]"></i>
                            Play
                        </button>
                        <button
                            type="button"
                            onClick={handlePause}
                            disabled={!canPlay || !isPlaying}
                            className="kk-midi-button"
                        >
                            <i className="fa fa-pause text-[10px]"></i>
                            Pause
                        </button>
                        <button
                            type="button"
                            onClick={handleStop}
                            disabled={!canPlay}
                            className="kk-midi-button"
                        >
                            <i className="fa fa-stop text-[10px]"></i>
                            Stop
                        </button>
                    </div>

                    <label className="kk-midi-control-section kk-midi-field">
                        <span className="kk-midi-field-label">
                            Transpose
                        </span>
                        <select
                            value={transpose}
                            onChange={(event) => setTranspose(Number(event.target.value))}
                            disabled={!canPlay}
                            className="kk-midi-select"
                        >
                            {transposeOptions.map((option) => (
                                <option key={option} value={option}>
                                    {option > 0 ? `+${option}` : option}
                                </option>
                            ))}
                        </select>
                    </label>

                    <label className="kk-midi-control-section kk-midi-field kk-midi-tempo-section">
                        <span className="kk-midi-field-label">
                            Tempo
                        </span>
                        <div className="kk-midi-tempo-control">
                            <input
                                type="number"
                                min="40"
                                max="220"
                                step="1"
                                value={tempo}
                                onChange={(event) => handleTempoChange(event.target.value)}
                                disabled={!canPlay}
                                className="kk-midi-number"
                            />
                            <input
                                type="range"
                                min="40"
                                max="220"
                                step="1"
                                value={tempo}
                                onChange={(event) => handleTempoChange(event.target.value)}
                                disabled={!canPlay}
                                className="kk-midi-range"
                                style={rangeFill(tempoPercent)}
                                aria-label="MIDI tempo"
                            />
                            <button
                                type="button"
                                onClick={() => setTempo(baseTempo)}
                                disabled={!canPlay || tempo === baseTempo}
                                className="kk-midi-reset"
                            >
                                Reset
                            </button>
                        </div>
                    </label>
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
