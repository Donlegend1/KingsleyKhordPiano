import ReactDOM from "react-dom/client";
import React, { useState, useEffect } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";


const OtherComment = () => {

const [lesson, setLesson] = useState(null);

  useEffect(() => {
    if (window.lessonData) {
      setLesson(window.lessonData);
    }
  }, []);
    const lastSegment = window.location.pathname
        .split("/")
        .filter(Boolean)
        .pop();

    const [comment, setComment] = useState("");
    const [comments, setComments] = useState([]);
    const [commentSubmitting, setCommentSubmitting] = useState(false);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    const { showMessage } = useFlashMessage();

    const [activeMenuId, setActiveMenuId] = useState(null);
    const [editingCommentId, setEditingCommentId] = useState(null);
    const [editedComment, setEditedComment] = useState("");
    const [replyText, setReplyText] = useState({});
    const [activeReplyId, setActiveReplyId] = useState(null);

    const handleMenuToggle = (commentId) => {
        setActiveMenuId(activeMenuId === commentId ? null : commentId);
    };

    const handleReplyChange = (commentId, text) => {
        setReplyText((prev) => ({
            ...prev,
            [commentId]: text,
        }));
    };

    const toggleReplyInput = (commentId) => {
        setActiveReplyId((prev) => (prev === commentId ? null : commentId));
    };

    useEffect(() => {
        fetchComments();
    }, [lesson]);

    const fetchComments = async () => {
        try {
            const response = await axios.get(
                `/api/member/comments/course?category=others&course_id=${lastSegment}`,
                {
                    headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
                );
            setComments(response.data?.data || []);
        } catch (error) {
            console.error("Failed to fetch comments", error.response);
        }
    };


    const handleSubmitComment = async (e) => {
        e.preventDefault();

        console.log("Submitting comment:", lesson);


        setCommentSubmitting(true);
        try {
            const response = await axios.post(
                `/api/member/course/${lastSegment}/video-comment`,
                {
                    comment: comment,
                    category: 'others', // Assuming category is quiz for this context
                    course_id: lastSegment,
                },
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );

            setComment("");
            fetchComments();
            showMessage("Comment added!", "success");
        } catch (error) {
            console.error("Error submitting comment", error);
            showMessage("Failed to submit comment.", "error");
        } finally {
            setCommentSubmitting(false);
        }
    };

    const handleEdit = (comment) => {
        setEditingCommentId(comment.id);
        setEditedComment(comment.comment);
        setActiveMenuId(null); // Close menu
    };

    const handleUpdateComment = async (id) => {
        try {
            await axios.put(`/api/member/comment/${id}`, {
                comment: editedComment,
            });
            setEditingCommentId(null);
            showMessage("Comment updated!", "success");
            fetchComments();
        } catch (err) {
            console.error("Failed to update comment:", err);
            showMessage("Error updating comment!", "error");
        }
    };

    const handleDelete = async (commentId) => {
        try {
            await axios.delete(`/api/member/comment/${commentId}`);
            setEditingCommentId(null);
            showMessage("Comment deleted!", "success");
            fetchComments();
        } catch (err) {
            console.error("Failed to update comment:", err);
            showMessage("Error deleting comment!", "error");
        }
    };

    const submitReply = async (commentId) => {
        if (!replyText[commentId]) return;

        try {
            const res = await axios.post(
                `/api/member/comment/${commentId}/reply`,
                {
                    comment: replyText[commentId],
                }
            );

            // Optional: Update UI
            const updated = comments.map((c) => {
                if (c.id === commentId) {
                    return {
                        ...c,
                        replies: [...(c.replies || []), res.data],
                    };
                }
                return c;
            });

            setComments(updated);
            setReplyText((prev) => ({ ...prev, [commentId]: "" }));
            setActiveReplyId(null);
            showMessage("Reply submitted!", "success");
            fetchComments();
        } catch (error) {
            showMessage("Failed to submit reply.", "error");
            console.error("Reply failed", error);
        }
    };

    return (
        <div className="p-6 bg-white dark:bg-black rounded shadow-lg">

            {/* Comment Section */}
            <div className="mt-10">
                <h3 className="font-semibold text-lg mb-2 text-gray-800 dark:text-gray-100">
                    Comments
                </h3>

                <form onSubmit={handleSubmitComment} className="mb-4">
                    <textarea
                        className="w-full p-2 border rounded shadow-sm focus:outline-none focus:ring 
                       bg-white text-gray-900 dark:bg-gray-900 dark:text-white 
                       dark:border-gray-700"
                        rows="3"
                        placeholder="Write a comment..."
                        value={comment}
                        onChange={(e) => setComment(e.target.value)}
                    ></textarea>
                    <button
                        type="submit"
                        disabled={commentSubmitting}
                        className="mt-2 px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        <span className="fa fa-paper-plane mr-2"></span>
                        Post Comment
                    </button>
                </form>

                <div className="space-y-4">
                    {comments.length === 0 ? (
                        <div className="text-center text-gray-500 dark:text-gray-400">
                            <i className="fa fa-comments fa-2x mb-2"></i>
                            <p>No comments yet.</p>
                        </div>
                    ) : (
                        comments.map((c) => (
                            <div
                                key={c.id}
                                className="border p-3 rounded shadow-sm relative 
                               bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-100 
                               dark:border-gray-700"
                            >
                                {/* Header */}
                                <div className="flex items-start justify-between">
                                    <div className="flex items-center gap-3">
                                        <img
                                            src={
                                                c.user?.passport ||
                                                "/avatar1.jpg"
                                            }
                                            alt="Avatar"
                                            className="w-10 h-10 rounded-full object-cover"
                                        />
                                        <div>
                                            <div className="font-semibold">
                                                {c.user?.first_name}{" "}
                                                {c.user?.last_name}
                                            </div>
                                            <div className="text-xs text-gray-500 dark:text-gray-400">
                                                {new Date(
                                                    c.created_at
                                                ).toLocaleString()}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Menu */}
                                    <div className="relative">
                                        <button
                                            className="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white"
                                            onClick={() =>
                                                handleMenuToggle(c.id)
                                            }
                                        >
                                            <i className="fa fa-ellipsis-v"></i>
                                        </button>

                                        {activeMenuId === c.id && (
                                            <div
                                                className="absolute right-0 mt-2 bg-white dark:bg-gray-900 
                                                border dark:border-gray-700 rounded shadow-md z-10 w-32"
                                            >
                                                <button
                                                    onClick={() =>
                                                        handleEdit(c)
                                                    }
                                                    className="block w-full px-4 py-2 text-left text-sm 
                                                   hover:bg-gray-100 dark:hover:bg-gray-700"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDelete(c.id)
                                                    }
                                                    className="block w-full px-4 py-2 text-left text-sm 
                                                   text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                {/* Comment Text or Edit */}
                                {editingCommentId === c.id ? (
                                    <div className="mt-2 space-y-2">
                                        <textarea
                                            className="w-full border px-2 py-1 text-sm rounded 
                                           bg-white dark:bg-gray-900 
                                           text-gray-900 dark:text-white 
                                           dark:border-gray-700"
                                            value={editedComment}
                                            onChange={(e) =>
                                                setEditedComment(e.target.value)
                                            }
                                        />
                                        <div className="flex items-center gap-2">
                                            <button
                                                onClick={() =>
                                                    handleUpdateComment(c.id)
                                                }
                                                className="text-blue-600 text-sm"
                                            >
                                                Save
                                            </button>
                                            <button
                                                onClick={() =>
                                                    setEditingCommentId(null)
                                                }
                                                className="text-gray-500 dark:text-gray-300 text-sm"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="mt-2 text-sm text-gray-700 dark:text-gray-200">
                                        {c.comment}
                                    </div>
                                )}

                                {/* Replies */}
                                {c.replies && c.replies.length > 0 && (
                                    <div className="mt-4 border-t pt-2 space-y-2 pl-5 dark:border-gray-700">
                                        {c.replies.map((reply) => (
                                            <div
                                                key={reply.id}
                                                className="text-sm text-gray-600 dark:text-gray-300"
                                            >
                                                <strong>
                                                    {reply.user?.first_name}:
                                                </strong>{" "}
                                                {reply.reply}
                                            </div>
                                        ))}
                                    </div>
                                )}

                                {/* Reply input */}
                                <div className="pl-5 mt-2">
                                    <button
                                        className="text-xs text-blue-600"
                                        onClick={() => toggleReplyInput(c.id)}
                                    >
                                        {activeReplyId === c.id
                                            ? "Cancel"
                                            : "Reply"}
                                    </button>

                                    {activeReplyId === c.id && (
                                        <div className="mt-2 space-y-2">
                                            <input
                                                type="text"
                                                value={replyText[c.id] || ""}
                                                onChange={(e) =>
                                                    handleReplyChange(
                                                        c.id,
                                                        e.target.value
                                                    )
                                                }
                                                placeholder="Write a reply..."
                                                className="w-full border rounded px-2 py-1 text-sm 
                                               bg-white dark:bg-gray-900 
                                               text-gray-900 dark:text-white 
                                               dark:border-gray-700"
                                            />
                                            <button
                                                onClick={() =>
                                                    submitReply(c.id)
                                                }
                                                className="text-sm text-white bg-blue-500 px-3 py-1 rounded"
                                            >
                                                Submit
                                            </button>
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </div>
    );
};

export default OtherComment;

if (document.getElementById("comment-section")) {
    const Index = ReactDOM.createRoot(document.getElementById("comment-section"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <OtherComment />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}