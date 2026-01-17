import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";

import {
    formatRelativeTime,
    capitaliseAndRemoveHyphen,
} from "../utils/formatRelativeTime";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";
import { Trash2, Bookmark, Heart, MessageCircle } from "lucide-react";
import AuthorNameWithVerification from "./User/AuthorNameWithVerification";

const getInitials = (firstName, lastName) => {
    return `${firstName?.charAt(0) || ""}${
        lastName?.charAt(0) || ""
    }`.toUpperCase();
};

// render plain text but make http/https links clickable
const renderTextWithLinks = (text) => {
    if (!text) return null;
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    const parts = text.split(urlRegex);
    return parts.map((part, i) => {
        if (part.match(/^https?:\/\//)) {
            return (
                <a
                    key={i}
                    href={part}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-indigo-600 underline"
                >
                    {part}
                </a>
            );
        }
        return <span key={i}>{part}</span>;
    });
};

const PostWithComments = ({
    post,
    newComment,
    setNewComment,
    handleCommentSubmit,
    setSelectedPost,
    handleDeletePost,
    commenting,
    onUpdatePost,
}) => {
    const [comments, setComments] = useState(post.comments || []);
    const [showPostAction, setShowPostAction] = useState(false);
    const [showCommentAction, setShowCommentAction] = useState(false);
    const { showMessage } = useFlashMessage();
    const [showCommentActionId, setShowCommentActionId] = useState(null);

    const [commentsVisible, setCommentsVisible] = useState(false);
    const [likes, setLikes] = useState(post.likes || []);
    const [liked, setLiked] = useState(post.liked_by_user || false);
    const [replySectionFor, setReplySectionFor] = useState(null);

    const [showReplySection, setShowReplySection] = useState(false);
    const [newReply, setNewReply] = useState("");

    const author = post.user || {};
    const initials = getInitials(author.first_name, author.last_name);

    const toggleLike = async () => {
        try {
            const response = await axios.post("/api/member/like", {
                post_id: post.id,
            });
            const updatedLikes =
                response.data?.likes || response.data?.data || [];

            setLikes(updatedLikes);
            const nowLiked = updatedLikes.some(
                (like) => like.user?.id === window.authUser?.id
            );
            setLiked(nowLiked);

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

    const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

    const handleEditComment = (comment) => {
        setNewComment(comment.body);
        setSelectedPost(post); // or use a dedicated `selectedComment`
        // Open comment box in edit mode
    };

    const handleDeleteComment = async (commentId) => {
        try {
            await axios.delete(`/api/member/comments/${commentId}`);

            // remove locally
            setComments((prev) => prev.filter((c) => c.id !== commentId));
            showMessage("Comment deleted", "success");

            // notify parent to update its posts comments array
            if (typeof onUpdatePost === "function") {
                onUpdatePost(post.id, {
                    comments: (comments || []).filter(
                        (c) => c.id !== commentId
                    ),
                });
            }
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
    };

    const handleShowPostAction = (post) => {
        setShowPostAction(!showPostAction);
        setSelectedPost(post);
    };

    const handleShowCommentAction = (commentId) => {
        setShowCommentActionId((prevId) =>
            prevId === commentId ? null : commentId
        );
    };

    const handleToggleReply = (commentId) => {
        setReplySectionFor((prev) => (prev === commentId ? null : commentId));
    };

    const handlePostReply = async (e, commentId) => {
        try {
            const res = await axios.post(
                `/api/member/comment/reply/${commentId}`,
                {
                    body: newReply,
                }
            );
            const created = res.data?.data || res.data;

            // append locally
            setComments((prev) =>
                prev.map((c) =>
                    c.id === commentId
                        ? { ...c, replies: [...(c.replies || []), created] }
                        : c
                )
            );

            // notify parent
            if (typeof onUpdatePost === "function") {
                onUpdatePost(post.id, {
                    comments: (comments || []).map((c) =>
                        c.id === commentId
                            ? { ...c, replies: [...(c.replies || []), created] }
                            : c
                    ),
                });
            }

            setNewReply("");
            setReplySectionFor(null);
            showMessage("Replied", "success");
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
    };

    // keep comments in sync when parent updates
    useEffect(() => {
        setComments(post.comments || []);
    }, [post.comments]);

    const toggleBookmark = async (id) => {
        try {
            await axios.post(
                `/member/bookmark/toggle`,
                {
                    bookmarkable_id: id,
                    bookmarkable_type: "posts",
                },
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );
            showMessage("Added to bookmarks!", "success");
        } catch (err) {
            console.error("Bookmark toggle failed:", err);
            showMessage("Error toggling bookmark", "error");
        }
    };
    return (
        <div
            className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-5"
            style={{ boxShadow: "0 1px 3px rgba(0,0,0,0.1)" }}
        >
            {/* Post Header */}
            <div className="flex justify-between items-start mb-4">
                {/* Left: Avatar and Info */}
                <div className="flex items-start gap-3">
                    {/* Avatar */}
                    {author.passport ? (
                        <img
                            src={author.passport}
                            alt={author.first_name}
                            className="w-10 h-10 rounded-full object-cover"
                        />
                    ) : (
                        <div className="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-sm font-bold text-gray-700 dark:text-gray-300">
                            {initials}
                        </div>
                    )}

                    {/* Author Info */}
                    <div>
                        <AuthorNameWithVerification author={author} />

                        <div className="flex items-center gap-1">
                            <span className="text-xs text-[#6B7280] dark:text-gray-300">
                                posted an update
                            </span>
                            <span className="text-xs text-[#9CA3AF] dark:text-gray-400">
                                • {formatRelativeTime(post.created_at)}
                            </span>
                            <svg
                                className="w-3 h-3 text-[#9CA3AF] dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 1 1 0 011 1v.667a3.353 3.353 0 01-3.5 3.333 3.354 3.354 0 01-3.5-3.333 2 2 0 00-2-2zm1.941 2.667a1.5 1.5 0 01-.581-2.893l.666-.666a3.75 3.75 0 005.666 0l.666.666a1.5 1.5 0 01-.581 2.893 1.75 1.75 0 01-1.75 1.75 1.75 1.75 0 01-1.75-1.75z"
                                    clipRule="evenodd"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {/* Right: Three-dot menu */}
                <div className="relative">
                    <button
                        onClick={() => handleShowPostAction(post)}
                        className="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1"
                        aria-label="Post actions"
                    >
                        <svg
                            className="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                    </button>

                    {showPostAction && (
                        <div className="absolute right-0 mt-2 w-44 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg z-30 overflow-hidden">
                            <button
                                onClick={() => {
                                    if (!confirm("Delete this post?")) return;
                                    handleDeletePost(post.id);
                                    setShowPostAction(false);
                                }}
                                className="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                            >
                                <Trash2 className="w-4 h-4" />
                                <span>Delete post</span>
                            </button>

                            <button
                                onClick={() => {
                                    toggleBookmark(post.id);
                                    setShowPostAction(false);
                                }}
                                className="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                            >
                                <Bookmark className="w-4 h-4" />
                                <span>Save post</span>
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Post Content: media (video/audio/image) or text/link */}
            <div className="mb-3">
                {post.media && post.media.length > 0 ? (
                    <div className="grid grid-cols-1 gap-2">
                        {post.media.map((file, idx) => {
                            const src = file.file_path
                                ? `/${file.file_path}`
                                : file.url || file.path || "";
                            const type = (file.type || "").toLowerCase();

                            if (
                                type.includes("video") ||
                                src.match(/\.(mp4|webm|ogg)(\?|$)/i)
                            ) {
                                return (
                                    <video
                                        key={idx}
                                        controls
                                        className="w-full rounded-lg bg-black"
                                    >
                                        <source src={src} />
                                        Your browser does not support the video
                                        tag.
                                    </video>
                                );
                            }

                            if (
                                type.includes("audio") ||
                                src.match(/\.(mp3|wav|ogg)(\?|$)/i)
                            ) {
                                return (
                                    <audio
                                        key={idx}
                                        controls
                                        className="w-full"
                                    >
                                        <source src={src} />
                                        Your browser does not support the audio
                                        element.
                                    </audio>
                                );
                            }

                            if (
                                type.includes("image") ||
                                src.match(/\.(jpe?g|png|gif|webp)(\?|$)/i)
                            ) {
                                return (
                                    <img
                                        key={idx}
                                        src={src}
                                        alt={`media-${idx}`}
                                        className="w-full h-auto object-cover rounded-lg"
                                    />
                                );
                            }

                            // fallback: if media is a link
                            return (
                                <a
                                    key={idx}
                                    href={src}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="text-indigo-600"
                                >
                                    {src}
                                </a>
                            );
                        })}
                    </div>
                ) : (
                    // render body with clickable links
                    <div className="text-sm whitespace-pre-wrap leading-relaxed text-gray-800 dark:text-gray-100">
                        {renderTextWithLinks(post.body)}
                    </div>
                )}
            </div>

            {/* Engagement Section */}
            <div className="flex justify-between items-center mb-3">
                <div className="flex items-center gap-2">
                    <svg
                        className="w-5 h-5 text-red-500"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                    </svg>
                    <span className="text-sm text-[#6B7280] dark:text-gray-300">
                        {likes.length} Loves
                    </span>
                </div>
                <span className="text-sm text-[#6B7280] dark:text-gray-300">
                    {comments.length} Comments
                </span>
            </div>

            {/* Action Buttons */}

            <div className="border-t border-gray-200 dark:border-gray-700 pt-2">
                <div className="flex">
                    {/* Like */}
                    <button
                        onClick={toggleLike}
                        className="flex-1 flex items-center justify-center gap-1.5 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors active:scale-[0.98]"
                    >
                        <Heart
                            className={`w-4 h-4 ${
                                likes.length > 0
                                    ? "fill-red-500 text-red-500"
                                    : ""
                            }`}
                        />

                        <span className="font-medium">
                            Love
                            <span className="ml-0.5 text-[11px] sm:text-xs text-gray-400">
                                ({likes.length})
                            </span>
                        </span>
                    </button>

                    {/* Comment */}
                    <button
                        onClick={() => {
                            setCommentsVisible(!commentsVisible);
                            setSelectedPost(post);
                        }}
                        className="flex-1 flex items-center justify-center gap-1.5 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors active:scale-[0.98]"
                    >
                        <MessageCircle className="w-4 h-4" />

                        <span className="font-medium">Comment</span>
                    </button>
                </div>
            </div>

            {/* Comments Section */}
            {commentsVisible && (
                <div className="mt-4 space-y-4">
                    {comments && comments.length > 0 ? (
                        comments.map((comment) => (
                            <div
                                key={comment.id}
                                className="flex items-start gap-3"
                            >
                                <img
                                    src={
                                        comment.user?.passport || "/avatar1.jpg"
                                    }
                                    alt={comment.user?.first_name}
                                    className="w-8 h-8 rounded-full object-cover"
                                />
                                <div className="flex-1">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <div className="text-[13px] font-semibold text-gray-800 dark:text-gray-200">
                                                {comment.user?.first_name}{" "}
                                                {comment.user?.last_name}
                                            </div>
                                            <div className="text-xs text-gray-400">
                                                {formatRelativeTime(
                                                    comment.created_at
                                                )}
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <button
                                                onClick={() =>
                                                    handleToggleReply(
                                                        comment.id
                                                    )
                                                }
                                                className="text-xs text-teal-600"
                                            >
                                                Reply
                                            </button>
                                            <button
                                                onClick={() => {
                                                    if (
                                                        !confirm(
                                                            "Delete this comment?"
                                                        )
                                                    )
                                                        return;
                                                    handleDeleteComment(
                                                        comment.id
                                                    );
                                                }}
                                                className="text-xs text-red-500"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>

                                    <div className="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                        {renderTextWithLinks(comment.body)}
                                    </div>

                                    {/* Replies for this comment */}
                                    {comment.replies &&
                                        comment.replies.length > 0 && (
                                            <div className="mt-2 ml-10 space-y-2">
                                                {comment.replies.map((r) => (
                                                    <div
                                                        key={r.id}
                                                        className="text-sm bg-gray-50 dark:bg-gray-900 rounded px-3 py-2"
                                                    >
                                                        <div className="text-[13px] font-semibold">
                                                            {r.user?.first_name}{" "}
                                                            {r.user?.last_name}
                                                        </div>
                                                        <div className="text-xs text-gray-400">
                                                            {formatRelativeTime(
                                                                r.created_at
                                                            )}
                                                        </div>
                                                        <div className="mt-1">
                                                            {renderTextWithLinks(
                                                                r.body
                                                            )}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        )}

                                    {/* Reply input */}
                                    {replySectionFor === comment.id && (
                                        <form
                                            className="mt-2 flex items-center gap-2 ml-10"
                                            onSubmit={(e) => {
                                                e.preventDefault();
                                                handlePostReply(e, comment.id);
                                            }}
                                        >
                                            <input
                                                type="text"
                                                value={newReply}
                                                onChange={(e) =>
                                                    setNewReply(e.target.value)
                                                }
                                                placeholder="Write a reply..."
                                                className="flex-1 px-3 py-2 rounded-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"
                                            />
                                            <button
                                                type="submit"
                                                className="px-3 py-1.5 bg-yellow-400 rounded-full text-sm font-semibold"
                                            >
                                                Send
                                            </button>
                                        </form>
                                    )}
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="text-sm text-gray-500 italic">
                            No comments yet.
                        </div>
                    )}

                    {/* Add Comment Input */}
                    <form
                        onSubmit={handleCommentSubmit}
                        className="flex items-center gap-2"
                    >
                        <input
                            type="text"
                            placeholder="Write a comment..."
                            className="flex-1 px-4 py-2 rounded-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm"
                            value={newComment}
                            onChange={(e) => setNewComment(e.target.value)}
                        />
                        <button
                            disabled={commenting}
                            type="submit"
                            className="px-4 py-2 bg-yellow-400 rounded-full text-sm font-semibold"
                        >
                            {commenting ? "Posting..." : "Post"}
                        </button>
                    </form>
                </div>
            )}
        </div>
    );
};

export default PostWithComments;
