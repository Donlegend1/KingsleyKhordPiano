import React, { useState } from "react";
import ReactDOM from "react-dom/client";

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
}) => {
    const [comments, setComments] = useState(post.comments || []);
    const [showPostAction, setShowPostAction] = useState(false);
    const [showCommentAction, setShowCommentAction] = useState(false);
    const [showCommentActionId, setShowCommentActionId] = useState(null);

    const [commentsVisible, setCommentsVisible] = useState(false);
    const [likes, setLikes] = useState(post.likes || []);
    const [liked, setLiked] = useState(post.liked_by_user || false);
    const [replySectionFor, setReplySectionFor] = useState(null);

    const [showReplySection, setShowReplySection] = useState(false);
    const [newReply, setNewReply] = useState("");

    const author = post.user || {};
    const initials = getInitials(author.first_name, author.last_name);

    const formatRelativeTime = (dateString) => {
        const now = Date.now(); // current UTC timestamp in ms
        const posted = Date.parse(dateString); // parse ISO date string as UTC

        const diffInSeconds = Math.floor((now - posted) / 1000);

        if (isNaN(diffInSeconds)) return "Invalid date";

        if (diffInSeconds < 60) {
            return `${diffInSeconds} second${
                diffInSeconds !== 1 ? "s" : ""
            } ago`;
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes} minute${minutes !== 1 ? "s" : ""} ago`;
        } else {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours} hour${hours !== 1 ? "s" : ""} ago`;
        }
    };

    const capitaliseAndRemoveHyphen = (text) => {
        return text
            .split("_")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ");
    };

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
            await axios.delete(`/api/member/comment/${commentId}`);
            showMessage("Comment deleted", "success");
            fetchPosts(); // Refresh
        } catch (error) {
            showMessage("Failed to delete comment", "error");
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
            fetchPosts(); // Refresh
        } catch (error) {
            showMessage("Failed to add reply", "error");
        }
    };

    return (
        <div className="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm mb-6">
            {/* Post Header */}
            <div className="flex justify-between items-start mb-4">
                {/* Left: Avatar and Info */}
                <div className="flex items-start space-x-3">
                    {/* Avatar */}
                    {author.passport ? (
                        <img
                            src={author.passport}
                            alt={author.first_name}
                            className="w-10 h-10 rounded-full object-cover"
                        />
                    ) : (
                        <div className="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-sm font-bold text-white">
                            {initials}
                        </div>
                    )}

                    {/* Author Info */}
                    <div>
                        <h3 className="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {author.first_name} {author.last_name}
                        </h3>
                        <span className="text-xs text-gray-500 dark:text-gray-400">
                            {formatRelativeTime(post.created_at)} •{" "}
                            {"Posted in "}
                            {capitaliseAndRemoveHyphen(post.subcategory) ||
                                "General"}
                            {post.category === "forum" ? " Forum" : ""}
                        </span>
                    </div>
                </div>

                {/* Right: Icons */}
                <div className="relative flex items-center space-x-2 text-gray-500 dark:text-gray-300">
                    <i
                        className="fa fa-ellipsis-v cursor-pointer"
                        aria-hidden="true"
                        onClick={() => handleShowPostAction(post)}
                        onFocus={() => setSelectedPost(post)}
                    ></i>
                    <i className="fa fa-bookmark" aria-hidden="true"></i>

                    {showPostAction && (
                        <div className="absolute right-0 top-6 z-20 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg w-28 py-2 text-sm">
                            <button
                                className="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                                onClick={() => handleEditPost(post)}
                            >
                                Edit
                            </button>
                            <button
                                className="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-800"
                                onClick={() => handleDeletePost(post.id)}
                            >
                                Delete
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Post Content */}
            <div className="mb-4">
                <p className="text-sm text-gray-600 dark:text-gray-300 mt-1">
                    {post.body}
                </p>

                {post.media && post.media.length > 0 && (
                    <div className="mt-3 flex flex-wrap gap-3">
                        {post.media.map((file, index) =>
                            file.type === "image" ? (
                                <img
                                    key={index}
                                    src={`/${file.file_path}`}
                                    alt={`Post media ${index}`}
                                    className="rounded-lg w-auto"
                                />
                            ) : (
                                <video
                                    key={index}
                                    src={`/${file.file_path}`}
                                    controls
                                    className="rounded-lg w-auto"
                                />
                            )
                        )}
                    </div>
                )}
            </div>

            <div className="mt-4 border-t  border-gray-300 dark:border-gray-600 pt-4" onFocus={() => setSelectedPost(post)}>
                <div className="flex justify-between items-center mb-4">
                    <div className="flex justify-end space-x-4 text-gray-600 dark:text-gray-300 text-sm">
                        <div className="flex space-x-5">
                            <button
                                onFocus={() => setSelectedPost(post)}
                                onClick={toggleLike}
                                className="flex items-center space-x-1 text-red-500 hover:text-red-600"
                            >
                                <div className="flex items-center gap-1">
                                    {liked ? (
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="red"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="w-5 h-5 cursor-pointer"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M21.752 7.501c0 5.25-9.001 11.25-9.001 11.25S3.75 12.751 3.75 7.501A4.501 4.501 0 018.25 3c1.49 0 2.82.72 3.501 1.837A4.502 4.502 0 0115.25 3a4.501 4.501 0 014.502 4.501z"
                                            />
                                        </svg>
                                    ) : (
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="w-5 h-5 cursor-pointer"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M21.752 7.501c0 5.25-9.001 11.25-9.001 11.25S3.75 12.751 3.75 7.501A4.501 4.501 0 018.25 3c1.49 0 2.82.72 3.501 1.837A4.502 4.502 0 0115.25 3a4.501 4.501 0 014.502 4.501z"
                                            />
                                        </svg>
                                    )}

                                    <span className="text-sm text-gray-600 dark:text-gray-300">
                                        {post.likes.length}
                                    </span>
                                </div>
                            </button>

                            <button
                                
                                onClick={() => setCommentsVisible(!commentsVisible)}
                                className="flex items-center space-x-1 hover:text-blue-500"
                            >
                                <i
                                    className="fa fa-comment"
                                    aria-hidden="true"
                                ></i>
                            </button>
                        </div>
                    </div>

                    {/* Overlapping Avatars of Likes */}
                    <div className="flex items-center justify-between">
                        {/* Likes avatars */}
                        <div className="flex -space-x-2 overflow-hidden">
                            {post.likes?.slice(0, 5).map((like) => {
                                const user = like.user;
                                const initials = `${
                                    user?.first_name?.[0] || ""
                                }${user?.last_name?.[0] || ""}`.toUpperCase();

                                return user?.passport ? (
                                    <img
                                        key={`like-avatar-${like.id}`}
                                        className="inline-block h-7 w-7 rounded-full ring-2 ring-white dark:ring-gray-800 object-cover"
                                        src={user.passport}
                                        alt={user.first_name}
                                        title={`${user?.first_name} ${user?.last_name}`}
                                    />
                                ) : (
                                    <div
                                        key={`like-initial-${like.id}`}
                                        className="inline-flex items-center justify-center h-7 w-7 rounded-full bg-gray-300 dark:bg-gray-600 ring-2 ring-white dark:ring-gray-800 text-xs font-bold text-white"
                                        title={`${user?.first_name} ${user?.last_name}`}
                                    >
                                        {initials}
                                    </div>
                                );
                            })}
                        </div>

                        <div
                            className="ml-4 text-xs text-gray-500 dark:text-gray-400 cursor-pointer"
                            onClick={() => setCommentsVisible(!commentsVisible)}
                        >
                            {comments.length} Comment
                            {comments.length !== 1 ? "s" : ""}
                        </div>
                    </div>
                </div>

                {commentsVisible && (
                    <div className="border-t border-gray-300 dark:border-gray-600 pt-4">
                        <ul className="space-y-4 mb-4">
                            {comments.length > 0 ? (
                                <div className="space-y-4">
                                    {comments.map((comment) => (
                                        <div
                                            key={comment.id}
                                            className="bg-white dark:bg-gray-800 rounded-lg p-3 shadow"
                                        >
                                            {/* Comment header */}
                                            <div className="flex items-start space-x-3">
                                                <img
                                                    src={
                                                        comment.user
                                                            ?.passport ||
                                                        "/avatar1.png"
                                                    }
                                                    alt="avatar"
                                                    className="w-8 h-8 rounded-full"
                                                />
                                                <div className="flex-1">
                                                    <p className="text-sm font-semibold">
                                                        {
                                                            comment.user
                                                                ?.first_name
                                                        }{" "}
                                                        {
                                                            comment.user
                                                                ?.last_name
                                                        }
                                                        <small className="mx-2">
                                                            {formatRelativeTime(
                                                                comment.created_at
                                                            )}
                                                        </small>
                                                    </p>
                                                    <p className="text-sm text-gray-700 dark:text-gray-300">
                                                        {comment.body}
                                                    </p>

                                                    {/* Comment actions */}
                                                    <div className="flex space-x-3 text-xs text-gray-500 mt-1">
                                                        <button
                                                            onClick={() =>
                                                                handleToggleReply(
                                                                    comment.id
                                                                )
                                                            }
                                                        >
                                                            Reply
                                                        </button>
                                                    </div>

                                                    {/* Reply input for this comment */}
                                                    {replySectionFor ===
                                                        comment.id && (
                                                        <form
                                                            onSubmit={(e) =>
                                                                handlePostReply(
                                                                    e,
                                                                    comment.id
                                                                )
                                                            }
                                                            className="mt-2 flex space-x-2"
                                                        >
                                                            <input
                                                                type="text"
                                                                placeholder="Write a reply..."
                                                                value={newReply}
                                                                onChange={(e) =>
                                                                    setNewReply(
                                                                        e.target
                                                                            .value
                                                                    )
                                                                }
                                                                className="flex-1 px-3 py-1 rounded-full border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm"
                                                            />
                                                            <button
                                                                type="submit"
                                                                className="px-3 py-1 bg-yellow-400 rounded-full text-sm font-semibold"
                                                            >
                                                                Send
                                                            </button>
                                                        </form>
                                                    )}

                                                    {/* Replies list */}
                                                    <ul className="mt-3 space-y-2">
                                                        {comment.replies?.map(
                                                            (reply) => (
                                                                <li
                                                                    key={
                                                                        reply.id
                                                                    }
                                                                    className="flex items-start space-x-2"
                                                                >
                                                                    <img
                                                                        src={
                                                                            reply
                                                                                .user
                                                                                ?.passport ||
                                                                            "/avatar1.png"
                                                                        }
                                                                        alt="avatar"
                                                                        className="w-6 h-6 rounded-full"
                                                                    />
                                                                    <div className="bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-1 text-xs">
                                                                        <span className="font-semibold">
                                                                            {
                                                                                reply
                                                                                    .user
                                                                                    ?.first_name
                                                                            }{" "}
                                                                            {
                                                                                reply
                                                                                    .user
                                                                                    ?.last_name
                                                                            }
                                                                        </span>{" "}
                                                                        {formatRelativeTime(
                                                                            reply.created_at
                                                                        )}
                                                                        <p>
                                                                            {
                                                                                reply.body
                                                                            }
                                                                        </p>
                                                                        {/* Reply actions */}
                                                                        <div className="mt-1 text-[10px] text-gray-500 flex space-x-2">
                                                                            <button
                                                                                onClick={() =>
                                                                                    handleLikeReply(
                                                                                        reply.id,
                                                                                        comment.id
                                                                                    )
                                                                                }
                                                                            >
                                                                                <i className="fa fa-heart mr-1"></i>
                                                                                {reply.likes ||
                                                                                    0}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            )
                                                        )}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-gray-500 italic">
                                    No comments yet.
                                </p>
                            )}
                        </ul>

                        {/* Add Comment Input */}
                        <form
                            onSubmit={handleCommentSubmit}
                            className="flex items-center space-x-2 mt-2"
                        >
                            <input
                                type="text"
                                placeholder="Write a comment..."
                                className="flex-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-[#FFD736]"
                                value={newComment}
                                onChange={(e) => setNewComment(e.target.value)}
                            />
                            <button
                                type="submit"
                                className="px-4 py-1.5 bg-[#FFD736] text-gray-800 font-semibold rounded-full hover:bg-yellow-400 transition text-sm"
                            >
                                Post
                            </button>
                        </form>
                    </div>
                )}
            </div>
        </div>
    );
};

export default PostWithComments;
