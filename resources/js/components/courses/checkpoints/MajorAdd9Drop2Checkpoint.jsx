import React from "react";

const DEGREE_COLORS = {
    root: "bg-purple-600",
    third: "bg-blue-600",
    fifth: "bg-green-600",
    ninth: "bg-rose-600",
};

const DegreeBadge = ({ numeral, role }) => (
    <span
        className={`flex items-center justify-center w-11 h-11 rounded-full text-white font-bold text-sm ${DEGREE_COLORS[role]}`}
    >
        {numeral}
    </span>
);

const ADD9_PATTERN = [
    { numeral: "1", role: "root" },
    { numeral: "3", role: "third" },
    { numeral: "5", role: "fifth" },
    { numeral: "9", role: "ninth" },
];

const CLOSE_POSITION = [
    { numeral: "1", role: "root" },
    { numeral: "3", role: "third" },
    { numeral: "5", role: "fifth" },
    { numeral: "9", role: "ninth" },
];
const DROP2_VOICING = [
    { numeral: "5", role: "fifth" },
    { numeral: "1", role: "root" },
    { numeral: "3", role: "third" },
    { numeral: "9", role: "ninth" },
];

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
                <DegreeBadge key={idx} {...note} />
            ))}
        </div>
    </div>
);

const MajorAdd9Drop2Checkpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to build Drop 2 voicings for Major add9 chords.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            What is a Major add9 Drop 2 Voicing?
        </h3>
        <p className="text-gray-700 dark:text-gray-300 mb-10">
            A Major add9 chord is a major triad with a 9th added on top — no 7th involved.
            To turn it into a Drop 2 voicing, take that close-position 4-note chord, find the
            2nd note from the top, and drop it down an octave so it becomes the new lowest
            note. Same notes, wider and more open sound.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            The add9 Chord Pattern
        </h3>
        <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 mb-4">
            <div className="flex flex-wrap items-start justify-center gap-4">
                {ADD9_PATTERN.map((degree, idx) => (
                    <DegreeBadge key={idx} {...degree} />
                ))}
            </div>
        </div>
        <div className="flex flex-wrap gap-4 mb-10 text-sm font-medium">
            {[
                { label: "Root", role: "root" },
                { label: "3rd", role: "third" },
                { label: "5th", role: "fifth" },
                { label: "9th", role: "ninth" },
            ].map(({ label, role }) => (
                <span key={role} className="flex items-center gap-2">
                    <span className={`w-3 h-3 rounded-full ${DEGREE_COLORS[role]}`} />
                    <span className="text-gray-600 dark:text-gray-300">{label}</span>
                </span>
            ))}
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Drop 2 Example — Cadd9
        </h3>
        <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 max-w-md">
            <VoicingRow label="Close Position (1 – 3 – 5 – 9)" notes={CLOSE_POSITION} />
            <DownArrow caption="Drop the 5th (2nd note from the top) down an octave" />
            <VoicingRow label="Drop 2 Voicing" notes={DROP2_VOICING} />
            <p className="text-sm text-gray-600 dark:text-gray-300 mt-6">
                Cadd9 close position: C – E – G – D. Drop 2 voicing: G – C – E – D.
            </p>
        </div>
    </div>
);

export default MajorAdd9Drop2Checkpoint;
