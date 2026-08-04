import React from "react";

const FINGER_LABELS = [
    { num: 1, label: "Thumb" },
    { num: 2, label: "Index" },
    { num: 3, label: "Middle" },
    { num: 4, label: "Ring" },
    { num: 5, label: "Pinky" },
];

// Geometric hand glyph: a rounded palm with four evenly-spaced pill-shaped
// fingers and a separate thumb stub, each tipped with a numbered badge.
// Built from plain rects (no freehand paths) so it stays crisp and
// perfectly proportioned at any size. `mirrored` reflects the whole
// diagram horizontally to produce a left hand from the same geometry —
// coordinates are mirrored via simple arithmetic (not a CSS transform), so
// the badge numbers are never rendered backwards.
const VIEW_WIDTH = 200;

const PALM = { x: 40, y: 118, width: 120, height: 66, rx: 18 };

const FINGERS = [
    { num: 2, label: "Index", x: 50, y: 66, width: 20, height: 58 },
    { num: 3, label: "Middle", x: 76, y: 43, width: 20, height: 81 },
    { num: 4, label: "Ring", x: 102, y: 53, width: 20, height: 71 },
    { num: 5, label: "Pinky", x: 128, y: 78, width: 20, height: 46 },
];

const THUMB = { num: 1, label: "Thumb", x: 16, y: 146, width: 44, height: 24, rx: 12 };

const mirrorX = (x, width) => VIEW_WIDTH - x - width;

const HandDiagram = ({ mirrored = false }) => {
    const rects = [
        { ...PALM, tip: null },
        ...FINGERS.map((f) => ({ ...f, tip: { cx: f.x + f.width / 2, cy: f.y + f.width / 2 } })),
        { ...THUMB, tip: { cx: THUMB.x + THUMB.height / 2, cy: THUMB.y + THUMB.height / 2 } },
    ];

    return (
        <svg viewBox="0 0 200 200" className="w-full h-auto">
            <g>
                {rects.map((r, idx) => (
                    <rect
                        key={idx}
                        x={mirrored ? mirrorX(r.x, r.width) : r.x}
                        y={r.y}
                        width={r.width}
                        height={r.height}
                        rx={r.rx ?? r.width / 2}
                        className="fill-white stroke-blue-600 dark:fill-gray-800"
                        strokeWidth="2.5"
                    />
                ))}
            </g>
            {rects
                .filter((r) => r.tip)
                .map((r) => {
                    const cx = mirrored ? mirrorX(r.x, r.width) + r.tip.cx - r.x : r.tip.cx;
                    return (
                        <g key={r.num}>
                            <circle cx={cx} cy={r.tip.cy} r="11" className="fill-blue-600" />
                            <text
                                x={cx}
                                y={r.tip.cy + 4}
                                textAnchor="middle"
                                className="fill-white text-[11px] font-bold"
                            >
                                {r.num}
                            </text>
                        </g>
                    );
                })}
        </svg>
    );
};

