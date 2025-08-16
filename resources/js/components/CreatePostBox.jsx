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
        <div className="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm transition-all duration-300">
            <div className="flex justify-between items-start mb-4">
                <div className="flex items-center space-x-3 flex-1">
                    <img
                        src="/avatar1.jpg"
                        alt="Profile"
                        className="w-10 h-10 rounded-full object-cover"
                    />
                    <input
                        type="text"
                        placeholder="What's on your mind?"
                        onFocus={handleFocus}
                        value={postDetails.body}
                        onChange={(e) =>
                            setPostDetails({
                                ...postDetails,
                                body: e.target.value,
                            })
                        }
                        className={`flex-1 px-4 ${
                            expanded ? "py-5" : "py-2"
                        } bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md outline-none focus:ring-2 focus:ring-[#FFD736]`}
                    />
                </div>

                {expanded && (
                    <div className="flex items-center px-2 space-x-3 text-gray-500 dark:text-gray-300">
                        <i className="fas fa-eye"></i>
                        <button onClick={handleClose}>
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                )}
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

                    <hr className="border-gray-300 dark:border-gray-600 mb-3" />

                    <div className="flex justify-between items-center text-sm text-gray-600 dark:text-gray-300">
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

                            <button
                                type="button"
                                onClick={triggerImageSelect}
                                className="flex items-center space-x-1 hover:text-[#FFD736]"
                            >
                                <i className="fas fa-image text-green-500"></i>
                            </button>
                            <button
                                type="button"
                                onClick={triggerVideoSelect}
                                className="flex items-center space-x-1 hover:text-[#FFD736]"
                            >
                                <i className="fas fa-video text-red-500"></i>
                            </button>
                            <button className="flex items-center space-x-1 hover:text-[#FFD736]">
                                <i className="fas fa-poll text-blue-500"></i>
                            </button>
                        </div>
                        <div className="flex items-center gap-2">
                            <span className="text-gray-500 dark:text-gray-400 font-medium">
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
                            )}
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
