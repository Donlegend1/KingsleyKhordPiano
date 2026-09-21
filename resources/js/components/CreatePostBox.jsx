import React, { useEffect, useRef, useState } from "react";
import extractLinksFromText from "../utils/extractLinksFromText";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const IconButton = ({ label, onClick, disabled, children }) => (
    <div className="relative group">
        <button
            type="button"
            onClick={onClick}
            disabled={disabled}
            className={`w-9 h-9 rounded-lg flex items-center justify-center transition-colors ${
                disabled
                    ? "text-gray-300 dark:text-gray-600 cursor-not-allowed"
                    : "text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"
            }`}
        >
            {children}
        </button>
        <span className="pointer-events-none absolute -top-9 left-1/2 -translate-x-1/2 whitespace-nowrap bg-gray-900 text-white text-xs font-medium px-2.5 py-1.5 rounded-lg opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-150 shadow-lg z-10">
            {label}
            <span className="absolute left-1/2 -translate-x-1/2 top-full w-2 h-2 bg-gray-900 rotate-45 -mt-1"></span>
        </span>
    </div>
);

const EMOJI_LIST = [
    "😀", "😂", "😍", "🥳", "😢", "😮",
    "👍", "🙌", "👏", "🔥", "🎉", "❤️",
    "🎹", "🎵", "🎶", "✨", "💯", "🙏",
];

