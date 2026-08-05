import React from "react";
import { Target } from "lucide-react";
import MajorScaleCheckpoint from "./MajorScaleCheckpoint";
import MajorTriadsCheckpoint from "./MajorTriadsCheckpoint";
import MinorTriadsCheckpoint from "./MinorTriadsCheckpoint";
import RelativePitchCheckpoint from "./RelativePitchCheckpoint";
import DiatonicTriadsCheckpoint from "./DiatonicTriadsCheckpoint";
import SeventhChordsCheckpoint from "./SeventhChordsCheckpoint";
import ChordExtensionsCheckpoint from "./ChordExtensionsCheckpoint";
import Drop2ChordsCheckpoint from "./Drop2ChordsCheckpoint";
import MelodicMinorScaleCheckpoint from "./MelodicMinorScaleCheckpoint";
import MajorAdd9Drop2Checkpoint from "./MajorAdd9Drop2Checkpoint";
import SecondaryDominantPassingCheckpoint from "./SecondaryDominantPassingCheckpoint";
import SeventhChordInversionsCheckpoint from "./SeventhChordInversionsCheckpoint";

// Registry of premade checkpoint content components, keyed by
// `checkpoint_key` (see app/Support/Checkpoints/CheckpointCatalog.php on the
// backend). Adding a new checkpoint template = one new component here + one
// new catalog entry in PHP.
const CHECKPOINT_COMPONENTS = {
    "major-scale": MajorScaleCheckpoint,
    "major-triads": MajorTriadsCheckpoint,
    "minor-triads": MinorTriadsCheckpoint,
    "relative-pitch-ear-training": RelativePitchCheckpoint,
    "diatonic-triads": DiatonicTriadsCheckpoint,
    "7th-chords": SeventhChordsCheckpoint,
    "chord-extensions": ChordExtensionsCheckpoint,
    "drop-2-chords": Drop2ChordsCheckpoint,
    "melodic-minor-scale": MelodicMinorScaleCheckpoint,
    "major-add9-drop2": MajorAdd9Drop2Checkpoint,
    "secondary-dominant-passing": SecondaryDominantPassingCheckpoint,
    "7th-chord-inversions": SeventhChordInversionsCheckpoint,
};

export const checkpointHasCta = (checkpoint) =>
    Boolean(checkpoint?.redirect_url || checkpoint?.linked_course);

// Rendered by the parent page as a fixed footer (outside the scrolling
// content area) so the CTA stays visible without the user needing to
// scroll all the way down through a long checkpoint page.
export const CheckpointCta = ({ checkpoint, onSelectCourse }) => {
    if (!checkpointHasCta(checkpoint)) return null;

    const content = (
        <>
            <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z" />
            </svg>
            Watch Lesson
        </>
    );

    const className =
        "w-full flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3.5 transition";

    if (checkpoint.redirect_url) {
        return (
            <a href={checkpoint.redirect_url} className={className}>
                {content}
            </a>
        );
    }

    return (
        <button
            type="button"
            onClick={() => onSelectCourse(checkpoint.linked_course)}
            className={className}
        >
            {content}
        </button>
    );
};

const CheckpointDetails = ({ checkpoint }) => {
    const Body = CHECKPOINT_COMPONENTS[checkpoint.checkpoint_key];

    return (
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6 w-full max-w-7xl mx-auto">
            <div className="flex items-start gap-4 mb-6">
                <div className="flex items-center justify-center w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-900/40 shadow-sm flex-shrink-0">
                    <Target className="w-6 h-6 text-indigo-600 dark:text-indigo-300" strokeWidth={2} />
                </div>
                <div>
                    <span className="text-xs font-bold tracking-wide text-indigo-600 dark:text-indigo-300 uppercase">
                        {checkpoint.label || "Practice Checkpoint"}
                    </span>
                    <h2 className="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-1">
                        {checkpoint.title}
                    </h2>
                </div>
            </div>

            <hr className="border-gray-100 dark:border-gray-700 mb-6" />

            {Body ? (
                <Body />
            ) : (
                <p className="text-gray-500 dark:text-gray-400 italic">
                    Content for this checkpoint is coming soon.
                </p>
            )}
        </div>
    );
};

export default CheckpointDetails;
