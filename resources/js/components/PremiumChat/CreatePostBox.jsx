import React, { useEffect, useRef } from "react";
import { capitaliseAndRemoveHyphen } from "../../utils/formatRelativeTime";

const CreatePostBox = ({
    handlePost,
    postDetails,
    setPostDetails,
    posting,
    expanded,
    setExpanded,
    mediaFiles,
    setMediaFiles,
    subcategory,
}) => {
    const imageInputRef = useRef(null);
    const videoInputRef = useRef(null);

    const handleFocus = () => setExpanded(true);

    const handleClose = () => {
        setExpanded(false);
        setPostDetails({ body: "", category: "", subcategory: "" });
        setMediaFiles([]);
    };

    const handleMediaSelect = (e) => {
        const files = Array.from(e.target.files || []);
        if (files.length === 0) return;
        setMediaFiles((prev) => [...prev, ...files]);
    };

    const handleRemoveMedia = (index) => {
        setMediaFiles((prev) => prev.filter((_, i) => i !== index));
    };

    const triggerImageSelect = () => imageInputRef.current?.click();
    const triggerVideoSelect = () => videoInputRef.current?.click();

    const postCategories = [
        {
            category: { name: "GetStarted", value: "get_started" },
            subCategories: [
                { name: "Say Hello", value: "say_hello" },
                { name: "Ask Question", value: "ask_question" },
            ],
        },
        {
            category: { name: "Others", value: "others" },
            subCategories: [
                { name: "Post Progress", value: "post_progress" },
                { name: "Lessons", value: "lessons" },
            ],
        },
        {
            category: { name: "Forum", value: "forum" },
            subCategories: [
                { name: "Beginner", value: "beginner" },
                { name: "Intermediate", value: "intermediate" },
                { name: "Advance", value: "advance" },
            ],
        },
    ];

    useEffect(() => {
        if (subcategory !== undefined && subcategory !== null) {
            const formattedSubcategory = subcategory.replace(/-/g, "_");

            const foundCategory = postCategories.find((cat) =>
                cat.subCategories.some(
                    (sub) => sub.value === formattedSubcategory
                )
            );

            const categoryValue = foundCategory
                ? foundCategory.category.value
                : null;

            setPostDetails((prev) => ({
                ...prev,
                subcategory: formattedSubcategory,
                category: categoryValue,
            }));
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [subcategory]);

    const submitPost = () => {
        const formData = new FormData();
        formData.append("body", postDetails.body || "");

        mediaFiles.forEach((file) => formData.append("media[]", file));

        handlePost(formData);
    };

    return (
        <div className="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-2xl p-3 shadow-lg transition-all duration-200 mx-5 mt-5">
            <div className="flex items-start gap-3">
                <img
                    src="/avatar1.jpg"
                    alt="Profile"
                    className="w-10 h-10 rounded-full object-cover shadow"
                />

                <div className="flex-1">
                    <textarea
                        rows={expanded ? 3 : 1}
                        placeholder="Share something with the group..."
                        onFocus={handleFocus}
                        value={postDetails.body}
                        onChange={(e) =>
                            setPostDetails({
                                ...postDetails,
                                body: e.target.value,
                            })
                        }
                        className="w-full resize-none px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 outline-none focus:ring-2 focus:ring-[#FFD736] placeholder-gray-400"
                    />

                    {/* thumbnails when expanded */}
                    {expanded && mediaFiles.length > 0 && (
                        <div className="mt-3 flex flex-wrap gap-3">
                            {mediaFiles.map((file, index) => (
                                <div
                                    key={index}
                                    className="relative w-28 h-20 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700"
                                >
                                    {file.type &&
                                    file.type.startsWith("image/") ? (
                                        <img
                                            src={URL.createObjectURL(file)}
                                            alt="preview"
                                            className="w-full h-full object-cover"
                                        />
                                    ) : (
                                        <video
                                            src={URL.createObjectURL(file)}
                                            className="w-full h-full object-cover"
                                            controls
                                        />
                                    )}
                                    <button
                                        type="button"
                                        onClick={() => handleRemoveMedia(index)}
                                        className="absolute top-1 right-1 bg-black/60 text-white rounded-full px-1"
                                    >
                                        ✕
                                    </button>
                                    <div className="absolute left-1 bottom-1 text-[11px] bg-black/40 text-white px-2 rounded">
                                        {file.type &&
                                        file.type.startsWith("image/")
                                            ? "Image"
                                            : "Video"}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="mt-3 flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <input
                                type="file"
                                accept="image/*"
                                ref={imageInputRef}
                                onChange={handleMediaSelect}
                                className="hidden"
                            />
                            <input
                                type="file"
                                accept="video/*"
                                ref={videoInputRef}
                                onChange={handleMediaSelect}
                                className="hidden"
                            />

                            <button
                                type="button"
                                onClick={triggerImageSelect}
                                className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <i className="fas fa-image text-green-500"></i>
                                <span className="text-xs text-gray-600 dark:text-gray-300">
                                    Photo
                                </span>
                            </button>

                            {/* <button
                                type="button"
                                onClick={triggerVideoSelect}
                                className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <i className="fas fa-video text-red-500"></i>
                                <span className="text-xs text-gray-600 dark:text-gray-300">
                                    Video
                                </span>
                            </button> */}

                            {/* <button
                                type="button"
                                onClick={() => setExpanded((s) => !s)}
                                className="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <i className="fas fa-smile text-yellow-500"></i>
                                <span className="text-xs text-gray-600 dark:text-gray-300">
                                    Emoji
                                </span>
                            </button> */}
                        </div>

                        <div className="flex items-center gap-2">
                            {/* <button
                                onClick={handleClose}
                                className="text-xs text-gray-500"
                            >
                                Cancel
                            </button> */}
                            <button
                                disabled={posting}
                                onClick={submitPost}
                                className="px-4 py-2 rounded-lg bg-[#FFD736] text-black font-semibold shadow"
                            >
                                {posting ? "Posting..." : "Post"}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CreatePostBox;
