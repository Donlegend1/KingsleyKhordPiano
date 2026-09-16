import React, { useState, useEffect, useRef } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";

import {
    formatRelativeTime,
    formatSubcategoryLabel,
} from "../utils/formatRelativeTime";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";
import { Trash2, Bookmark, ThumbsUp, MessageCircle, PinIcon } from "lucide-react";
import AuthorNameWithVerification from "./User/AuthorNameWithVerification";
import PostBlocks from "./PostBlocks";

const REACTIONS = [
    { type: "like", emoji: "👍", label: "Like", color: "text-blue-600 dark:text-blue-400" },
    { type: "love", emoji: "❤️", label: "Love", color: "text-red-600 dark:text-red-400" },
    { type: "haha", emoji: "😂", label: "Haha", color: "text-yellow-500" },
    { type: "sad", emoji: "😢", label: "Sad", color: "text-yellow-600" },
    { type: "wow", emoji: "😮", label: "Wow", color: "text-yellow-500" },
];

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
    togglePinPost
}) => {
    const [comments, setComments] = useState(post.comments || []);
    const [showPostAction, setShowPostAction] = useState(false);
    const [showCommentAction, setShowCommentAction] = useState(false);
    const { showMessage } = useFlashMessage();
    const [showCommentActionId, setShowCommentActionId] = useState(null);

    const [commentsVisible, setCommentsVisible] = useState((post.comments || []).length > 0);
    const [expandedReplies, setExpandedReplies] = useState({});
    const [showCommentEmojiPicker, setShowCommentEmojiPicker] = useState(false);

    const insertCommentEmoji = (emoji) => {
        setNewComment((prev) => (prev || "") + emoji);
        setShowCommentEmojiPicker(false);
    };

    const toggleReplies = (commentId) => {
        setExpandedReplies((prev) => ({ ...prev, [commentId]: !prev[commentId] }));
    };
    const [likes, setLikes] = useState(post.likes || []);
    const [liked, setLiked] = useState(post.liked_by_user || false);
    const [heartPulse, setHeartPulse] = useState(false);
    const [showReactionPicker, setShowReactionPicker] = useState(false);
    const reactionCloseTimer = useRef(null);
    const longPressTimer = useRef(null);
    const [isBookmarked, setIsBookmarked] = useState(post.is_bookmarked || false);
    const [replySectionFor, setReplySectionFor] = useState(null);
    const [editingCommentId, setEditingCommentId] = useState(null);
    const [editCommentText, setEditCommentText] = useState("");
    const [editingReplyId, setEditingReplyId] = useState(null);
    const [editReplyText, setEditReplyText] = useState("");

    const totalCommentCount = (comments || []).reduce(
        (sum, comment) => sum + 1 + (comment.replies?.length || 0),
        0,
    );

    const [showReplySection, setShowReplySection] = useState(false);
    const [newReply, setNewReply] = useState("");

    const author = post.user || {};
    const initials = getInitials(author.first_name, author.last_name);

    const myLike = likes.find((like) => like.user?.id === window.authUser?.id);
    const myReaction = REACTIONS.find((r) => r.type === myLike?.type);

    const openReactionPicker = () => {
        clearTimeout(reactionCloseTimer.current);
        setShowReactionPicker(true);
    };

    const scheduleCloseReactionPicker = () => {
        reactionCloseTimer.current = setTimeout(
            () => setShowReactionPicker(false),
            350,
        );
    };

    const handleLikeTouchStart = () => {
        longPressTimer.current = setTimeout(() => {
            openReactionPicker();
            longPressTimer.current = null;
        }, 350);
    };

    const handleLikeTouchEnd = () => {
        if (longPressTimer.current) {
            clearTimeout(longPressTimer.current);
            longPressTimer.current = null;
        }
    };

    const selectReaction = (type) => {
        setShowReactionPicker(false);
        toggleLike(type);
    };

    const toggleLike = async (type = "like") => {
        setHeartPulse(true);
        setTimeout(() => setHeartPulse(false), 400);

        try {
            const response = await axios.post("/api/member/like", {
                post_id: post.id,
                type,
            });
            const updatedLikes =
                response.data?.likes || response.data?.data || [];

            setLikes(updatedLikes);
            const nowLiked = updatedLikes.some(
                (like) => like.user?.id === window.authUser?.id,
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

    const toggleCommentLike = async (commentId) => {
        try {
            const { data } = await axios.post(`/api/member/comment/${commentId}/like`);
            setComments((prev) =>
                prev.map((c) =>
                    c.id === commentId
                        ? { ...c, liked_by_user: data.liked, likes_count: data.likes_count }
                        : c,
                ),
            );
        } catch (error) {
            console.error("Error toggling comment like:", error);
        }
    };

    const toggleReplyLike = async (commentId, replyId) => {
        try {
            const { data } = await axios.post(`/api/member/reply/${replyId}/like`);
            setComments((prev) =>
                prev.map((c) =>
                    c.id === commentId
                        ? {
                              ...c,
                              replies: (c.replies || []).map((r) =>
                                  r.id === replyId
                                      ? { ...r, liked_by_user: data.liked, likes_count: data.likes_count }
                                      : r,
                              ),
                          }
                        : c,
                ),
            );
        } catch (error) {
            console.error("Error toggling reply like:", error);
        }
    };

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const handleEditComment = (comment) => {
        setEditingCommentId(comment.id);
        setEditCommentText(comment.body);
    };

    const handleCancelEditComment = () => {
        setEditingCommentId(null);
        setEditCommentText("");
    };

    const handleSaveEditComment = async (commentId) => {
        if (!editCommentText.trim()) {
            showMessage("Comment can't be empty", "error");
            return;
        }

        try {
            const res = await axios.put(`/api/member/comments/${commentId}`, {
                body: editCommentText,
            });

            const updated = res.data?.data || res.data;

            setComments((prev) =>
                prev.map((c) =>
                    c.id === commentId ? { ...c, body: updated.body } : c,
                ),
            );

            if (typeof onUpdatePost === "function") {
                onUpdatePost(post.id, {
                    comments: (comments || []).map((c) =>
                        c.id === commentId ? { ...c, body: updated.body } : c,
                    ),
                });
            }

            setEditingCommentId(null);
            setEditCommentText("");
            showMessage("Comment updated", "success");
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
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
                        (c) => c.id !== commentId,
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
            prevId === commentId ? null : commentId,
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
                },
            );
            const created = res.data?.data || res.data;

            // append locally
            setComments((prev) =>
                prev.map((c) =>
                    c.id === commentId
                        ? { ...c, replies: [...(c.replies || []), created] }
                        : c,
                ),
            );

            // notify parent
            if (typeof onUpdatePost === "function") {
                onUpdatePost(post.id, {
                    comments: (comments || []).map((c) =>
                        c.id === commentId
                            ? { ...c, replies: [...(c.replies || []), created] }
                            : c,
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

    const handleEditReply = (reply) => {
        setEditingReplyId(reply.id);
        setEditReplyText(reply.body);
    };

    const handleCancelEditReply = () => {
        setEditingReplyId(null);
        setEditReplyText("");
    };

    const handleSaveEditReply = async (commentId, replyId) => {
        if (!editReplyText.trim()) {
            showMessage("Reply can't be empty", "error");
            return;
        }

        try {
            const res = await axios.put(`/api/member/reply/${replyId}`, {
                body: editReplyText,
            });

            const updated = res.data?.data || res.data;

            const applyUpdate = (c) =>
                c.id === commentId
                    ? {
                          ...c,
                          replies: (c.replies || []).map((r) =>
                              r.id === replyId
                                  ? { ...r, body: updated.body }
                                  : r,
                          ),
                      }
                    : c;

            setComments((prev) => prev.map(applyUpdate));

            if (typeof onUpdatePost === "function") {
                onUpdatePost(post.id, {
                    comments: (comments || []).map(applyUpdate),
                });
            }

            setEditingReplyId(null);
            setEditReplyText("");
            showMessage("Reply updated", "success");
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
    };

    const handleDeleteReply = async (commentId, replyId) => {
        try {
            await axios.delete(`/api/member/reply/${replyId}`);

            const applyDelete = (c) =>
                c.id === commentId
                    ? {
                          ...c,
                          replies: (c.replies || []).filter(
                              (r) => r.id !== replyId,
                          ),
                      }
                    : c;

            setComments((prev) => prev.map(applyDelete));

            if (typeof onUpdatePost === "function") {
                onUpdatePost(post.id, {
                    comments: (comments || []).map(applyDelete),
                });
            }

            showMessage("Reply deleted", "success");
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
            const { data } = await axios.post(
                `/member/bookmark/toggle`,
                {
                    bookmarkable_id: id,
                    bookmarkable_type: "posts",
                },
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                },
            );
            const saved = data.status === "added";
            setIsBookmarked(saved);
            showMessage(saved ? "Added to bookmarks!" : "Removed from bookmarks", "success");
        } catch (err) {
            console.error("Bookmark toggle failed:", err);
            showMessage("Error toggling bookmark", "error");
        }
    };

    
    return (
        <div
            className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-3 hover:bg-gray-50 transition-colors duration-150"
            style={{ boxShadow: "0 1px 3px rgba(0,0,0,0.07)" }}
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
                        <div className="flex items-center gap-1.5 flex-wrap">
                            <AuthorNameWithVerification author={author} />
                            <span className="text-sm text-gray-500 dark:text-gray-400">
                                posted an update
                            </span>
                        </div>

                        <div className="flex items-center gap-1 flex-wrap mt-0.5">
                            <span className="text-xs text-[#9CA3AF] dark:text-gray-400">
                                {formatRelativeTime(post.created_at)}
                            </span>
                            {post.subcategory && (
                                <>
                                    <span className="text-xs text-[#9CA3AF] dark:text-gray-400">•</span>
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
                                    <span className="text-xs text-gray-500 dark:text-gray-400">
                                        {formatSubcategoryLabel(post.subcategory)}
                                    </span>
                                </>
                            )}
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
                            {(post.user_id === window.authUser?.id ||
                                window.authUser?.email === "kingsleykhord@gmail.com") && (
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
                            )}

                            <button
                                onClick={() => {
                                    toggleBookmark(post.id);
                                    setShowPostAction(false);
                                }}
                                className={`flex w-full items-center gap-3 px-4 py-2.5 text-sm transition ${
                                    isBookmarked
                                        ? "text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30"
                                        : "text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                                }`}
                            >
                                <Bookmark className={`w-4 h-4 ${isBookmarked ? "fill-indigo-600" : ""}`} />
                                <span>{isBookmarked ? "Saved post" : "Save post"}</span>
                            </button>
                            {window.authUser?.email === "kingsleykhord@gmail.com" && (
                                <button
                                    onClick={() => {
                                        togglePinPost(post.id);
                                        setShowPostAction(false);
                                    }}
                                    className="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                >
                                    <PinIcon className="w-4 h-4" />
                                    <span>Pin Post</span>
                                </button>
                            )}
                        </div>
                    )}
                </div>
            </div>

            {/* Post Content: media (video/audio/image) or text/link */}

            {post.title && (
                <h2 className="text-base font-bold text-gray-900 dark:text-white mb-2">
                    {post.title}
                </h2>
            )}

            <div className="mb-3">
                <PostBlocks post={post} />
            </div>

            {/* Engagement Summary */}
            {(likes.length > 0 || totalCommentCount > 0) && (
                <div className="flex items-center justify-between text-sm text-gray-400 dark:text-gray-500 pb-2">
                    <span className="flex items-center gap-1.5">
                        {likes.length > 0 && (
                            <>
                                {(() => {
                                    const firstReaction = REACTIONS.find(
                                        (r) => r.type === likes[0]?.type,
                                    );

                                    if (!firstReaction || firstReaction.type === "like") {
                                        return (
                                            <span className="w-4 h-4 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                                                <ThumbsUp className="w-2.5 h-2.5 fill-white text-white" />
                                            </span>
                                        );
                                    }

                                    return (
                                        <span className="text-sm leading-none">
                                            {firstReaction.emoji}
                                        </span>
                                    );
                                })()}
                                {(() => {
                                    const names = likes.map(
                                        (l) =>
                                            l.user?.first_name ||
                                            l.user?.display_name ||
                                            "Someone",
                                    );

                                    if (names.length === 1) return names[0];
                                    if (names.length === 2)
                                        return `${names[0]} and ${names[1]}`;
                                    return `${names[0]} and ${names.length - 1} others`;
                                })()}
                            </>
                        )}
                    </span>
                    <span>
                        {totalCommentCount > 0 &&
                            `${totalCommentCount} ${totalCommentCount === 1 ? "Comment" : "Comments"}`}
                    </span>
                </div>
            )}

            {/* Action Buttons */}

            <div className="border-t border-gray-200 dark:border-gray-700 pt-2">
                <div className="flex items-center">
                    {/* Like */}
                    <div
                        className="relative flex-1"
                        onMouseEnter={openReactionPicker}
                        onMouseLeave={scheduleCloseReactionPicker}
                    >
                        {showReactionPicker && (
                            <div
                                className="absolute bottom-full left-0 mb-1 flex items-center gap-0.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-full shadow-lg px-1.5 py-1 z-10"
                                onMouseEnter={openReactionPicker}
                                onMouseLeave={scheduleCloseReactionPicker}
                            >
                                {REACTIONS.map((reaction) => (
                                    <button
                                        key={reaction.type}
                                        type="button"
                                        title={reaction.label}
                                        onClick={() => selectReaction(reaction.type)}
                                        className="w-9 h-9 flex items-center justify-center text-2xl rounded-full hover:scale-125 hover:-translate-y-1 transition-transform duration-150"
                                    >
                                        {reaction.emoji}
                                    </button>
                                ))}
                            </div>
                        )}

                        <button
                            onClick={() => {
                                // A plain click only removes an existing reaction.
                                // Choosing "Like" (or any other reaction) requires
                                // hovering/pressing to open the picker above.
                                if (liked) toggleLike(myReaction?.type || "like");
                            }}
                            onTouchStart={handleLikeTouchStart}
                            onTouchEnd={handleLikeTouchEnd}
                            className={`w-full flex items-center justify-center gap-2 py-2.5 text-sm font-medium rounded-md transition-colors duration-150 active:scale-95 ${
                                liked
                                    ? myReaction?.color || "text-blue-600 dark:text-blue-400"
                                    : "text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            }`}
                        >
                            {liked && myReaction && myReaction.type !== "like" ? (
                                <span
                                    className={`text-base transition-transform ease-out ${
                                        heartPulse ? "scale-125 duration-200" : "scale-100 duration-300"
                                    }`}
                                >
                                    {myReaction.emoji}
                                </span>
                            ) : (
                                <ThumbsUp
                                    className={`w-4 h-4 transition-transform ease-out ${
                                        heartPulse
                                            ? "scale-125 duration-200"
                                            : "scale-100 duration-300"
                                    } ${liked ? "fill-blue-600 text-blue-600 dark:fill-blue-400 dark:text-blue-400" : ""}`}
                                />
                            )}
                            <span>{liked ? myReaction?.label || "Liked" : "Like"}</span>
                        </button>
                    </div>

                    {/* Comment */}
                    <button
                        onClick={() => {
                            setCommentsVisible(!commentsVisible);
                            setSelectedPost(post);
                        }}
                        className="flex-1 flex items-center justify-center gap-2 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 active:scale-95"
                    >
                        <MessageCircle className="w-4 h-4" />
                        <span>Comment</span>
                    </button>
                </div>
            </div>

            {/* Comments Section */}
            {commentsVisible && (
                <div className="mt-4 space-y-3">
                    {comments && comments.length > 0 ? (
                        comments.map((comment) => {
                            const repliesOpen = !!expandedReplies[comment.id];
                            const replyCount = comment.replies?.length || 0;

                            return (
                                <div key={comment.id} className="flex items-start gap-2.5">
                                    <img
                                        src={comment.user?.passport || "/avatar1.jpg"}
                                        alt={comment.user?.first_name}
                                        className="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                    />
                                    <div className="flex-1 min-w-0">
                                        {editingCommentId === comment.id ? (
                                            <div className="flex items-center gap-2">
                                                <input
                                                    type="text"
                                                    value={editCommentText}
                                                    onChange={(e) => setEditCommentText(e.target.value)}
                                                    className="flex-1 px-3 py-2 rounded-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm dark:text-gray-200"
                                                />
                                                <button
                                                    onClick={() => handleSaveEditComment(comment.id)}
                                                    className="px-3 py-1.5 bg-indigo-600 text-white rounded-full text-sm font-semibold"
                                                >
                                                    Save
                                                </button>
                                            </div>
                                        ) : (
                                            <div className="inline-block bg-gray-100 dark:bg-gray-700 rounded-2xl px-3.5 py-2 max-w-full">
                                                <div className="text-[13px] font-semibold text-gray-900 dark:text-gray-100">
                                                    {comment.user?.display_name ||
                                                        `${comment.user?.first_name ?? ""} ${comment.user?.last_name ?? ""}`}
                                                </div>
                                                <div className="text-sm text-gray-800 dark:text-gray-200 break-words">
                                                    {renderTextWithLinks(comment.body)}
                                                </div>
                                            </div>
                                        )}

                                        <div className="flex items-center gap-3 mt-1 ml-1 text-xs text-gray-500 dark:text-gray-400">
                                            <button
                                                onClick={() => toggleCommentLike(comment.id)}
                                                className={`font-semibold hover:underline ${comment.liked_by_user ? "text-blue-600 dark:text-blue-400" : ""}`}
                                            >
                                                Like{comment.likes_count > 0 ? ` (${comment.likes_count})` : ""}
                                            </button>
                                            <button
                                                onClick={() => handleToggleReply(comment.id)}
                                                className="font-semibold hover:underline"
                                            >
                                                Reply
                                            </button>
                                            <span>{formatRelativeTime(comment.created_at)}</span>
                                            {comment.user_id === window.authUser?.id && (
                                                <>
                                                    <button
                                                        onClick={() =>
                                                            editingCommentId === comment.id
                                                                ? handleCancelEditComment()
                                                                : handleEditComment(comment)
                                                        }
                                                        className="hover:underline"
                                                    >
                                                        {editingCommentId === comment.id ? "Cancel" : "Edit"}
                                                    </button>
                                                    <button
                                                        onClick={() => {
                                                            if (!confirm("Delete this comment?")) return;
                                                            handleDeleteComment(comment.id);
                                                        }}
                                                        className="hover:underline text-red-500"
                                                    >
                                                        Delete
                                                    </button>
                                                </>
                                            )}
                                        </div>

                                        {/* View/hide replies toggle */}
                                        {replyCount > 0 && (
                                            <button
                                                onClick={() => toggleReplies(comment.id)}
                                                className="flex items-center gap-1 mt-2 ml-1 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:underline"
                                            >
                                                <span className="text-gray-300 dark:text-gray-600">↳</span>
                                                {repliesOpen
                                                    ? "Hide replies"
                                                    : `View ${replyCount} ${replyCount === 1 ? "reply" : "replies"}`}
                                            </button>
                                        )}

                                        {/* Replies for this comment */}
                                        {repliesOpen && replyCount > 0 && (
                                            <div className="mt-2 ml-2 pl-4 border-l-2 border-gray-200 dark:border-gray-700 space-y-3">
                                                {comment.replies.map((r) => (
                                                    <div key={r.id} className="flex items-start gap-2.5">
                                                        <img
                                                            src={r.user?.passport || "/avatar1.jpg"}
                                                            alt={r.user?.first_name}
                                                            className="w-7 h-7 rounded-full object-cover flex-shrink-0"
                                                        />
                                                        <div className="flex-1 min-w-0">
                                                            {editingReplyId === r.id ? (
                                                                <div className="flex items-center gap-2">
                                                                    <input
                                                                        type="text"
                                                                        value={editReplyText}
                                                                        onChange={(e) => setEditReplyText(e.target.value)}
                                                                        className="flex-1 px-3 py-1.5 rounded-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm dark:text-gray-200"
                                                                    />
                                                                    <button
                                                                        onClick={() => handleSaveEditReply(comment.id, r.id)}
                                                                        className="px-3 py-1.5 bg-indigo-600 text-white rounded-full text-xs font-semibold"
                                                                    >
                                                                        Save
                                                                    </button>
                                                                </div>
                                                            ) : (
                                                                <div className="inline-block bg-gray-100 dark:bg-gray-700 rounded-2xl px-3.5 py-2 max-w-full">
                                                                    <div className="text-[13px] font-semibold text-gray-900 dark:text-gray-100">
                                                                        {r.user?.display_name ||
                                                                            `${r.user?.first_name ?? ""} ${r.user?.last_name ?? ""}`}
                                                                    </div>
                                                                    <div className="text-sm text-gray-800 dark:text-gray-200 break-words">
                                                                        {renderTextWithLinks(r.body)}
                                                                    </div>
                                                                </div>
                                                            )}

                                                            <div className="flex items-center gap-3 mt-1 ml-1 text-xs text-gray-500 dark:text-gray-400">
                                                                <button
                                                                    onClick={() => toggleReplyLike(comment.id, r.id)}
                                                                    className={`font-semibold hover:underline ${r.liked_by_user ? "text-blue-600 dark:text-blue-400" : ""}`}
                                                                >
                                                                    Like{r.likes_count > 0 ? ` (${r.likes_count})` : ""}
                                                                </button>
                                                                <button
                                                                    onClick={() => handleToggleReply(comment.id)}
                                                                    className="font-semibold hover:underline"
                                                                >
                                                                    Reply
                                                                </button>
                                                                <span>{formatRelativeTime(r.created_at)}</span>
                                                                {r.user_id === window.authUser?.id && (
                                                                    <>
                                                                        <button
                                                                            onClick={() =>
                                                                                editingReplyId === r.id
                                                                                    ? handleCancelEditReply()
                                                                                    : handleEditReply(r)
                                                                            }
                                                                            className="hover:underline"
                                                                        >
                                                                            {editingReplyId === r.id ? "Cancel" : "Edit"}
                                                                        </button>
                                                                        <button
                                                                            onClick={() => {
                                                                                if (!confirm("Delete this reply?")) return;
                                                                                handleDeleteReply(comment.id, r.id);
                                                                            }}
                                                                            className="hover:underline text-red-500"
                                                                        >
                                                                            Delete
                                                                        </button>
                                                                    </>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        )}

                                        {/* Reply input */}
                                        {replySectionFor === comment.id && (
                                            <form
                                                className="mt-2 flex items-center gap-2 ml-2"
                                                onSubmit={(e) => {
                                                    e.preventDefault();
                                                    handlePostReply(e, comment.id);
                                                }}
                                            >
                                                <input
                                                    type="text"
                                                    value={newReply}
                                                    onChange={(e) => setNewReply(e.target.value)}
                                                    placeholder="Write a reply..."
                                                    className="flex-1 px-3 py-2 rounded-full border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm dark:text-gray-200"
                                                />
                                                <button
                                                    type="submit"
                                                    className="px-3 py-1.5 bg-indigo-600 text-white rounded-full text-sm font-semibold"
                                                >
                                                    Send
                                                </button>
                                            </form>
                                        )}
                                    </div>
                                </div>
                            );
                        })
                    ) : (
                        <div className="text-sm text-gray-500 italic">
                            No comments yet.
                        </div>
                    )}

                    {/* Add Comment Input */}
                    <form
                        onSubmit={handleCommentSubmit}
                        className="flex items-center gap-2.5 pt-1"
                    >
                        {window.authUser?.passport ? (
                            <img
                                src={window.authUser.passport}
                                alt=""
                                className="w-8 h-8 rounded-full object-cover flex-shrink-0"
                            />
                        ) : (
                            <div className="w-8 h-8 rounded-full bg-gray-900 dark:bg-gray-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {(window.authUser?.display_name || window.authUser?.first_name || "?").charAt(0).toUpperCase()}
                            </div>
                        )}

                        <div className="relative flex-1 flex items-center bg-gray-100 dark:bg-gray-700 rounded-full pl-4 pr-1.5 py-1">
                            <input
                                type="text"
                                placeholder="Write a comment..."
                                className="flex-1 bg-transparent text-sm text-gray-800 dark:text-gray-200 focus:outline-none"
                                value={newComment}
                                onChange={(e) => setNewComment(e.target.value)}
                            />

                            <div className="flex items-center gap-0.5">
                                <button
                                    type="button"
                                    onClick={() => setShowCommentEmojiPicker((prev) => !prev)}
                                    className="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9.75h.008v.008H9V9.75zm6 0h.008v.008H15V9.75z" />
                                    </svg>
                                </button>
                            </div>

                            {showCommentEmojiPicker && (
                                <div className="absolute right-0 bottom-full mb-2 flex flex-wrap gap-0.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg p-2 w-56 z-10">
                                    {["😀", "😂", "😍", "🥳", "😢", "👍", "🙌", "🔥", "❤️", "🎹", "✨", "💯"].map((emoji) => (
                                        <button
                                            key={emoji}
                                            type="button"
                                            onClick={() => insertCommentEmoji(emoji)}
                                            className="w-8 h-8 flex items-center justify-center text-lg rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                        >
                                            {emoji}
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>

                        <button
                            disabled={commenting || !newComment?.trim()}
                            type="submit"
                            title={commenting ? "Posting..." : "Post"}
                            className="w-9 h-9 flex items-center justify-center bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg flex-shrink-0 transition-colors"
                        >
                            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3.478 2.404a.75.75 0 00-.926.941l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.404z" />
                            </svg>
                        </button>
                    </form>
                </div>
            )}
        </div>
    );
};

export default PostWithComments;
