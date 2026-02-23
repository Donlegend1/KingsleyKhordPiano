import React, { useState, useEffect, useRef } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";

import {
    formatRelativeTime,
    capitaliseAndRemoveHyphen,
} from "../../utils/formatRelativeTime";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import { Heart } from "lucide-react";

const getInitials = (firstName, lastName) => {
    return `${firstName?.charAt(0) || ""}${
        lastName?.charAt(0) || ""
    }`.toUpperCase();
};

const ChatWithComments = ({
    post,
    fetchPosts,
    handleDeletePost,
    setSelectedPostToReply,
}) => {
    const [comments, setComments] = useState(post.replies || []);
    const [hovered, setHovered] = useState(false);
    const [showMenu, setShowMenu] = useState(false);

    useEffect(() => {
        setComments(post.replies || []);
    }, [post.replies]);
    // keep likes in sync when parent updates the post
    useEffect(() => {
        setLikes(post.likes || []);
        setLiked(post.liked_by_user || false);
    }, [post.likes, post.liked_by_user]);
    const [showPostActionId, setShowPostActionId] = useState(null);
    const showPostAction = showPostActionId === post.id;
    const { showMessage } = useFlashMessage();
    const [showCommentActionId, setShowCommentActionId] = useState(null);
    const [openMenuId, setOpenMenuId] = useState(null);

    const [commentsVisible, setCommentsVisible] = useState(false);
    const [likes, setLikes] = useState(post.likes || []);
    const [liked, setLiked] = useState(post.liked_by_user || false);
    const [replySectionFor, setReplySectionFor] = useState(null);

    const [showReplySection, setShowReplySection] = useState(false);
    const [newReply, setNewReply] = useState("");

    const author = post.user || {};
    const initials = getInitials(author.first_name, author.last_name);

    const openCommentSection = (post) => {
        setCommentsVisible(!commentsVisible);
        setSelectedPost(post);
    };

    const handleDeleteComment = async (commentId) => {
        try {
            await axios.delete(`/api/member/premium/messages/${commentId}`);
            // remove locally without full reload
            setComments((prev) => prev.filter((c) => c.id !== commentId));
            showMessage("Comment deleted", "success");
        } catch (error) {
            showMessage(
                error.response?.data?.message || "Failed to delete comment",
                "error",
            );
            console.error("Delete comment error:", error);
        }
    };

    const handleShowPostAction = (post) => {
        setShowPostActionId((prev) => (prev === post.id ? null : post.id));
        setSelectedPost(post);
    };

    const handleShowCommentAction = (commentId) => {
        setShowCommentActionId((prevId) =>
            prevId === commentId ? null : commentId,
        );
    };

    const handleToggleReply = (commentId) => {
        setReplySectionFor((prev) => (prev === commentId ? null : commentId));
    };

    const handlePostReply = async (e, commentId) => {
        e.preventDefault();
        if (!newReply.trim()) return;
        try {
            const res = await axios.post(
                `/api/member/comment/reply/${commentId}`,
                {
                    body: newReply,
                },
            );
            const created = res.data?.data || res.data;
            // update local comments: find parent comment and append reply
            setComments((prev) =>
                prev.map((c) =>
                    c.id === commentId
                        ? { ...c, replies: [...(c.replies || []), created] }
                        : c,
                ),
            );
            setNewReply("");
            setReplySectionFor(null);
            showMessage("Replied", "success");
        } catch (error) {
            showMessage(
                error.response?.data?.message || "Failed to post reply",
                "error",
            );
            console.error("Post reply error:", error);
        }
    };

    // toggle like for this post and notify parent to keep posts array in sync
    const toggleLike = async () => {
        try {
            const response = await axios.post(
                `/api/member/premium/messages/${post.id}/like`,
                { post_id: post.id },
            );

            fetchPosts();

            if (typeof onUpdatePost === "function") {
                onUpdatePost(post.id, {
                    likes: updatedLikes,
                    liked_by_user: nowLiked,
                });
            }
        } catch (error) {
            console.error("Error toggling like:", error);
        }
    };

    // Best-effort detection of current user to style own messages
    const currentUser = window.authUser || null;

    const isOwn = Boolean(
        post.user &&
        (post.user.is_current_user ||
            post.user.id === currentUser.id ||
            currentUser.email === "kingsleykhord@gmail.com"),
    );

    return (
        <div className="mb-4">
            {/* Message wrapper: avatar + bubble */}
            <div
                className={`flex items-start gap-3 ${
                    isOwn ? "justify-end" : "justify-start"
                }`}
            >
                {/* Avatar (left for others, right for own) */}
                {!isOwn &&
                    (author.passport ? (
                        <img
                            src={author.passport}
                            alt={author.first_name}
                            className="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm"
                        />
                    ) : (
                        <div className="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-700 flex items-center justify-center text-sm font-bold text-indigo-700 dark:text-indigo-100 ring-1 ring-white dark:ring-gray-800">
                            {initials}
                        </div>
                    ))}

                {/* Bubble */}
                <div
                    className="relative inline-block"
                    onMouseEnter={() => setHovered(true)}
                    onMouseLeave={() => {
                        setHovered(false);
                        setShowMenu(false);
                    }}
                >
                    <div
                        className={`inline-block px-4 py-3 rounded-2xl ${
                            isOwn
                                ? "bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg"
                                : "bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-100 dark:border-gray-700 shadow-sm"
                        }`}
                        style={{ wordBreak: "break-word" }}
                    >
                        <div className="flex items-center justify-between mb-1">
                            <div className="flex items-center gap-2">
                                <span
                                    className={`${
                                        isOwn
                                            ? "text-indigo-100"
                                            : "text-indigo-700 dark:text-indigo-300"
                                    } text-xs font-semibold`}
                                >
                                    {author.first_name} {author.last_name}
                                </span>
                                <span
                                 className={`${
                                        isOwn
                                            ? "text-indigo-100"
                                            : "text-gray-500 dark:text-indigo-300"
                                        } text-[11px] font-normal`}
                                    >
                                    {formatRelativeTime(post.created_at)}
                                </span>
                            </div>
                            <button
                                onClick={() => setShowMenu((prev) => !prev)}
                                className="ml-2 text-gray-400 hover:text-gray-600"
                            >
                                <i className="fa fa-chevron-down text-xs"></i>
                            </button>
                        </div>
                        <div className="text-sm whitespace-pre-wrap leading-relaxed">
                            {post.body}
                        </div>
                        {post.media && post.media.length > 0 && (
                            <div className="mt-2 flex flex-wrap gap-2">
                                {post.media.map((file, index) =>
                                    file.type === "image" ? (
                                        <img
                                            key={index}
                                            src={`/${file.file_path}`}
                                            alt={`media-${index}`}
                                            className="rounded-md max-h-40 object-contain"
                                        />
                                    ) : (
                                        <video
                                            key={index}
                                            src={`/${file.file_path}`}
                                            controls
                                            className="rounded-md max-h-40"
                                        />
                                    ),
                                )}
                            </div>
                        )}
                    </div>
                    {post.likes && post.likes.length > 0 && (
                        <div className="flex my-1">
                            <Heart fill="#f80d0d" size={16} />{" "}
                            <span className="text-xs">{post.likes.length}</span>
                        </div>
                    )}

                    {/* Actions row */}
                    <div className="mt-2 flex items-center justify-between text-sm relative">
                        {hovered && !showMenu && (
                            <div className="absolute -top-10 left-1/2 -translate-x-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full px-3 py-1 shadow-lg flex items-center gap-2 animate-fadeIn z-40">
                                {["❤️"].map((emoji, i) => (
                                    <button
                                        key={i}
                                        className="text-lg hover:scale-125 transition"
                                        onClick={toggleLike}
                                    >
                                        {emoji}
                                    </button>
                                ))}
                            </div>
                        )}

                        {showMenu && (
                            <div className="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50">
                                <div className="p-2 mx-2">
                                    <button
                                        onClick={(e) => {
                                            setSelectedPostToReply(post);
                                            setShowMenu(false);
                                        }}
                                    >
                                        Reply
                                    </button>
                                </div>
                                {isOwn && (
                                    <button
                                        className="w-full text-left px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900 text-red-600"
                                        onClick={() => {
                                            handleDeletePost(post.id);
                                            setShowMenu(false);
                                        }}
                                    >
                                        Delete
                                    </button>
                                )}
                            </div>
                        )}
                    </div>

                    {/* Replies */}

                    <div className="mt-3 space-y-3">
                        {comments?.length > 0 && (
                            <div className="space-y-4">
                                {comments.map((comment) => (
                                    <div
                                        key={comment.id}
                                        className="flex gap-3"
                                    >
                                        <div className="flex-1 space-y-2">
                                            {/* Quoted message */}
                                            <div className="flex gap-2 rounded-lg bg-gray-100 px-3 py-2 dark:bg-gray-800">
                                                <div className="w-1 rounded-full bg-indigo-500" />
                                                <div className="flex-1">
                                                    <p className="text-xs font-semibold text-indigo-600 dark:text-indigo-300">
                                                        {post.user?.first_name}
                                                    </p>
                                                    <p className="text-xs text-gray-600 line-clamp-2 dark:text-gray-400">
                                                        {post.body}
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Reply bubble */}
                                            <div className="relative rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
                                                <div className="flex items-center justify-between gap-2">
                                                    <p className="text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                                        {
                                                            comment.user
                                                                ?.first_name
                                                        }{" "}
                                                        {
                                                            comment.user
                                                                ?.last_name
                                                        }
                                                    </p>

                                                    <div className="flex items-center gap-2 relative">
                                                        <p className="text-[11px] text-gray-500">
                                                            {formatRelativeTime(
                                                                comment.created_at,
                                                            )}
                                                        </p>

                                                        {/* Down icon */}
                                                        <button
                                                            onClick={() =>
                                                                setOpenMenuId(
                                                                    openMenuId ===
                                                                        comment.id
                                                                        ? null
                                                                        : comment.id,
                                                                )
                                                            }
                                                            className="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                                        >
                                                            ▾
                                                        </button>

                                                        {/* Dropdown */}
                                                        {openMenuId ===
                                                            comment.id && (
                                                            <div className="absolute right-0 top-6 z-10 w-24 rounded-lg border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">
                                                                <button
                                                                    onClick={() =>
                                                                        handleDeleteComment(
                                                                            comment.id,
                                                                        )
                                                                    }
                                                                    className="w-full px-3 py-2 text-left text-xs text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                                >
                                                                    Delete
                                                                </button>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>

                                                <p className="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                                    {comment.body}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ChatWithComments;
