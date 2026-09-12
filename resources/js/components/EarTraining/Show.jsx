import React, { useState, useEffect, useRef } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import CourseComment from "../Comment/CourseComment";
import { CheckCircle2, Trophy, Headphones, Play, Square, Gauge, ListChecks, Repeat, Lightbulb, ArrowLeft, Check, ChevronDown, HelpCircle, ChevronRight, X, RotateCcw, Trash2 } from "lucide-react";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const shuffleArray = (array) => {
    return [...array].sort(() => Math.random() - 0.5);
};

const QUIZ_QUESTION_CAP = 25;

// Builds the question set a student actually plays through: if the lesson has
// fewer questions than the cap, the pool is reshuffled and repeated until the
// cap is reached, instead of ending the quiz early after just the pool size.
const buildQuestionSet = (questions, cap = QUIZ_QUESTION_CAP) => {
    if (!questions || questions.length === 0) return [];
    if (questions.length >= cap) {
        return shuffleArray(questions).slice(0, cap);
    }

    const result = [];
    while (result.length < cap) {
        result.push(...shuffleArray(questions));
    }
    return result.slice(0, cap);
};

const formatAudioTime = (seconds) => {
    if (!seconds || !isFinite(seconds)) return "0:00";
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${String(secs).padStart(2, "0")}`;
};

const ReferenceAudioItem = ({ name, src }) => {
    const audioRef = useRef(null);
    const [isPlaying, setIsPlaying] = useState(false);
    const [progress, setProgress] = useState(0);
    const [currentTime, setCurrentTime] = useState(0);
    const [duration, setDuration] = useState(0);

    const toggle = () => {
        const audioEl = audioRef.current;
        if (!audioEl) return;
        if (audioEl.paused) {
            audioEl.play();
            setIsPlaying(true);
        } else {
            audioEl.pause();
            audioEl.currentTime = 0;
            setProgress(0);
            setCurrentTime(0);
            setIsPlaying(false);
        }
    };

    const seek = (event) => {
        const audioEl = audioRef.current;
        if (!audioEl || !audioEl.duration) return;
        const rect = event.currentTarget.getBoundingClientRect();
        const ratio = Math.min(Math.max((event.clientX - rect.left) / rect.width, 0), 1);
        audioEl.currentTime = ratio * audioEl.duration;
    };

    const handleTimeUpdate = () => {
        const audioEl = audioRef.current;
        if (!audioEl || !audioEl.duration) return;
        setProgress((audioEl.currentTime / audioEl.duration) * 100);
        setCurrentTime(audioEl.currentTime);
    };

    return (
        <div className="flex items-center gap-4">
            <audio
                ref={audioRef}
                src={src}
                onLoadedMetadata={(e) => setDuration(e.currentTarget.duration)}
                onTimeUpdate={handleTimeUpdate}
                onEnded={() => {
                    setIsPlaying(false);
                    setProgress(0);
                    setCurrentTime(0);
                }}
                className="hidden"
            />

            <button
                onClick={toggle}
                className="flex items-center justify-center w-12 h-12 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 flex-shrink-0 hover:bg-gray-800 dark:hover:bg-gray-100 active:scale-95 transition-all duration-150"
            >
                {isPlaying ? (
                    <Square className="w-3.5 h-3.5 fill-current" />
                ) : (
                    <Play className="w-4 h-4 fill-current ml-0.5" />
                )}
            </button>

            <div className="flex-1 min-w-0">
                <div className="flex items-center justify-between mb-1.5">
                    <p className="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">
                        {name || "Reference Audio"}
                    </p>
                    <span className="text-xs text-gray-400 dark:text-gray-500 tabular-nums flex-shrink-0 ml-3">
                        {formatAudioTime(currentTime)} / {formatAudioTime(duration)}
                    </span>
                </div>

                <div
                    onClick={seek}
                    className="group relative w-full h-1.5 bg-gray-100 dark:bg-white/10 rounded-full cursor-pointer"
                >
                    <div
                        className="absolute inset-y-0 left-0 bg-gray-900 dark:bg-white rounded-full"
                        style={{ width: `${progress}%` }}
                    ></div>
                    <div
                        className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-gray-900 dark:bg-white opacity-0 group-hover:opacity-100 transition-opacity duration-150"
                        style={{ left: `${progress}%` }}
                    ></div>
                </div>
            </div>
        </div>
    );
};

// One octave, C3 -> C4.
const PIANO_WHITE_KEYS = [
    { label: "C", note: "C3" },
    { label: "D", note: "D3" },
    { label: "E", note: "E3" },
    { label: "F", note: "F3" },
    { label: "G", note: "G3" },
    { label: "A", note: "A3" },
    { label: "B", note: "B3" },
    { label: "C", note: "C4" },
];
const PIANO_BLACK_KEYS = [
    { label: "C♯", note: "C♯3", afterIndex: 0 },
    { label: "D♯", note: "D♯3", afterIndex: 1 },
    { label: "F♯", note: "F♯3", afterIndex: 3 },
    { label: "G♯", note: "G♯3", afterIndex: 4 },
    { label: "A♯", note: "A♯3", afterIndex: 5 },
];

const NOTE_FREQUENCIES = {
    C3: 130.81,
    "C♯3": 138.59,
    D3: 146.83,
    "D♯3": 155.56,
    E3: 164.81,
    F3: 174.61,
    "F♯3": 185.0,
    G3: 196.0,
    "G♯3": 207.65,
    A3: 220.0,
    "A♯3": 233.08,
    B3: 246.94,
    C4: 261.63,
};

// Synthesized piano-like tone (fundamental + decaying harmonics) —
// no licensed Yamaha C7 samples are bundled with the app.
const playPianoNote = (audioCtxRef, freq) => {
    if (!window.AudioContext && !window.webkitAudioContext) return;
    if (!audioCtxRef.current) {
        audioCtxRef.current = new (window.AudioContext || window.webkitAudioContext)();
    }
    const ctx = audioCtxRef.current;
    if (ctx.state === "suspended") ctx.resume();

    const now = ctx.currentTime;
    const masterGain = ctx.createGain();
    masterGain.gain.setValueAtTime(0, now);
    masterGain.gain.linearRampToValueAtTime(0.35, now + 0.008);
    masterGain.gain.exponentialRampToValueAtTime(0.0001, now + 1.6);
    masterGain.connect(ctx.destination);

    const partials = [
        { mult: 1, gain: 1 },
        { mult: 2, gain: 0.5 },
        { mult: 3, gain: 0.22 },
        { mult: 4, gain: 0.1 },
    ];
    partials.forEach(({ mult, gain }) => {
        const osc = ctx.createOscillator();
        osc.type = "sine";
        osc.frequency.setValueAtTime(freq * mult, now);
        const partialGain = ctx.createGain();
        partialGain.gain.setValueAtTime(gain, now);
        osc.connect(partialGain);
        partialGain.connect(masterGain);
        osc.start(now);
        osc.stop(now + 1.7);
    });
};

const PianoKeyboard = ({ highlightNote = null, includeOctaveC = true }) => {
    const [pressedKey, setPressedKey] = useState(null);
    const audioCtxRef = useRef(null);
    const whiteKeys = includeOctaveC
        ? PIANO_WHITE_KEYS
        : PIANO_WHITE_KEYS.filter((k) => k.note !== "C4");

    const press = (note) => {
        setPressedKey(note);
        if (!highlightNote || note === highlightNote) {
            playPianoNote(audioCtxRef, NOTE_FREQUENCIES[note]);
        }
    };
    const release = () => setPressedKey(null);

    return (
        <div className="relative flex w-full max-w-xl h-32 sm:h-36 mx-auto select-none">
            {whiteKeys.map(({ label, note }) => {
                const isPressed = pressedKey === note;
                const isReference = highlightNote === note;
                return (
                    <div
                        key={note}
                        onPointerDown={() => press(note)}
                        onPointerUp={release}
                        onPointerLeave={release}
                        className={`relative flex-1 flex items-end justify-center pb-3 border first:rounded-l-lg last:rounded-r-lg border-l-0 first:border-l shadow-sm cursor-pointer transition-colors duration-150 ${
                            isPressed
                                ? "bg-emerald-400 border-gray-200"
                                : isReference
                                ? "bg-amber-100 border-amber-400 border-2"
                                : "bg-white dark:bg-white border-gray-200"
                        }`}
                    >
                        {isReference && !isPressed && (
                            <span className="absolute top-2 w-2 h-2 rounded-full bg-amber-500"></span>
                        )}
                        <span className={`text-xs font-semibold transition-colors duration-150 ${isPressed ? "text-white" : isReference ? "text-amber-700" : "text-gray-400"}`}>
                            {label}
                        </span>
                    </div>
                );
            })}
            {PIANO_BLACK_KEYS.map(({ label, note, afterIndex }) => {
                const isPressed = pressedKey === note;
                const isReference = highlightNote === note;
                return (
                    <div
                        key={note}
                        onPointerDown={() => press(note)}
                        onPointerUp={release}
                        onPointerLeave={release}
                        className={`absolute top-0 h-[62%] w-[9%] flex items-end justify-center pb-2 rounded-b-md shadow-md z-10 cursor-pointer transition-colors duration-150 ${
                            isPressed
                                ? "bg-emerald-500"
                                : isReference
                                ? "bg-amber-500"
                                : "bg-gray-900 dark:bg-gray-900"
                        }`}
                        style={{ left: `${((afterIndex + 1) / whiteKeys.length) * 100}%`, transform: "translateX(-50%)" }}
                    >
                        <span className="text-[10px] font-semibold text-gray-400">{label}</span>
                    </div>
                );
            })}
        </div>
    );
};

const CircularProgress = ({ percent, size = 18, trackClassName, fillClassName }) => {
    const strokeWidth = 2.5;
    const radius = (size - strokeWidth) / 2;
    const circumference = 2 * Math.PI * radius;
    const offset = circumference - (Math.min(100, Math.max(0, percent)) / 100) * circumference;

    return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className="flex-shrink-0 -rotate-90">
            <circle
                cx={size / 2}
                cy={size / 2}
                r={radius}
                fill="none"
                strokeWidth={strokeWidth}
                className={trackClassName}
                stroke="currentColor"
            />
            <circle
                cx={size / 2}
                cy={size / 2}
                r={radius}
                fill="none"
                strokeWidth={strokeWidth}
                strokeLinecap="round"
                strokeDasharray={circumference}
                strokeDashoffset={offset}
                className={`${fillClassName} transition-all duration-500 ease-out`}
                stroke="currentColor"
            />
        </svg>
    );
};

const ShowEartraining = () => {
    const { showMessage } = useFlashMessage();
    const [quiz, setQuiz] = useState(null);
    const [currentQuestion, setCurrentQuestion] = useState(0);
    const [selectedOption, setSelectedOption] = useState(null);
    const [selectedSequence, setSelectedSequence] = useState([]);
    const [selectedQuality, setSelectedQuality] = useState(null);
    const [selectedInversion, setSelectedInversion] = useState(null);
    const [selectedMultiOptions, setSelectedMultiOptions] = useState([]);
    const [selectedChordQuality, setSelectedChordQuality] = useState([]);
    const [selectedChordDegree, setSelectedChordDegree] = useState([]);
    const [selectedChordDegreeAccidentals, setSelectedChordDegreeAccidentals] = useState({});
    const [selectedExclude, setSelectedExclude] = useState([]);
    const [selectedProgression, setSelectedProgression] = useState([]);
    const [selectedProgQuality, setSelectedProgQuality] = useState([]);
    const [pressedPaletteKey, setPressedPaletteKey] = useState(null);
    const [isSiblingDropdownOpen, setIsSiblingDropdownOpen] = useState(false);
    const [openQualityDropdownPosition, setOpenQualityDropdownPosition] = useState(null);
    const [selectedProgDegreeMulti, setSelectedProgDegreeMulti] = useState([]);
    const [isSubmitted, setIsSubmitted] = useState(false);
    const [showResult, setShowResult] = useState(false);
    const [score, setScore] = useState(0);
    const [quizStarted, setQuizStarted] = useState(false);
    const [isCompleted, setIsCompleted] = useState(
        document.getElementById("ear-training-quiz-show")?.dataset.completed === "1"
    );
    const [markingComplete, setMarkingComplete] = useState(false);
    const [siblings, setSiblings] = useState([]);
    const [isQuestionPlaying, setIsQuestionPlaying] = useState(false);
    const questionAudioRef = useRef(null);
    const videoEmbedRef = useRef(null);

    const toggleQuestionAudio = () => {
        const audioEl = questionAudioRef.current;
        if (!audioEl) return;
        if (audioEl.paused) {
            audioEl.play();
        } else {
            audioEl.pause();
        }
    };

    const WAVEFORM_BARS = [4, 8, 14, 10, 20, 12, 26, 16, 22, 8, 18, 24, 10, 20, 6, 14];


    const startQuiz = () => {
        setQuizStarted(true);
    };

    const exitQuiz = () => {
        setQuizStarted(false);
        setCurrentQuestion(0);
        setSelectedOption(null);
        setSelectedSequence([]);
        setSelectedQuality(null);
        setSelectedInversion(null);
        setSelectedMultiOptions([]);
        setSelectedChordQuality([]);
        setSelectedChordDegree([]);
        setSelectedChordDegreeAccidentals({});
        setSelectedExclude([]);
        setSelectedProgression([]);
        setSelectedProgQuality([]);
        setSelectedProgDegreeMulti([]);
        setIsSubmitted(false);
        setShowResult(false);
        setScore(0);
    };

    const handleMarkAsCompleted = async () => {
        if (!quiz?.id || isCompleted) return;
        setMarkingComplete(true);
        try {
            await axios.post("/member/lesson-completion", {
                completable_id: quiz.id,
                completable_type: "quizzes",
            });
            setIsCompleted(true);
            showMessage("Marked as completed!", "success");
        } catch (error) {
            console.error("Error marking quiz as completed:", error);
            showMessage("Failed to mark as completed.", "error");
        } finally {
            setMarkingComplete(false);
        }
    };

    const SINGLE_TONE_SOLFA_OPTIONS = ["Doh", "Reh", "Mi", "Fah", "Sol", "Lah", "Ti"];
    const RELATIVE_SECOND_OPTIONS = [
        "Doh - Reh",
        "Reh - Mi",
        "Mi - Fah",
        "Fah - Sol",
        "Sol - Lah",
        "Lah - Ti",
        "Ti - Doh",
    ];
    const RELATIVE_OPTIONS = [
        "Doh - Mi",
        "Reh - Fah",
        "Mi - Sol",
        "Fah - Lah",
        "Sol - Ti",
        "Lah - Doh",
        "Ti - Reh",
    ];
    const RELATIVE_FOURTH_OPTIONS = [
        "Doh - Fah",
        "Reh - Sol",
        "Mi - Lah",
        "Fah - Ti",
        "Sol - Doh'",
        "Lah - Reh'",
        "Ti - Mi'",
    ];
    const RELATIVE_FIFTH_OPTIONS = [
        "Doh - Sol",
        "Reh - Lah",
        "Mi - Ti",
        "Fah - Doh'",
        "Sol - Reh'",
        "Lah - Mi'",
        "Ti - Fah'",
    ];
    const RELATIVE_SIXTH_OPTIONS = [
        "Doh - Lah",
        "Reh - Ti",
        "Mi - Doh'",
        "Fah - Reh'",
        "Sol - Mi'",
        "Lah - Fah'",
        "Ti - Sol'",
    ];
    const RELATIVE_SEVENTH_OPTIONS = [
        "Doh - Ti",
        "Reh - Doh'",
        "Mi - Reh'",
        "Fah - Mi'",
        "Sol - Fah'",
        "Lah - Sol'",
        "Ti - Lah'",
    ];
    const FIND_THE_KEY_OPTIONS = [
        "C",
        "C♯/Db",
        "D",
        "D♯/Eb",
        "E",
        "F",
        "F♯/Gb",
        "G",
        "G♯/Ab",
        "A",
        "A♯/Bb",
        "B",
    ];
    // Maps the admin's saved reference_note label (matching FIND_THE_KEY_OPTIONS)
    // to the note string PianoKeyboard/PIANO_WHITE_KEYS/PIANO_BLACK_KEYS expect.
    const REFERENCE_NOTE_TO_KEY = {
        "C": "C3",
        "C♯/Db": "C♯3",
        "D": "D3",
        "D♯/Eb": "D♯3",
        "E": "E3",
        "F": "F3",
        "F♯/Gb": "F♯3",
        "G": "G3",
        "G♯/Ab": "G♯3",
        "A": "A3",
        "A♯/Bb": "A♯3",
        "B": "B3",
    };
    const SOLFA_NOTE_OPTIONS = ["Doh", "Re", "Mi", "Fa", "Sol", "La", "Ti"];
    const OCTAVE_WHITE_KEY_LABELS = ["Doh", "Re", "Mi", "Fa", "Sol", "La", "Ti", "Doh"];
    const OCTAVE_BLACK_KEYS = [
        { label: "Di", afterIndex: 0 },
        { label: "Ri", afterIndex: 1 },
        { label: "Fi", afterIndex: 3 },
        { label: "Si", afterIndex: 4 },
        { label: "Toh", afterIndex: 5 },
    ];
    const OCTAVE_KEY_OPTIONS = [
        ...OCTAVE_WHITE_KEY_LABELS,
        ...OCTAVE_BLACK_KEYS.map((k) => k.label),
    ];
    const DITONE_OPTIONS = [
        "DOH MI",
        "REH FAH",
        "MI SOH",
        "FAH LAH",
        "SOH TI",
        "LAH DOH",
        "TI REH",
    ];
    const DIATOMIC_INTERVALS = [
        "Major 2nd",
        "Major 3rd",
        "Perfect 4th",
        "Perfect 5th",
        "Major 6th",
        "Major 7th",
        "Octave",
    ];
    const NONDIATOMIC_INTERVALS = [
        "Minor 2nd",
        "Minor 3rd",
        "Tri tone",
        "Minor 6th",
        "Minor 7th",
    ];
    const BASICTRIADS = ["Augmented", "Diminished", "Major", "Minor", "Sus"];
    const KEY_C_ROOT_INVERSIONS_OPTIONS = ["Major", "Minor", "Diminished", "Augmented", "Suspended"];
    const CHORD_QUALITY_OPTIONS = ["Major", "Minor", "Diminished", "Augmented", "Suspended"];
    const CHORD_INVERSION_OPTIONS = ["Root Position", "1st Inversion", "2nd Inversion"];
    const SEVENTH_CHORD_INVERSION_OPTIONS = ["Root Inversion", "1st Inversion", "2nd Inversion", "3rd Inversion"];
    const EXTENDED_SEVENTH_OPTIONS = [
        "Augmented 7th",
        "minMaj 7th",
        "minMaj 7(b5)",
        "Dominant 7(b5)",
        "Major 7(#5)",
        "Major 7b5",
    ];
    const ADD9_OPTIONS = ["Major add 9", "Minor add 9", "Diminished add 9", "Augmented add9"];
    const B9_OPTIONS = ["Major b9", "Minor b9", "Diminished b9", "Augmented b9"];
    const ADD9_AND_B9_OPTIONS = [...ADD9_OPTIONS, ...B9_OPTIONS];
    const NINTH_CHORD_QUALITY_OPTIONS = [
        "Major 9th",
        "Minor 9th",
        "Diminished 7(9)",
        "Dominant 9th",
        "Minor 9(b5)",
        "Dominant 7(b9)",
        "Minor 7(b5,b9)",
    ];
    const NINTH_CHORD_QUALITY_SECONDARY_OPTIONS = [
        "Major 9(#5)",
        "minMaj 9(b5)",
        "Diminished 7(b9)",
        "Dominant 9(b5)",
        "minMaj 9th",
        "Augmented 9th",
        "Minor 7(b9,b5)",
        "Major 7 (9,b5)",
    ];
    const NINTH_CHORD_QUALITY_GENERAL_OPTIONS = [
        "minMaj 9th",
        "Dominant 7(b9)",
        "Minor 9(b5)",
        "Augmented 9th",
        "Major 9(#5)",
        "Diminished 7(9)",
        "Minor 7(b9,b5)",
        "Dominant 9th",
        "minMaj 9(b5)",
        "Major 9th",
        "Diminished 7(b9)",
        "Minor 9th",
        "Dominant 9(b5)",
        "Major 7 (9,b5)",
    ];
    const SEVENTH_CHORD_ROOT_INVERSION_OPTIONS = [
        "Major 7th",
        "Minor 7th",
        "Diminished 7th",
        "Dominant 7th",
        "Minor 7(b5)",
    ];

    const INTERVALS = [
        "Minor 2nd",
        "Major 2nd",
        "Minor 3rd",
        "Major 3rd",
        "Perfect 4th",
        "Tri tone",
        "Perfect 5th",
        "Minor 6th",
        "Major 6th",
        "Minor 7th",
        "Major 7th",
        "Octave",
    ];

    const SEVENDEGREECHORD = [
        "Diminished 7th",
        "Dominant 7th",
        "Minor 7b5",
        "Major 7th",
        "Minor 7th",
    ];

    const SEVENDEGREECHORDSECONDARY = [
        "Augmented 7th",
        "minMaj 7th",
        "minMaj 7(b5)",
        "Dominant 7(b5)",
        "Major 7(#5)",
        "Major 7b5",
    ];
    const SEVENDEGREECHORDGENERAL = [
        "Diminished 7th",
        "Minor 7(b5)",
        "Dominant 7th",
        "Augmented 7th",
        "minMaj 7th",
        "Dominant 7(b5)",
        "Minor 7th",
        "Major 7th",
        "Major 7(#5)",
        "minMaj 7(b5)",
        "Major 7b5",
    ];

    const NINEDEGREECHORD = [
        "Dim7 (9)",
        "Dom9",
        "Dom7 (b9)",
        "Maj 6/9",
        "min 6/9",
        "min9",
        "min9 (b5)",
    ];

    const NINEDEGREECHORDSECONDARY = [
        "DimMaj7 (9)",
        "Dom9 (b5)",
        "Dom9 (#5)",
        "Maj9 (b5)",
        "Maj9 (#5)",
        "min (Maj9)",
    ];

    const NINEDEGREECHORDGENERAL = [
        "DimMaj7 (9)",
        "Dom9 (b5)",
        "Dom9 (#5)",
        "Maj9 (b5)",
        "Maj9 (#5)",
        "min (Maj9)",
        "Dim7 (9)",
        "Dom9",
        "Dom7 (b9)",
        "Maj 6/9",
        "min 6/9",
        "min9",
        "min9 (b5)",
    ];

    const ELEVENDEGREE = [
        "6/9 (#11)",
        "Dom9 (#11)",
        "Dom7 (b9#11)",
        "Maj9 (#11)",
        "min6/9 (11)",
        "min 9 (11)",
    ];

    const ELEVENTH_CHORD_QUALITY_OPTIONS = [
        "Major 9(#11)",
        "Minor 11th",
        "Diminished 7(9,11)",
        "Dominant 9(#11)",
        "Minor 11(b5)",
        "Dominant 7(b9,#11)",
    ];

    const ELEVENTH_CHORD_QUALITY_SECONDARY_OPTIONS = [
        "Major 9(#11,#5)",
        "minMaj 9(#11,b5)",
        "Diminished 7(11,b9)",
        "minMaj 11th",
        "Augmented 9(#11)",
        "Minor 11(b9,b5)",
    ];

    const ELEVENTH_CHORD_QUALITY_GENERAL_OPTIONS = [
        "minMaj 11th",
        "Dominant 9(#11)",
        "Minor 11(b9,b5)",
        "Major 9(#11,#5)",
        "Diminished 7(11,b9)",
        "Major 9(#11)",
        "Dominant 7(b9,#11)",
        "Minor 11th",
        "minMaj 9(#11,b5)",
        "Diminished 7(9,11)",
        "Augmented 9(#11)",
        "Minor 11(b5)",
    ];

    const THIRTEENDEGREE = [
        "13sus4",
        "Dom 9 (13) #11",
        "Dom 13 (b9#11)",
        "Maj13 (#11)",
        "min13 (9,11)",
    ];

    const THIRTEENTH_CHORD_QUALITY_OPTIONS = [
        "Major 9(#11,13)",
        "Minor 13th",
        "Dominant 9(#11,13)",
        "Minor #11(13,b5)",
        "Dominant 7(b9,#11,13)",
    ];

    const EXTENSION_RECOGNITION_OPTIONS = ["7th", "b9", "9th", "11th", "#11", "b13", "13th"];
    const DOMINANT_EXTENSION_RECOGNITION_OPTIONS = ["b9", "9th", "#9", "11th", "#11", "b13", "13th"];

    const CHORD_NAMING_QUALITY_OPTIONS = [
        "Major",
        "Minor",
        "Augmented",
        "Dominant",
        "Diminished",
    ];
    const CHORD_NAMING_DEGREE_OPTIONS = [
        "b5",
        "6th",
        "7th",
        "9th",
        "11th",
        "13th",
        "Sus 2",
        "Sus 4",
    ];
    const CHORD_NAMING_DEGREE_ACCIDENTAL_INDICES = [3, 4, 5];
    const CHORD_NAMING_EXCLUDE_OPTIONS = ["No 3", "No 5", "None"];

    const DIATONIC_TRIADS_KEY_C_OPTIONS = [
        "I (C)",
        "ii (Dm)",
        "iii (Em)",
        "IV (F)",
        "V (G)",
        "vi (Am)",
        "vii° (Bdim)",
    ];

    const DIATONIC_SEVENTH_CHORDS_KEY_C_OPTIONS = [
        "Imaj7 (Cmaj7)",
        "ii7 (Dm7)",
        "iii7 (Em7)",
        "IVmaj7 (Fmaj7)",
        "V7 (G7)",
        "vi7 (Am7)",
        "vii7b5 (Bm7b5)",
    ];

    const PASSING_PROGRESSION_OPTIONS = [
        "2-5-1",
        "3-6-2",
        "b5-7-3",
        "5-1-4",
        "6-2-5",
        "7-3-6",
    ];

    const MODAL_VOICINGS_SINGLE_NOTE_OPTIONS = [
        "Aeolian",
        "Lydian Augmented",
        "Altered Dominant",
        "Mixolydian",
        "Lydian Diminished",
    ];

    const MODAL_VOICINGS_MAJOR_7TH_OPTIONS = [
        "Gypsy major",
        "Lydian Mode",
        "Harmonic Major",
        "Ionian mode",
    ];

    const MODAL_VOICINGS_DOMINANT_OPTIONS = [
        "Phrygian Dominant",
        "Lydian Dominant",
        "Hungarian Major",
        "Romanian Major",
        "Super Phrygian",
        "Dominant Diminished",
    ];

    const MODAL_VOICINGS_MINOR_7TH_OPTIONS = [
        "Phrygian",
        "Romanian Minor",
        "Dorian",
        "Altered Minor",
        "Phrygian Blues",
    ];

    const CADENCE_IDENTIFICATION_OPTIONS = [
        "Authentic",
        "Plagal",
        "Minor Plagal",
        "Sub-Plagal",
        "Deceptive",
    ];

    const DOM_RESOLUTION_OPTIONS = [
        "Authentic",
        "Tri-tone",
        "Back-door",
        "Double Back-door",
        "Double Plagal",
        "Screen door",
        "Double Predominant",
    ];

    const MODULATION_OPTIONS = [
        "Minor 6th",
        "Major 2nd",
        "Minor 2nd",
        "Perfect 4th",
        "Perfect Fifth",
        "Minor 3rd",
    ];

    const TRIAD_PAIRS_OPTIONS = ["Major", "Minor", "Diminished"];
    const SEVENTH_TRIAD_PAIRS_OPTIONS = [
        "Major 7th",
        "Minor 7th",
        "Diminished 7th",
        "Dominant 7th",
        "Minor 7b5",
    ];

    const TONAL_MODES_OPTIONS = [
        "Ionian",
        "Harmonic Major",
        "Harmonic Minor",
        "Melodic Minor",
        "Aeolian",
    ];

    const MAJOR_MODE_OPTIONS = [
        "Ionian",
        "Dorian",
        "Phrygian",
        "Lydian",
        "Mixolydian",
        "Aeolian",
        "Locrian",
    ];

    const DORIAN_MODE_OPTIONS = ["Dorian", "Dorian b2", "Dorian #4", "Dorian #5"];
    const LYDIAN_MODE_OPTIONS = [
        "Lydian",
        "Lydian Augmented",
        "Lydian Dominant",
        "Lydian #2",
        "Lydian Diminished",
    ];

    const LOCRIAN_MODE_OPTIONS = [
        "Locrian",
        "Locrian ♮2",
        "Super Locrian",
        "Locrian ♮6",
        "Ultralocrian",
        "Locrian 𝄫7",
    ];

    const PHRYGIAN_MODE_OPTIONS = [
        "Phrygian",
        "Phrygian Dominant",
        "Phrygian #6",
        "Phrygian b4",
        "Melodic Phrygian",
    ];

    const MIXOLYDIAN_MODE_OPTIONS = [
        "Mixolydian",
        "Mixolydian b9",
        "Mixolydian #11",
        "Mixolydian Altered",
        "Mixolydian b13",
    ];

    const DOMINANT_TECHNIQUE_OPTIONS = [
        "Sus Dominant",
        "Slash Dominant",
        "Rootless Dominant",
        "Altered Dominant",
        "Augmented Dominant",
    ];

    const PROGRESSION_RECOGNITION_QUALITY_OPTIONS = [
        "Major 7th",
        "Minor 7th",
        "Minor 7b5",
        "Dominant 7th",
        "Minor 6",
        "Major 6",
        "minMaj7",
    ];
    const PROGRESSION_DEGREE_MULTI_OPTIONS = [
        "b9",
        "9th",
        "#9",
        "11th",
        "#11",
        "b13",
        "13th",
        "Sus4",
        "Sus2",
        "b5",
    ];

    const OTHERS = [
        "9sus4",
        "DimM9 (#5)",
        "Dom9 (#5b5)",
        "Maj9sus4",
        "Maj9 (b5#5)",
        "min9/11 (Maj7)",
    ];

    const lastSegment = window.location.pathname
        .split("/")
        .filter(Boolean)
        .pop();

    useEffect(() => {
        const fetchQuiz = async () => {
            try {
                const response = await axios.get(
                    `/admin/ear-training/${lastSegment}`
                );
                let fetchedQuiz = response.data;

                if (fetchedQuiz?.questions?.length) {
                    fetchedQuiz.questions = buildQuestionSet(fetchedQuiz.questions);
                }

                setQuiz(fetchedQuiz);
            } catch (error) {
                console.error("Error fetching quiz:", error);
            }
        };
        fetchQuiz();
    }, [lastSegment]);

    useEffect(() => {
        const fetchSiblings = async () => {
            try {
                const response = await axios.get(
                    `/member/ear-training/${lastSegment}/siblings`
                );
                setSiblings(response.data || []);
            } catch (error) {
                console.error("Error fetching quiz list:", error);
            }
        };
        fetchSiblings();
    }, [lastSegment]);

    useEffect(() => {
        if (!quiz || !quiz.video_url) return;

        // Parse any script tags in the video_url and inject them into the document
        const parser = new DOMParser();
        const doc = parser.parseFromString(quiz.video_url, "text/html");
        const scripts = doc.querySelectorAll("script");

        scripts.forEach((script) => {
            const src = script.getAttribute("src");
            if (src) {
                // Check if script is already in document to avoid duplicates
                const existingScript = document.querySelector(`script[src="${src}"]`);
                if (!existingScript) {
                    const newScript = document.createElement("script");
                    newScript.src = src;
                    newScript.async = true;
                    
                    const type = script.getAttribute("type");
                    if (type) {
                        newScript.type = type;
                    }

                    newScript.onload = () => {
                        console.log(`Successfully loaded script: ${src}`);
                    };

                    newScript.onerror = (e) => {
                        console.error(`Failed to load script: ${src}`, e);
                    };
                    
                    document.head.appendChild(newScript);
                }
            }
        });
    }, [quiz?.video_url]);

    // The injected iframe embed (e.g. VdoCipher) ships with fixed inline
    // width/height — override it so it fills the responsive aspect-ratio box.
    useEffect(() => {
        if (videoEmbedRef.current) {
            const iframe = videoEmbedRef.current.querySelector("iframe");
            if (iframe) {
                iframe.style.position = "absolute";
                iframe.style.top = "0";
                iframe.style.left = "0";
                iframe.style.width = "100%";
                iframe.style.height = "100%";
                iframe.style.border = "0";
            }
        }
    }, [quiz?.video_url]);

    if (!quiz || !quiz.questions?.length) {
        return (
            <div className="text-center p-6">
                <p className="text-lg font-semibold">Loading quiz...</p>
            </div>
        );
    }

    const question = quiz.questions[currentQuestion];
    const isSequenceQuiz =
        (quiz.title?.startsWith("Melodic dictation") ?? false) ||
        quiz.category === "Melodic Dictation";
    const isKeyboardSequenceQuiz = quiz.category === "Melodic Dictation";
    const isBlackKeyMelodyQuiz =
        quiz.title === "Other Notes" || quiz.title === "chordal melodies";
    const correctSequence = isSequenceQuiz
        ? String(question.correct_option)
              .split(",")
              .map((s) => parseInt(s.trim()))
        : [];

    const isTriadInversionQuiz =
        quiz.category === "Basic Triad" &&
        (quiz.title === "Key C all inversions" ||
            quiz.title === "Inversions all keys" ||
            quiz.title === "drop 2 key c" ||
            quiz.title === "drop 2 all keys");
    const isSeventhInversionQuiz =
        quiz.category === "7th Degree Chords" &&
        (quiz.title === "KEY C" ||
            quiz.title === "ALL KEYS" ||
            quiz.title === "drop 2 key c" ||
            quiz.title === "drop 2 all keys");
    const isSecondaryInversionQuiz =
        quiz.category === "Secondary 7th Chords" &&
        (quiz.title === "On key C" ||
            quiz.title === "on all keys" ||
            quiz.title === "drop 2 key c" ||
            quiz.title === "drop 2 all keys");
    const isChordInversionQuiz =
        isTriadInversionQuiz || isSeventhInversionQuiz || isSecondaryInversionQuiz;
    const isSingleToneQuiz =
        quiz.category === "Relative Pitch" &&
        (quiz.title === "Single tone Pitch" ||
            quiz.title === "Single Tone Pitch" ||
            quiz.title === "Single Tone Pitch 2");
    const isMultiSelectQuiz =
        (quiz.category === "Extentions recognition" &&
            (quiz.title === "Major Chord Extentions" ||
                quiz.title === "Minor Chord Extentions" ||
                quiz.title === "Dominant Chord Extentions")) ||
        (quiz.category === "Basic Triad" && quiz.title === "Triad Pairs") ||
        (quiz.category === "7th Degree Chords" && quiz.title === "Triad Pairs");
    const correctMultiOptions = isMultiSelectQuiz
        ? String(question.correct_option)
              .split(",")
              .map((s) => parseInt(s.trim()))
        : [];
    const isChordNamingQuiz =
        quiz.category === "Extentions recognition" && quiz.title === "Chord naming";
    const chordNamingAnswerGroups = isChordNamingQuiz
        ? String(question.correct_option)
              .split(";")
              .map((group) =>
                  group === ""
                      ? []
                      : group.split(",").map((s) => parseInt(s.trim()))
              )
        : [];
    const correctChordQuality = chordNamingAnswerGroups[0] ?? [];
    const correctChordDegree = chordNamingAnswerGroups[1] ?? [];
    const correctExclude = chordNamingAnswerGroups[2] ?? [];
    const correctChordDegreeAccidentals = {};
    if (isChordNamingQuiz) {
        const degreeRawGroup = String(question.correct_option).split(";")[1];
        if (degreeRawGroup) {
            degreeRawGroup.split(",").forEach((token) => {
                const match = token.trim().match(/^(\d+)([#b])$/);
                if (match) {
                    correctChordDegreeAccidentals[parseInt(match[1])] = match[2];
                }
            });
        }
    }
    const isChordProgressionQuiz =
        quiz.category === "Chord Progressions" &&
        (quiz.title === "Basic 4 lines" || quiz.title === "Basic 5 lines");
    const progressionChordOptions =
        quiz.title === "Basic 5 lines"
            ? DIATONIC_SEVENTH_CHORDS_KEY_C_OPTIONS
            : DIATONIC_TRIADS_KEY_C_OPTIONS;
    const correctProgression = isChordProgressionQuiz
        ? String(question.correct_option)
              .split(",")
              .map((s) => parseInt(s.trim()))
        : [];
    const isProgressionRecognitionQuiz =
        quiz.category === "Chord Progressions" &&
        (quiz.title === "Progression recognition" ||
            quiz.title === "6-2-5-1 progression");
    const isProgressionDegreeQuiz =
        quiz.category === "Chord Progressions" &&
        (quiz.title === "2-5-1 chord degree" ||
            quiz.title === "6-2-5-1 chord degree");
    const isDefaultAnswerUi =
        !isChordInversionQuiz &&
        !isSequenceQuiz &&
        !isMultiSelectQuiz &&
        !isChordNamingQuiz &&
        !isChordProgressionQuiz &&
        !isProgressionRecognitionQuiz &&
        !isProgressionDegreeQuiz;
    const progressionRecognitionParts =
        isProgressionRecognitionQuiz || isProgressionDegreeQuiz
            ? String(question.correct_option).split(";")
            : [];
    const progressionFixedNumbers = progressionRecognitionParts[0]
        ? progressionRecognitionParts[0].split(",").map((s) => s.trim())
        : [];
    const correctProgQuality =
        isProgressionRecognitionQuiz && progressionRecognitionParts[1]
            ? progressionRecognitionParts[1].split("|").map((s) => parseInt(s.trim()))
            : [];
    const progressionDegreeGroups = isProgressionDegreeQuiz
        ? progressionRecognitionParts.slice(1).map((group) => {
              const [q, degs] = group.split(",");
              return {
                  quality: parseInt(q),
                  degrees: degs ? degs.split("-").map((s) => parseInt(s.trim())) : [],
              };
          })
        : [];
    const correctProgQualityDeg = progressionDegreeGroups.map((g) => g.quality);
    const correctProgDegreeMulti = progressionDegreeGroups.map((g) => g.degrees);
    const chordQualityOptions = isSecondaryInversionQuiz
        ? EXTENDED_SEVENTH_OPTIONS
        : isSeventhInversionQuiz
        ? SEVENTH_CHORD_ROOT_INVERSION_OPTIONS
        : CHORD_QUALITY_OPTIONS;
    const chordInversionOptions = isSecondaryInversionQuiz
        ? SEVENTH_CHORD_INVERSION_OPTIONS
        : isSeventhInversionQuiz
        ? SEVENTH_CHORD_INVERSION_OPTIONS
        : CHORD_INVERSION_OPTIONS;
    const [correctQuality, correctInversion] = isChordInversionQuiz
        ? String(question.correct_option)
              .split(",")
              .map((s) => parseInt(s.trim()))
        : [null, null];

    const handleOptionSelect = (index) => {
        if (isSubmitted) return;

        if (isSequenceQuiz) {
            if (selectedSequence.length >= correctSequence.length) return;
            setSelectedSequence((prev) => [...prev, index]);
        } else {
            setSelectedOption(index);
        }
    };

    const handleRemoveLastNote = () => {
        if (isSubmitted) return;
        setSelectedSequence((prev) => prev.slice(0, -1));
    };

    const handleClearSequence = () => {
        if (isSubmitted) return;
        setSelectedSequence([]);
    };

    const handleQualitySelect = (index) => {
        if (isSubmitted) return;
        setSelectedQuality(index);
    };

    const handleInversionSelect = (index) => {
        if (isSubmitted) return;
        setSelectedInversion(index);
    };

    const handleMultiOptionToggle = (index) => {
        if (isSubmitted) return;
        setSelectedMultiOptions((prev) =>
            prev.includes(index)
                ? prev.filter((i) => i !== index)
                : [...prev, index]
        );
    };

    const toggleInArray = (setter) => (index) => {
        if (isSubmitted) return;
        setter((prev) =>
            prev.includes(index)
                ? prev.filter((i) => i !== index)
                : [...prev, index]
        );
    };

    const handleChordQualitySelect = (index) => {
        if (isSubmitted) return;
        setSelectedChordQuality([index]);
    };
    const handleChordDegreeToggle = toggleInArray(setSelectedChordDegree);
    const handleExcludeSelect = (index) => {
        if (isSubmitted) return;
        setSelectedExclude([index]);
    };

    const handleChordDegreeAccidentalSelect = (index, accidental) => {
        if (isSubmitted) return;
        setSelectedChordDegreeAccidentals((prev) => ({
            ...prev,
            [index]: prev[index] === accidental ? undefined : accidental,
        }));
    };

    const handleProgressionSelect = (index) => {
        if (isSubmitted) return;
        if (selectedProgression.length >= correctProgression.length) return;
        setSelectedProgression((prev) => [...prev, index]);
    };

    const handleRemoveLastProgressionChord = () => {
        if (isSubmitted) return;
        setSelectedProgression((prev) => prev.slice(0, -1));
    };

    const handleClearProgression = () => {
        if (isSubmitted) return;
        setSelectedProgression([]);
    };

    const handleProgQualitySelect = (position, index) => {
        if (isSubmitted) return;
        setSelectedProgQuality((prev) => {
            const next = [...prev];
            next[position] = index;
            return next;
        });
    };

    const handleProgDegreeMultiToggle = (position, index) => {
        if (isSubmitted) return;
        setSelectedProgDegreeMulti((prev) => {
            const next = [...prev];
            const current = next[position] || [];
            next[position] = current.includes(index)
                ? current.filter((i) => i !== index)
                : [...current, index];
            return next;
        });
    };

    const sameSet = (a, b) => {
        const sortedA = [...a].sort();
        const sortedB = [...b].sort();
        return (
            sortedA.length === sortedB.length &&
            sortedA.every((val, i) => val === sortedB[i])
        );
    };

    const handleSubmit = () => {
        if (isProgressionDegreeQuiz) {
            const positions = progressionFixedNumbers.length;
            const isComplete =
                selectedProgQuality.length === positions &&
                selectedProgQuality.every((v) => v !== undefined && v !== null);
            if (!isComplete) return;

            setIsSubmitted(true);
            const isMatch =
                selectedProgQuality.every((val, i) => val === correctProgQualityDeg[i]) &&
                progressionFixedNumbers.every((_, i) =>
                    sameSet(selectedProgDegreeMulti[i] || [], correctProgDegreeMulti[i])
                );
            setScore((prevScore) => (isMatch ? prevScore + 1 : prevScore));
            return;
        }

        if (isProgressionRecognitionQuiz) {
            const positions = progressionFixedNumbers.length;
            const isComplete =
                selectedProgQuality.length === positions &&
                selectedProgQuality.every((v) => v !== undefined && v !== null);
            if (!isComplete) return;

            setIsSubmitted(true);
            const isMatch = selectedProgQuality.every(
                (val, i) => val === correctProgQuality[i]
            );
            setScore((prevScore) => (isMatch ? prevScore + 1 : prevScore));
            return;
        }

        if (isChordProgressionQuiz) {
            if (selectedProgression.length !== correctProgression.length) return;

            setIsSubmitted(true);
            const isMatch = selectedProgression.every(
                (val, i) => val === correctProgression[i]
            );
            setScore((prevScore) => (isMatch ? prevScore + 1 : prevScore));
            return;
        }

        if (isChordNamingQuiz) {
            if (
                selectedChordQuality.length === 0 ||
                selectedChordDegree.length === 0 ||
                selectedExclude.length === 0
            )
                return;

            setIsSubmitted(true);
            const isMatch =
                sameSet(selectedChordQuality, correctChordQuality) &&
                sameSet(selectedChordDegree, correctChordDegree) &&
                selectedChordDegree.every(
                    (idx) =>
                        (selectedChordDegreeAccidentals[idx] || null) ===
                        (correctChordDegreeAccidentals[idx] || null)
                ) &&
                sameSet(selectedExclude, correctExclude);
            setScore((prevScore) => (isMatch ? prevScore + 1 : prevScore));
            return;
        }

        if (isMultiSelectQuiz) {
            if (selectedMultiOptions.length === 0) return;

            setIsSubmitted(true);
            const sortedSelected = [...selectedMultiOptions].sort();
            const sortedCorrect = [...correctMultiOptions].sort();
            const isMatch =
                sortedSelected.length === sortedCorrect.length &&
                sortedSelected.every((val, i) => val === sortedCorrect[i]);
            setScore((prevScore) => (isMatch ? prevScore + 1 : prevScore));
            return;
        }

        if (isChordInversionQuiz) {
            if (selectedQuality === null || selectedInversion === null) return;

            setIsSubmitted(true);
            setScore((prevScore) =>
                selectedQuality === correctQuality && selectedInversion === correctInversion
                    ? prevScore + 1
                    : prevScore
            );
            return;
        }

        if (isSequenceQuiz) {
            if (selectedSequence.length !== correctSequence.length) return;

            setIsSubmitted(true);
            setScore((prevScore) =>
                selectedSequence.every((val, i) => val === correctSequence[i])
                    ? prevScore + 1
                    : prevScore
            );
            return;
        }

        if (selectedOption === null) return;

        setIsSubmitted(true);
        setScore((prevScore) =>
            parseInt(selectedOption) === parseInt(question.correct_option)
                ? prevScore + 1
                : prevScore
        );
    };

    const handleNext = () => {
        if (currentQuestion < quiz.questions.length - 1) {
            setCurrentQuestion((prev) => prev + 1);
            setSelectedOption(null);
            setSelectedSequence([]);
            setSelectedQuality(null);
            setSelectedInversion(null);
            setSelectedMultiOptions([]);
            setSelectedChordQuality([]);
            setSelectedChordDegree([]);
            setSelectedChordDegreeAccidentals({});
            setSelectedExclude([]);
            setSelectedProgression([]);
            setSelectedProgQuality([]);
            setSelectedProgDegreeMulti([]);
            setIsSubmitted(false);
        } else {
            setShowResult(true);
        }
    };

    const handlePrevious = () => {
        if (currentQuestion > 0) {
            setCurrentQuestion((prev) => prev - 1);
            setSelectedOption(null);
            setSelectedSequence([]);
            setSelectedQuality(null);
            setSelectedInversion(null);
            setSelectedMultiOptions([]);
            setSelectedChordQuality([]);
            setSelectedChordDegree([]);
            setSelectedChordDegreeAccidentals({});
            setSelectedExclude([]);
            setSelectedProgression([]);
            setSelectedProgQuality([]);
            setSelectedProgDegreeMulti([]);
            setIsSubmitted(false);
        }
    };

    const getOptionsByCategory = (category, title) => {
        if (
            category === "Relative Pitch" &&
            (title === "Single tone Pitch" ||
                title === "Single Tone Pitch" ||
                title === "Single Tone Pitch 2")
        ) {
            return SINGLE_TONE_SOLFA_OPTIONS;
        }
        if (category === "Relative Pitch" && title === "Relative second pitch") {
            return RELATIVE_SECOND_OPTIONS;
        }
        if (category === "Chord Progressions" && title === "Passing Progression") {
            return PASSING_PROGRESSION_OPTIONS;
        }
        if (category === "Chord Progressions" && title === "Cadence Identification") {
            return CADENCE_IDENTIFICATION_OPTIONS;
        }
        if (category === "Chord Progressions" && title === "Dom resolution") {
            return DOM_RESOLUTION_OPTIONS;
        }
        if (category === "Chord Progressions" && title === "Modulation") {
            return MODULATION_OPTIONS;
        }
        if (category === "Basic Triad" && title === "Triad Pairs") {
            return TRIAD_PAIRS_OPTIONS;
        }
        if (category === "7th Degree Chords" && title === "Triad Pairs") {
            return SEVENTH_TRIAD_PAIRS_OPTIONS;
        }
        if (
            category === "Scales" &&
            (title === "Tonal Modes" || title === "Tonal Modes #2")
        ) {
            return TONAL_MODES_OPTIONS;
        }
        if (
            category === "Scales" &&
            (title === "Major mode" || title === "Major mode #2")
        ) {
            return MAJOR_MODE_OPTIONS;
        }
        if (category === "Scales" && title === "Dorian Mode") {
            return DORIAN_MODE_OPTIONS;
        }
        if (category === "Scales" && title === "Lydian Mode") {
            return LYDIAN_MODE_OPTIONS;
        }
        if (category === "Scales" && title === "Locrian Mode") {
            return LOCRIAN_MODE_OPTIONS;
        }
        if (category === "Scales" && title === "Phrygian Mode") {
            return PHRYGIAN_MODE_OPTIONS;
        }
        if (category === "Scales" && title === "Mixolydian Mode") {
            return MIXOLYDIAN_MODE_OPTIONS;
        }
        if (
            category === "Modal Voicings" &&
            (title === "Over Single Note #1" || title === "Over Single Note #2")
        ) {
            return MODAL_VOICINGS_SINGLE_NOTE_OPTIONS;
        }
        if (
            category === "Modal Voicings" &&
            (title === "Over Major 7ths #1" || title === "Over Major 7ths #2")
        ) {
            return MODAL_VOICINGS_MAJOR_7TH_OPTIONS;
        }
        if (
            category === "Modal Voicings" &&
            (title === "Over Dominant #1" || title === "Over Dominant #2")
        ) {
            return MODAL_VOICINGS_DOMINANT_OPTIONS;
        }
        if (
            category === "Modal Voicings" &&
            (title === "Over Minor 7th #1" || title === "Over Minor 7th #2")
        ) {
            return MODAL_VOICINGS_MINOR_7TH_OPTIONS;
        }
        if (title === "Relative Fourth Pitch") {
            return RELATIVE_FOURTH_OPTIONS;
        }
        if (title === "Relative Fifth Pitch") {
            return RELATIVE_FIFTH_OPTIONS;
        }
        if (title === "Relative Sixth Pitch") {
            return RELATIVE_SIXTH_OPTIONS;
        }
        if (title === "Relative Seventh Pitch") {
            return RELATIVE_SEVENTH_OPTIONS;
        }
        if (title === "Find the Key" || title === "Find the key #2") {
            return FIND_THE_KEY_OPTIONS;
        }
        if (category === "Melodic Dictation") {
            return OCTAVE_KEY_OPTIONS;
        }
        if (title?.startsWith("Melodic dictation")) {
            return SOLFA_NOTE_OPTIONS;
        }
        if (title === "Non-diatonic Intervals") {
            return NONDIATOMIC_INTERVALS;
        }
        if (title === "Intervals") {
            return INTERVALS;
        }
        if (
            title === "Key C root inversions" ||
            title === "All keys Root inversions" ||
            title === "Drop 2s Root Inversions" ||
            title === "Drop 2s all keys Root inversions"
        ) {
            return KEY_C_ROOT_INVERSIONS_OPTIONS;
        }
        if (
            category === "Add 9 & b9" &&
            (title === "Add 9 key c" ||
                title === "Add 9 all keys" ||
                title === "drop 2 key c" ||
                title === "drop 2 all keys")
        ) {
            return ADD9_OPTIONS;
        }
        if (
            category === "Add 9 & b9" &&
            (title === "add b9 key c" ||
                title === "add b9 all keys" ||
                title === "drop 2 add b9" ||
                title === "drop 2 add b9 all keys")
        ) {
            return B9_OPTIONS;
        }
        if (category === "Add 9 & b9" && title === "add 9 and add b9") {
            return ADD9_AND_B9_OPTIONS;
        }
        if (category === "Secondary 11th Chords" && title === "general") {
            return ELEVENTH_CHORD_QUALITY_GENERAL_OPTIONS;
        }
        if (
            category === "Secondary 11th Chords" &&
            (title === "key c" ||
                title === "diff key" ||
                title === "Inversion (with the root base)" ||
                title === "Inversion (without the root base)")
        ) {
            return ELEVENTH_CHORD_QUALITY_SECONDARY_OPTIONS;
        }
        if (
            category === "11th Degree Chords" &&
            (title === "key c" ||
                title === "diff keys" ||
                title === "Inversion (with the root base)" ||
                title === "Inversion without the root on the bass")
        ) {
            return ELEVENTH_CHORD_QUALITY_OPTIONS;
        }
        if (
            category === "13th Degree Chords" &&
            (title === "key c" ||
                title === "diff key" ||
                title === "Inversion (with the root base)" ||
                title === "Inversion (without the root base)")
        ) {
            return THIRTEENTH_CHORD_QUALITY_OPTIONS;
        }
        if (
            category === "Extentions recognition" &&
            (title === "Major Chord Extentions" ||
                title === "Minor Chord Extentions")
        ) {
            return EXTENSION_RECOGNITION_OPTIONS;
        }
        if (
            category === "Extentions recognition" &&
            title === "Dominant Chord Extentions"
        ) {
            return DOMINANT_EXTENSION_RECOGNITION_OPTIONS;
        }
        if (
            category === "Extentions recognition" &&
            title === "Dominant Technique"
        ) {
            return DOMINANT_TECHNIQUE_OPTIONS;
        }
        if (category === "Secondary 9th Chords" && title === "general") {
            return NINTH_CHORD_QUALITY_GENERAL_OPTIONS;
        }
        if (
            category === "Secondary 9th Chords" &&
            (title === "sec key c" ||
                title === "diff keys" ||
                title === "Inversion (with the root base)" ||
                title === "Inversion without the root on the bass")
        ) {
            return NINTH_CHORD_QUALITY_SECONDARY_OPTIONS;
        }
        if (
            category === "9th Degree Chords" &&
            (title === "key c" ||
                title === "diff keys" ||
                title === "Inversion (with the root base)" ||
                title === "Inversion without the root on the bass")
        ) {
            return NINTH_CHORD_QUALITY_OPTIONS;
        }
        if (
            category === "7th Degree Chords" &&
            (title === "all keys root inv" ||
                title === "drop 2 key c root" ||
                title === "drop 2 all keys root")
        ) {
            return SEVENTH_CHORD_ROOT_INVERSION_OPTIONS;
        }
        if (category === "Secondary 7th Chords" && title === "general") {
            return SEVENDEGREECHORDGENERAL;
        }
        if (category === "7th Degree Chords") {
            return SEVENDEGREECHORD;
        }
        if (
            category === "Secondary 7th Chords" &&
            (title === "key c root" || title === "all keys root")
        ) {
            return EXTENDED_SEVENTH_OPTIONS;
        }
        if (category === "Secondary 7th Chords") {
            return SEVENDEGREECHORDSECONDARY;
        }
        switch (category) {
            case "Relative Pitch":
                return RELATIVE_OPTIONS;
            case "Di-tone Pitch":
                return DITONE_OPTIONS;
            case "Diatonic Intervals":
                return DIATOMIC_INTERVALS;
            case "Non-diatonic Intervals":
                return NONDIATOMIC_INTERVALS;
            case "Intervals":
                return INTERVALS;
            case "Basic Triad":
                return BASICTRIADS;
            case "7th Degree Chords":
            case "7th Degree Chords (Basic)":
                return SEVENDEGREECHORD;
            case "Secondary 7th Chords":
            case "7th Degree Chords (Secondary)":
                return SEVENDEGREECHORDSECONDARY;
            case "7th Degree Chords (General)":
                return SEVENDEGREECHORDGENERAL;
            case "9th Degree Chords (Basic)":
                return NINEDEGREECHORD;
            case "9th Degree Chords (Secondary)":
                return NINEDEGREECHORDSECONDARY;
            case "9th Degree Chords (General)":
                return NINEDEGREECHORDGENERAL;
            case "11th Degree Chords":
                return ELEVENDEGREE;
            case "13th Degree Chords":
                return THIRTEENDEGREE;
            case "Others":
                return OTHERS;
            default:
                return [];
        }
    };
    const extractGoogleDriveFileId = (url) => {
        const regex = /(?:file\/d\/|open\?id=)([a-zA-Z0-9_-]+)/;
        const match = url.match(regex);
        return match ? match[1] : null;
    };



    const renderVideo = () => {
        const videoUrl = quiz.video_url;

        if (!videoUrl) {
            return <p className="text-gray-500">No video available</p>;
        }

        // Check if it is an HTML embed snippet (contains HTML tags)
        if (/<[a-z][\s\S]*>/i.test(videoUrl)) {
            return (
                <div className="relative w-full aspect-video rounded overflow-hidden shadow bg-black">
                    <div
                        ref={videoEmbedRef}
                        className="absolute inset-0"
                        dangerouslySetInnerHTML={{ __html: videoUrl }}
                    />
                </div>
            );
        }

        // Check if it is a Google Drive URL
        const googleDriveId = extractGoogleDriveFileId(videoUrl);
        if (googleDriveId) {
            return (
                <iframe
                    src={`https://drive.google.com/file/d/${googleDriveId}/preview`}
                    width="100%"
                    height="400"
                    allow="autoplay"
                    className="rounded shadow border-0"
                />
            );
        }

        // Check if it is a YouTube URL
        const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
        const youtubeMatch = videoUrl.match(youtubeRegex);
        if (youtubeMatch) {
            return (
                <iframe
                    src={`https://www.youtube.com/embed/${youtubeMatch[1]}`}
                    width="100%"
                    height="400"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowFullScreen
                    className="rounded shadow border-0"
                />
            );
        }

        // If it's a direct video link or fallback
        return (
            <video
                src={videoUrl}
                controls
                className="w-full rounded shadow"
            />
        );
    };

    const completedCount = siblings.filter((sibling) => sibling.completed).length;
    const categoryProgress = siblings.length
        ? Math.round((completedCount / siblings.length) * 100)
        : 0;

    const questionOptions = getOptionsByCategory(quiz.category, quiz.title);
    const optionsGridClass =
        questionOptions.length === 8
            ? "grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6"
            : questionOptions.length === 12
            ? "grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6"
            : "grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-6";

    const isSubmitDisabled = isProgressionDegreeQuiz
        ? selectedProgQuality.length !== progressionFixedNumbers.length ||
          selectedProgQuality.some((v) => v === undefined || v === null)
        : isProgressionRecognitionQuiz
        ? selectedProgQuality.length !== progressionFixedNumbers.length ||
          selectedProgQuality.some((v) => v === undefined || v === null)
        : isChordProgressionQuiz
        ? selectedProgression.length !== correctProgression.length
        : isChordNamingQuiz
        ? selectedChordQuality.length === 0 ||
          selectedChordDegree.length === 0 ||
          selectedExclude.length === 0
        : isMultiSelectQuiz
        ? selectedMultiOptions.length === 0
        : isChordInversionQuiz
        ? selectedQuality === null || selectedInversion === null
        : isSequenceQuiz
        ? selectedSequence.length !== correctSequence.length
        : selectedOption === null;

    return (
        <div className="max-w-6xl mx-auto p-4 sm:p-6 mt-4 sm:mt-6 bg-[#f8f9fb] dark:bg-transparent">

            {/* Mobile header */}
            {!quizStarted && (
                <div className="lg:hidden mb-5">
                    <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Audio Quiz</h1>
                            <div className="flex items-center gap-1.5 text-sm text-gray-400 dark:text-gray-500 mt-1 truncate">
                                <a href="/member/audio-quiz" className="hover:text-gray-600 dark:hover:text-gray-300">
                                    Audio Quiz
                                </a>
                                <span>/</span>
                                <span className="text-gray-500 dark:text-gray-400 truncate">
                                    {quiz.category || quiz.title}
                                </span>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setIsSiblingDropdownOpen((prev) => !prev)}
                            className="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 flex-shrink-0"
                        >
                            {isSiblingDropdownOpen ? (
                                <X className="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            ) : (
                                <ChevronDown className="w-4 h-4 text-gray-700 dark:text-gray-200" />
                            )}
                        </button>
                    </div>
                </div>
            )}

            {/* Quiz-in-progress header */}
            {quizStarted && !showResult && (
                <div className="mb-5">
                    <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Audio Quiz</h1>
                            <div className="flex items-center gap-1.5 text-sm text-gray-400 dark:text-gray-500 mt-1 truncate">
                                <a href="/member/audio-quiz" className="hover:text-gray-600 dark:hover:text-gray-300">
                                    Audio Quiz
                                </a>
                                <span>/</span>
                                <button
                                    type="button"
                                    onClick={exitQuiz}
                                    className="text-gray-900 dark:text-white font-semibold hover:text-gray-600 dark:hover:text-gray-300 truncate"
                                >
                                    {quiz.title}
                                </button>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={exitQuiz}
                            className="hidden sm:inline-flex items-center px-5 py-2.5 rounded-full border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161617] text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 flex-shrink-0"
                        >
                            Exit Quiz
                        </button>
                        <button
                            type="button"
                            onClick={exitQuiz}
                            className="sm:hidden flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 flex-shrink-0"
                        >
                            <X className="w-4 h-4 text-gray-700 dark:text-gray-200" />
                        </button>
                    </div>
                </div>
            )}

            {/* Mobile lesson list panel */}
            {!quizStarted && isSiblingDropdownOpen && (
                <div className="lg:hidden">
                    <a
                        href="/member/audio-quiz"
                        className="inline-flex items-center gap-1.5 text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 mb-3 transition-colors"
                    >
                        <ArrowLeft className="w-3.5 h-3.5" />
                        Audio Quiz
                    </a>
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-2 truncate">
                        {quiz.category || quiz.title}
                    </h2>

                    <div className="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                        <span>
                            {completedCount}/{siblings.length} Completed
                        </span>
                        <span>{categoryProgress}%</span>
                    </div>
                    <div className="w-full bg-gray-200 dark:bg-white/10 rounded-full h-1.5 overflow-hidden mb-5">
                        <div
                            className="bg-gray-900 dark:bg-white h-1.5 rounded-full transition-all duration-700 ease-out"
                            style={{ width: `${categoryProgress}%` }}
                        ></div>
                    </div>

                    <div className="space-y-2 pb-2">
                        {siblings.map((sibling) => {
                            const isCurrent = String(sibling.id) === String(quiz.id);

                            return (
                                <a
                                    key={sibling.id}
                                    href={`/member/ear-training/${sibling.id}`}
                                    onClick={() => setIsSiblingDropdownOpen(false)}
                                    className={`flex items-center justify-between gap-2 px-4 py-3 rounded-xl text-sm bg-white dark:bg-[#161617] border transition-colors duration-150 ${
                                        isCurrent
                                            ? "border-gray-900 dark:border-white font-semibold text-gray-900 dark:text-white"
                                            : "border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:border-gray-300 dark:hover:border-white/20"
                                    }`}
                                >
                                    <span className="truncate">{sibling.title}</span>
                                    <span
                                        className={`flex items-center justify-center w-4 h-4 rounded-full border flex-shrink-0 ${
                                            sibling.completed || isCurrent
                                                ? "border-gray-900 dark:border-white"
                                                : "border-gray-300 dark:border-white/20"
                                        }`}
                                    >
                                        {(sibling.completed || isCurrent) && (
                                            <span className="w-2 h-2 rounded-full bg-gray-900 dark:bg-white"></span>
                                        )}
                                    </span>
                                </a>
                            );
                        })}
                    </div>
                </div>
            )}

            <div
                className={`${
                    isSiblingDropdownOpen ? "hidden lg:flex" : "flex"
                } flex-col lg:flex-row gap-6 items-start`}
            >
            {/* Sidebar */}
            <div className={`${quizStarted ? "hidden" : "hidden lg:block"} w-full lg:w-72 flex-shrink-0 lg:sticky lg:top-24`}>
                <div className="mb-4">
                    <a
                        href="/member/audio-quiz"
                        className="inline-flex items-center gap-1.5 text-sm text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 mb-3 transition-colors"
                    >
                        <ArrowLeft className="w-3.5 h-3.5" />
                        Audio Quiz
                    </a>
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-2 truncate">
                        {quiz.category || quiz.title}
                    </h2>

                    <div className="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                        <span>
                            {completedCount}/{siblings.length} Completed
                        </span>
                        <span>{categoryProgress}%</span>
                    </div>
                    <div className="w-full bg-gray-200 dark:bg-white/10 rounded-full h-1.5 overflow-hidden">
                        <div
                            className="bg-gray-900 dark:bg-white h-1.5 rounded-full transition-all duration-700 ease-out"
                            style={{ width: `${categoryProgress}%` }}
                        ></div>
                    </div>
                </div>

                <div>
                    <div className="space-y-2">
                        {siblings.map((sibling) => {
                            const isCurrent = String(sibling.id) === String(quiz.id);

                            return (
                                <a
                                    key={sibling.id}
                                    href={`/member/ear-training/${sibling.id}`}
                                    className={`flex items-center justify-between gap-2 px-4 py-3 rounded-xl text-sm bg-white dark:bg-[#161617] border transition-colors duration-150 ${
                                        isCurrent
                                            ? "border-gray-900 dark:border-white font-semibold text-gray-900 dark:text-white"
                                            : "border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:border-gray-300 dark:hover:border-white/20"
                                    }`}
                                >
                                    <span className="truncate">{sibling.title}</span>
                                    <span
                                        className={`flex items-center justify-center w-4 h-4 rounded-full border flex-shrink-0 ${
                                            sibling.completed || isCurrent
                                                ? "border-gray-900 dark:border-white"
                                                : "border-gray-300 dark:border-white/20"
                                        }`}
                                    >
                                        {(sibling.completed || isCurrent) && (
                                            <span className="w-2 h-2 rounded-full bg-gray-900 dark:bg-white"></span>
                                        )}
                                    </span>
                                </a>
                            );
                        })}
                    </div>
                </div>
            </div>

            {/* Main content */}
            <div className="flex-1 w-full min-w-0 bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 rounded-3xl p-5 sm:p-7">
            {!quizStarted && (
                <div className="flex items-center justify-between gap-3 mb-5 flex-wrap">
                    <h2 className="text-2xl font-bold text-gray-900 dark:text-white">{quiz.title}</h2>
                    <button
                        onClick={handleMarkAsCompleted}
                        disabled={markingComplete || isCompleted}
                        className={`flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-full border transition-all duration-200 flex-shrink-0 ${
                            isCompleted
                                ? "bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 cursor-not-allowed"
                                : "bg-white dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/10"
                        }`}
                    >
                        <CheckCircle2 className="w-4 h-4" />
                        {isCompleted ? "Completed" : "Mark as Complete"}
                    </button>
                </div>
            )}

            {!quizStarted ? (
                <>
                    {/* Main Video */}
                    <div className="rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10">
                        {renderVideo()}
                    </div>

                    {/* Quiz Meta */}
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5">
                        <div className="flex items-center gap-3 bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 rounded-2xl p-4">
                            <span className="flex items-center justify-center w-9 h-9 rounded-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 flex-shrink-0">
                                <Gauge className="w-4 h-4" />
                            </span>
                            <div>
                                <p className="text-sm text-gray-400 dark:text-gray-500">
                                    Difficulty
                                </p>
                                <p className="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    {quiz.difficulty || "Beginner"}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-3 bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 rounded-2xl p-4">
                            <span className="flex items-center justify-center w-9 h-9 rounded-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 flex-shrink-0">
                                <HelpCircle className="w-4 h-4" />
                            </span>
                            <div>
                                <p className="text-sm text-gray-400 dark:text-gray-500">
                                    Questions
                                </p>
                                <p className="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    {quiz.questions?.length ?? 20}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-3 bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 rounded-2xl p-4">
                            <span className="flex items-center justify-center w-9 h-9 rounded-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 flex-shrink-0">
                                <ListChecks className="w-4 h-4" />
                            </span>
                            <div>
                                <p className="text-sm text-gray-400 dark:text-gray-500">
                                    Attempts
                                </p>
                                <p className="text-sm font-bold text-gray-800 dark:text-gray-100">
                                    Unlimited
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Pro Tips */}
                    <div className="mt-3 bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 rounded-2xl p-4 sm:p-5">
                        <div className="flex items-center gap-2.5 mb-3">
                            <span className="flex items-center justify-center w-7 h-7 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 flex-shrink-0">
                                <Lightbulb className="w-4 h-4" />
                            </span>
                            <p className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Pro Tips
                            </p>
                        </div>
                        <ul className="space-y-1.5">
                            <li className="text-sm text-gray-600 dark:text-gray-300">
                                - Use headphones for best results.
                            </li>
                            <li className="text-sm text-gray-600 dark:text-gray-300">
                                - Listen carefully before answering.
                            </li>
                            <li className="text-sm text-gray-600 dark:text-gray-300">
                                - Take your time and answer the questions.
                            </li>
                        </ul>
                    </div>

                    {/* Reference Audio */}
                    {quiz.reference_audios && quiz.reference_audios.length > 0 && (
                        <div className="mt-3 bg-white dark:bg-[#161617] border border-gray-100 dark:border-white/10 rounded-2xl p-5 sm:p-6 space-y-5">
                            <div className="flex items-center gap-2.5">
                                <span className="flex items-center justify-center w-7 h-7 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400 flex-shrink-0">
                                    <Headphones className="w-4 h-4" />
                                </span>
                                <p className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Reference Audio
                                </p>
                            </div>

                            {quiz.reference_audios.map((refAudio) => (
                                <ReferenceAudioItem
                                    key={refAudio.id}
                                    name={refAudio.name}
                                    src={refAudio.audio_path}
                                />
                            ))}
                        </div>
                    )}

                    <button
                        onClick={startQuiz}
                        className="
                            w-full flex items-center justify-center gap-2
                            mt-6 py-4
                            bg-gray-900 dark:bg-white text-white dark:text-gray-900
                            rounded-xl
                            text-sm font-semibold tracking-wide uppercase
                            hover:bg-gray-800 dark:hover:bg-gray-100
                            active:scale-[0.99]
                            transition-all duration-200
                        "
                    >
                        Start Quiz
                        <ChevronRight className="w-4 h-4" />
                    </button>

                    <CourseComment course={quiz} group={"quiz"} />
                </>
            ) : (
                <div className="mt-1">
                    {!showResult ? (
                        <>
                            <div className="flex items-center justify-between gap-3 mb-3 flex-wrap">
                                <h3 className="text-xl font-bold text-gray-900 dark:text-white">
                                    {quiz.title}
                                </h3>
                                <p className="text-sm text-gray-400 dark:text-gray-500">
                                    Questions {currentQuestion + 1} of {quiz.questions.length}
                                </p>
                            </div>

                            {/* Progress */}
                            <div className="w-full bg-gray-100 dark:bg-white/10 rounded-full h-1.5 overflow-hidden mb-8">
                                <div
                                    className="bg-gray-900 dark:bg-white h-1.5 rounded-full transition-all duration-500 ease-out"
                                    style={{
                                        width: `${((currentQuestion + 1) / quiz.questions.length) * 100}%`,
                                    }}
                                ></div>
                            </div>

                            <h2 className="text-xl font-bold text-gray-900 dark:text-white text-center mb-8">
                                {quiz.question_prompt
                                    ? quiz.question_prompt
                                    : isChordInversionQuiz
                                    ? "Identify the chord quality and inversion you hear."
                                    : isSequenceQuiz
                                    ? `Identify the ${correctSequence.length}-note melody you hear.`
                                    : isMultiSelectQuiz
                                    ? "Select every extension you hear (multiple answers)."
                                    : isChordNamingQuiz
                                    ? "Listen to the chord and select its quality, degree, and any excluded note (if applicable)."
                                    : isChordProgressionQuiz
                                    ? `Listen to the ${correctProgression.length}-chord progression and choose each diatonic triad in order.`
                                    : isProgressionRecognitionQuiz
                                    ? "Listen to the chord progression. Identify the chord quality and degree for each chord."
                                    : isProgressionDegreeQuiz
                                    ? "Listen to the chord progression. Identify the chord quality and every degree for each chord."
                                    : isSingleToneQuiz
                                    ? "Identify the note you hear."
                                    : "Identify the interval you hear."}
                            </h2>

                            {/* Question Audio */}
                            <div className="bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/10 rounded-2xl p-6 sm:p-8 mb-8 text-center">
                                <audio
                                    key={currentQuestion}
                                    ref={questionAudioRef}
                                    autoPlay
                                    src={`${question.audio_path}`}
                                    onPlay={() => setIsQuestionPlaying(true)}
                                    onPause={() => setIsQuestionPlaying(false)}
                                    onEnded={() => setIsQuestionPlaying(false)}
                                    className="hidden"
                                />

                                <p className="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    Please listen carefully to the audio
                                </p>

                                <div className="flex items-end justify-center gap-[3px] h-8 mb-6">
                                    {[...WAVEFORM_BARS, ...WAVEFORM_BARS].map((h, i) => (
                                        <span
                                            key={i}
                                            className={`w-1 rounded-full flex-shrink-0 ${
                                                isQuestionPlaying
                                                    ? i % 2 === 0
                                                        ? "bg-indigo-500"
                                                        : "bg-gray-800 dark:bg-white"
                                                    : "bg-gray-300 dark:bg-white/15"
                                            }`}
                                            style={{ height: `${h}px` }}
                                        ></span>
                                    ))}
                                </div>

                                <button
                                    onClick={toggleQuestionAudio}
                                    className="flex items-center justify-center w-16 h-16 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 mx-auto hover:scale-105 active:scale-95 transition-transform duration-150"
                                >
                                    {isQuestionPlaying ? (
                                        <Square className="w-5 h-5 fill-current" />
                                    ) : (
                                        <Play className="w-6 h-6 fill-current ml-0.5" />
                                    )}
                                </button>

                                <p className="text-xs text-gray-400 dark:text-gray-500 mt-6">
                                    You must listen before answering.
                                </p>
                            </div>

                            {(quiz.title === "Find the Key" || quiz.title === "Find the key #2") && (
                                <div className="mb-8">
                                    {question.reference_note && (
                                        <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 text-center">
                                            The highlighted key is your reference note
                                        </p>
                                    )}
                                    <PianoKeyboard
                                        highlightNote={REFERENCE_NOTE_TO_KEY[question.reference_note] ?? null}
                                        includeOctaveC={false}
                                    />
                                </div>
                            )}

                            {isChordInversionQuiz ? (
                                <>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                        <div className="bg-gray-50/70 dark:bg-white/[0.03] border border-gray-100 dark:border-white/10 rounded-2xl p-4">
                                            <div className="flex items-center gap-2.5 mb-3">
                                                <span className="flex items-center justify-center w-6 h-6 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold flex-shrink-0">
                                                    1
                                                </span>
                                                <p className="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                    Select the chord quality
                                                </p>
                                            </div>
                                            <div className="space-y-1.5">
                                                {chordQualityOptions.map((opt, i) => {
                                                    const isSelectedOpt = selectedQuality === i;
                                                    const showCorrect = isSubmitted && i === correctQuality;
                                                    const showWrong = isSubmitted && isSelectedOpt && i !== correctQuality;
                                                    return (
                                                        <button
                                                            key={i}
                                                            onClick={() => handleQualitySelect(i)}
                                                            aria-disabled={isSubmitted}
                                                            className={`w-full flex items-center gap-3 px-3 py-2 border rounded-xl text-left text-sm font-medium transition-all duration-200 bg-white dark:bg-[#161617] ${
                                                                isSubmitted ? "pointer-events-none" : ""
                                                            } ${
                                                                showCorrect
                                                                    ? "border-emerald-400"
                                                                    : showWrong
                                                                    ? "border-rose-400"
                                                                    : isSelectedOpt
                                                                    ? "border-gray-900 dark:border-white"
                                                                    : "border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20"
                                                            }`}
                                                        >
                                                            <span
                                                                className={`flex items-center justify-center w-4 h-4 rounded-full border-2 flex-shrink-0 ${
                                                                    showCorrect
                                                                        ? "border-emerald-500 bg-emerald-500"
                                                                        : showWrong
                                                                        ? "border-rose-500 bg-rose-500"
                                                                        : isSelectedOpt
                                                                        ? "border-gray-900 dark:border-white bg-gray-900 dark:bg-white"
                                                                        : "border-gray-300 dark:border-white/20"
                                                                }`}
                                                            >
                                                                {(isSelectedOpt || showCorrect) && (
                                                                    <span className="w-1.5 h-1.5 rounded-full bg-white dark:bg-gray-900"></span>
                                                                )}
                                                            </span>
                                                            <span className="text-gray-900 dark:text-gray-100">{opt}</span>
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>

                                        <div className="bg-gray-50/70 dark:bg-white/[0.03] border border-gray-100 dark:border-white/10 rounded-2xl p-4">
                                            <div className="flex items-center gap-2.5 mb-3">
                                                <span className="flex items-center justify-center w-6 h-6 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold flex-shrink-0">
                                                    2
                                                </span>
                                                <p className="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                    Select the inversion
                                                </p>
                                            </div>
                                            <div className="flex flex-wrap gap-1.5">
                                                {chordInversionOptions.map((opt, i) => {
                                                    const isSelectedOpt = selectedInversion === i;
                                                    const showCorrect = isSubmitted && i === correctInversion;
                                                    const showWrong = isSubmitted && isSelectedOpt && i !== correctInversion;
                                                    return (
                                                        <button
                                                            key={i}
                                                            onClick={() => handleInversionSelect(i)}
                                                            aria-disabled={isSubmitted}
                                                            className={`flex items-center gap-1.5 px-3 py-1.5 border rounded-full text-xs font-medium transition-all duration-200 bg-white dark:bg-[#161617] ${
                                                                isSubmitted ? "pointer-events-none" : ""
                                                            } ${
                                                                showCorrect
                                                                    ? "border-emerald-400 text-emerald-600 dark:text-emerald-400"
                                                                    : showWrong
                                                                    ? "border-rose-400 text-rose-600 dark:text-rose-400"
                                                                    : isSelectedOpt
                                                                    ? "border-gray-900 dark:border-white text-gray-900 dark:text-white"
                                                                    : "border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-gray-300 dark:hover:border-white/20"
                                                            }`}
                                                        >
                                                            {(isSelectedOpt || showCorrect) && (
                                                                <Check
                                                                    className={`w-3 h-3 ${
                                                                        showCorrect
                                                                            ? "text-emerald-500"
                                                                            : showWrong
                                                                            ? "text-rose-500"
                                                                            : ""
                                                                    }`}
                                                                    strokeWidth={3}
                                                                />
                                                            )}
                                                            <span>{opt}</span>
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Correct Answer */}
                                    {isSubmitted && (
                                        <div className="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-2">
                                            <CheckCircle2 className="w-4 h-4 flex-shrink-0" />
                                            <span>
                                                Correct Answer:{" "}
                                                <strong>
                                                    {chordQualityOptions[correctQuality]}, {chordInversionOptions[correctInversion]}
                                                </strong>
                                            </span>
                                        </div>
                                    )}
                                </>
                            ) : isSequenceQuiz ? (
                                <>
                                    <div className="bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/10 rounded-2xl p-5 sm:p-6 mb-6">
                                        <div className="flex items-center justify-between mb-4">
                                            <p className="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                Your Sequence
                                            </p>
                                            {!isSubmitted && (
                                                <div className="flex items-center gap-2">
                                                    <button
                                                        onClick={handleRemoveLastNote}
                                                        disabled={selectedSequence.length === 0}
                                                        className="flex items-center gap-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                                    >
                                                        <RotateCcw className="w-3.5 h-3.5" />
                                                        Undo
                                                    </button>
                                                    <button
                                                        onClick={handleClearSequence}
                                                        disabled={selectedSequence.length === 0}
                                                        className="flex items-center gap-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                                    >
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                        Clear
                                                    </button>
                                                </div>
                                            )}
                                        </div>

                                        {/* Sequence field */}
                                        <div className="flex gap-3 flex-wrap">
                                            {correctSequence.map((_, slotIndex) => {
                                                const note = selectedSequence[slotIndex];
                                                const isCorrectSlot = isSubmitted && note === correctSequence[slotIndex];
                                                const isWrongSlot = isSubmitted && note !== undefined && note !== correctSequence[slotIndex];
                                                return (
                                                    <div key={slotIndex} className="flex flex-col items-center gap-1.5">
                                                        <span className="text-[10px] font-semibold text-gray-300 dark:text-gray-600">
                                                            {slotIndex + 1}
                                                        </span>
                                                        <div
                                                            className={`flex items-center justify-center min-w-[4.5rem] h-14 px-3 rounded-xl border-2 text-sm font-bold transition-all duration-150 ${
                                                                isCorrectSlot
                                                                    ? "border-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                                                                    : isWrongSlot
                                                                    ? "border-rose-400 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400"
                                                                    : note !== undefined
                                                                    ? "border-gray-900 dark:border-white bg-white dark:bg-[#161617] text-gray-900 dark:text-white shadow-sm"
                                                                    : "border-dashed border-gray-300 dark:border-white/15 bg-white/60 dark:bg-white/[0.02] text-gray-300 dark:text-gray-600"
                                                            }`}
                                                        >
                                                            {note !== undefined ? questionOptions[note] : "—"}
                                                        </div>
                                                        {isWrongSlot && (
                                                            <span className="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                                                {questionOptions[correctSequence[slotIndex]]}
                                                            </span>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>

                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                        Tap the notes in the order you hear them
                                    </p>

                                    {/* Note palette */}
                                    <div
                                        className={`transition-opacity duration-300 ${
                                            isSubmitted ? "opacity-30 pointer-events-none" : "opacity-100"
                                        }`}
                                    >
                                    {isKeyboardSequenceQuiz ? (
                                        <div className="relative flex w-full max-w-xl mx-auto h-32 sm:h-36 mb-6 select-none">
                                            {OCTAVE_WHITE_KEY_LABELS.map((label, i) => {
                                                const isDisabled =
                                                    isSubmitted ||
                                                    selectedSequence.length >= correctSequence.length;
                                                const isPressed = pressedPaletteKey === i;
                                                return (
                                                    <button
                                                        key={label}
                                                        onClick={() => handleOptionSelect(i)}
                                                        onPointerDown={() => setPressedPaletteKey(i)}
                                                        onPointerUp={() => setPressedPaletteKey(null)}
                                                        onPointerLeave={() => setPressedPaletteKey(null)}
                                                        disabled={isDisabled}
                                                        className={`relative flex-1 flex items-end justify-center pb-3 border border-gray-200 dark:border-white/10 first:rounded-l-lg last:rounded-r-lg border-l-0 first:border-l shadow-sm transition-colors duration-150 disabled:cursor-not-allowed ${
                                                            isPressed
                                                                ? "bg-emerald-400 dark:bg-emerald-400"
                                                                : "bg-white dark:bg-white hover:bg-gray-50"
                                                        }`}
                                                    >
                                                        <span
                                                            className={`text-xs font-semibold transition-colors duration-150 ${
                                                                isPressed ? "text-white" : "text-gray-500"
                                                            }`}
                                                        >
                                                            {label}
                                                        </span>
                                                    </button>
                                                );
                                            })}
                                            {OCTAVE_BLACK_KEYS.map((key, j) => {
                                                if (!isBlackKeyMelodyQuiz) {
                                                    // Sharps/flats are shown for a realistic octave but
                                                    // are not used in this lesson, so never selectable.
                                                    return (
                                                        <button
                                                            key={key.label}
                                                            disabled
                                                            title="Sharps/flats aren't used in this lesson"
                                                            className="absolute top-0 h-[62%] w-[9%] flex items-end justify-center pb-2 rounded-b-md shadow-md z-10 bg-gray-900"
                                                            style={{
                                                                left: `${
                                                                    ((key.afterIndex + 1) /
                                                                        OCTAVE_WHITE_KEY_LABELS.length) *
                                                                    100
                                                                }%`,
                                                                transform: "translateX(-50%)",
                                                            }}
                                                        >
                                                            <span className="text-[10px] font-semibold text-gray-300">
                                                                {key.label}
                                                            </span>
                                                        </button>
                                                    );
                                                }

                                                const flatIndex = OCTAVE_WHITE_KEY_LABELS.length + j;
                                                const isDisabled =
                                                    isSubmitted ||
                                                    selectedSequence.length >= correctSequence.length;
                                                const isPressed = pressedPaletteKey === flatIndex;
                                                return (
                                                    <button
                                                        key={key.label}
                                                        onClick={() => handleOptionSelect(flatIndex)}
                                                        onPointerDown={() => setPressedPaletteKey(flatIndex)}
                                                        onPointerUp={() => setPressedPaletteKey(null)}
                                                        onPointerLeave={() => setPressedPaletteKey(null)}
                                                        disabled={isDisabled}
                                                        className={`absolute top-0 h-[62%] w-[9%] flex items-end justify-center pb-2 rounded-b-md shadow-md z-10 transition-colors duration-150 disabled:cursor-not-allowed ${
                                                            isPressed ? "bg-blue-500" : "bg-gray-900"
                                                        }`}
                                                        style={{
                                                            left: `${
                                                                ((key.afterIndex + 1) /
                                                                    OCTAVE_WHITE_KEY_LABELS.length) *
                                                                100
                                                            }%`,
                                                            transform: "translateX(-50%)",
                                                        }}
                                                    >
                                                        <span className="text-[10px] font-semibold text-gray-300">
                                                            {key.label}
                                                        </span>
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    ) : (
                                        <div className="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
                                            {questionOptions.map((opt, i) => (
                                                <button
                                                    key={i}
                                                    onClick={() => handleOptionSelect(i)}
                                                    disabled={isSubmitted || selectedSequence.length >= correctSequence.length}
                                                    className="flex items-center justify-center p-4 border rounded-2xl font-medium bg-white dark:bg-[#161617] border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-900 dark:text-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200"
                                                >
                                                    {opt}
                                                </button>
                                            ))}
                                        </div>
                                    )}
                                    </div>
                                </>
                            ) : isMultiSelectQuiz ? (
                                <>
                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                        Choose all correct answers
                                    </p>

                                    {/* Options */}
                                    <div className={optionsGridClass}>
                                        {questionOptions.map((opt, i) => {
                                            const isCorrectOpt = correctMultiOptions.includes(i);
                                            const isSelectedOpt = selectedMultiOptions.includes(i);
                                            const showCorrect = isSubmitted && isCorrectOpt;
                                            const showWrong = isSubmitted && isSelectedOpt && !isCorrectOpt;
                                            return (
                                                <button
                                                    key={i}
                                                    onClick={() => handleMultiOptionToggle(i)}
                                                    disabled={isSubmitted}
                                                    className={`w-full flex items-center gap-3 p-4 border rounded-2xl text-left font-medium transition-all duration-200 bg-white dark:bg-[#161617] ${
                                                        showCorrect
                                                            ? "border-emerald-400"
                                                            : showWrong
                                                            ? "border-rose-400"
                                                            : isSelectedOpt
                                                            ? "border-gray-900 dark:border-white"
                                                            : "border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20"
                                                    }`}
                                                >
                                                    <span
                                                        className={`flex items-center justify-center w-5 h-5 rounded-md border-2 flex-shrink-0 ${
                                                            showCorrect
                                                                ? "border-emerald-500 bg-emerald-500"
                                                                : showWrong
                                                                ? "border-rose-500 bg-rose-500"
                                                                : isSelectedOpt
                                                                ? "border-gray-900 dark:border-white bg-gray-900 dark:bg-white"
                                                                : "border-gray-300 dark:border-white/20"
                                                        }`}
                                                    >
                                                        {(isSelectedOpt || showCorrect) && (
                                                            <Check className="w-3 h-3 text-white dark:text-gray-900" strokeWidth={3} />
                                                        )}
                                                    </span>
                                                    <span className="text-gray-900 dark:text-gray-100">{opt}</span>
                                                </button>
                                            );
                                        })}
                                    </div>

                                    {/* Correct Answer */}
                                    {isSubmitted && (
                                        <div className="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-2">
                                            <CheckCircle2 className="w-4 h-4 flex-shrink-0" />
                                            <span>
                                                Correct Answer:{" "}
                                                <strong>
                                                    {correctMultiOptions
                                                        .map((idx) => questionOptions[idx])
                                                        .join(", ")}
                                                </strong>
                                            </span>
                                        </div>
                                    )}
                                </>
                            ) : isChordNamingQuiz ? (
                                <>
                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">
                                        Select all that apply in each category
                                    </p>

                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                        {[
                                            {
                                                num: 1,
                                                label: "Chord Quality",
                                                hint: "Pick one",
                                                singleSelect: true,
                                                options: CHORD_NAMING_QUALITY_OPTIONS,
                                                selected: selectedChordQuality,
                                                correct: correctChordQuality,
                                                onToggle: handleChordQualitySelect,
                                                note: null,
                                            },
                                            {
                                                num: 2,
                                                label: "Chord Degree",
                                                hint: "Select all that apply",
                                                singleSelect: false,
                                                options: CHORD_NAMING_DEGREE_OPTIONS,
                                                selected: selectedChordDegree,
                                                correct: correctChordDegree,
                                                onToggle: handleChordDegreeToggle,
                                                accidentalIndices: CHORD_NAMING_DEGREE_ACCIDENTAL_INDICES,
                                                accidentals: selectedChordDegreeAccidentals,
                                                correctAccidentals: correctChordDegreeAccidentals,
                                                onAccidentalToggle: handleChordDegreeAccidentalSelect,
                                                note: null,
                                            },
                                            {
                                                num: 3,
                                                label: "Exclude (if any)",
                                                hint: "Pick one",
                                                singleSelect: true,
                                                options: CHORD_NAMING_EXCLUDE_OPTIONS,
                                                selected: selectedExclude,
                                                correct: correctExclude,
                                                onToggle: handleExcludeSelect,
                                                note: 'Select "None" if no notes are excluded.',
                                            },
                                        ].map((group) => (
                                            <div
                                                key={group.label}
                                                className="flex flex-col bg-gray-50/70 dark:bg-white/[0.03] border border-gray-100 dark:border-white/10 rounded-2xl p-4"
                                            >
                                                <div className="flex items-center justify-between gap-2 mb-3">
                                                    <div className="flex items-center gap-2.5">
                                                        <span className="flex items-center justify-center w-6 h-6 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-bold flex-shrink-0">
                                                            {group.num}
                                                        </span>
                                                        <p className="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                            {group.label}
                                                        </p>
                                                    </div>
                                                    <span className="text-[11px] font-medium text-gray-400 dark:text-gray-500">
                                                        {group.hint}
                                                    </span>
                                                </div>
                                                <div className="flex flex-wrap gap-2">
                                                    {group.options.map((opt, i) => {
                                                        const isCorrectOpt = group.correct.includes(i);
                                                        const isSelectedOpt = group.selected.includes(i);
                                                        const showCorrect = isSubmitted && isCorrectOpt;
                                                        const showWrong =
                                                            isSubmitted && isSelectedOpt && !isCorrectOpt;
                                                        const hasAccidental =
                                                            group.accidentalIndices?.includes(i);
                                                        const showAccidentalSegment =
                                                            hasAccidental && (isSelectedOpt || showCorrect);
                                                        const chosenAccidental = group.accidentals?.[i];
                                                        const correctAccidental =
                                                            group.correctAccidentals?.[i] || null;
                                                        const toneClass = showCorrect
                                                            ? "border-emerald-400 text-emerald-600 dark:text-emerald-400"
                                                            : showWrong
                                                            ? "border-rose-400 text-rose-600 dark:text-rose-400"
                                                            : isSelectedOpt
                                                            ? "border-gray-900 dark:border-white text-gray-900 dark:text-white"
                                                            : "border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-gray-300 dark:hover:border-white/20";
                                                        return (
                                                            <div
                                                                key={i}
                                                                className={`inline-flex items-stretch rounded-full border overflow-hidden bg-white dark:bg-[#161617] transition-colors duration-200 flex-shrink-0 ${
                                                                    isSubmitted ? "pointer-events-none" : ""
                                                                } ${toneClass}`}
                                                            >
                                                                <button
                                                                    onClick={() => group.onToggle(i)}
                                                                    aria-disabled={isSubmitted}
                                                                    className="flex items-center gap-1.5 pl-3 pr-3 py-1.5 text-xs font-medium"
                                                                >
                                                                    {(isSelectedOpt || showCorrect) &&
                                                                        (group.singleSelect ? (
                                                                            <span
                                                                                className={`w-2 h-2 rounded-full flex-shrink-0 ${
                                                                                    showCorrect
                                                                                        ? "bg-emerald-500"
                                                                                        : showWrong
                                                                                        ? "bg-rose-500"
                                                                                        : "bg-gray-900 dark:bg-white"
                                                                                }`}
                                                                            ></span>
                                                                        ) : (
                                                                            <Check
                                                                                className={`w-3 h-3 flex-shrink-0 ${
                                                                                    showCorrect
                                                                                        ? "text-emerald-500"
                                                                                        : showWrong
                                                                                        ? "text-rose-500"
                                                                                        : ""
                                                                                }`}
                                                                                strokeWidth={3}
                                                                            />
                                                                        ))}
                                                                    <span>{opt}</span>
                                                                </button>

                                                                {showAccidentalSegment && (
                                                                    <div
                                                                        className={`flex items-stretch border-l ${
                                                                            toneClass.split(" ")[0]
                                                                        }`}
                                                                    >
                                                                        {["#", "b"].map((sym, symIdx) => {
                                                                            const isChosen = chosenAccidental === sym;
                                                                            const symCorrect =
                                                                                isSubmitted &&
                                                                                isSelectedOpt &&
                                                                                sym === correctAccidental;
                                                                            const symWrong =
                                                                                isSubmitted &&
                                                                                isChosen &&
                                                                                sym !== correctAccidental;
                                                                            return (
                                                                                <button
                                                                                    key={sym}
                                                                                    type="button"
                                                                                    aria-disabled={isSubmitted}
                                                                                    onClick={() =>
                                                                                        group.onAccidentalToggle(i, sym)
                                                                                    }
                                                                                    className={`w-6 flex items-center justify-center text-[11px] font-bold transition-colors duration-150 ${
                                                                                        symIdx === 0
                                                                                            ? "border-r " + toneClass.split(" ")[0]
                                                                                            : ""
                                                                                    } ${
                                                                                        symCorrect
                                                                                            ? "bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                                                                                            : symWrong
                                                                                            ? "bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400"
                                                                                            : isChosen
                                                                                            ? "bg-gray-900 dark:bg-white text-white dark:text-gray-900"
                                                                                            : "text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-white/5"
                                                                                    }`}
                                                                                >
                                                                                    {sym}
                                                                                </button>
                                                                            );
                                                                        })}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        );
                                                    })}
                                                </div>
                                                {group.note && (
                                                    <p className="text-xs text-gray-400 dark:text-gray-500 mt-3 pt-3 border-t border-gray-100 dark:border-white/10 flex items-start gap-1.5">
                                                        <Lightbulb className="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                        <span>{group.note}</span>
                                                    </p>
                                                )}
                                            </div>
                                        ))}
                                    </div>

                                    {/* Correct Answer */}
                                    {isSubmitted && (
                                        <div className="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-2">
                                            <CheckCircle2 className="w-4 h-4 flex-shrink-0" />
                                            <span>
                                                Correct Answer:{" "}
                                                <strong>
                                                    {correctChordQuality
                                                        .map((i) => CHORD_NAMING_QUALITY_OPTIONS[i])
                                                        .join("/")}
                                                    {" · "}
                                                    {correctChordDegree
                                                        .map((i) => {
                                                            const acc = correctChordDegreeAccidentals[i];
                                                            const label = CHORD_NAMING_DEGREE_OPTIONS[i];
                                                            return acc
                                                                ? `${acc}${label.replace(/(st|nd|rd|th)$/, "")}`
                                                                : label;
                                                        })
                                                        .join("/")}
                                                    {!correctExclude.includes(
                                                        CHORD_NAMING_EXCLUDE_OPTIONS.length - 1
                                                    ) && (
                                                        <>
                                                            {" · Exclude: "}
                                                            {correctExclude
                                                                .map((i) => CHORD_NAMING_EXCLUDE_OPTIONS[i])
                                                                .join("/")}
                                                        </>
                                                    )}
                                                </strong>
                                            </span>
                                        </div>
                                    )}
                                </>
                            ) : isChordProgressionQuiz ? (
                                <>
                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                        Your progression
                                    </p>

                                    {/* Progression slots */}
                                    <div className="flex items-center gap-2 flex-wrap mb-6">
                                        {correctProgression.map((_, slotIndex) => {
                                            const chordIdx = selectedProgression[slotIndex];
                                            const isCorrectSlot =
                                                isSubmitted && chordIdx === correctProgression[slotIndex];
                                            const isWrongSlot =
                                                isSubmitted &&
                                                chordIdx !== undefined &&
                                                chordIdx !== correctProgression[slotIndex];
                                            return (
                                                <div
                                                    key={slotIndex}
                                                    className={`flex items-center justify-center min-w-[5.5rem] h-12 px-3 rounded-xl border-2 text-sm font-semibold transition-colors duration-150 ${
                                                        isCorrectSlot
                                                            ? "border-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                                                            : isWrongSlot
                                                            ? "border-rose-400 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400"
                                                            : chordIdx !== undefined
                                                            ? "border-gray-900 dark:border-white text-gray-900 dark:text-white"
                                                            : "border-dashed border-gray-200 dark:border-white/10 text-gray-300 dark:text-gray-600"
                                                    }`}
                                                >
                                                    {chordIdx !== undefined
                                                        ? progressionChordOptions[chordIdx]
                                                        : "—"}
                                                </div>
                                            );
                                        })}

                                        {!isSubmitted && (
                                            <div className="flex items-center gap-2 ml-1">
                                                <button
                                                    onClick={handleRemoveLastProgressionChord}
                                                    disabled={selectedProgression.length === 0}
                                                    className="text-xs font-semibold text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                                                >
                                                    Undo
                                                </button>
                                                <span className="text-gray-200 dark:text-white/10">|</span>
                                                <button
                                                    onClick={handleClearProgression}
                                                    disabled={selectedProgression.length === 0}
                                                    className="text-xs font-semibold text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
                                                >
                                                    Clear
                                                </button>
                                            </div>
                                        )}
                                    </div>

                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                        Tap the chords in the order you hear them
                                    </p>

                                    {/* Diatonic chord palette */}
                                    <div className="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
                                        {progressionChordOptions.map((opt, i) => (
                                            <button
                                                key={i}
                                                onClick={() => handleProgressionSelect(i)}
                                                disabled={
                                                    isSubmitted ||
                                                    selectedProgression.length >= correctProgression.length
                                                }
                                                className="flex items-center justify-center p-4 border rounded-2xl font-medium bg-white dark:bg-[#161617] border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 hover:bg-gray-50 dark:hover:bg-white/5 text-gray-900 dark:text-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200"
                                            >
                                                {opt}
                                            </button>
                                        ))}
                                    </div>

                                    {/* Correct Answer */}
                                    {isSubmitted && (
                                        <div className="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-2">
                                            <CheckCircle2 className="w-4 h-4 flex-shrink-0" />
                                            <span>
                                                Correct Progression:{" "}
                                                <strong>
                                                    {correctProgression
                                                        .map((i) => progressionChordOptions[i])
                                                        .join(" - ")}
                                                </strong>
                                            </span>
                                        </div>
                                    )}
                                </>
                            ) : isProgressionRecognitionQuiz ? (
                                <>
                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                        Select the chord quality for each chord
                                    </p>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                        {progressionFixedNumbers.map((num, position) => {
                                            const qualityIdx = selectedProgQuality[position];
                                            const isCorrectPosition =
                                                isSubmitted &&
                                                qualityIdx === correctProgQuality[position];
                                            const isWrongPosition = isSubmitted && !isCorrectPosition;
                                            return (
                                                <div
                                                    key={position}
                                                    className={`bg-gray-50/70 dark:bg-white/[0.03] border rounded-2xl p-4 ${
                                                        isCorrectPosition
                                                            ? "border-emerald-400"
                                                            : isWrongPosition
                                                            ? "border-rose-400"
                                                            : "border-gray-100 dark:border-white/10"
                                                    }`}
                                                >
                                                    <div className="flex items-center gap-2.5 mb-3">
                                                        <span className="flex items-center justify-center w-8 h-8 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-bold flex-shrink-0">
                                                            {num}
                                                        </span>
                                                        <span className="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                            Chord {position + 1}
                                                        </span>
                                                    </div>

                                                    <label className="block text-xs font-medium text-gray-400 dark:text-gray-500 mb-1.5">
                                                        Chord quality
                                                    </label>
                                                    {(() => {
                                                        const dropdownKey = `rec-${position}`;
                                                        const isOpen = openQualityDropdownPosition === dropdownKey;
                                                        return (
                                                            <div className="relative">
                                                                <button
                                                                    type="button"
                                                                    disabled={isSubmitted}
                                                                    onClick={() =>
                                                                        setOpenQualityDropdownPosition((prev) =>
                                                                            prev === dropdownKey ? null : dropdownKey
                                                                        )
                                                                    }
                                                                    className={`w-full flex items-center justify-between gap-2 p-2.5 border rounded-xl text-sm font-medium bg-white dark:bg-[#161617] text-gray-900 dark:text-gray-100 disabled:opacity-70 transition-colors duration-150 ${
                                                                        isOpen
                                                                            ? "border-gray-900 dark:border-white"
                                                                            : "border-gray-200 dark:border-white/10"
                                                                    }`}
                                                                >
                                                                    <span className={qualityIdx === undefined ? "text-gray-400 dark:text-gray-500" : ""}>
                                                                        {qualityIdx !== undefined
                                                                            ? PROGRESSION_RECOGNITION_QUALITY_OPTIONS[qualityIdx]
                                                                            : "Select"}
                                                                    </span>
                                                                    <ChevronDown
                                                                        className={`w-4 h-4 flex-shrink-0 text-gray-400 dark:text-gray-500 transition-transform duration-200 ${
                                                                            isOpen ? "rotate-180" : ""
                                                                        }`}
                                                                    />
                                                                </button>
                                                                {isOpen && (
                                                                    <div className="absolute z-20 mt-2 w-full bg-white dark:bg-[#1c1c1d] border border-gray-100 dark:border-white/10 rounded-2xl shadow-xl shadow-gray-200/60 dark:shadow-black/40 p-1.5 space-y-0.5">
                                                                        {PROGRESSION_RECOGNITION_QUALITY_OPTIONS.map((opt, i) => (
                                                                            <button
                                                                                key={i}
                                                                                type="button"
                                                                                onClick={() => {
                                                                                    handleProgQualitySelect(position, i);
                                                                                    setOpenQualityDropdownPosition(null);
                                                                                }}
                                                                                className={`w-full flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-sm font-medium text-left transition-colors duration-150 ${
                                                                                    qualityIdx === i
                                                                                        ? "bg-gray-900 dark:bg-white text-white dark:text-gray-900"
                                                                                        : "text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5"
                                                                                }`}
                                                                            >
                                                                                <span>{opt}</span>
                                                                                {qualityIdx === i && (
                                                                                    <Check className="w-3.5 h-3.5" strokeWidth={3} />
                                                                                )}
                                                                            </button>
                                                                        ))}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        );
                                                    })()}
                                                </div>
                                            );
                                        })}
                                    </div>

                                    {/* Correct Answer */}
                                    {isSubmitted && (
                                        <div className="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-2">
                                            <CheckCircle2 className="w-4 h-4 flex-shrink-0" />
                                            <span>
                                                Correct Answer:{" "}
                                                <strong>
                                                    {correctProgQuality
                                                        .map((q) => PROGRESSION_RECOGNITION_QUALITY_OPTIONS[q])
                                                        .join(" - ")}
                                                </strong>
                                            </span>
                                        </div>
                                    )}
                                </>
                            ) : isProgressionDegreeQuiz ? (
                                <>
                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                        Select the chord quality and every degree for each chord
                                    </p>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                        {progressionFixedNumbers.map((num, position) => {
                                            const qualityIdx = selectedProgQuality[position];
                                            const degreeIdxs = selectedProgDegreeMulti[position] || [];
                                            const isQualityCorrect =
                                                isSubmitted &&
                                                qualityIdx === correctProgQualityDeg[position];
                                            const isQualityWrong = isSubmitted && !isQualityCorrect;
                                            const isCorrectPosition =
                                                isSubmitted &&
                                                qualityIdx === correctProgQualityDeg[position] &&
                                                sameSet(degreeIdxs, correctProgDegreeMulti[position]);
                                            const isWrongPosition = isSubmitted && !isCorrectPosition;
                                            return (
                                                <div
                                                    key={position}
                                                    className={`bg-gray-50/70 dark:bg-white/[0.03] border rounded-2xl p-4 ${
                                                        isCorrectPosition
                                                            ? "border-emerald-400"
                                                            : isWrongPosition
                                                            ? "border-rose-400"
                                                            : "border-gray-100 dark:border-white/10"
                                                    }`}
                                                >
                                                    <div className="flex items-center gap-2.5 mb-3">
                                                        <span className="flex items-center justify-center w-8 h-8 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-bold flex-shrink-0">
                                                            {num}
                                                        </span>
                                                        <span className="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                            Chord {position + 1}
                                                        </span>
                                                    </div>

                                                    <label className="block text-xs font-medium text-gray-400 dark:text-gray-500 mb-1.5">
                                                        Chord quality
                                                    </label>
                                                    {(() => {
                                                        const dropdownKey = `deg-${position}`;
                                                        const isOpen = openQualityDropdownPosition === dropdownKey;
                                                        return (
                                                            <div className="relative mb-3">
                                                                <button
                                                                    type="button"
                                                                    aria-disabled={isSubmitted}
                                                                    onClick={() =>
                                                                        setOpenQualityDropdownPosition((prev) =>
                                                                            prev === dropdownKey ? null : dropdownKey
                                                                        )
                                                                    }
                                                                    className={`w-full flex items-center justify-between gap-2 p-2.5 border-2 rounded-xl text-sm font-medium bg-white dark:bg-[#161617] text-gray-900 dark:text-gray-100 transition-colors duration-150 ${
                                                                        isSubmitted ? "pointer-events-none" : ""
                                                                    } ${
                                                                        isQualityCorrect
                                                                            ? "border-emerald-400"
                                                                            : isQualityWrong
                                                                            ? "border-rose-400"
                                                                            : isOpen
                                                                            ? "border-gray-900 dark:border-white"
                                                                            : "border-gray-200 dark:border-white/10"
                                                                    }`}
                                                                >
                                                                    <span className={qualityIdx === undefined ? "text-gray-400 dark:text-gray-500" : ""}>
                                                                        {qualityIdx !== undefined
                                                                            ? PROGRESSION_RECOGNITION_QUALITY_OPTIONS[qualityIdx]
                                                                            : "Select"}
                                                                    </span>
                                                                    <ChevronDown
                                                                        className={`w-4 h-4 flex-shrink-0 text-gray-400 dark:text-gray-500 transition-transform duration-200 ${
                                                                            isOpen ? "rotate-180" : ""
                                                                        }`}
                                                                    />
                                                                </button>
                                                                {isOpen && (
                                                                    <div className="absolute z-20 mt-2 w-full bg-white dark:bg-[#1c1c1d] border border-gray-100 dark:border-white/10 rounded-2xl shadow-xl shadow-gray-200/60 dark:shadow-black/40 p-1.5 space-y-0.5">
                                                                        {PROGRESSION_RECOGNITION_QUALITY_OPTIONS.map((opt, i) => (
                                                                            <button
                                                                                key={i}
                                                                                type="button"
                                                                                onClick={() => {
                                                                                    handleProgQualitySelect(position, i);
                                                                                    setOpenQualityDropdownPosition(null);
                                                                                }}
                                                                                className={`w-full flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-sm font-medium text-left transition-colors duration-150 ${
                                                                                    qualityIdx === i
                                                                                        ? "bg-gray-900 dark:bg-white text-white dark:text-gray-900"
                                                                                        : "text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5"
                                                                                }`}
                                                                            >
                                                                                <span>{opt}</span>
                                                                                {qualityIdx === i && (
                                                                                    <Check className="w-3.5 h-3.5" strokeWidth={3} />
                                                                                )}
                                                                            </button>
                                                                        ))}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        );
                                                    })()}

                                                    <label className="block text-xs font-medium text-gray-400 dark:text-gray-500 mb-1.5">
                                                        Chord degree{" "}
                                                        <span className="font-normal text-gray-300 dark:text-gray-600">
                                                            (optional)
                                                        </span>
                                                    </label>
                                                    <div className="flex flex-wrap gap-1.5">
                                                        {PROGRESSION_DEGREE_MULTI_OPTIONS.map((opt, i) => {
                                                            const isPicked = degreeIdxs.includes(i);
                                                            const isCorrectDeg = (
                                                                correctProgDegreeMulti[position] || []
                                                            ).includes(i);
                                                            const showDegCorrect =
                                                                isSubmitted && isCorrectDeg;
                                                            const showDegWrong =
                                                                isSubmitted && isPicked && !isCorrectDeg;
                                                            return (
                                                                <button
                                                                    key={i}
                                                                    type="button"
                                                                    aria-disabled={isSubmitted}
                                                                    onClick={() =>
                                                                        handleProgDegreeMultiToggle(position, i)
                                                                    }
                                                                    className={`flex items-center gap-1 px-2.5 py-1 border rounded-full text-xs font-medium transition-colors duration-150 bg-white dark:bg-[#161617] ${
                                                                        isSubmitted ? "pointer-events-none" : ""
                                                                    } ${
                                                                        showDegCorrect
                                                                            ? "border-emerald-400 text-emerald-600 dark:text-emerald-400"
                                                                            : showDegWrong
                                                                            ? "border-rose-400 text-rose-600 dark:text-rose-400"
                                                                            : isPicked
                                                                            ? "border-gray-900 dark:border-white text-gray-900 dark:text-white"
                                                                            : "border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-gray-300 dark:hover:border-white/20"
                                                                    }`}
                                                                >
                                                                    {(isPicked || showDegCorrect) && (
                                                                        <Check
                                                                            className={`w-2.5 h-2.5 ${
                                                                                showDegCorrect
                                                                                    ? "text-emerald-500"
                                                                                    : showDegWrong
                                                                                    ? "text-rose-500"
                                                                                    : ""
                                                                            }`}
                                                                            strokeWidth={3}
                                                                        />
                                                                    )}
                                                                    <span>{opt}</span>
                                                                </button>
                                                            );
                                                        })}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>

                                    {/* Correct Answer */}
                                    {isSubmitted && (
                                        <div className="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-2">
                                            <CheckCircle2 className="w-4 h-4 flex-shrink-0" />
                                            <span>
                                                Correct Answer:{" "}
                                                <strong>
                                                    {correctProgQualityDeg
                                                        .map(
                                                            (q, i) =>
                                                                `${PROGRESSION_RECOGNITION_QUALITY_OPTIONS[q]} (${correctProgDegreeMulti[i]
                                                                    .map((d) => PROGRESSION_DEGREE_MULTI_OPTIONS[d])
                                                                    .join(", ")})`
                                                        )
                                                        .join(" - ")}
                                                </strong>
                                            </span>
                                        </div>
                                    )}
                                </>
                            ) : (
                                <>
                                    <p className="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                                        Choose the Correct Answer
                                    </p>

                                    {/* Options */}
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                                        {questionOptions.map(
                                            (opt, i) => {
                                                const isCorrectOpt = i === parseInt(question.correct_option);
                                                const isSelectedOpt = selectedOption === i;
                                                const showCorrect = isSubmitted && isCorrectOpt;
                                                const showWrong = isSubmitted && isSelectedOpt && !isCorrectOpt;
                                                return (
                                                <button
                                                    key={i}
                                                    onClick={() =>
                                                        handleOptionSelect(i)
                                                    }
                                                    disabled={isSubmitted}
                                                    className={`w-full flex items-center gap-3 p-4 border rounded-xl text-left font-medium transition-all duration-200 bg-white dark:bg-[#161617] ${
                                                        showCorrect
                                                            ? "border-emerald-400"
                                                            : showWrong
                                                            ? "border-rose-400"
                                                            : isSelectedOpt
                                                            ? "border-gray-900 dark:border-white"
                                                            : "border-gray-200 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20"
                                                    }`}
                                                >
                                                    <span
                                                        className={`flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold flex-shrink-0 ${
                                                            showCorrect
                                                                ? "bg-emerald-500 text-white"
                                                                : showWrong
                                                                ? "bg-rose-500 text-white"
                                                                : isSelectedOpt
                                                                ? "bg-gray-900 dark:bg-white text-white dark:text-gray-900"
                                                                : "bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400"
                                                        }`}
                                                    >
                                                        {String.fromCharCode(65 + i)}
                                                    </span>
                                                    <span className="flex-1 text-gray-900 dark:text-gray-100">{opt}</span>
                                                    {showCorrect && (
                                                        <span className="flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500 text-white flex-shrink-0">
                                                            <Check className="w-3.5 h-3.5" strokeWidth={3} />
                                                        </span>
                                                    )}
                                                    {showWrong && (
                                                        <span className="flex items-center justify-center w-6 h-6 rounded-full bg-rose-500 text-white flex-shrink-0">
                                                            <X className="w-3.5 h-3.5" strokeWidth={3} />
                                                        </span>
                                                    )}
                                                </button>
                                                );
                                            }
                                        )}
                                    </div>

                                    {/* Result banner */}
                                    {isSubmitted && (
                                        <div
                                            className={`mt-4 p-4 rounded-2xl border flex items-center justify-between gap-3 flex-wrap ${
                                                selectedOption === parseInt(question.correct_option)
                                                    ? "bg-emerald-50 dark:bg-emerald-500/10 border-emerald-100 dark:border-emerald-500/20"
                                                    : "bg-rose-50 dark:bg-rose-500/10 border-rose-100 dark:border-rose-500/20"
                                            }`}
                                        >
                                            <span
                                                className={`font-semibold ${
                                                    selectedOption === parseInt(question.correct_option)
                                                        ? "text-emerald-700 dark:text-emerald-400"
                                                        : "text-rose-700 dark:text-rose-400"
                                                }`}
                                            >
                                                {selectedOption === parseInt(question.correct_option) ? (
                                                    "🎉 You are Correct!"
                                                ) : (
                                                    <>
                                                        Incorrect — correct answer:{" "}
                                                        <strong>{questionOptions[question.correct_option]}</strong>
                                                    </>
                                                )}
                                            </span>
                                            <button
                                                onClick={handleNext}
                                                className="flex items-center gap-1.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors duration-200 flex-shrink-0"
                                            >
                                                {currentQuestion === quiz.questions.length - 1
                                                    ? "Finish"
                                                    : "Next Question"}
                                                <ChevronRight className="w-4 h-4" />
                                            </button>
                                        </div>
                                    )}
                                </>
                            )}

                            {/* Navigation Buttons */}
                            {!(isDefaultAnswerUi && isSubmitted) && (
                                <div className="mt-6 flex flex-wrap gap-4 justify-end">
                                    {!isSubmitted ? (
                                        <button
                                            onClick={handleSubmit}
                                            disabled={isSubmitDisabled}
                                            className={`ml-auto px-6 py-2 rounded-full font-semibold transition-all duration-200 ${
                                                isSubmitDisabled
                                                    ? "bg-gray-100 dark:bg-white/10 text-gray-400 dark:text-gray-500 cursor-not-allowed"
                                                    : "bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5"
                                            }`}
                                        >
                                            Submit Answer
                                        </button>
                                    ) : (
                                        <button
                                            onClick={handleNext}
                                            className="ml-auto bg-gradient-to-r from-gray-900 to-gray-800 text-white px-6 py-2 rounded-full font-semibold shadow-md hover:shadow-lg hover:from-emerald-600 hover:to-emerald-600 hover:-translate-y-0.5 transition-all duration-200"
                                        >
                                            {currentQuestion ===
                                            quiz.questions.length - 1
                                                ? "Finish"
                                                : "Next Question"}
                                        </button>
                                    )}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="text-center py-8">
                            <div className={`flex justify-center mb-4 w-16 h-16 mx-auto rounded-full items-center ${score > 70 ? "bg-gradient-to-br from-amber-100 to-orange-100" : "bg-gradient-to-br from-emerald-100 to-teal-100"}`}>
                                {score > 70 ? (
                                    <Trophy className="w-8 h-8 text-amber-500" />
                                ) : (
                                    <CheckCircle2 className="w-8 h-8 text-emerald-500" />
                                )}
                            </div>

                            <h3 className="text-xl font-semibold mb-2 text-gray-900 dark:text-white">
                                Quiz Complete!
                            </h3>

                            <p className="inline-block mt-2 px-5 py-2 rounded-full bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-500/10">
                                {score} / {quiz.questions.length}
                            </p>

                            <div className="mt-6">
                                <button
                                    onClick={exitQuiz}
                                    className="px-6 py-2.5 rounded-full font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/20 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                                >
                                    Back to Lesson
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            )}
            </div>
            </div>
        </div>
    );
};

// Mount the component
if (document.getElementById("ear-training-quiz-show")) {
    const root = ReactDOM.createRoot(
        document.getElementById("ear-training-quiz-show")
    );
    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <ShowEartraining />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
