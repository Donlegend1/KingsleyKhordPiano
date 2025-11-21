import React, { useState, useEffect } from "react";
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

const getInitials = (firstName, lastName) => {
    return `${firstName?.charAt(0) || ""}${
        lastName?.charAt(0) || ""
    }`.toUpperCase();
};

const ChatWithComments = ({
    post,
    newComment,
    setNewComment,
    handleCommentSubmit,
    setSelectedPost,
    handleDeletePost,
    commenting,
    onUpdatePost,
}) => {
    const [comments, setComments] = useState(post.replies || []);

    useEffect(() => {
        setComments(post.replies || []);
    }, [post.replies]);
    // keep likes in sync when parent updates the post
    useEffect(() => {
        setLikes(post.likes || []);
        setLiked(post.liked_by_user || false);
    }, [post.likes, post.liked_by_user]);
    const [showPostActionId, setShowPostActionId] = useState(null);
    const [showCommentAction, setShowCommentAction] = useState(false);
    const showPostAction = showPostActionId === post.id;
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
                "error"
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
            prevId === commentId ? null : commentId
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
                }
            );
            const created = res.data?.data || res.data;
            // update local comments: find parent comment and append reply
            setComments((prev) =>
                prev.map((c) =>
                    c.id === commentId
                        ? { ...c, replies: [...(c.replies || []), created] }
                        : c
                )
            );
            setNewReply("");
            setReplySectionFor(null);
            showMessage("Replied", "success");
        } catch (error) {
            showMessage(
                error.response?.data?.message || "Failed to post reply",
                "error"
            );
            console.error("Post reply error:", error);
        }
    };

    // toggle like for this post and notify parent to keep posts array in sync
    const toggleLike = async () => {
        try {
            const response = await axios.post(
                `/api/member/premium/messages/${post.id}/like`,
                { post_id: post.id }
            );

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

    // Best-effort detection of current user to style own messages
    const currentUser = window.authUser || null;

    const isOwn = Boolean(
        post.user &&
            (post.user.is_current_user ||
                post.user.id === currentUser.id ||
                currentUser.email === "kingsleykhord@gmail.com")
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
                    className={`max-w-[78%] ${
                        isOwn ? "text-right" : "text-left"
                    }`}
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
                                <span className="text-[11px] font-normal text-gray-400 dark:text-gray-500">
                                    {formatRelativeTime(post.created_at)}
                                </span>
                            </div>
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
                                    )
                                )}
                            </div>
                        )}
                    </div>

                    {/* Actions row */}
                    <div className="mt-2 flex items-center justify-between text-sm relative">
                        <div className="flex items-center gap-4 text-gray-600 dark:text-gray-300">
                            <button
                                onClick={toggleLike}
                                className="flex items-center gap-2 hover:text-indigo-600 transition"
                            >
                                <i className="fa fa-heart text-red-500"></i>
                                <span className="text-sm">{likes.length}</span>
                            </button>

                            <button
                                onClick={() => openCommentSection(post)}
                                className="flex items-center gap-2 hover:text-teal-600 transition"
                            >
                                <i className="fa fa-comment"></i>
                                <span className="text-sm">
                                    {comments.length}
                                </span>
                            </button>
                        </div>

                        <div className="flex items-center gap-2">
                            {isOwn && (
                                <div className="relative">
                                    <button
                                        onClick={() =>
                                            handleShowPostAction(post)
                                        }
                                        className="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-300"
                                    >
                                        •••
                                    </button>
                                    {showPostActionId === post.id && (
                                        <div className="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-30">
                                            <button
                                                className="w-full text-left px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900 text-red-600"
                                                onClick={() => {
                                                    if (
                                                        !confirm(
                                                            "Delete this post?"
                                                        )
                                                    )
                                                        return;
                                                    handleDeletePost(post.id);
                                                    setShowPostActionId(null);
                                                }}
                                            >
                                                Delete post
                                            </button>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Replies */}
                    {commentsVisible && (
                        <div className="mt-3 space-y-3">
                            {comments && comments.length > 0 ? (
                                comments.map((comment) => (
                                    <div
                                        key={comment.id}
                                        className="flex items-start gap-2"
                                    >
                                        <img
                                            src={
                                                comment.user?.passport ||
                                                "/avatar1.jpg"
                                            }
                                            alt={comment.user?.first_name}
                                            className="w-7 h-7 rounded-full object-cover"
                                        />
                                        <div>
                                            <div className="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-1.5 border border-gray-100 dark:border-gray-700">
                                                <div className="flex items-center justify-between">
                                                    <div className="text-[12px] font-semibold text-indigo-700 dark:text-indigo-300">
                                                        {
                                                            comment.user
                                                                ?.first_name
                                                        }{" "}
                                                        {
                                                            comment.user
                                                                ?.last_name
                                                        }
                                                    </div>
                                                    <div className="text-[11px] text-gray-400">
                                                        {formatRelativeTime(
                                                            comment.created_at
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                                    {comment.body}
                                                </div>
                                            </div>

                                            <div className="mt-1 text-xs text-gray-500 flex items-center gap-3">
                                                <button
                                                    onClick={() =>
                                                        handleToggleReply(
                                                            comment.id
                                                        )
                                                    }
                                                    className="hover:text-teal-600"
                                                >
                                                    Reply
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDeleteComment(
                                                            comment.id
                                                        )
                                                    }
                                                    className="text-red-500 hover:text-red-700"
                                                >
                                                    Delete
                                                </button>
                                            </div>

                                            {replySectionFor === comment.id && (
                                                <form
                                                    onSubmit={(e) =>
                                                        handlePostReply(
                                                            e,
                                                            comment.id
                                                        )
                                                    }
                                                    className="mt-2 flex items-center gap-2"
                                                >
                                                    <input
                                                        type="text"
                                                        placeholder="Write a reply..."
                                                        value={newReply}
                                                        onChange={(e) =>
                                                            setNewReply(
                                                                e.target.value
                                                            )
                                                        }
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
                                    onChange={(e) =>
                                        setNewComment(e.target.value)
                                    }
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

                {/* Avatar for own on the right */}
                {isOwn &&
                    (author.passport ? (
                        <img
                            src={author.passport}
                            alt={author.first_name}
                            className="w-9 h-9 rounded-full object-cover"
                        />
                    ) : (
                        <div className="w-9 h-9 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-sm font-bold text-white">
                            {initials}
                        </div>
                    ))}
            </div>
        </div>
    );
};

export default ChatWithComments;
