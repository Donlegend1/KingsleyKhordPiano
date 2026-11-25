import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import { Smile } from "lucide-react";
import { useFlashMessage } from "../Alert/FlashMessageContext";

const commentEmojis = [
    ["😀", "Smile"], ["😂", "Laugh"], ["😍", "Heart eyes"], ["🥳", "Celebrate"],
    ["😢", "Sad"], ["👍", "Thumbs up"], ["🙌", "Raised hands"], ["🔥", "Fire"],
    ["❤️", "Heart"], ["🎹", "Piano"], ["✨", "Sparkles"], ["💯", "One hundred"],
];

const CommentEmojiPicker = ({ inputRef, onChange }) => {
    const [open, setOpen] = useState(false);
    const buttonRef = useRef(null);

    const insertEmoji = (emoji) => {
        const input = inputRef.current;
        if (!input) return;

        const { value, selectionStart, selectionEnd } = input;
        onChange(value.slice(0, selectionStart) + emoji + value.slice(selectionEnd));
        setOpen(false);
        input.focus();
        requestAnimationFrame(() => {
            if (document.activeElement === input) {
                const cursor = selectionStart + emoji.length;
                input.setSelectionRange(cursor, cursor);
            }
        });
    };

    return (
        <div
            className="relative flex-shrink-0"
            onBlur={(e) => {
                if (!e.currentTarget.contains(e.relatedTarget)) setOpen(false);
            }}
            onKeyDown={(e) => {
                if (e.key === "Escape" && open) {
                    e.stopPropagation();
                    setOpen(false);
                    buttonRef.current?.focus();
                }
            }}
        >
            <button
                ref={buttonRef}
                type="button"
                aria-label="Add emoji"
                aria-expanded={open}
                title="Add emoji"
                onClick={(e) => {
                    e.currentTarget.focus();
                    setOpen((previous) => !previous);
                }}
                className="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:bg-gray-100 hover:text-[#1447A6] focus-visible:ring-2 focus-visible:ring-[#1447A6] dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/10"
            >
                <Smile size={18} aria-hidden="true" />
            </button>
            {open && (
                <div role="group" aria-label="Choose an emoji" className="absolute bottom-full left-0 z-10 mb-2 grid w-52 grid-cols-6 rounded-xl border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-600 dark:bg-gray-800">
                    {commentEmojis.map(([emoji, label]) => (
                        <button
                            key={emoji}
                            type="button"
                            aria-label={label}
                            onMouseDown={(e) => e.preventDefault()}
                            onClick={() => insertEmoji(emoji)}
                            className="h-8 w-8 rounded text-lg hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-[#1447A6] dark:hover:bg-gray-700"
                        >
                            {emoji}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
};

export default function LessonDiscussion({ topicId, category }) {
    const [comment, setComment] = useState("");
    const [comments, setComments] = useState([]);
    const [loadingComments, setLoadingComments] = useState(true);
    const [commentSubmitting, setCommentSubmitting] = useState(false);
    const [editingCommentId, setEditingCommentId] = useState(null);
    const [editedComment, setEditedComment] = useState("");
    const [replyText, setReplyText] = useState({});
    const [activeReplyId, setActiveReplyId] = useState(null);
    const [replyEdit, setReplyEdit] = useState(null);
    const [replyActionPending, setReplyActionPending] = useState(false);
    const replyEditInputRef = useRef(null);
    const commentInputRef = useRef(null);
    const editCommentInputRef = useRef(null);
    const replyInputRef = useRef(null);

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const { showMessage } = useFlashMessage();

    useEffect(() => {
        const controller = new AbortController();

        axios.get("/api/member/comments/course", {
            params: { course_id: topicId, category },
            signal: controller.signal,
        }).then(({ data }) => {
            setComments(data.data || []);
        }).catch((error) => {
            if (!axios.isCancel(error)) showMessage("Could not load comments.", "error");
        }).finally(() => {
            if (!controller.signal.aborted) setLoadingComments(false);
        });

        return () => controller.abort();
    }, [category, topicId, showMessage]);

    const handleReplyChange = (commentId, text) => {
        setReplyText((prev) => ({
            ...prev,
            [commentId]: text,
        }));
    };

    const toggleReplyInput = (commentId) => {
        setActiveReplyId((prev) => (prev === commentId ? null : commentId));
    };

    const handleSubmitComment = async (e) => {
        e.preventDefault();
        if (loadingComments || commentSubmitting || !comment.trim()) return;

        setCommentSubmitting(true);
        try {
            const response = await axios.post(
                "/api/member/comments/course",
                {
                    comment: comment,
                    category,
                    course_id: topicId,
                    url: window.location.href,
                },
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );

            setComment("");
            setComments((previous) => [response.data.data, ...previous]);
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
    };

    const handleUpdateComment = async (id) => {
        try {
            const { data } = await axios.put(`/api/member/comment/${id}`, {
                comment: editedComment,
            });
            setComments((previous) => previous.map((item) =>
                item.id === id ? { ...item, ...data.data } : item
            ));
            setEditingCommentId(null);
            showMessage("Comment updated!", "success");
        } catch (err) {
            console.error("Failed to update comment:", err);
            showMessage("Error updating comment!", "error");
        }
    };

    const handleDelete = async (commentId) => {
        try {
            await axios.delete(`/api/member/comment/${commentId}`);
            setEditingCommentId(null);
            setComments((previous) => previous.filter((item) => item.id !== commentId));
            showMessage("Comment deleted!", "success");
        } catch (err) {
            console.error("Failed to update comment:", err);
            showMessage("Error deleting comment!", "error");
        }
    };

    const changeReply = async (commentId, replyId, method) => {
        if (replyActionPending) return;
        if (method === "DELETE" && !window.confirm("Delete this reply?")) return;

        setReplyActionPending(true);
        try {
            const response = await axios({
                method,
                url: `/api/member/comment-replies/${replyId}`,
                data: method === "PUT" ? { comment: replyEdit.text } : undefined,
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });
            setComments((previous) => previous.map((item) => {
                if (item.id !== commentId) return item;

                const replies = method === "DELETE"
                    ? item.replies.filter((reply) => reply.id !== replyId)
                    : item.replies.map((reply) => reply.id === replyId ? { ...reply, ...response.data.data } : reply);
                return { ...item, replies };
            }));
            if (method === "PUT") setReplyEdit(null);
        } catch (error) {
            showMessage(error.response?.data?.message || "Could not save the reply change. Please try again.", "error");
        } finally {
            setReplyActionPending(false);
        }
    };

    const submitReply = async (commentId) => {
        if (replyActionPending || !replyText[commentId]?.trim()) return;
        setReplyActionPending(true);

        try {
            const res = await axios.post(
                `/api/member/comment/${commentId}/reply`,
                {
                    comment: replyText[commentId],
                }
            );

            setComments((previous) => previous.map((item) =>
                item.id === commentId ? { ...item, replies: [...(item.replies || []), res.data.data] } : item
            ));
            setReplyText((prev) => ({ ...prev, [commentId]: "" }));
            setActiveReplyId(null);
            showMessage("Reply submitted!", "success");
        } catch (error) {
            showMessage("Failed to submit reply.", "error");
            console.error("Reply failed", error);
        } finally {
            setReplyActionPending(false);
        }
    };

    return (
        <div className="mt-10 pt-6 border-t border-gray-100 dark:border-white/10 bg-white dark:bg-gray-800 rounded-2xl p-6">
            <div className="flex items-center gap-2.5 mb-5">
                <span className="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400">
                    <i className="fa-regular fa-comments text-sm"></i>
                </span>
                <h3 className="text-base font-bold text-gray-900 dark:text-white">
                    Comments{" "}
                    <span className="text-gray-400 font-medium">
                        ({comments.length})
                    </span>
                </h3>
            </div>

            <form onSubmit={handleSubmitComment} className="mb-8">
                <textarea
                    ref={commentInputRef}
                    className="w-full border border-gray-200 dark:border-white/10 dark:bg-black/20 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-[#1447A6] focus:border-[#1447A6] outline-none transition resize-none"
                    rows="3"
                    placeholder="Write a comment..."
                    value={comment}
                    onChange={(e) => setComment(e.target.value)}
                    required
                ></textarea>
                <div className="mt-3 flex items-center justify-between gap-2">
                    <CommentEmojiPicker inputRef={commentInputRef} onChange={setComment} />
                    <button
                        type="submit"
                        disabled={loadingComments || commentSubmitting || !comment.trim()}
                        className="flex items-center gap-2 bg-[#1447A6] hover:bg-[#0F3A8A] disabled:opacity-60 text-white text-sm font-semibold px-5 py-2.5 rounded-full transition-colors"
                    >
                        <i className="fa-solid fa-paper-plane text-[11px]"></i>
                        {commentSubmitting ? "Posting..." : "Post"}
                    </button>
                </div>
            </form>

            {loadingComments ? (
                <p className="py-4 text-center text-sm text-gray-500 dark:text-gray-400" role="status">
                    Loading comments…
                </p>
            ) : comments.length === 0 ? (
                <div className="py-8 text-center">
                    <p className="text-sm text-gray-400">
                        No comments yet. Be the first to share your thoughts.
                    </p>
                </div>
            ) : (
                <div className="divide-y divide-gray-100 dark:divide-white/10">
                    {comments.map((c) => (
                        <div key={c.id} className="flex gap-3 py-4 first:pt-0">
                            <div className="w-9 h-9 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center text-gray-500 dark:text-gray-300 font-semibold text-xs flex-shrink-0 overflow-hidden">
                                {c.user?.passport ? (
                                    <img
                                        src={c.user.passport}
                                        alt="Avatar"
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    (c.user?.first_name || "U").charAt(0)
                                )}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">
                                        {c.user?.first_name} {c.user?.last_name}
                                    </p>
                                    <span className="text-xs text-gray-500 dark:text-gray-400">
                                        {new Date(c.created_at).toLocaleString()}
                                    </span>
                                    {c.user_id === window.authUser?.id && (
                                        <div className="flex items-center gap-3 ml-auto">
                                            <button
                                                type="button"
                                                onClick={() => handleEdit(c)}
                                                className="text-xs font-medium text-gray-500 hover:text-gray-900 dark:hover:text-white"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => handleDelete(c.id)}
                                                className="text-xs font-medium text-red-500 hover:underline"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    )}
                                </div>

                                {editingCommentId === c.id ? (
                                    <div className="mt-2">
                                        <textarea
                                            className="w-full border border-gray-200 dark:border-white/10 dark:bg-black/20 rounded-lg p-2 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-[#1447A6] focus:border-[#1447A6] outline-none transition resize-none"
                                            ref={editCommentInputRef}
                                            rows="2"
                                            value={editedComment}
                                            onChange={(e) => setEditedComment(e.target.value)}
                                        />
                                        <div className="flex items-center gap-3 mt-1.5">
                                            <CommentEmojiPicker inputRef={editCommentInputRef} onChange={setEditedComment} />
                                            <button
                                                onClick={() => handleUpdateComment(c.id)}
                                                className="bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-xs font-semibold px-3.5 py-1.5 rounded-full transition-colors"
                                            >
                                                Save
                                            </button>
                                            <button
                                                onClick={() => setEditingCommentId(null)}
                                                className="text-xs font-medium text-gray-500 hover:text-gray-900 dark:hover:text-white"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                ) : (
                                    <p className="mt-1 whitespace-pre-wrap break-words text-sm leading-relaxed text-gray-700 dark:text-gray-200">
                                        {c.comment}
                                    </p>
                                )}

                                {c.replies?.length > 0 && (
                                    <div className="mt-3 space-y-2 border-l-2 border-gray-200 pl-3 dark:border-white/15">
                                        {c.replies.map((reply) => (
                                            <div key={reply.id} className="rounded-lg bg-gray-50 px-3 py-2.5 dark:bg-white/5">
                                                <div className="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                                                    <p className="text-sm font-semibold text-gray-900 dark:text-white">
                                                        {reply.user?.first_name || "Member"} {reply.user?.last_name}
                                                    </p>
                                                    {reply.user_id === window.authUser?.id && replyEdit?.id !== reply.id && (
                                                        <div className="flex items-center gap-3">
                                                            <button
                                                                type="button"
                                                                disabled={replyActionPending}
                                                                onClick={() => setReplyEdit({ id: reply.id, text: reply.reply })}
                                                                className="text-xs font-medium text-gray-600 hover:underline disabled:opacity-50 dark:text-gray-300"
                                                            >
                                                                Edit
                                                            </button>
                                                            <button
                                                                type="button"
                                                                disabled={replyActionPending}
                                                                onClick={() => changeReply(c.id, reply.id, "DELETE")}
                                                                className="text-xs font-medium text-red-700 hover:underline disabled:opacity-50 dark:text-red-400"
                                                            >
                                                                Delete
                                                            </button>
                                                        </div>
                                                    )}
                                                </div>
                                                {replyEdit?.id === reply.id ? (
                                                    <form
                                                        className="mt-2"
                                                        onSubmit={(event) => {
                                                            event.preventDefault();
                                                            if (replyEdit.text.trim()) changeReply(c.id, reply.id, "PUT");
                                                        }}
                                                    >
                                                        <textarea
                                                            ref={replyEditInputRef}
                                                            aria-label="Edit reply"
                                                            rows="2"
                                                            value={replyEdit.text}
                                                            onChange={(event) => setReplyEdit({ id: reply.id, text: event.target.value })}
                                                            className="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:ring-1 focus:ring-[#1447A6] dark:border-white/10 dark:bg-black/20 dark:text-white"
                                                        />
                                                        <div className="mt-1 flex items-center gap-3">
                                                            <CommentEmojiPicker
                                                                inputRef={replyEditInputRef}
                                                                onChange={(text) => setReplyEdit({ id: reply.id, text })}
                                                            />
                                                            <button type="submit" disabled={replyActionPending || !replyEdit.text.trim()} className="rounded-full bg-[#1447A6] px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-[#0F3A8A] disabled:opacity-50">
                                                                Save
                                                            </button>
                                                            <button type="button" disabled={replyActionPending} onClick={() => setReplyEdit(null)} className="text-xs font-medium text-gray-600 hover:underline disabled:opacity-50 dark:text-gray-300">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </form>
                                                ) : (
                                                    <p className="mt-1 whitespace-pre-wrap break-words text-sm leading-relaxed text-gray-700 dark:text-gray-200">
                                                        {reply.reply}
                                                    </p>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}

                                <div className="mt-2">
                                    <button
                                        type="button"
                                        className="text-xs font-semibold text-[#1447A6] hover:underline"
                                        onClick={() => toggleReplyInput(c.id)}
                                    >
                                        {activeReplyId === c.id ? "Cancel" : "Reply"}
                                    </button>

                                    {activeReplyId === c.id && (
                                        <div className="mt-2 flex items-center gap-2">
                                            <CommentEmojiPicker
                                                inputRef={replyInputRef}
                                                onChange={(value) => handleReplyChange(c.id, value)}
                                            />
                                            <input
                                                ref={replyInputRef}
                                                type="text"
                                                value={replyText[c.id] || ""}
                                                onChange={(e) =>
                                                    handleReplyChange(c.id, e.target.value)
                                                }
                                                placeholder="Write a reply..."
                                                className="min-w-0 flex-1 border border-gray-200 dark:border-white/10 dark:bg-black/20 rounded-full px-3.5 py-1.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-[#1447A6] focus:border-[#1447A6] outline-none transition"
                                            />
                                            <button
                                                type="button"
                                                disabled={replyActionPending || !replyText[c.id]?.trim()}
                                                onClick={() => submitReply(c.id)}
                                                className="bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-xs font-semibold px-3.5 py-1.5 rounded-full transition-colors flex-shrink-0 disabled:opacity-50"
                                            >
                                                Send
                                            </button>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
