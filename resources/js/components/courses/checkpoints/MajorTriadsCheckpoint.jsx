import React from "react";

// One-octave keyboard (C to C) with the root/3rd/5th of a C Major triad
// highlighted, used as the worked example.
const WHITE_KEYS = ["C", "D", "E", "F", "G", "A", "B"];
// Black key sits after these white-key indices (no black key after E or B).
const BLACK_KEY_AFTER = [true, true, false, true, true, true, false];

const TRIAD_DOTS = {
    C: { color: "#7c3aed", label: "1" },
    E: { color: "#2563eb", label: "3" },
    G: { color: "#16a34a", label: "5" },
};

const TriadKeyboard = () => {
    const whiteKeyWidth = 44;
    const width = WHITE_KEYS.length * whiteKeyWidth;
    const height = 150;
    const blackKeyHeight = 88;

    return (
        <svg viewBox={`0 0 ${width} ${height}`} className="w-full h-auto max-w-md">
            {/* Keybed base, rounded corners for a clean flat-icon look */}
            <rect
                x="0"
                y="0"
                width={width}
                height={height}
                rx="8"
                className="fill-white stroke-gray-200 dark:fill-gray-800 dark:stroke-gray-600"
                strokeWidth="1.5"
            />
            {/* Divider lines between white keys */}
            {WHITE_KEYS.slice(1).map((_, idx) => (
                <line
                    key={`divider-${idx}`}
                    x1={(idx + 1) * whiteKeyWidth}
                    y1="0"
                    x2={(idx + 1) * whiteKeyWidth}
                    y2={height}
                    className="stroke-gray-200 dark:stroke-gray-600"
                    strokeWidth="1.5"
                />
            ))}
            {BLACK_KEY_AFTER.map(
                (hasBlack, idx) =>
                    hasBlack && (
                        <rect
                            key={`black-${idx}`}
                            x={(idx + 1) * whiteKeyWidth - whiteKeyWidth * 0.27}
                            y={0}
                            width={whiteKeyWidth * 0.54}
                            height={blackKeyHeight}
                            rx="3"
                            className="fill-gray-900 dark:fill-black"
                        />
                    )
            )}
            {WHITE_KEYS.map((note, idx) => {
                const dot = TRIAD_DOTS[note];
                if (!dot) return null;
                const cx = idx * whiteKeyWidth + whiteKeyWidth / 2;
                return (
                    <g key={`dot-${note}`}>
                        <circle
                            cx={cx}
                            cy={height - 24}
                            r="12"
                            fill={dot.color}
                            className="stroke-white dark:stroke-gray-800"
                            strokeWidth="2"
                        />
                        <text
                            x={cx}
                            y={height - 20}
                            textAnchor="middle"
                            className="fill-white text-[12px] font-bold"
                        >
                            {dot.label}
                        </text>
                    </g>
                );
            })}
        </svg>
    );
};

const DEGREE_PATTERN = [
    { numeral: "1", label: "Root", color: "bg-purple-600" },
    { numeral: "3", label: "Major Third", color: "bg-blue-600" },
    { numeral: "5", label: "Perfect Fifth", color: "bg-green-600" },
];

const DegreeBadge = ({ numeral, color }) => (
    <span className={`flex items-center justify-center w-12 h-12 rounded-full text-white font-bold text-sm ${color}`}>
        {numeral}
    </span>
);

const MAJOR_TRIADS = [
    { key: "C Major", notes: "C – E – G" },
    { key: "C# Major / Db Major", notes: "C# – E# – G#" },
    { key: "D Major", notes: "D – F# – A" },
    { key: "D# Major / Eb Major", notes: "D# – G – A#" },
    { key: "E Major", notes: "E – G# – B" },
    { key: "F Major", notes: "F – A – C" },
    { key: "F# Major / Gb Major", notes: "F# – A# – C#" },
    { key: "G Major", notes: "G – B – D" },
    { key: "G# Major / Ab Major", notes: "G# – C – D#" },
    { key: "A Major", notes: "A – C# – E" },
    { key: "A# Major / Bb Major", notes: "A# – D – F" },
    { key: "B Major", notes: "B – D# – F#" },
];

const TriadTable = ({ rows }) => (
    <div className="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
        <table className="min-w-full text-sm">
            <thead className="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                        Key
                    </th>
                    <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                        Major Triad (1 – 3 – 5)
                    </th>
                </tr>
            </thead>
            <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                {rows.map((row) => (
                    <tr key={row.key} className="bg-white dark:bg-gray-900">
                        <td className="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                            {row.key}
                        </td>
                        <td className="px-4 py-3 text-blue-600 font-semibold whitespace-nowrap">
                            {row.notes}
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    </div>
);

const MajorTriadsCheckpoint = () => {
    const half = Math.ceil(MAJOR_TRIADS.length / 2);
    const firstHalf = MAJOR_TRIADS.slice(0, half);
    const secondHalf = MAJOR_TRIADS.slice(half);

    return (
        <div>
            <p className="text-gray-500 dark:text-gray-400 mb-8">
                Learn how to build and play Major Triads in all 12 keys.
            </p>

            <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                The Triad Pattern
            </h3>
            <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 mb-4">
                <div className="flex flex-wrap items-start justify-center gap-8">
                    {DEGREE_PATTERN.map((degree) => (
                        <DegreeBadge key={degree.numeral} {...degree} />
                    ))}
                </div>
            </div>
            <div className="flex flex-wrap gap-4 mb-10 text-sm font-medium">
                {DEGREE_PATTERN.map((degree) => (
                    <span key={degree.numeral} className="flex items-center gap-2">
                        <span className={`w-3 h-3 rounded-full ${degree.color}`} />
                        <span className="text-gray-600 dark:text-gray-300">{degree.label}</span>
                    </span>
                ))}
            </div>

            <div className="rounded-2xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50/60 dark:bg-indigo-900/20 p-6 mb-10">
                <div className="flex items-center gap-2.5 mb-4">
                    <span className="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex-shrink-0">
                        <svg className="w-4 h-4 text-indigo-600 dark:text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <rect x="3" y="6" width="18" height="12" rx="2" />
                            <path d="M7 6v6M11 6v6M15 6v6" />
                        </svg>
                    </span>
                    <span className="text-indigo-700 dark:text-indigo-300 font-semibold">
                        Example in C Major
                    </span>
                </div>
                <div className="max-w-xs">
                    <TriadKeyboard />
                </div>
            </div>

            <p className="text-gray-700 dark:text-gray-300 mb-10">
                Play the 1st, 3rd, and 5th notes of the scale together using any hand.
            </p>

            <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                Major Triads in All 12 Keys
            </h3>
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <TriadTable rows={firstHalf} />
                <TriadTable rows={secondHalf} />
            </div>
        </div>
    );
};

export default MajorTriadsCheckpoint;
