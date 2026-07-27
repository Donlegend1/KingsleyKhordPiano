import React from "react";

const SOLFA = ["do", "re", "mi", "fa", "so", "la", "ti", "do"];
// C major scale, middle C to the octave above, used as the worked example.
const EXAMPLE_NOTES = ["C4", "D4", "E4", "F4", "G4", "A4", "B4", "C5"];
const HIGHLIGHTED_INDEX = 4; // "so" / G4

// Diatonic staff step for each pitch, relative to the bottom staff line
// (E4 = step 0). Each step is one line-or-space on the treble clef.
const STEP_BY_NOTE = { C4: -2, D4: -1, E4: 0, F4: 1, G4: 2, A4: 3, B4: 4, C5: 5 };

const StaffExample = () => {
    const stepHeight = 8;
    const baseline = 80; // y position of E4 (bottom staff line)
    const noteX = (idx) => 75 + idx * 42;
    const noteY = (note) => baseline - STEP_BY_NOTE[note] * stepHeight;
    const width = 75 + EXAMPLE_NOTES.length * 42;

    return (
        <svg viewBox={`0 0 ${width} 110`} className="w-full h-auto">
            {[16, 32, 48, 64, 80].map((y) => (
                <line
                    key={y}
                    x1="10"
                    y1={y}
                    x2={width - 10}
                    y2={y}
                    className="stroke-gray-400 dark:stroke-gray-500"
                    strokeWidth="1"
                />
            ))}
            <text x="14" y="76" className="fill-gray-700 dark:fill-gray-300" style={{ fontSize: 58 }}>
                &#119070;
            </text>
            {EXAMPLE_NOTES.map((note, idx) => {
                const x = noteX(idx);
                const y = noteY(note);
                const isHighlighted = idx === HIGHLIGHTED_INDEX;
                return (
                    <g key={note}>
                        {note === "C4" && (
                            <line
                                x1={x - 10}
                                y1={y}
                                x2={x + 10}
                                y2={y}
                                className="stroke-gray-400 dark:stroke-gray-500"
                                strokeWidth="1"
                            />
                        )}
                        <ellipse
                            cx={x}
                            cy={y}
                            rx="6.5"
                            ry="5"
                            className={isHighlighted ? "fill-blue-600" : "fill-gray-800 dark:fill-gray-200"}
                        />
                        <line
                            x1={x + 6}
                            y1={y}
                            x2={x + 6}
                            y2={y - 28}
                            className={isHighlighted ? "stroke-blue-600" : "stroke-gray-800 dark:stroke-gray-200"}
                            strokeWidth="1.3"
                        />
                    </g>
                );
            })}
            {SOLFA.map((syl, idx) => (
                <text
                    key={idx}
                    x={noteX(idx)}
                    y="103"
                    textAnchor="middle"
                    className={`text-[12px] font-semibold ${
                        idx === HIGHLIGHTED_INDEX
                            ? "fill-blue-600"
                            : "fill-gray-700 dark:fill-gray-300"
                    }`}
                >
                    {syl}
                </text>
            ))}
        </svg>
    );
};

const SolfaBadge = ({ syllable, degree, highlighted }) => (
    <div className="flex flex-col items-center gap-2">
        <span
            className={`flex items-center justify-center w-12 h-12 rounded-full text-white font-bold text-sm ${
                highlighted ? "bg-blue-600" : "bg-indigo-600"
            }`}
        >
            {degree}
        </span>
        <span className="text-sm font-semibold text-gray-800 dark:text-gray-200">
            {syllable}
        </span>
    </div>
);

const HOW_IT_WORKS = [
    'You will hear a reference note (the tonal center "do").',
    "Then you will hear another note.",
    "Identify the solfa name of that note (do, re, mi, fa, so, la, ti) relative to the tonal center.",
];

const NumberedStep = ({ num, children }) => (
    <div className="flex items-start gap-3">
        <span className="flex items-center justify-center w-6 h-6 rounded-full border-2 border-indigo-600 text-indigo-600 text-xs font-bold flex-shrink-0 mt-0.5">
            {num}
        </span>
        <p className="text-gray-700 dark:text-gray-300">{children}</p>
    </div>
);

