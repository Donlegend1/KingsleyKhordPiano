import React from "react";

const DEGREE_COLORS = {
    root: "bg-purple-600",
    third: "bg-blue-600",
    fifth: "bg-green-600",
    seventh: "bg-amber-600",
};

const DegreeBadge = ({ numeral, role }) => (
    <span
        className={`flex items-center justify-center w-11 h-11 rounded-full text-white font-bold text-sm ${DEGREE_COLORS[role]}`}
    >
        {numeral}
    </span>
);

const DownArrow = ({ caption }) => (
    <div className="flex flex-col items-center gap-1 my-4">
        <svg className="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 5v14M6 13l6 6 6-6" />
        </svg>
        <p className="text-xs text-gray-500 dark:text-gray-400 text-center">{caption}</p>
    </div>
);

const VoicingRow = ({ label, notes }) => (
    <div>
        <p className="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">
            {label}
        </p>
        <div className="flex items-center gap-2">
            {notes.map((note, idx) => (
                <DegreeBadge key={idx} numeral={note.numeral} role={note.role} />
            ))}
        </div>
    </div>
);

const TRIAD_CLOSE = [
    { numeral: "1", role: "root" },
    { numeral: "3", role: "third" },
    { numeral: "5", role: "fifth" },
];
const TRIAD_DROP2 = [
    { numeral: "3", role: "third" },
    { numeral: "1", role: "root" },
    { numeral: "5", role: "fifth" },
];

const SEVENTH_CLOSE = [
    { numeral: "1", role: "root" },
    { numeral: "3", role: "third" },
    { numeral: "5", role: "fifth" },
    { numeral: "7", role: "seventh" },
];
const SEVENTH_DROP2 = [
    { numeral: "5", role: "fifth" },
    { numeral: "1", role: "root" },
    { numeral: "3", role: "third" },
    { numeral: "7", role: "seventh" },
];

const Drop2ChordsCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to build Drop 2 voicings for triads and 7th chords.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            What is a Drop 2 Voicing?
        </h3>
        <p className="text-gray-700 dark:text-gray-300 mb-10">
            Start with a chord in close position (its notes stacked in order, right next to
            each other). Find the 2nd note from the top, and drop it down an octave so it
            becomes the new lowest note. The chord still contains the same notes — it just
            sounds wider and more open.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Drop 2 Examples
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <h4 className="text-indigo-600 dark:text-indigo-300 font-semibold mb-5">
                    Triad — C Major
                </h4>
                <VoicingRow label="Close Position (1 – 3 – 5)" notes={TRIAD_CLOSE} />
                <DownArrow caption="Drop the 3rd (2nd note from the top) down an octave" />
                <VoicingRow label="Drop 2 Voicing" notes={TRIAD_DROP2} />
            </div>

            <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                <h4 className="text-indigo-600 dark:text-indigo-300 font-semibold mb-5">
                    7th Chord — Cmaj7
                </h4>
                <VoicingRow label="Close Position (1 – 3 – 5 – 7)" notes={SEVENTH_CLOSE} />
                <DownArrow caption="Drop the 5th (2nd note from the top) down an octave" />
                <VoicingRow label="Drop 2 Voicing" notes={SEVENTH_DROP2} />
            </div>
        </div>
    </div>
);

export default Drop2ChordsCheckpoint;
