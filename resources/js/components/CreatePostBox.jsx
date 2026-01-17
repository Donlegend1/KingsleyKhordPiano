import React, { useEffect, useRef } from "react";
import { capitaliseAndRemoveHyphen } from "../utils/formatRelativeTime";

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

    const isAdmin = Boolean(
        window.authUser?.email == "kingsleykhord@gmail.com"
    );

    const handleClose = () => {
        setExpanded(false);
        setPostDetails({ body: "", category: "", subcategory: "" });
        setMediaFiles([]);
    };

    const handleMediaSelect = (e) => {
        const files = Array.from(e.target.files);
        setMediaFiles((prev) => [...prev, ...files]);
    };

    const handleRemoveMedia = (index) => {
        setMediaFiles((prev) => prev.filter((_, i) => i !== index));
    };

    const triggerImageSelect = () => {
        imageInputRef.current?.click();
    };

    const triggerVideoSelect = () => {
        videoInputRef.current?.click();
    };

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
        {
            category: { name: "ProgressReports", value: "progress_report" },
            subCategories: [
                { name: "Progress Reports", value: "progress_report" },
            ],
        },

        {
            category: { name: "ExclusiveFeed", value: "exclusive_feed" },
            subCategories: [
                { name: "Exclusive Feed", value: "exclusive_feed" },
            ],
        },

        {
            category: { name: "General", value: "general" },
            subCategories: [
                { name: "Activity Feed", value: "activity_feed" },
            ],
        },
    ];

    useEffect(() => {
        if (subcategory !== undefined) {
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
        else {
            setPostDetails((prev) => ({
                ...prev,
                subcategory: 'activity_feed',
                category: 'general',
            }));
        }
    }, [subcategory]);

    const submitPost = () => {
        const formData = new FormData();
        formData.append("body", postDetails.body);
        formData.append("category", postDetails.category);
        if (subcategory != null) {
            formData.append("subcategory", postDetails.subcategory);
        } else {
            formData.append("subcategory", postDetails.subcategory);
        }

        mediaFiles.forEach((file) => {
            formData.append("media[]", file);
        });

        handlePost(formData);
    };

    return (
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                    <svg
                        className="w-6 h-6 text-gray-500 dark:text-gray-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fillRule="evenodd"
                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clipRule="evenodd"
                        ></path>
                    </svg>
                </div>
                <input
                    type="text"
                    placeholder="Share what's on your mind..."
                    onFocus={handleFocus}
                    value={postDetails.body}
                    onChange={(e) =>
                        setPostDetails({
                            ...postDetails,
                            body: e.target.value,
                        })
                    }
                    className="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-full text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 border-0 focus:outline-none focus:ring-2 focus:ring-[#585757] focus:bg-white dark:focus:bg-gray-700 transition-all"
                />
            </div>

            {expanded && (
                <>
                    {/* Media Preview */}
                    {mediaFiles.length > 0 && (
                        <div className="flex flex-wrap gap-3 mb-3">
                            {mediaFiles.map((file, index) => (
                                <div key={index} className="relative w-auto">
                                    {file.type.startsWith("image/") ? (
                                        <img
                                            src={URL.createObjectURL(file)}
                                            alt="preview"
                                            className="w-full h-full object-cover rounded"
                                        />
                                    ) : (
                                        <video
                                            src={URL.createObjectURL(file)}
                                            className="w-full h-full object-cover rounded"
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
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="flex justify-between items-center text-sm text-gray-600 dark:text-gray-300 mt-10">
                        <div className="flex space-x-4">
                            {/* Hidden inputs for file selection */}
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

                            {isAdmin && (
                                <button
                                    type="button"
                                    onClick={triggerImageSelect}
                                    className="flex items-center space-x-1 hover:text-[#FFD736]"
                                >
                                    <i className="fas fa-image text-green-500"></i>
                                </button>
                            )}
                            {/* <button
                                type="button"
                                onClick={triggerVideoSelect}
                                className="flex items-center space-x-1 hover:text-[#FFD736]"
                            >
                                <i className="fas fa-video text-red-500"></i>
                            </button> */}
                            {/* <button className="flex items-center space-x-1 hover:text-[#FFD736]">
                                <i className="fas fa-poll text-blue-500"></i>
                            </button> */}
                        </div>
                        <div className="flex items-center gap-2">
                            {/* <span className="text-gray-500 dark:text-gray-400 font-medium">
                                Post in:
                            </span>
                            {subcategory !== null && (
                                <p>{capitaliseAndRemoveHyphen(subcategory)}</p>
                            )}{" "}
                            {subcategory === undefined && (
                                <select
                                    className="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm px-3 py-1 focus:outline-none focus:ring-2 focus:ring-[#FFD736]"
                                    value={postDetails.subcategory}
                                    onChange={(e) => {
                                        const selectedSub = e.target.value;
                                        const foundCategory =
                                            postCategories.find((cat) =>
                                                cat.subCategories.some(
                                                    (sub) =>
                                                        sub.value ===
                                                        selectedSub
                                                )
                                            );

                                        if (foundCategory) {
                                            setPostDetails({
                                                ...postDetails,
                                                category:
                                                    foundCategory.category
                                                        .value,
                                                subcategory: selectedSub,
                                            });
                                        }
                                    }}
                                >
                                    <option value="" disabled>
                                        Select
                                    </option>
                                    {postCategories.map((category, index) => (
                                        <React.Fragment key={index}>
                                            <option
                                                disabled
                                                className="font-bold text-gray-500"
                                            >
                                                ─ {category.category.name} ─
                                            </option>
                                            {category.subCategories.map(
                                                (sub) => (
                                                    <option
                                                        key={sub.value}
                                                        value={sub.value}
                                                    >
                                                        {sub.name}
                                                    </option>
                                                )
                                            )}
                                        </React.Fragment>
                                    ))}
                                </select>
                            )} */}
                            <button
                                disabled={posting}
                                className="bg-black dark:bg-gray-800 text-white px-4 py-1 rounded hover:brightness-90 transition text-sm"
                                onClick={submitPost}
                            >
                                {posting ? "Posting.." : "Post"}
                            </button>
                        </div>
                    </div>
                </>
            )}
        </div>
    );
};

export default CreatePostBox;
