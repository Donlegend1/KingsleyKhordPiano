import React, { useEffect, useRef } from "react";
import extractLinksFromText from "../utils/extractLinksFromText";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const CreatePostBox = ({
    handlePost,
    postDetails,
    setPostDetails,
    posting,
    expanded,
    setExpanded,
    subcategory,
    blocks,
    setBlocks,
    fetchPosts

}) => {
    const imageInputRef = useRef(null);
    const videoInputRef = useRef(null);
    const textareaRef = useRef(null);
    const { showMessage } = useFlashMessage();

    const handleFocus = () => setExpanded(true);

    const isAdmin = window.authUser?.email === "kingsleykhord@gmail.com";
    const auth = window.authUser || {};

    /* ---------------- CATEGORY LOGIC ---------------- */
    useEffect(() => {
        if (subcategory) {
            const formatted = subcategory.replace(/-/g, "_");
            setPostDetails((prev) => ({
                ...prev,
                subcategory: formatted,
                category: prev.category ?? "general",
            }));
        } else {
            setPostDetails((prev) => ({
                ...prev,
                subcategory: "activity_feed",
                category: "general",
            }));
        }
    }, [subcategory]);

    /* ---------------- MEDIA HANDLING ---------------- */
    const handleMediaSelect = (e) => {
        const files = Array.from(e.target.files);

        const mediaBlocks = files.map((file) => ({
            id: crypto.randomUUID(),
            type: file.type.startsWith("image/")
                ? "image"
                : file.type.startsWith("video/")
                  ? "video"
                  : "file",
            file,
        }));

        setBlocks((prev) => [...prev, ...mediaBlocks]);
        e.target.value = null;
    };

    const removeBlock = (id) => {
        setBlocks((prev) => prev.filter((b) => b.id !== id));
    };

    /* ---------------- SUBMIT ---------------- */

    const submitPost = async () => {
        const textValue = textareaRef.current?.value.trim();
        if (!textValue) {
            showMessage("Post content cannot be empty.", "error");
            return;
        }

        const formData = new FormData();
        formData.append("category", postDetails.category);
        formData.append("subcategory", postDetails.subcategory);

        let index = 0;

        if (textValue) {
            const textBlocks = extractLinksFromText(textValue);
            setBlocks(textBlocks);

            textBlocks.forEach((block) => {
                formData.append(`blocks[${index}][type]`, block.type);
                formData.append(`blocks[${index}][content]`, block.content);
                index++;
            });
        }

        // blocks.forEach((block) => {
        //     formData.append(`blocks[${index}][type]`, block.type);
        //     if (block.file) {
        //         formData.append(`blocks[${index}][file]`, block.file);
        //     } else {
        //         formData.append(`blocks[${index}][content]`, block.content);
        //     }
        //     index++;
        // });

        handlePost(formData);

        setBlocks([]);
        if (textareaRef.current) textareaRef.current.value = "";
        setPostDetails((prev) => ({ ...prev, body: "" }));
       await fetchPosts();
    };

    /* ---------------- UI ---------------- */
    return (
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <div className="flex items-start gap-4">
                <img
                    src={auth.passport ? auth.passport : "/default-avatar.png"}
                    alt="Profile"
                    className="w-12 h-12 rounded-full object-cover"
                />
                <textarea
                    ref={textareaRef}
                    placeholder="Share what's on your mind..."
                    onFocus={handleFocus}
                    className="flex-1 px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-700 resize-none text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#585757]"
                    rows={3}
                />
            </div>

            <div className="mt-4 space-y-3">
                {blocks.map((block) => {
                    if (block.type === "image") {
                        return (
                            <div key={block.id} className="relative">
                                <img
                                    src={URL.createObjectURL(block.file)}
                                    className="rounded-lg w-full"
                                />
                                <button
                                    onClick={() => removeBlock(block.id)}
                                    className="absolute top-2 right-2 bg-black/60 text-white px-2 rounded"
                                >
                                    ✕
                                </button>
                            </div>
                        );
                    }

                    if (block.type === "video") {
                        return (
                            <div key={block.id} className="relative">
                                <video
                                    src={URL.createObjectURL(block.file)}
                                    controls
                                    className="rounded-lg w-full"
                                />
                                <button
                                    onClick={() => removeBlock(block.id)}
                                    className="absolute top-2 right-2 bg-black/60 text-white px-2 rounded"
                                >
                                    ✕
                                </button>
                            </div>
                        );
                    }

                    return null;
                })}
            </div>

            {expanded && (
                <div className="flex justify-between items-center mt-4">
                    <div className="flex gap-3">
                        <input
                            type="file"
                            accept="image/*"
                            ref={imageInputRef}
                            onChange={handleMediaSelect}
                            hidden
                        />
                        <input
                            type="file"
                            accept="video/*"
                            ref={videoInputRef}
                            onChange={handleMediaSelect}
                            hidden
                        />
                        {isAdmin && (
                            <button
                                onClick={() => imageInputRef.current?.click()}
                            >
                                🖼
                            </button>
                        )}
                    </div>

                    <button
                        disabled={posting}
                        onClick={submitPost}
                        className="bg-black text-white px-4 py-1 rounded"
                    >
                        {posting ? "Posting..." : "Post"}
                    </button>
                </div>
            )}
        </div>
    );
};

export default CreatePostBox;
