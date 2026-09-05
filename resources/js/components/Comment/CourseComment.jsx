import ReactDOM from "react-dom/client";
import React, { useEffect, useMemo, useState } from "react";
import axios from "axios";
import { EllipsisVertical, MessageSquare, Pencil, Reply, Send, Trash2 } from "lucide-react";

import {
    FlashMessageProvider,
    useFlashMessage,
} from "../Alert/FlashMessageContext";
import { Input } from "../ui/input";
import { Textarea } from "../ui/textarea";

const buttonBase =
    "inline-flex items-center justify-center rounded-full text-sm font-medium transition focus:outline-none disabled:pointer-events-none disabled:opacity-40";
const primaryButton = `${buttonBase} bg-gray-900 px-4 py-2 text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100`;
const secondaryButton = `${buttonBase} rounded-lg border border-gray-200 bg-white px-3 py-2 text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:bg-[#161617] dark:text-gray-200 dark:hover:bg-white/5`;
const ghostButton = `${buttonBase} rounded-lg px-2.5 py-1.5 text-gray-400 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-500 dark:hover:bg-white/5 dark:hover:text-white`;

function formatTimestamp(value) {
    if (!value) return "";

    try {
        return new Date(value).toLocaleString();
    } catch {
        return "";
    }
}

function CommentCard({
    comment,
    activeMenuId,
    editingCommentId,
    editedComment,
    activeReplyId,
    replyValue,
    onToggleMenu,
    onEditStart,
    onEditCancel,
    onEditChange,
    onUpdateComment,
    onDeleteComment,
    onToggleReply,
    onReplyChange,
    onReplySubmit,
}) {
    const isEditing = editingCommentId === comment.id;
    const isReplying = activeReplyId === comment.id;
    const menuOpen = activeMenuId === comment.id;

    return (
        <article className="rounded-2xl border border-gray-100 bg-white p-4 dark:border-white/10 dark:bg-[#161617]">
            <div className="flex items-start justify-between gap-4">
                <div className="flex min-w-0 items-center gap-3">
                    <img
                        src={comment.user?.passport || "/avatar1.jpg"}
                        alt="Avatar"
                        className="h-10 w-10 rounded-full object-cover ring-1 ring-gray-100 dark:ring-white/10"
                    />
                    <div className="min-w-0">
                        <p className="truncate text-sm font-semibold text-gray-900 dark:text-white">
                            {comment.user?.first_name} {comment.user?.last_name}
                        </p>
                        <p className="text-xs text-gray-400 dark:text-gray-500">
                            {formatTimestamp(comment.created_at)}
                        </p>
                    </div>
                </div>

                <div className="relative shrink-0">
                    <button
                        type="button"
                        onClick={() => onToggleMenu(comment.id)}
                        className={ghostButton}
                    >
                        <EllipsisVertical className="h-4 w-4" />
                    </button>

                    {menuOpen && (
                        <div className="absolute right-0 z-10 mt-2 w-32 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-lg dark:border-white/10 dark:bg-[#1c1c1d]">
                            <button
                                type="button"
                                onClick={() => onEditStart(comment)}
                                className="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5"
                            >
                                <Pencil className="h-4 w-4" />
                                Edit
                            </button>
                            <button
                                type="button"
                                onClick={() => onDeleteComment(comment.id)}
                                className="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-600 transition hover:bg-red-50 dark:hover:bg-red-950/40"
                            >
                                <Trash2 className="h-4 w-4" />
                                Delete
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {isEditing ? (
                <div className="mt-4 space-y-3">
                    <Textarea
                        className="min-h-[96px]"
                        value={editedComment}
                        onChange={(event) => onEditChange(event.target.value)}
                    />
                    <div className="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            onClick={() => onUpdateComment(comment.id)}
                            className={primaryButton}
                        >
                            Save changes
                        </button>
                        <button
                            type="button"
                            onClick={onEditCancel}
                            className={secondaryButton}
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            ) : (
                <p className="mt-4 text-sm leading-6 text-gray-700 dark:text-gray-200">
                    {comment.comment}
                </p>
            )}

            {comment.replies?.length > 0 && (
                <div className="mt-4 space-y-3 border-t border-gray-100 pt-4 dark:border-white/10">
                    {comment.replies.map((reply) => (
                        <div
                            key={reply.id}
                            className="rounded-xl bg-gray-50 px-3 py-3 text-sm text-gray-700 dark:bg-white/5 dark:text-gray-300"
                        >
                            <span className="font-semibold text-gray-900 dark:text-white">
                                {reply.user?.first_name}:
                            </span>{" "}
                            {reply.reply}
                        </div>
                    ))}
                </div>
            )}

            <div className="mt-4">
                <button
                    type="button"
                    onClick={() => onToggleReply(comment.id)}
                    className="inline-flex items-center gap-2 text-xs font-medium text-gray-500 transition hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    <Reply className="h-3.5 w-3.5" />
                    {isReplying ? "Cancel reply" : "Reply"}
                </button>

                {isReplying && (
                    <div className="mt-3 flex flex-col gap-2 sm:flex-row">
                        <Input
                            type="text"
                            value={replyValue}
                            onChange={(event) => onReplyChange(comment.id, event.target.value)}
                            placeholder="Write a reply..."
                            className="h-10 flex-1"
                        />
                        <button
                            type="button"
                            onClick={() => onReplySubmit(comment.id)}
                            className={`${primaryButton} gap-2 px-4 py-2`}
                        >
                            <Send className="h-4 w-4" />
                            Submit
                        </button>
                    </div>
                )}
            </div>
        </article>
    );
}

const CourseComment = ({ course, group }) => {
    const [comment, setComment] = useState("");
    const [comments, setComments] = useState([]);
    const [commentSubmitting, setCommentSubmitting] = useState(false);
    const [activeMenuId, setActiveMenuId] = useState(null);
    const [editingCommentId, setEditingCommentId] = useState(null);
    const [editedComment, setEditedComment] = useState("");
    const [replyText, setReplyText] = useState({});
    const [activeReplyId, setActiveReplyId] = useState(null);

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    const { showMessage } = useFlashMessage();

    const resolvedCourse = useMemo(() => {
        if (course) return course;
        if (typeof window !== "undefined" && window.uploadData) return window.uploadData;
        return null;
    }, [course]);

    const resolvedGroup = group || "course";

    useEffect(() => {
        if (resolvedCourse?.id) {
            fetchComments();
        }
    }, [resolvedCourse?.id, resolvedGroup]);

    const fetchComments = async () => {
        if (!resolvedCourse?.id) return;

        try {
            const response = await axios.get(
                `/api/member/comments/course?category=${resolvedGroup}&course_id=${resolvedCourse.id}`,
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

    const handleMenuToggle = (commentId) => {
        setActiveMenuId((current) => (current === commentId ? null : commentId));
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

    const handleSubmitComment = async (event) => {
        event.preventDefault();
        if (!comment.trim() || !resolvedCourse?.id) return;

        setCommentSubmitting(true);
        try {
            await axios.post(
                `/api/member/course/${resolvedCourse.id}/video-comment`,
                {
                    comment,
                    category: resolvedGroup,
                    course_id: resolvedCourse.id,
                    url: `/member/course/${resolvedCourse.level}?course_id=${resolvedCourse.id}`,
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

    const handleEdit = (selectedComment) => {
        setEditingCommentId(selectedComment.id);
        setEditedComment(selectedComment.comment);
        setActiveMenuId(null);
    };

    const handleUpdateComment = async (id) => {
        try {
            await axios.put(`/api/member/comment/${id}`, {
                comment: editedComment,
            });
            setEditingCommentId(null);
            setEditedComment("");
            showMessage("Comment updated!", "success");
            fetchComments();
        } catch (error) {
            console.error("Failed to update comment:", error);
            showMessage("Error updating comment!", "error");
        }
    };

    const handleDelete = async (commentId) => {
        try {
            await axios.delete(`/api/member/comment/${commentId}`);
            setEditingCommentId(null);
            setActiveMenuId(null);
            showMessage("Comment deleted!", "success");
            fetchComments();
        } catch (error) {
            console.error("Failed to delete comment:", error);
            showMessage("Error deleting comment!", "error");
        }
    };

    const submitReply = async (commentId) => {
        if (!replyText[commentId]?.trim()) return;

        try {
            await axios.post(`/api/member/comment/${commentId}/reply`, {
                comment: replyText[commentId],
            });

            setReplyText((prev) => ({ ...prev, [commentId]: "" }));
            setActiveReplyId(null);
            showMessage("Reply submitted!", "success");
            fetchComments();
        } catch (error) {
            showMessage("Failed to submit reply.", "error");
            console.error("Reply failed", error);
        }
    };

    if (!resolvedCourse?.id) {
        return null;
    }

    return (
        <section className="mt-3 rounded-2xl border border-gray-100 bg-white p-5 dark:border-white/10 dark:bg-[#161617] sm:p-6">
            <div className="flex items-center gap-2.5">
                <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-50 text-gray-500 dark:bg-white/5 dark:text-gray-400">
                    <MessageSquare className="h-4 w-4" />
                </span>
                <div>
                    <h3 className="text-sm font-semibold text-gray-900 dark:text-white">Comments</h3>
                    <p className="text-xs text-gray-400 dark:text-gray-500">
                        Share your thoughts, questions, and follow-up replies.
                    </p>
                </div>
            </div>

            <form onSubmit={handleSubmitComment} className="mt-5 space-y-3">
                <Textarea
                    className="min-h-[110px] rounded-xl dark:border-white/10"
                    rows="4"
                    placeholder="Write a comment..."
                    value={comment}
                    onChange={(event) => setComment(event.target.value)}
                />
                <div className="flex justify-end">
                    <button
                        type="submit"
                        disabled={commentSubmitting || !comment.trim()}
                        className={`${primaryButton} gap-2 px-5 py-2.5`}
                    >
                        <Send className="h-4 w-4" />
                        {commentSubmitting ? "Posting..." : "Post Comment"}
                    </button>
                </div>
            </form>

            <div className="mt-6 space-y-3">
                {comments.length === 0 ? (
                    <div className="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-10 text-center dark:border-white/10 dark:bg-white/[0.03]">
                        <MessageSquare className="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" />
                        <p className="mt-3 text-sm font-medium text-gray-500 dark:text-gray-400">
                            No comments yet.
                        </p>
                    </div>
                ) : (
                    comments.map((entry) => (
                        <CommentCard
                            key={entry.id}
                            comment={entry}
                            activeMenuId={activeMenuId}
                            editingCommentId={editingCommentId}
                            editedComment={editedComment}
                            activeReplyId={activeReplyId}
                            replyValue={replyText[entry.id] || ""}
                            onToggleMenu={handleMenuToggle}
                            onEditStart={handleEdit}
                            onEditCancel={() => {
                                setEditingCommentId(null);
                                setEditedComment("");
                            }}
                            onEditChange={setEditedComment}
                            onUpdateComment={handleUpdateComment}
                            onDeleteComment={handleDelete}
                            onToggleReply={toggleReplyInput}
                            onReplyChange={handleReplyChange}
                            onReplySubmit={submitReply}
                        />
                    ))
                )}
            </div>
        </section>
    );
};

export default CourseComment;

if (document.getElementById("comments")) {
    const root = ReactDOM.createRoot(document.getElementById("comments"));

    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <CourseComment />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