const TOPIC_OPTIONS = [
    { label: "Select Topic", value: "" },
    { label: "Say Hello", value: "say_hello" },
    { label: "Discussions", value: "ask_question" },
    { label: "Announcements", value: "announcement" },
    { label: "Suggestions", value: "suggestions" },
];

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
    fetchPosts,
    fixedSubcategory,
    hideTrigger,
    initialTopic,
    parentPostId

}) => {
    const imageInputRef = useRef(null);
    const attachmentInputRef = useRef(null);
    const textareaRef = useRef(null);
    const [title, setTitle] = useState("");
    const [topic, setTopic] = useState(initialTopic || "");
    const [showLinkInput, setShowLinkInput] = useState(false);
    const [linkUrl, setLinkUrl] = useState("");
    const [showEmojiPicker, setShowEmojiPicker] = useState(false);
    const { showMessage } = useFlashMessage();

    const openModal = () => setExpanded(true);
    const closeModal = () => setExpanded(false);

    const auth = window.authUser || {};
    const displayName =
        auth.display_name ||
        `${auth.first_name || ""} ${auth.last_name || ""}`.trim() ||
        auth.email ||
        "You";
    const fullName =
        `${auth.first_name || ""} ${auth.last_name || ""}`.trim() ||
        auth.display_name ||
        auth.email ||
        "You";
    const avatarInitial = (auth.display_name || auth.first_name || auth.email || "?")
        .trim()
        .charAt(0)
        .toUpperCase();
    const firstName = auth.first_name || auth.display_name || "there";
    const effectiveTopic = fixedSubcategory || topic;
    const detailsPlaceholder = {
        say_hello: `Introduce yourself, ${firstName}...`,
        ask_question: `What's on your mind? Ask your question, ${firstName}...`,
        suggestions: `Share your suggestion, ${firstName}...`,
    }[effectiveTopic] || `Insert your YouTube link or share what's on your mind, ${firstName}...`;
    const isAdmin = ["admin", "superadmin"].includes(auth.role);
    const topicOptions = TOPIC_OPTIONS.filter(
        (opt) => opt.value !== "announcement" || isAdmin,
    );
    const getDefaultTitle = (topicValue) =>
        ({
            say_hello: `New Member: ${fullName}`,
            workspace_showcase: "My Workspace",
            public_pledges: "My Pledge",
        })[topicValue] || "";

    /* ---------------- CATEGORY LOGIC ---------------- */
    useEffect(() => {
        setPostDetails((prev) => ({
            ...prev,
            subcategory: fixedSubcategory || topic,
            category: "general",
        }));
    }, [topic, fixedSubcategory]);

    // Resolve `topic` from `initialTopic` AND pre-fill the title in the same
    // effect, off the same freshly-resolved value — doing this as two
    // separate effects (one updating `topic`, another reacting to the
    // derived `effectiveTopic`) meant the title effect saw a stale
    // `effectiveTopic` for one extra render, since `effectiveTopic` only
    // updates a render after `topic` state actually changes.
    const autoTitleRef = useRef("");
    useEffect(() => {
        if (fixedSubcategory) return;
        const allowed = topicOptions.some((opt) => opt.value === initialTopic);
        const resolvedTopic = allowed ? initialTopic : "";
        setTopic(resolvedTopic);

        const defaultTitle = getDefaultTitle(resolvedTopic);
        setTitle((current) => {
            if (current.trim() !== "" && current !== autoTitleRef.current) {
                return current;
            }
            return defaultTitle;
        });
        autoTitleRef.current = defaultTitle;
    }, [initialTopic, fixedSubcategory, isAdmin]);

    // fixedSubcategory pages (e.g. Workspace Showcase) never go through the
    // effect above, so pre-fill their title once on mount here instead.
    useEffect(() => {
        if (!fixedSubcategory) return;
        const defaultTitle = getDefaultTitle(fixedSubcategory);
        setTitle((current) => (current.trim() === "" ? defaultTitle : current));
        autoTitleRef.current = defaultTitle;
    }, [fixedSubcategory]);

    /* ---------------- MEDIA HANDLING ---------------- */
    // `blockType` is fixed by which button the user clicked (image vs.
    // document), rather than guessed from the browser-reported MIME type —
    // a .mid file reports as "audio/midi", which the backend has no
    // handling for and would fail to save.
    const handleMediaSelect = (blockType) => (e) => {
        const files = Array.from(e.target.files);

        const mediaBlocks = files.map((file) => ({
            id: crypto.randomUUID(),
            type: blockType,
            file,
        }));

        setBlocks((prev) => [...prev, ...mediaBlocks]);
        e.target.value = null;
        setExpanded(true);
    };

    const removeBlock = (id) => {
        setBlocks((prev) => prev.filter((b) => b.id !== id));
    };

    const handleInsertLink = () => {
        setExpanded(true);
        setShowLinkInput(true);
    };

    const confirmLink = () => {
        const url = linkUrl.trim();
        if (url && textareaRef.current) {
            const current = textareaRef.current.value;
            textareaRef.current.value = current
                ? `${current}\n${url}`
                : url;
        }
        setLinkUrl("");
        setShowLinkInput(false);
        textareaRef.current?.focus();
    };

    const cancelLink = () => {
        setLinkUrl("");
        setShowLinkInput(false);
    };

    const insertEmoji = (emoji) => {
        if (textareaRef.current) {
            const el = textareaRef.current;
            const start = el.selectionStart ?? el.value.length;
            const end = el.selectionEnd ?? el.value.length;
            el.value = el.value.slice(0, start) + emoji + el.value.slice(end);
            const cursor = start + emoji.length;
            el.focus();
            el.setSelectionRange(cursor, cursor);
        }
        setShowEmojiPicker(false);
    };

    /* ---------------- SUBMIT ---------------- */

    const submitPost = async () => {
        if (!fixedSubcategory && !topic) {
            showMessage("Please select a topic before posting.", "error");
            return;
        }

        if (!title.trim()) {
            showMessage("Please add a title before posting.", "error");
            return;
        }

        const textValue = textareaRef.current?.value.trim();
        if (!textValue) {
            showMessage("Post content cannot be empty.", "error");
            return;
        }

        const formData = new FormData();
        formData.append("category", postDetails.category);
        formData.append("subcategory", postDetails.subcategory);
        if (parentPostId) {
            formData.append("parent_post_id", parentPostId);
        }
        if (title.trim()) {
            formData.append("title", title.trim());
        }

        let index = 0;

        if (textValue) {
            const textBlocks = extractLinksFromText(textValue);

            textBlocks.forEach((block) => {
                formData.append(`blocks[${index}][type]`, block.type);
                formData.append(`blocks[${index}][content]`, block.content);
                index++;
            });
        }

        // Media blocks (images/videos picked via the composer). The backend
        // resolves the uploaded file by looking up `hasFile($block.content)`,
        // so `content` must hold the exact FormData field name the file was
        // appended under — not the file itself.
        blocks.forEach((block) => {
            if (!block.file) return;
            const fieldName = `media_${index}`;
            formData.append(`blocks[${index}][type]`, block.type);
            formData.append(`blocks[${index}][content]`, fieldName);
            formData.append(fieldName, block.file);
            index++;
        });

        await handlePost(formData);

        setBlocks([]);
        const resetTitle = getDefaultTitle(fixedSubcategory || "");
        setTitle(resetTitle);
        autoTitleRef.current = resetTitle;
        setTopic("");
        setShowLinkInput(false);
        setLinkUrl("");
        setShowEmojiPicker(false);
        if (textareaRef.current) textareaRef.current.value = "";
        setPostDetails((prev) => ({ ...prev, body: "" }));
        closeModal();
    };

    const isLockedForViewer =
        !fixedSubcategory && initialTopic === "announcement" && !isAdmin;

    /* ---------------- UI ---------------- */
    return (
        <>
            {!hideTrigger && isLockedForViewer && (
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 flex items-center gap-3">
                    <div className="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 flex items-center justify-center flex-shrink-0">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                        Only admins can post in Announcements.
                    </p>
                </div>
            )}

            {!hideTrigger && !isLockedForViewer && (
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <div className="flex items-center gap-3">
                        {auth.passport ? (
                            <img
                                src={auth.passport}
                                alt="Profile"
                                className="w-10 h-10 rounded-full object-cover flex-shrink-0"
                            />
                        ) : (
                            <div className="w-10 h-10 rounded-full bg-gray-900 dark:bg-gray-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {avatarInitial}
                            </div>
                        )}

                        <input
                            type="text"
                            readOnly
                            onClick={openModal}
                            onFocus={openModal}
                            placeholder={`Insert your YouTube link or share what's on your mind, ${firstName}...`}
                            className="flex-1 px-4 py-4 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-100 text-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-500/40"
                        />
                    </div>

                    <div className="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div className="flex items-center gap-1">
                            <IconButton label="Attach photo" onClick={openModal}>
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.174C3.05 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.8-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.174 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </IconButton>
                            <IconButton label="Add a link" onClick={openModal}>
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                </svg>
                            </IconButton>
                            <IconButton label="Attach document" onClick={openModal}>
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                </svg>
                            </IconButton>
                        </div>
                    </div>
                </div>
            )}

            {expanded && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div className="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h2 className="text-base font-bold text-gray-900 dark:text-white">Create a post</h2>
                            <button
                                type="button"
                                onClick={closeModal}
                                className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div
                            className="p-6 max-h-[75vh] overflow-y-auto overflow-x-hidden [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
                        >
                            <div className="flex items-center gap-3 mb-4">
                                {auth.passport ? (
                                    <img
                                        src={auth.passport}
                                        alt="Profile"
                                        className="w-10 h-10 rounded-full object-cover flex-shrink-0"
                                    />
                                ) : (
                                    <div className="w-10 h-10 rounded-full bg-gray-900 dark:bg-gray-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                        {avatarInitial}
                                    </div>
                                )}
                                <div>
                                    <div className="text-sm font-semibold text-gray-900 dark:text-white">
                                        {displayName}
                                    </div>
                                    <span className="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full mt-0.5">
                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zM3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 014 9 15 15 0 01-4 9 15 15 0 01-4-9 15 15 0 014-9z" />
                                        </svg>
                                        Public
                                    </span>
                                </div>
                            </div>

                            <div className="flex items-center gap-2 mb-1.5">
                                <label className="text-xs font-semibold text-gray-500 dark:text-gray-400">Title</label>
                                <span className="text-[9px] font-bold uppercase tracking-wide text-red-500">Required</span>
                            </div>
                            <input
                                type="text"
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                                className="w-full text-lg font-medium placeholder-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 mb-3 bg-transparent text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-500/40"
                            />

                            <div className="flex items-center gap-2 mb-1.5">
                                <label className="text-xs font-semibold text-gray-500 dark:text-gray-400">Details</label>
                                <span className="text-[9px] font-bold uppercase tracking-wide text-red-500">Required</span>
                            </div>
                            <textarea
                                ref={textareaRef}
                                autoFocus
                                placeholder={detailsPlaceholder}
                                rows={7}
                                className="w-full resize-none border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2.5 bg-transparent text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-500/40"
                            />

                            {blocks.length > 0 && (
                                <div className="mt-3 space-y-3">
                                    {blocks.map((block) => {
                                        if (block.type === "image") {
                                            return (
                                                <div key={block.id} className="relative">
                                                    <img
                                                        src={URL.createObjectURL(block.file)}
                                                        className="rounded-lg w-full max-h-64 object-cover"
                                                    />
                                                    <button
                                                        onClick={() => removeBlock(block.id)}
                                                        className="absolute top-2 right-2 bg-black/60 text-white w-7 h-7 rounded-full flex items-center justify-center"
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
                                                        className="rounded-lg w-full max-h-64"
                                                    />
                                                    <button
                                                        onClick={() => removeBlock(block.id)}
                                                        className="absolute top-2 right-2 bg-black/60 text-white w-7 h-7 rounded-full flex items-center justify-center"
                                                    >
                                                        ✕
                                                    </button>
                                                </div>
                                            );
                                        }

                                        return (
                                            <div key={block.id} className="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-2">
                                                <span className="text-sm text-gray-600 dark:text-gray-300 truncate">
                                                    {block.file.name}
                                                </span>
                                                <button
                                                    onClick={() => removeBlock(block.id)}
                                                    className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 ml-2 flex-shrink-0"
                                                >
                                                    ✕
                                                </button>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}

                            {showLinkInput && (
                                <div className="mt-3 bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-xl p-3">
                                    <label className="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                        </svg>
                                        Paste a link to share
                                    </label>
                                    <div className="flex items-center gap-2">
                                        <input
                                            type="url"
                                            autoFocus
                                            value={linkUrl}
                                            onChange={(e) => setLinkUrl(e.target.value)}
                                            onKeyDown={(e) => {
                                                if (e.key === "Enter") {
                                                    e.preventDefault();
                                                    confirmLink();
                                                }
                                                if (e.key === "Escape") cancelLink();
                                            }}
                                            placeholder="https://example.com"
                                            className="flex-1 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-500/40"
                                        />
                                        <button
                                            type="button"
                                            onClick={cancelLink}
                                            className="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-3 py-2"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="button"
                                            disabled={!linkUrl.trim()}
                                            onClick={confirmLink}
                                            className="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
                                        >
                                            Add
                                        </button>
                                    </div>
                                </div>
                            )}

                            <div className="relative flex items-center justify-end mt-3">
                                <IconButton label="Add emoji" onClick={() => setShowEmojiPicker((prev) => !prev)}>
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9.75h.008v.008H9V9.75zm6 0h.008v.008H15V9.75z" />
                                    </svg>
                                </IconButton>

                                {showEmojiPicker && (
                                    <div className="absolute right-0 bottom-11 z-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg p-3 grid grid-cols-6 gap-1 w-64">
                                        {EMOJI_LIST.map((emoji) => (
                                            <button
                                                key={emoji}
                                                type="button"
                                                onClick={() => insertEmoji(emoji)}
                                                className="w-9 h-9 flex items-center justify-center text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            >
                                                {emoji}
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>

                        <div className="flex items-center justify-between px-5 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                            <div className="flex items-center gap-1">
                                <input
                                    type="file"
                                    accept="image/*"
                                    ref={imageInputRef}
                                    onChange={handleMediaSelect("image")}
                                    hidden
                                />
                                <input
                                    type="file"
                                    accept=".pdf,.doc,.docx,.txt,.mid,.midi,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain,audio/midi,audio/x-midi"
                                    ref={attachmentInputRef}
                                    onChange={handleMediaSelect("file")}
                                    hidden
                                />
                                <IconButton label="Attach photo" onClick={() => imageInputRef.current?.click()}>
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.174C3.05 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.8-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.174 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                    </svg>
                                </IconButton>
                                <IconButton label="Add a link" onClick={handleInsertLink}>
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                    </svg>
                                </IconButton>
                                <IconButton label="Attach document" onClick={() => attachmentInputRef.current?.click()}>
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                    </svg>
                                </IconButton>
                            </div>

                            <div className="flex items-center gap-2">
                                {!fixedSubcategory && (
                                    <div className="relative">
                                        <select
                                            value={topic}
                                            onChange={(e) => setTopic(e.target.value)}
                                            className="appearance-none text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg pl-3 pr-9 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-500/40"
                                        >
                                            {topicOptions.map((opt) => (
                                                <option key={opt.label} value={opt.value}>
                                                    {opt.label}
                                                </option>
                                            ))}
                                        </select>
                                        <svg
                                            className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            strokeWidth="2"
                                            viewBox="0 0 24 24"
                                        >
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                )}

                                <button
                                    type="button"
                                    disabled={posting}
                                    onClick={submitPost}
                                    className="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors"
                                >
                                    {posting ? "Posting..." : "Post"}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default CreatePostBox;