const MAJOR_SCALES = [
    {
        key: "C Major",
        notes: ["C", "D", "E", "F", "G", "A", "B", "C"],
        rightHand: [1, 2, 3, 1, 2, 3, 4, 5],
        leftHand: [5, 4, 3, 2, 1, 3, 2, 1],
    },
    {
        key: "G Major",
        notes: ["G", "A", "B", "C", "D", "E", "F#", "G"],
        rightHand: [1, 2, 3, 1, 2, 3, 4, 5],
        leftHand: [5, 4, 3, 2, 1, 3, 2, 1],
    },
    {
        key: "D Major",
        notes: ["D", "E", "F#", "G", "A", "B", "C#", "D"],
        rightHand: [1, 2, 3, 1, 2, 3, 4, 5],
        leftHand: [5, 4, 3, 2, 1, 3, 2, 1],
    },
    {
        key: "A Major",
        notes: ["A", "B", "C#", "D", "E", "F#", "G#", "A"],
        rightHand: [1, 2, 3, 1, 2, 3, 4, 5],
        leftHand: [5, 4, 3, 2, 1, 3, 2, 1],
    },
    {
        key: "E Major",
        notes: ["E", "F#", "G#", "A", "B", "C#", "D#", "E"],
        rightHand: [1, 2, 3, 1, 2, 3, 4, 5],
        leftHand: [5, 4, 3, 2, 1, 3, 2, 1],
    },
    {
        key: "B Major",
        notes: ["B", "C#", "D#", "E", "F#", "G#", "A#", "B"],
        rightHand: [1, 2, 3, 1, 2, 3, 4, 5],
        leftHand: [4, 3, 2, 1, 4, 3, 2, 1],
    },
    {
        key: "F# Major",
        notes: ["F#", "G#", "A#", "B", "C#", "D#", "E#", "F#"],
        rightHand: [2, 3, 4, 1, 2, 3, 4, 5],
        leftHand: [4, 3, 2, 1, 3, 2, 1, 2],
    },
    {
        key: "Db Major",
        notes: ["Db", "Eb", "F", "Gb", "Ab", "Bb", "C", "Db"],
        rightHand: [2, 3, 1, 2, 3, 4, 1, 2],
        leftHand: [3, 2, 1, 4, 3, 2, 1, 3],
    },
    {
        key: "Ab Major",
        notes: ["Ab", "Bb", "C", "Db", "Eb", "F", "G", "Ab"],
        rightHand: [3, 4, 1, 2, 3, 1, 2, 3],
        leftHand: [3, 2, 1, 4, 3, 2, 1, 3],
    },
    {
        key: "Eb Major",
        notes: ["Eb", "F", "G", "Ab", "Bb", "C", "D", "Eb"],
        rightHand: [3, 1, 2, 3, 4, 1, 2, 3],
        leftHand: [3, 2, 1, 4, 3, 2, 1, 3],
    },
    {
        key: "Bb Major",
        notes: ["Bb", "C", "D", "Eb", "F", "G", "A", "Bb"],
        rightHand: [4, 1, 2, 3, 1, 2, 3, 4],
        leftHand: [3, 2, 1, 4, 3, 2, 1, 2],
    },
    {
        key: "F Major",
        notes: ["F", "G", "A", "Bb", "C", "D", "E", "F"],
        rightHand: [1, 2, 3, 4, 1, 2, 3, 4],
        leftHand: [5, 4, 3, 2, 1, 3, 2, 1],
    },
];

const FingeringNumbers = ({ numbers, colorClass }) => (
    <span className="inline-flex gap-1.5">
        {numbers.map((n, idx) => (
            <span key={idx} className={`font-semibold ${colorClass}`}>
                {n}
            </span>
        ))}
    </span>
);

const MajorScaleCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to play Major Scales with the correct fingerings in all 12 keys.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Fingering Guide
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            {[
                { title: "Right Hand", mirrored: false },
                { title: "Left Hand", mirrored: true },
            ].map(({ title, mirrored }) => (
                <div
                    key={title}
                    className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 shadow-sm"
                >
                    <h4 className="text-indigo-600 dark:text-indigo-300 font-semibold mb-5">
                        {title}
                    </h4>
                    <div className="flex items-center gap-8">
                        <div className="w-28 flex-shrink-0">
                            <HandDiagram mirrored={mirrored} />
                        </div>
                        <ul className="space-y-2 text-sm">
                            {FINGER_LABELS.map(({ num, label }) => (
                                <li key={num} className="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                    <span className="flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white text-[11px] font-bold">
                                        {num}
                                    </span>
                                    {label}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            ))}
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Major Scales in All 12 Keys
        </h3>
        <div className="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
            <table className="min-w-full text-sm">
                <thead className="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Key</th>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Scale (Ascending)</th>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Right Hand Fingering</th>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Left Hand Fingering</th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                    {MAJOR_SCALES.map((scale) => (
                        <tr key={scale.key} className="bg-white dark:bg-gray-900">
                            <td className="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                {scale.key}
                            </td>
                            <td className="px-4 py-3 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {scale.notes.join(" ")}
                            </td>
                            <td className="px-4 py-3 whitespace-nowrap">
                                <FingeringNumbers numbers={scale.rightHand} colorClass="text-blue-600" />
                            </td>
                            <td className="px-4 py-3 whitespace-nowrap">
                                <FingeringNumbers numbers={scale.leftHand} colorClass="text-blue-600" />
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    </div>
);

export default MajorScaleCheckpoint;
