import React from "react";

const DEGREE_COLORS = {
    chordTone: "bg-purple-600",
    extension: "bg-rose-600",
};

const STACK_DEGREES = [
    { numeral: "1", role: "chordTone" },
    { numeral: "3", role: "chordTone" },
    { numeral: "5", role: "chordTone" },
    { numeral: "7", role: "chordTone" },
    { numeral: "9", role: "extension" },
    { numeral: "11", role: "extension" },
    { numeral: "13", role: "extension" },
];

const EXTENDED_CHORDS = [
    {
        name: "Add9",
        suffix: "add9",
        degrees: [
            { numeral: "1", role: "chordTone" },
            { numeral: "3", role: "chordTone" },
            { numeral: "5", role: "chordTone" },
            { numeral: "9", role: "extension" },
        ],
        example: { chord: "Cadd9", notes: "C – E – G – D" },
    },
    {
        name: "Major 9",
        suffix: "maj9",
        degrees: [
            { numeral: "1", role: "chordTone" },
            { numeral: "3", role: "chordTone" },
            { numeral: "5", role: "chordTone" },
            { numeral: "7", role: "chordTone" },
            { numeral: "9", role: "extension" },
        ],
        example: { chord: "Cmaj9", notes: "C – E – G – B – D" },
    },
    {
        name: "Minor 9",
        suffix: "m9",
        degrees: [
            { numeral: "1", role: "chordTone" },
            { numeral: "b3", role: "chordTone" },
            { numeral: "5", role: "chordTone" },
            { numeral: "b7", role: "chordTone" },
            { numeral: "9", role: "extension" },
        ],
        example: { chord: "Cm9", notes: "C – Eb – G – Bb – D" },
    },
    {
        name: "Dominant 9",
        suffix: "9",
        degrees: [
            { numeral: "1", role: "chordTone" },
            { numeral: "3", role: "chordTone" },
            { numeral: "5", role: "chordTone" },
            { numeral: "b7", role: "chordTone" },
            { numeral: "9", role: "extension" },
        ],
        example: { chord: "C9", notes: "C – E – G – Bb – D" },
    },
    {
        name: "Dominant 11",
        suffix: "11",
        degrees: [
            { numeral: "1", role: "chordTone" },
            { numeral: "3", role: "chordTone" },
            { numeral: "5", role: "chordTone" },
            { numeral: "b7", role: "chordTone" },
            { numeral: "9", role: "extension" },
            { numeral: "11", role: "extension" },
        ],
        example: { chord: "C11", notes: "C – E – G – Bb – D – F" },
    },
    {
        name: "Dominant 13",
        suffix: "13",
        degrees: [
            { numeral: "1", role: "chordTone" },
            { numeral: "3", role: "chordTone" },
            { numeral: "5", role: "chordTone" },
            { numeral: "b7", role: "chordTone" },
            { numeral: "9", role: "extension" },
            { numeral: "13", role: "extension" },
        ],
        example: { chord: "C13", notes: "C – E – G – Bb – D – A" },
    },
];

const DegreeBadge = ({ numeral, role, size = "w-9 h-9 text-xs" }) => (
    <span
        className={`flex items-center justify-center rounded-full text-white font-bold ${size} ${DEGREE_COLORS[role]}`}
    >
        {numeral}
    </span>
);

const ChordExtensionsCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to build 9th, 11th, and 13th chord extensions beyond the basic 7th chord.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            The Extension Stack
        </h3>
        <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 mb-4">
            <div className="flex flex-wrap items-start justify-center gap-4">
                {STACK_DEGREES.map((degree) => (
                    <DegreeBadge key={degree.numeral} {...degree} size="w-12 h-12 text-sm" />
                ))}
            </div>
        </div>
        <div className="flex flex-wrap gap-4 mb-10 text-sm font-medium">
            {[
                { label: "Chord Tone (1, 3, 5, 7)", role: "chordTone" },
                { label: "Extension (9, 11, 13)", role: "extension" },
            ].map(({ label, role }) => (
                <span key={role} className="flex items-center gap-2">
                    <span className={`w-3 h-3 rounded-full ${DEGREE_COLORS[role]}`} />
                    <span className="text-gray-600 dark:text-gray-300">{label}</span>
                </span>
            ))}
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Common Extended Chords
        </h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
            {EXTENDED_CHORDS.map((type) => (
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
                    <div className="flex flex-wrap items-center justify-center gap-1.5 mb-4">
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

        <p className="text-gray-700 dark:text-gray-300">
            Extensions keep stacking thirds past the 7th: the 9th is the 2nd scale degree an
            octave up, the 11th is the 4th an octave up, and the 13th is the 6th an octave up.
            Adding one to a 7th chord doesn't change its basic quality — it just colors it
            with a richer, jazzier sound.
        </p>
    </div>
);

export default ChordExtensionsCheckpoint;
