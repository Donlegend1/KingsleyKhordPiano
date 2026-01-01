import React, { useState, useRef, useEffect } from "react";
import EmojiPicker from "emoji-picker-react";
import { MessageCirclePlusIcon, Smile } from "lucide-react";

const CreatePostBox = ({ handlePost, postDetails, setPostDetails, posting }) => {
    const [showEmojiPicker, setShowEmojiPicker] = useState(false);
    const inputRef = useRef(null);

    // Auto-resize textarea
    const handleInput = (e) => {
        const el = e.target;
        el.style.height = "auto"; // reset height
        el.style.height = el.scrollHeight + "px"; // set new height
        setPostDetails({ ...postDetails, body: el.value });
    };

    const handleEmojiClick = (emojiObject) => {
        const el = inputRef.current;
        const cursorPos = el.selectionStart;
        const text = postDetails.body || "";
        const newText =
            text.slice(0, cursorPos) + emojiObject.emoji + text.slice(cursorPos);
        setPostDetails({ ...postDetails, body: newText });

        // Move cursor after emoji
        setTimeout(() => {
            el.selectionStart = cursorPos + emojiObject.emoji.length;
            el.selectionEnd = cursorPos + emojiObject.emoji.length;
            el.focus();
        }, 0);

        // Resize after adding emoji
        el.style.height = "auto";
        el.style.height = el.scrollHeight + "px";
    };

    const submitPost = () => {
        if (!postDetails.body) return;
        handlePost({ body: postDetails.body });
        setPostDetails({ body: "" });
        setShowEmojiPicker(false);
        if (inputRef.current) inputRef.current.style.height = "auto"; // reset height
    };

    return (
        <div className="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full p-2 flex items-center gap-2 shadow-lg mx-5 mt-5 relative">
            {/* Emoji Button */}
            <button
                type="button"
                onClick={() => setShowEmojiPicker((prev) => !prev)}
                className="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                <Smile className="w-5 h-5 text-yellow-500" />
            </button>

            {/* Emoji Picker */}
            {showEmojiPicker && (
                <div className="absolute bottom-12 left-2 z-50">
                    <EmojiPicker
                        onEmojiClick={handleEmojiClick}
                        height={300}
                        width={280}
                    />
                </div>
            )}

            {/* Auto-resizing textarea */}
            <textarea
                ref={inputRef}
                rows={1}
                placeholder="Type a message..."
                value={postDetails.body || ""}
                onInput={handleInput}
                className="flex-1 resize-none px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 outline-none placeholder-gray-400 max-h-40 overflow-y-auto"
            />

            {/* Send Button */}
            <button
                disabled={posting}
                onClick={submitPost}
                className="p-3 bg-[#FFD736] rounded-full text-black shadow flex items-center justify-center"
            >
                <MessageCirclePlusIcon className="w-5 h-5" />
            </button>
        </div>
    );
};

export default CreatePostBox;