const ESSENTIALS = [
    {
        title: "1. Intervals (by sound)",
        text: 'Learn how intervals sound (e.g., 2nd up, 3rd up, 5th up, etc.). This builds your mental "ear map."',
        icon: (
            <svg className="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0Z" />
                <path d="M5 12c0-1.5.5-2.5 1.5-3.5M19 12c0-1.5-.5-2.5-1.5-3.5M3 12c0-2.5 1-4.5 2.5-6M21 12c0-2.5-1-4.5-2.5-6" />
            </svg>
        ),
    },
    {
        title: "2. Solfa Mapping",
        text: "Train your ear to map every note you hear to do, re, mi, fa, so, la, ti from any starting note.",
        icon: (
            <svg className="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="12" cy="12" r="9" />
                <circle cx="12" cy="12" r="5" />
                <circle cx="12" cy="12" r="1" fill="currentColor" />
            </svg>
        ),
    },
    {
        title: "3. Consistency Across Keys",
        text: "Practice in different tonal centers so your ear becomes flexible and strong.",
        icon: (
            <svg className="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M3 12a9 9 0 0 1 15-6.7L21 8" />
                <path d="M21 3v5h-5" />
                <path d="M21 12a9 9 0 0 1-15 6.7L3 16" />
                <path d="M3 21v-5h5" />
            </svg>
        ),
    },
    {
        title: "4. Recognition Speed",
        text: "The more you practice, the faster and more accurate you'll become.",
        icon: (
            <svg className="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M9.5 3.5a5.5 5.5 0 0 0-3 10.1V16a2 2 0 0 0 2 2h.5v1a1.5 1.5 0 0 0 3 0v-1h1v1a1.5 1.5 0 0 0 3 0v-1H16a2 2 0 0 0 2-2v-2.4a5.5 5.5 0 0 0-3-10.1" />
            </svg>
        ),
    },
    {
        title: "5. Real-World Application",
        text: "Helps with sight-singing, transcribing, improvising, learning songs by ear, and understanding music deeper.",
        icon: (
            <svg className="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="12" cy="12" r="9" />
                <path d="m8 12 3 3 5-6" />
            </svg>
        ),
    },
];

const RelativePitchCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to identify solfa (do, re, mi, fa, so, la, ti) from any tonal center.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            The Solfa Pattern
        </h3>
        <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 mb-4">
            <div className="flex flex-wrap items-start justify-between gap-4">
                {SOLFA.map((syl, idx) => (
                    <SolfaBadge
                        key={idx}
                        syllable={syl}
                        degree={idx === 7 ? 1 : idx + 1}
                        highlighted={idx === HIGHLIGHTED_INDEX}
                    />
                ))}
            </div>
        </div>
        <p className="text-gray-700 dark:text-gray-300 mb-10">
            Relative pitch is the ability to hear and identify notes in relation to a reference
            note (the tonal center). We use solfa (do, re, mi, fa, so, la, ti) to describe the
            relationship of each note to the tonic — the same note can have a different solfa
            name depending on the tonal center.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            How It Works
        </h3>
        <div className="space-y-3 mb-10">
            {HOW_IT_WORKS.map((step, idx) => (
                <NumberedStep key={idx} num={idx + 1}>
                    {step}
                </NumberedStep>
            ))}
        </div>

        <div className="rounded-2xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50/60 dark:bg-indigo-900/20 p-6 mb-10">
            <div className="flex items-center gap-2.5 mb-4">
                <span className="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex-shrink-0">
                    <svg className="w-4 h-4 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M11 5 6 9H3v6h3l5 4V5Z" />
                        <path d="M16 9a3 3 0 0 1 0 6" />
                    </svg>
                </span>
                <span className="text-indigo-700 dark:text-indigo-300 font-semibold">
                    Example in C Major (Tonal Center = C)
                </span>
            </div>
            <p className="text-gray-600 dark:text-gray-300 text-sm mb-3">
                If you hear G, the answer is "so".
            </p>
            <StaffExample />
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Essential Things to Master in Relative Pitch
        </h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            {ESSENTIALS.map((item) => (
                <div
                    key={item.title}
                    className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 text-center"
                >
                    <span className="flex items-center justify-center w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 mx-auto mb-3">
                        {item.icon}
                    </span>
                    <h4 className="font-bold text-indigo-700 dark:text-indigo-300 mb-2">
                        {item.title}
                    </h4>
                    <p className="text-sm text-gray-600 dark:text-gray-300">{item.text}</p>
                </div>
            ))}
        </div>
    </div>
);

export default RelativePitchCheckpoint;
