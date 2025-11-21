import React, { useState } from "react";
import ReactDOM from "react-dom/client";

import {
    formatRelativeTime,
    capitaliseAndRemoveHyphen,
} from "../utils/formatRelativeTime";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const getInitials = (firstName, lastName) => {
    return `${firstName?.charAt(0) || ""}${
        lastName?.charAt(0) || ""
    }`.toUpperCase();
};

const PostWithComments = ({
    post,
    newComment,
    setNewComment,
    handleCommentSubmit,
    setSelectedPost,
    handleDeletePost,
    commenting,
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

            const updatedLikes = response.data.likes;

            setLikes(updatedLikes);
            setLiked(
                updatedLikes.some((like) => like.user?.id === post.user_id)
            ); // or your auth ID
        } catch (error) {
            console.error("Error toggling like:", error);
        }
    };

    const handleEditComment = (comment) => {
        setNewComment(comment.body);
        setSelectedPost(post); // or use a dedicated `selectedComment`
        // Open comment box in edit mode
    };

    const handleDeleteComment = async (commentId) => {
        try {
            await axios.delete(`/api/member/comments/${commentId}`);
            showMessage("Comment deleted", "success");
            window.location.reload(); // Refresh the page to reflect changes
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
            await axios.post(`/api/member/comment/reply/${commentId}`, {
                body: newReply,
            });
            showMessage("Relied", "success");
            window.location.reload(); // Refresh the page to reflect changes
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
    };

    return (
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-5" style={{boxShadow: '0 1px 3px rgba(0,0,0,0.1)'}}>
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
                        <h3 className="text-sm font-semibold text-[#1F2937] dark:text-white">
                            {author.first_name} {author.last_name}
                        </h3>
                        <div className="flex items-center gap-1">
                            <span className="text-xs text-[#6B7280] dark:text-gray-300">posted an update</span>
                            <span className="text-xs text-[#9CA3AF] dark:text-gray-400">• 4 years ago (edited)</span>
                            <svg className="w-3 h-3 text-[#9CA3AF] dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 1 1 0 011 1v.667a3.353 3.353 0 01-3.5 3.333 3.354 3.354 0 01-3.5-3.333 2 2 0 00-2-2zm1.941 2.667a1.5 1.5 0 01-.581-2.893l.666-.666a3.75 3.75 0 005.666 0l.666.666a1.5 1.5 0 01-.581 2.893 1.75 1.75 0 01-1.75 1.75 1.75 1.75 0 01-1.75-1.75z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {/* Right: Three-dot menu */}
                <div className="relative">
                    <button className="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {/* Post Content - Video Thumbnail */}
            <div className="mb-3">
                <div className="relative aspect-video bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                    <img
                        src="/images/sample-beach.jpg"
                        alt="Beach scene"
                        className="w-full h-full object-cover"
                    />
                    {/* Play Button Overlay */}
                    <div className="absolute inset-0 flex items-center justify-center">
                        <div className="bg-white dark:bg-gray-800 bg-opacity-90 rounded-full p-4">
                            <svg className="w-8 h-8 text-gray-800 dark:text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 5v10l8-5-8-5z"></path>
                            </svg>
                        </div>
                    </div>
                    {/* Duration Badge */}
                    <div className="absolute bottom-2 left-2 bg-black bg-opacity-80 text-white text-xs px-2 py-1 rounded">
                        0:13
                    </div>
                </div>
            </div>

            {/* Engagement Section */}
            <div className="flex justify-between items-center mb-3">
                <div className="flex items-center gap-2">
                    <svg className="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                    </svg>
                    <span className="text-sm text-[#6B7280] dark:text-gray-300">You and Jennifer</span>
                </div>
                <span className="text-sm text-[#6B7280] dark:text-gray-300">2 Comments</span>
            </div>

            {/* Action Buttons */}
            <div className="border-t border-gray-200 dark:border-gray-700 pt-3">
                <div className="flex">
                    <button className="flex-1 flex items-center justify-center gap-2 py-2 px-4 text-[#6B7280] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span className="font-medium">Love</span>
                    </button>
                    <button className="flex-1 flex items-center justify-center gap-2 py-2 px-4 text-[#6B7280] dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span className="font-medium">Comment</span>
                    </button>
                </div>
            </div>
        </div>
    );
};

export default PostWithComments;
