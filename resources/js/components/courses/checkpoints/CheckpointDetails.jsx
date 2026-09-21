import React from "react";
import { Target, Download } from "lucide-react";

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
        "w-full flex items-center justify-center gap-2 rounded-xl bg-[#1447A6] hover:bg-[#0F3A8A] text-white font-semibold py-3.5 transition";

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
    return (
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6 w-full max-w-7xl mx-auto">
            <div className="flex items-start gap-4 mb-6">
                <div className="flex items-center justify-center w-12 h-12 rounded-full bg-[#1447A6]/10 dark:bg-[#1447A6]/20 shadow-sm flex-shrink-0">
                    <Target className="w-6 h-6 text-[#1447A6] dark:text-[#1447A6]" strokeWidth={2} />
                </div>
                <div>
                    <span className="text-xs font-bold tracking-wide text-[#1447A6] dark:text-[#1447A6] uppercase">
                        {checkpoint.label || "Practice Checkpoint"}
                    </span>
                    <h2 className="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mt-1">
                        {checkpoint.title}
                    </h2>
                </div>
            </div>

            <hr className="border-gray-100 dark:border-gray-700 mb-6" />

            {checkpoint.video_embed_url && (
                <div className="mb-8 rounded-xl overflow-hidden bg-black aspect-video">
                    <iframe
                        className="w-full h-full"
                        src={checkpoint.video_embed_url}
                        title={checkpoint.title}
                        frameBorder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                    ></iframe>
                </div>
            )}

            {checkpoint.overview && (
                <div className="mb-8">
                    <h3 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                        Course Overview
                    </h3>
                    <div
                        className="ql-editor !p-0 text-gray-600 dark:text-gray-300 leading-relaxed"
                        dangerouslySetInnerHTML={{ __html: checkpoint.overview }}
                    />
                </div>
            )}

            {checkpoint.downloads && checkpoint.downloads.length > 0 && (
                <div className="mb-8 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div className="bg-red-600 px-4 py-2.5 sm:px-5 sm:py-3">
                        <h3 className="text-white text-xs sm:text-sm font-bold tracking-wide uppercase whitespace-nowrap">
                            Downloads for this Course
                        </h3>
                    </div>
                    <div className="bg-gray-50 dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        {checkpoint.downloads.map((download) => (
                            <div key={download.id} className="px-4 py-3 sm:px-5 sm:py-4 flex items-center gap-3 sm:gap-4">
                                <div className="w-12 h-9 sm:w-16 sm:h-10 rounded bg-gray-400 dark:bg-gray-600 flex items-center justify-center text-white text-[10px] sm:text-xs font-bold flex-shrink-0">
                                    PDF
                                </div>
                                <span className="flex-1 min-w-0 text-sm sm:text-base text-gray-800 dark:text-gray-100 font-semibold truncate">
                                    {download.title}
                                </span>
                                <a
                                    href={download.file_url}
                                    download={download.title}
                                    title="Download"
                                    className="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-lg text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition flex-shrink-0"
                                >
                                    <Download className="w-4 h-4" />
                                </a>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {!checkpoint.video_embed_url && !checkpoint.overview && (!checkpoint.downloads || checkpoint.downloads.length === 0) && (
                <p className="text-gray-500 dark:text-gray-400 italic">
                    Content for this checkpoint is coming soon.
                </p>
            )}
        </div>
    );
};

export default CheckpointDetails;
