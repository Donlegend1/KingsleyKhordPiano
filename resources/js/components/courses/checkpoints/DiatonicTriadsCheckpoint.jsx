import React from "react";

const QUALITY_STYLES = {
    Major: "bg-indigo-600",
    minor: "bg-purple-600",
    diminished: "bg-rose-600",
};

const DEGREE_PATTERN = [
    { numeral: "I", quality: "Major" },
    { numeral: "ii", quality: "minor" },
    { numeral: "iii", quality: "minor" },
    { numeral: "IV", quality: "Major" },
    { numeral: "V", quality: "Major" },
    { numeral: "vi", quality: "minor" },
    { numeral: "vii°", quality: "diminished" },
];

// Worked example in C Major, one chord per scale degree.
const C_MAJOR_EXAMPLE = ["C", "Dm", "Em", "F", "G", "Am", "B°"];

const ChordBadge = ({ numeral, quality, chord }) => (
    <div className="flex flex-col items-center gap-2">
        <span
            className={`flex items-center justify-center w-12 h-12 rounded-full text-white font-bold text-sm ${QUALITY_STYLES[quality]}`}
        >
            {numeral}
        </span>
        {chord && (
            <span className="text-sm font-semibold text-gray-800 dark:text-gray-200">
                {chord}
            </span>
        )}
    </div>
);

const DIATONIC_TRIADS = [
    { key: "C Major", chords: ["C", "Dm", "Em", "F", "G", "Am", "B°"] },
    { key: "Db Major", chords: ["Db", "Ebm", "Fm", "Gb", "Ab", "Bbm", "C°"] },
    { key: "D Major", chords: ["D", "Em", "F#m", "G", "A", "Bm", "C#°"] },
    { key: "Eb Major", chords: ["Eb", "Fm", "Gm", "Ab", "Bb", "Cm", "D°"] },
    { key: "E Major", chords: ["E", "F#m", "G#m", "A", "B", "C#m", "D#°"] },
    { key: "F Major", chords: ["F", "Gm", "Am", "Bb", "C", "Dm", "E°"] },
    { key: "F# Major", chords: ["F#", "G#m", "A#m", "B", "C#", "D#m", "E#°"] },
    { key: "G Major", chords: ["G", "Am", "Bm", "C", "D", "Em", "F#°"] },
    { key: "Ab Major", chords: ["Ab", "Bbm", "Cm", "Db", "Eb", "Fm", "G°"] },
    { key: "A Major", chords: ["A", "Bm", "C#m", "D", "E", "F#m", "G#°"] },
    { key: "Bb Major", chords: ["Bb", "Cm", "Dm", "Eb", "F", "Gm", "A°"] },
    { key: "B Major", chords: ["B", "C#m", "D#m", "E", "F#", "G#m", "A#°"] },
];

const DiatonicTriadsCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to build the seven diatonic triads in any major key.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            The Diatonic Triad Pattern
        </h3>
        <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 mb-4">
            <div className="flex flex-wrap items-start justify-between gap-4">
                {DEGREE_PATTERN.map((degree) => (
                    <ChordBadge key={degree.numeral} {...degree} />
                ))}
            </div>
        </div>
        <div className="flex flex-wrap gap-4 mb-8 text-sm font-medium">
            {["Major", "minor", "diminished"].map((quality) => (
                <span key={quality} className="flex items-center gap-2">
                    <span className={`w-3 h-3 rounded-full ${QUALITY_STYLES[quality]}`} />
                    <span className="text-gray-600 dark:text-gray-300 capitalize">{quality}</span>
                </span>
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
                    Example in C Major
                </span>
            </div>
            <div className="flex flex-wrap items-start justify-between gap-4">
                {DEGREE_PATTERN.map((degree, idx) => (
                    <ChordBadge
                        key={degree.numeral}
                        {...degree}
                        chord={C_MAJOR_EXAMPLE[idx]}
                    />
                ))}
            </div>
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            How to Build a Diatonic Triad
        </h3>
        <p className="text-gray-700 dark:text-gray-300 mb-10">
            Take any scale degree as the root, then stack the note two scale-steps above it
            (the 3rd) and the note two scale-steps above that (the 5th). Whether each triad
            comes out Major, minor, or diminished depends only on which degree it starts on
            — the pattern above is the same in every major key.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Diatonic Triads in All 12 Keys
        </h3>
        <div className="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
            <table className="min-w-full text-sm">
                <thead className="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">
                            Key
                        </th>
                        {DEGREE_PATTERN.map((degree) => (
                            <th
                                key={degree.numeral}
                                className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200"
                            >
                                {degree.numeral}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                    {DIATONIC_TRIADS.map((row) => (
                        <tr key={row.key} className="bg-white dark:bg-gray-900">
                            <td className="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                {row.key}
                            </td>
                            {row.chords.map((chord, idx) => (
                                <td key={idx} className="px-4 py-3 text-blue-600 font-semibold whitespace-nowrap">
                                    {chord}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    </div>
);

export default DiatonicTriadsCheckpoint;
