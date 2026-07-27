import React from "react";

const SECONDARY_DOMINANTS = [
    { label: "V7/ii", dominant: "A7", target: "Dm" },
    { label: "V7/iii", dominant: "B7", target: "Em" },
    { label: "V7/IV", dominant: "C7", target: "F" },
    { label: "V7/V", dominant: "D7", target: "G" },
    { label: "V7/vi", dominant: "E7", target: "Am" },
];

const RightArrow = () => (
    <svg className="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M5 12h14M13 6l6 6-6 6" />
    </svg>
);

const ChordChip = ({ chord, isSecondary }) => (
    <span
        className={`flex items-center justify-center px-4 h-11 rounded-full text-white font-bold text-sm flex-shrink-0 ${
            isSecondary ? "bg-rose-600" : "bg-purple-600"
        }`}
    >
        {chord}
    </span>
);

const PROGRESSION = [
    { chord: "C", numeral: "I" },
    { chord: "A7", numeral: "V7/ii", secondary: true },
    { chord: "Dm", numeral: "ii" },
    { chord: "D7", numeral: "V7/V", secondary: true },
    { chord: "G", numeral: "V" },
    { chord: "C", numeral: "I" },
];

const SecondaryDominantPassingCheckpoint = () => (
    <div>
        <p className="text-gray-500 dark:text-gray-400 mb-8">
            Learn how to use secondary dominants to smoothly connect diatonic chords.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            What is a Secondary Dominant?
        </h3>
        <p className="text-gray-700 dark:text-gray-300 mb-10">
            A secondary dominant is a dominant 7th chord borrowed from the key of the chord
            it's about to resolve to, rather than the song's main key. It's built a perfect
            5th above whichever diatonic chord it's targeting, and it briefly makes that
            chord feel like a "home" (I) before pulling back into the original key —
            adding tension and forward motion as a passing chord on the way there.
        </p>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Secondary Dominants in C Major
        </h3>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
            {SECONDARY_DOMINANTS.map((item) => (
                <div
                    key={item.label}
                    className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 text-center"
                >
                    <p className="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-3">
                        {item.label}
                    </p>
                    <div className="flex items-center justify-center gap-2">
                        <ChordChip chord={item.dominant} isSecondary />
                        <RightArrow />
                        <ChordChip chord={item.target} />
                    </div>
                </div>
            ))}
        </div>

        <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
            Using Secondary Dominants as Passing Chords
        </h3>
        <p className="text-gray-700 dark:text-gray-300 mb-6">
            Slip a secondary dominant in right before the diatonic chord it targets, and it
            acts as a passing chord that leads the ear smoothly toward it — even though it
            isn't part of the home key's chord palette.
        </p>
        <div className="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 mb-4 overflow-x-auto">
            <div className="flex items-center gap-3 w-max">
                {PROGRESSION.map((step, idx) => (
                    <React.Fragment key={idx}>
                        {idx > 0 && <RightArrow />}
                        <div className="flex flex-col items-center gap-2">
                            <ChordChip chord={step.chord} isSecondary={step.secondary} />
                            <span className="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                {step.numeral}
                            </span>
                        </div>
                    </React.Fragment>
                ))}
            </div>
        </div>
        <div className="flex flex-wrap gap-4 text-sm font-medium">
            {[
                { label: "Diatonic Chord", color: "bg-purple-600" },
                { label: "Secondary Dominant (Passing)", color: "bg-rose-600" },
            ].map(({ label, color }) => (
                <span key={label} className="flex items-center gap-2">
                    <span className={`w-3 h-3 rounded-full ${color}`} />
                    <span className="text-gray-600 dark:text-gray-300">{label}</span>
                </span>
            ))}
        </div>
    </div>
);

export default SecondaryDominantPassingCheckpoint;
