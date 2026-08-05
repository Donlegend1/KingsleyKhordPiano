import React from "react";

const DEGREE_COLORS = {
    root: "bg-purple-600",
    third: "bg-blue-600",
    fifth: "bg-green-600",
    seventh: "bg-amber-600",
};

const DEGREE_LABELS = {
    root: "Root",
    third: "3rd",
    fifth: "5th",
    seventh: "7th",
};

const DegreeBadge = ({ numeral, role, isBass, isOctave }) => (
    <div className="flex flex-col items-center gap-1.5">
        <span
            className={`flex items-center justify-center rounded-full text-white font-bold ${DEGREE_COLORS[role]} ${
                isBass || isOctave
                    ? "w-12 h-12 text-base ring-4 ring-gray-900/10 dark:ring-white/10"
                    : "w-11 h-11 text-sm"
            }`}
        >
            {numeral}
        </span>
        <span className="text-[11px] text-gray-500 dark:text-gray-400">
            {isOctave ? `${DEGREE_LABELS[role]} (8ve)` : DEGREE_LABELS[role]}
        </span>
    </div>
);

// Builds a 5-note block chord voicing (4 chord tones spanning one octave,
// with the bass note doubled an octave higher on top) for a given
// inversion of a generic 4-note 7th chord (degrees 1-3-5-7).
const buildBlockChord = (degrees, inversionIndex) => {
    const rotated = [...degrees.slice(inversionIndex), ...degrees.slice(0, inversionIndex)];
    return [...rotated, { ...rotated[0], isOctave: true }];
};

const DEGREES = [
    { numeral: "1", role: "root" },
    { numeral: "3", role: "third" },
    { numeral: "5", role: "fifth" },
    { numeral: "7", role: "seventh" },
];

const INVERSION_TITLES = ["Root Position", "1st Inversion", "2nd Inversion", "3rd Inversion"];

const CHORD_FORMULAS = [
    { name: "Major 7", formula: "1 – 3 – 5 – 7" },
    { name: "Minor 7", formula: "1 – b3 – 5 – b7" },
    { name: "Dominant 7", formula: "1 – 3 – 5 – b7" },
    { name: "Minor 7b5", formula: "1 – b3 – b5 – b7" },
    { name: "Diminished 7", formula: "1 – b3 – b5 – bb7" },
];

const SeventhChordInversionsCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to build all four block chord inversions of a 7th chord.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            What is a Block Chord Inversion?
        </h3>
        <p className="text-gray-700 dark:text-gray-300 mb-10">
            A 7th chord has four notes — root, 3rd, 5th, and 7th. In block chord style, you
            play all four within a single octave and double the bottom note an octave higher
            on top, giving you five notes that span exactly one octave (1 – 3 – 5 – 7 – 1).
            Each inversion moves a different chord tone into the bass, and the doubled note
            on top always matches whichever note is on the bottom. This same shape applies to
            any 7th chord — Major 7, Minor 7, Dominant 7, Minor 7b5, or Diminished 7 — you're
            just adjusting which degrees are flatted.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Block Chord Inversions
        </h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {INVERSION_TITLES.map((invTitle, invIdx) => {
                const notes = buildBlockChord(DEGREES, invIdx);
                return (
                    <div
                        key={invTitle}
                        className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 text-center"
                    >
                        <h4 className="font-bold text-indigo-700 dark:text-indigo-300 mb-1">
                            {invTitle}
                        </h4>
                        <p className="text-xs text-gray-400 dark:text-gray-500 mb-4">
                            {DEGREE_LABELS[DEGREES[invIdx].role]} in the bass
                        </p>
                        <div className="flex items-end justify-center gap-1.5">
                            {notes.map((n, idx) => (
                                <DegreeBadge
                                    key={idx}
                                    {...n}
                                    isBass={idx === 0}
                                    isOctave={n.isOctave}
                                />
                            ))}
                        </div>
                    </div>
                );
            })}
        </div>

        <div className="flex flex-wrap gap-4 mb-10 text-sm font-medium">
            {[
                { label: "Root", role: "root" },
                { label: "3rd", role: "third" },
                { label: "5th", role: "fifth" },
                { label: "7th", role: "seventh" },
            ].map(({ label, role }) => (
                <span key={role} className="flex items-center gap-2">
                    <span className={`w-3 h-3 rounded-full ${DEGREE_COLORS[role]}`} />
                    <span className="text-gray-600 dark:text-gray-300">{label}</span>
                </span>
            ))}
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Applying This to Every 7th Chord Type
        </h3>
        <div className="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
            <table className="min-w-full text-sm">
                <thead className="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                            Chord Type
                        </th>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                            Formula
                        </th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                    {CHORD_FORMULAS.map((row) => (
                        <tr key={row.name} className="bg-white dark:bg-gray-900">
                            <td className="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                {row.name}
                            </td>
                            <td className="px-4 py-3 text-blue-600 font-semibold whitespace-nowrap">
                                {row.formula}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    </div>
);

export default SeventhChordInversionsCheckpoint;
