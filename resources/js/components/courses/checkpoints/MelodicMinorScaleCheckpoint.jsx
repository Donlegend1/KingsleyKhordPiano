import React from "react";

const DEGREE_COLORS = {
    root: "bg-purple-600",
    plain: "bg-indigo-600",
    minorThird: "bg-blue-600",
    raised: "bg-green-600",
};

const PATTERN_DEGREES = [
    { numeral: "1", role: "root" },
    { numeral: "2", role: "plain" },
    { numeral: "b3", role: "minorThird" },
    { numeral: "4", role: "plain" },
    { numeral: "5", role: "plain" },
    { numeral: "6", role: "raised" },
    { numeral: "7", role: "raised" },
    { numeral: "8", role: "root" },
];

const DegreeBadge = ({ numeral, role }) => (
    <span
        className={`flex items-center justify-center w-11 h-11 rounded-full text-white font-bold text-sm ${DEGREE_COLORS[role]}`}
    >
        {numeral}
    </span>
);

const C_MELODIC_MINOR_ASCENDING = [
    { note: "C", role: "root" },
    { note: "D", role: "plain" },
    { note: "Eb", role: "minorThird" },
    { note: "F", role: "plain" },
    { note: "G", role: "plain" },
    { note: "A", role: "raised" },
    { note: "B", role: "raised" },
    { note: "C", role: "root" },
];

// Ascending melodic minor (natural minor with a raised 6th and 7th) across
// 12 keys. Two roots (Eb and Bb) use flat spellings for the full 7-note
// scale rather than their sharp-side enharmonic (D#/A#), since those sharp
// spellings would require a double-sharp on the raised 7th to stay
// letter-consistent — impractical for a beginner reference.
const MELODIC_MINOR_SCALES = [
    { key: "C Minor", notes: "C – D – Eb – F – G – A – B – C" },
    { key: "C# Minor / Db Minor", notes: "C# – D# – E – F# – G# – A# – B# – C#" },
    { key: "D Minor", notes: "D – E – F – G – A – B – C# – D" },
    { key: "D# Minor / Eb Minor", notes: "Eb – F – Gb – Ab – Bb – C – D – Eb" },
    { key: "E Minor", notes: "E – F# – G – A – B – C# – D# – E" },
    { key: "F Minor", notes: "F – G – Ab – Bb – C – D – E – F" },
    { key: "F# Minor / Gb Minor", notes: "F# – G# – A – B – C# – D# – E# – F#" },
    { key: "G Minor", notes: "G – A – Bb – C – D – E – F# – G" },
    { key: "G# Minor / Ab Minor", notes: "G# – A# – B – C# – D# – F – G – G#" },
    { key: "A Minor", notes: "A – B – C – D – E – F# – G# – A" },
    { key: "A# Minor / Bb Minor", notes: "Bb – C – Db – Eb – F – G – A – Bb" },
    { key: "B Minor", notes: "B – C# – D – E – F# – G# – A# – B" },
];

const MelodicMinorScaleCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to play the Melodic Minor Scale in all 12 keys.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            What is the Melodic Minor Scale?
        </h3>
        <p className="text-gray-700 dark:text-gray-300 mb-10">
            The melodic minor scale is a natural minor scale with a raised 6th and 7th degree
            when ascending — that's what gives it a smoother pull back up to the root compared
            to natural minor. Descending, it traditionally reverts back to the plain natural
            minor scale, played in reverse.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            The Melodic Minor Pattern
        </h3>
        <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 mb-4">
            <div className="flex flex-wrap items-start justify-center gap-4">
                {PATTERN_DEGREES.map((degree, idx) => (
                    <DegreeBadge key={idx} {...degree} />
                ))}
            </div>
        </div>
        <div className="flex flex-wrap gap-4 mb-10 text-sm font-medium">
            {[
                { label: "Root", role: "root" },
                { label: "Minor 3rd", role: "minorThird" },
                { label: "Natural Degree", role: "plain" },
                { label: "Raised 6th & 7th", role: "raised" },
            ].map(({ label, role }) => (
                <span key={role} className="flex items-center gap-2">
                    <span className={`w-3 h-3 rounded-full ${DEGREE_COLORS[role]}`} />
                    <span className="text-gray-600 dark:text-gray-300">{label}</span>
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
                    Example in C Melodic Minor
                </span>
            </div>
            <p className="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
                Ascending
            </p>
            <div className="flex flex-wrap items-center gap-2 mb-4">
                {C_MELODIC_MINOR_ASCENDING.map((n, idx) => (
                    <DegreeBadge key={idx} numeral={n.note} role={n.role} />
                ))}
            </div>
            <p className="text-sm text-gray-600 dark:text-gray-300">
                Descending: C – Bb – Ab – G – F – Eb – D – C (natural minor, in reverse)
            </p>
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Melodic Minor Scales in All 12 Keys (Ascending)
        </h3>
        <div className="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
            <table className="min-w-full text-sm">
                <thead className="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                            Key
                        </th>
                        <th className="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                            Scale (Ascending)
                        </th>
                    </tr>
                </thead>
                <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                    {MELODIC_MINOR_SCALES.map((row) => (
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
    </div>
);

export default MelodicMinorScaleCheckpoint;
