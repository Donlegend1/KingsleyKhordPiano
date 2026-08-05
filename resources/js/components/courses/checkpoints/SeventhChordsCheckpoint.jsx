import React from "react";

const DEGREE_COLORS = {
    root: "bg-purple-600",
    third: "bg-blue-600",
    fifth: "bg-green-600",
    seventh: "bg-amber-600",
};

const CHORD_TYPES = [
    {
        name: "Major 7",
        suffix: "maj7",
        degrees: [
            { numeral: "1", role: "root" },
            { numeral: "3", role: "third" },
            { numeral: "5", role: "fifth" },
            { numeral: "7", role: "seventh" },
        ],
        example: { chord: "Cmaj7", notes: "C – E – G – B" },
    },
    {
        name: "Minor 7",
        suffix: "m7",
        degrees: [
            { numeral: "1", role: "root" },
            { numeral: "b3", role: "third" },
            { numeral: "5", role: "fifth" },
            { numeral: "b7", role: "seventh" },
        ],
        example: { chord: "Cm7", notes: "C – Eb – G – Bb" },
    },
    {
        name: "Dominant 7",
        suffix: "7",
        degrees: [
            { numeral: "1", role: "root" },
            { numeral: "3", role: "third" },
            { numeral: "5", role: "fifth" },
            { numeral: "b7", role: "seventh" },
        ],
        example: { chord: "C7", notes: "C – E – G – Bb" },
    },
    {
        name: "Minor 7b5",
        suffix: "m7b5",
        degrees: [
            { numeral: "1", role: "root" },
            { numeral: "b3", role: "third" },
            { numeral: "b5", role: "fifth" },
            { numeral: "b7", role: "seventh" },
        ],
        example: { chord: "Cm7b5", notes: "C – Eb – Gb – Bb" },
    },
    {
        name: "Diminished 7",
        suffix: "°7",
        degrees: [
            { numeral: "1", role: "root" },
            { numeral: "b3", role: "third" },
            { numeral: "b5", role: "fifth" },
            { numeral: "bb7", role: "seventh" },
        ],
        example: { chord: "C°7", notes: "C – Eb – Gb – A" },
    },
];

const DegreeBadge = ({ numeral, role }) => (
    <span
        className={`flex items-center justify-center w-9 h-9 rounded-full text-white font-bold text-xs ${DEGREE_COLORS[role]}`}
    >
        {numeral}
    </span>
);

const SeventhChordsCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to build Major 7, Minor 7, Dominant 7, Minor 7b5, and Diminished 7 chords.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            7th Chord Types
        </h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            {CHORD_TYPES.map((type) => (
                <div
                    key={type.name}
                    className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 text-center"
                >
                    <h4 className="font-bold text-indigo-700 dark:text-indigo-300 mb-1">
                        {type.name}
                    </h4>
                    <p className="text-xs text-gray-400 dark:text-gray-500 mb-4">
                        {type.suffix}
                    </p>
                    <div className="flex items-center justify-center gap-1.5 mb-4">
                        {type.degrees.map((degree, idx) => (
                            <DegreeBadge key={idx} {...degree} />
                        ))}
                    </div>
                    <p className="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {type.example.chord}
                    </p>
                    <p className="text-xs text-gray-500 dark:text-gray-400">
                        {type.example.notes}
                    </p>
                </div>
            ))}
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

        <p className="text-gray-700 dark:text-gray-300">
            Every 7th chord is built the same way: stack a root, a 3rd, a 5th, and a 7th in
            thirds above the root. Whether each of those notes is raised or lowered a half
            step is what turns the same 4-note shape into a Major 7, Minor 7, Dominant 7,
            Minor 7b5, or Diminished 7 chord.
        </p>
    </div>
);

export default SeventhChordsCheckpoint;
