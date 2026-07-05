import ReactDOM from "react-dom/client";
import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import { Bookmark, BookmarkMinus, Check } from "lucide-react";

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

const CourseDetails = ({
    course,
    onComplete,
    onSelectCourse,
    onPrevLesson,
    onNextLesson,
    hasPrevLesson,
    hasNextLesson,
}) => {
    const [loading, setLoading] = useState(false);
    const [comment, setComment] = useState("");
    const [comments, setComments] = useState([]);
    const [commentSubmitting, setCommentSubmitting] = useState(false);
    const [isBookmarked, setIsBookmarked] = useState(
        course.isBookmarked || false
    );
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    const { showMessage } = useFlashMessage();

    const [activeMenuId, setActiveMenuId] = useState(null);
    const [editingCommentId, setEditingCommentId] = useState(null);
    const [editedComment, setEditedComment] = useState("");
    const [replyText, setReplyText] = useState({});
    const [activeReplyId, setActiveReplyId] = useState(null);
    const iframeWrapperRef = useRef(null);

    // The injected iframe embed (e.g. VdoCipher) ships with fixed inline
    // width/height — override it so it fills the responsive aspect-ratio box.
    useEffect(() => {
        if (course.video_type === "iframe" && iframeWrapperRef.current) {
            const iframe = iframeWrapperRef.current.querySelector("iframe");
            if (iframe) {
                iframe.style.position = "absolute";
                iframe.style.top = "0";
                iframe.style.left = "0";
                iframe.style.width = "100%";
                iframe.style.height = "100%";
                iframe.style.border = "0";
            }
        }
    }, [course.video_type, course.video_url]);

    const handleMenuToggle = (commentId) => {
        setActiveMenuId(activeMenuId === commentId ? null : commentId);
    };

    const toggleBookmark = async () => {
        try {
            await axios.post(
                `/member/bookmark/toggle`,
                {
                    bookmarkable_id: course.id,
                    bookmarkable_type: "courses",
                },
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );
            setIsBookmarked(!isBookmarked);
            showMessage(
                !isBookmarked
                    ? "Added to bookmarks!"
                    : "Removed from bookmarks!",
                "success"
            );
        } catch (err) {
            console.error("Bookmark toggle failed:", err);
            showMessage("Error toggling bookmark", "error");
        }
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
    }, [course.id]);

    useEffect(() => {
        setIsBookmarked(course.isBookmarked || false);
    }, [course]);

    const fetchComments = async () => {
        try {
            const response = await axios.get(
                `/api/member/comments/course?course_id=${course.id}&category=course`,
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

    const handleMarkAsCompleted = async () => {
        setLoading(true);
        try {
            await axios.post(
                `/member/course/${course.id}/complete`,
                {},
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );
            showMessage("Marked as completed!", "success");
            onComplete(course);
        } catch (error) {
            console.error("Error marking course:", error);
            showMessage("Failed to mark as completed.", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleSubmitComment = async (e) => {
        e.preventDefault();
        if (!comment.trim()) return;

        setCommentSubmitting(true);
        try {
            const response = await axios.post(
                `/api/member/course/${course.id}/video-comment`,
                {
                    comment: comment,
                    category: "course",
                    course_id: course.id,
                    url: window.location.href,
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

    const renderVideoPlayer = () => {
        if (!course.video_type || !course.video_url) {
            return course.thumbnail_url || course.thumbnail ? (
                <div className="mb-4 w-full h-[400px] rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <img
                        src={course.thumbnail_url || course.thumbnail}
                        alt={course.title}
                        className="w-full h-full object-cover"
                    />
                </div>
            ) : (
                <div className="mb-4 text-gray-500">No video available.</div>
            );
        }

        switch (course.video_type) {
            case "iframe":
                return (
                    <div className="relative w-full aspect-video rounded overflow-hidden shadow-lg bg-gray-100 mb-4">
                        <div
                            ref={iframeWrapperRef}
                            className="absolute inset-0"
                            dangerouslySetInnerHTML={{ __html: course.video_url }}
                        />
                    </div>
                );

            case "google":
                return (
                    <iframe
                        width="100%"
                        height="400"
                        src={`https://drive.google.com/file/d/${course.video_url}/preview`}
                        allow="autoplay"
                        allowFullScreen
                        className="rounded"
                    />
                );

            case "youtube":
                return (
                    <iframe
                        width="100%"
                        height="400"
                        src={`https://www.youtube.com/embed/${course.video_url}`}
                        title="YouTube video"
                        frameBorder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                        className="rounded"
                    />
                );

            case "local":
                return (
                    <video
                        controls
                        width="100%"
                        height="400"
                        className="rounded"
                        poster={course.thumbnail_url || course.thumbnail}
                    >
                        <source
                            src={`/uploads/videos/${course.video_url}`}
                            type="video/mp4"
                        />
                        Your browser does not support HTML5 video.
                    </video>
                );

            default:
                return (
                    <div className="mb-4 text-gray-500">
                        No video available.
                    </div>
                );
        }
    };    

    // console.log(lastSegment, 'lastSegment')

    return (
        <div className="p-6 bg-white dark:bg-black rounded shadow-lg">
            <div className="mb-6">
                {/* Course Title */}
                <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                    {course.title}
                </h2>
            </div>

            <div class="mb-4">{renderVideoPlayer()}</div>

            {course.description && (
                <div className="mt-4 mb-4 text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                    {course.description}
                </div>
            )}

            {course.image_urls && course.image_urls.length > 0 && (
                <div className="mt-6 mb-6">
                    <h4 className="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider mb-3">Course Walkthrough / Highlights</h4>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {course.image_urls.map((url, idx) => (
                            <div key={idx} className="rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm bg-gray-50 dark:bg-gray-900">
                                <img src={url} alt="Description Highlight" className="w-full h-auto object-cover hover:scale-105 transition-transform duration-300" />
                            </div>
                        ))}
                    </div>
                </div>
            )}

            <div className="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 mt-6">
                <button
                    onClick={toggleBookmark}
                    className={`px-5 py-2.5 rounded-full text-sm font-semibold flex items-center justify-center gap-2 border transition duration-200 whitespace-nowrap ${
                        isBookmarked
                            ? "border-indigo-200 bg-indigo-50 text-indigo-600 hover:bg-indigo-100"
                            : "border-gray-200 bg-white text-gray-700 hover:bg-gray-50"
                    }`}
                >
                    {isBookmarked ? (
                        <Bookmark className="w-4 h-4" />
                    ) : (
                        <BookmarkMinus className="w-4 h-4" />
                    )}
                    <span>{isBookmarked ? "Bookmarked" : "Bookmark"}</span>
                </button>

                <button
                    onClick={handleMarkAsCompleted}
                    disabled={loading || course.progress?.course_id}
                    className={`px-5 py-2.5 rounded-full text-sm font-semibold flex items-center justify-center gap-2 transition duration-200 whitespace-nowrap ${
                        course.progress?.course_id
                            ? "bg-green-500 text-white cursor-not-allowed"
                            : "bg-gray-900 text-white hover:bg-gray-800"
                    }`}
                >
                    <Check className="w-4 h-4 flex-shrink-0" />
                    {course.progress?.course_id
                        ? "Completed"
                        : "Mark as Completed"}
                </button>
            </div>

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
                                    {c.user_id === window.authUser?.id && (
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
                                    )}
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

            {/* Lesson Navigation */}
            <div className="flex items-center justify-between mt-8 border-t pt-6 dark:border-gray-700">
                <button
                    onClick={onPrevLesson}
                    disabled={!hasPrevLesson}
                    className="px-5 py-2.5 rounded-full text-sm font-semibold flex items-center gap-2 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    <i className="fa fa-chevron-left"></i>
                    Previous
                </button>
                <button
                    onClick={onNextLesson}
                    disabled={!hasNextLesson}
                    className="px-5 py-2.5 rounded-full text-sm font-semibold flex items-center gap-2 bg-gray-900 text-white hover:bg-gray-800 transition disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    Next
                    <i className="fa fa-chevron-right"></i>
                </button>
            </div>

            {/* Downloads for this lesson */}
            {course.pdf_resource_url && (
                <div className="mt-8 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div className="bg-gray-900 dark:bg-black px-5 py-3">
                        <h3 className="text-white text-sm font-bold tracking-wide uppercase">
                            Downloads for this lesson
                        </h3>
                    </div>
                    <div className="bg-gray-50 dark:bg-gray-800 px-5 py-4 flex items-center gap-4">
                        <div className="w-16 h-10 rounded bg-gray-400 dark:bg-gray-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            PDF
                        </div>
                        <a
                            href={course.pdf_resource_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-red-500 hover:text-red-600 font-bold"
                        >
                            View the Chart
                        </a>
                    </div>
                </div>
            )}

            {/* Related Courses Section */}
            {course.related && course.related.length > 0 && (
                <div className="mt-12 border-t pt-8">
                    <h3 className="text-xl font-bold mb-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <i className="fa fa-link text-blue-500"></i>
                        Related Courses
                    </h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {course.related.map((related) => (
                            <div
                                key={related.id}
                                onClick={() => onSelectCourse(related)}
                                className="group cursor-pointer bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300"
                            >
                                <div className="aspect-video relative overflow-hidden bg-gray-200 dark:bg-gray-700">
                                    {related.thumbnail ? (
                                        <img
                                            src={related.thumbnail}
                                            alt={related.title}
                                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-gray-400">
                                            <i className="fa fa-play-circle fa-3x"></i>
                                        </div>
                                    )}
                                    <div className="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition-colors" />
                                    <div className="absolute bottom-2 right-2 px-2 py-1 bg-black/60 text-white text-[10px] rounded uppercase font-bold">
                                        {related.level}
                                    </div>
                                </div>
                                <div className="p-4">
                                    <h4 className="font-semibold text-gray-900 dark:text-white line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {related.title}
                                    </h4>
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {related.category}
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
};

const CoursesPage = () => {
    const searchParams = new URLSearchParams(window.location.search);
    const selectedCourseId = searchParams.get("selected_course");
    const [courses, setCourses] = useState([]);
    const [selectedCourse, setSelectedCourse] = useState(null);
    const [expandedCategories, setExpandedCategories] = useState({});
    const [showCourseModal, setShowCourseModal] = useState(false);
    const [darkMode, setDarkMode] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const contentScrollRef = useRef(null);

    useEffect(() => {
        const isDark = localStorage.getItem("darkMode") === "true";
        if (isDark) {
            document.documentElement.classList.add("dark");
            setDarkMode(true);
        }
    }, []);

    const handleSidebarToggle = () => {
        setSidebarCollapsed((prev) => !prev);
    };
    const lastSegment = window.location.pathname
        .split("/")
        .filter(Boolean)
        .pop();

    useEffect(() => {
        const fetchCourses = async () => {
            try {
                const response = await axios.get(
                    `/api/member/courses/${lastSegment}`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        withCredentials: true,
                    }
                );

                console.log(response.data);
                setCourses(response.data);

                // On first load, show the mobile course list by default and
                // auto-expand just the first category so users immediately
                // see lessons without an extra tap.
                if (response.data.length > 0) {
                    setExpandedCategories((prev) => ({
                        ...prev,
                        __mobile: true,
                        [response.data[0].category]: true,
                    }));
                }
            } catch (error) {
                console.error("Error fetching courses:", error);
            }
        };
        fetchCourses();
    }, [lastSegment]);

    const toggleCategory = (category) => {
        setExpandedCategories((prev) => ({
            ...prev,
            [category]: !prev[category],
        }));
    };

    const handleCourseCompletion = (completedCourse) => {
        setCourses((prevCourses) =>
            prevCourses.map((cat) =>
                cat.category === completedCourse.category
                    ? {
                          ...cat,
                          courses: cat.courses.map((course) =>
                              course.id === completedCourse.id
                                  ? { ...course, progress: { course_id: course.id } }
                                  : course
                          ),
                      }
                    : cat
            )
        );

        setSelectedCourse((prev) =>
            prev && prev.id === completedCourse.id
                ? { ...prev, progress: { course_id: prev.id } }
                : prev
        );
    };

    const calculateGeneralProgress = () => {
        if (!Array.isArray(courses)) return 0;
        const allCourses = courses.flatMap((cat) => cat.courses || []);
        const total = allCourses.length;
        const completed = allCourses.filter((course) => course.progress).length;
        return total === 0 ? 0 : Math.round((completed / total) * 100);
    };

    const FolderIcon = ({ className }) => (
        <svg className={className} viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
            <path d="M3 7a2 2 0 0 1 2-2h3.5l2 2H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" />
        </svg>
    );

    const FileIcon = ({ className }) => (
        <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z" />
            <path d="M14 2v6h6" />
            <path d="M9 13h6M9 17h6" />
        </svg>
    );

    const ChevronIcon = ({ open, className }) => (
        <svg
            className={`${className} transition-transform duration-200 ${open ? "rotate-180" : ""}`}
            viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
        >
            <path d="M6 9l6 6 6-6" />
        </svg>
    );

    const CourseList = () => (
        <div className="p-3 space-y-3">
            {courses.map((categoryObj) => {
                const isOpen = !!expandedCategories[categoryObj.category];
                return (
                    <div
                        key={categoryObj.id || categoryObj.category}
                        className="rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden"
                    >
                        <div
                            className="px-4 py-3.5 bg-white dark:bg-gray-900 font-bold text-base text-gray-800 dark:text-gray-100 cursor-pointer flex justify-between items-center transition hover:bg-gray-50 dark:hover:bg-gray-800"
                            onClick={() => toggleCategory(categoryObj.category)}
                        >
                            <span className="flex items-center gap-3">
                                <FolderIcon className="w-5 h-5 text-red-500 flex-shrink-0" />
                                {categoryObj.category}
                                {categoryObj.hasNewLessons && (
                                    <span className="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full tracking-wide flex-shrink-0">
                                        NEW
                                    </span>
                                )}
                            </span>
                            <span className="flex items-center justify-center w-6 h-6 rounded-full border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 flex-shrink-0 text-base leading-none">
                                {isOpen ? "−" : "+"}
                            </span>
                        </div>
                        {isOpen && (
                            <div className="divide-y divide-gray-100 dark:divide-gray-700">
                                {categoryObj.courses &&
                                categoryObj.courses.length > 0 ? (
                                    categoryObj.courses.map((course, idx) => {
                                        const isSelected =
                                            selectedCourse &&
                                            selectedCourse.id === course.id;
                                        return (
                                            <div
                                                key={course.id}
                                                className={`flex items-center justify-between gap-3 px-4 py-3.5 cursor-pointer transition ${
                                                    isSelected
                                                        ? "bg-blue-500"
                                                        : "bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800"
                                                }`}
                                                onClick={() => {
                                                    setSelectedCourse(course);
                                                    setShowCourseModal(false);
                                                    setExpandedCategories(
                                                        (prev) => ({
                                                            ...prev,
                                                            __mobile: false,
                                                        }),
                                                    );
                                                }}
                                            >
                                                <div className="flex items-center gap-3 min-w-0">
                                                    <span
                                                        className={`flex items-center justify-center w-6 h-6 rounded-full text-[11px] font-bold flex-shrink-0 ${
                                                            isSelected
                                                                ? "bg-white/20 text-white"
                                                                : "bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400"
                                                        }`}
                                                    >
                                                        {idx + 1}
                                                    </span>
                                                    <span
                                                        className={`text-[15px] truncate font-medium ${
                                                            isSelected
                                                                ? "text-white"
                                                                : "text-gray-800 dark:text-gray-100"
                                                        }`}
                                                    >
                                                        {course.title}
                                                    </span>
                                                </div>
                                                {course.progress?.course_id && (
                                                    <span
                                                        className={`flex items-center justify-center w-5 h-5 rounded-full flex-shrink-0 ${
                                                            isSelected
                                                                ? "bg-white/20"
                                                                : "bg-green-500"
                                                        }`}
                                                    >
                                                        <i
                                                            className={`fa fa-check text-[10px] ${
                                                                isSelected
                                                                    ? "text-white"
                                                                    : "text-white"
                                                            }`}
                                                        ></i>
                                                    </span>
                                                )}
                                            </div>
                                        );
                                    })
                                ) : (
                                    <div className="text-sm text-gray-500 dark:text-gray-400 italic p-4">
                                        No courses available in this category
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                );
            })}
        </div>
    );

    const generalProgress = calculateGeneralProgress();

    const flatCourses = courses.flatMap((cat) => cat.courses || []);
    const currentIndex = selectedCourse
        ? flatCourses.findIndex((c) => c.id === selectedCourse.id)
        : -1;

    // Jump back to the top of the lesson content whenever the selected
    // lesson changes (e.g. via the Prev/Next buttons), so the video/title
    // is visible instead of staying scrolled wherever the user left off.
    useEffect(() => {
        if (contentScrollRef.current) {
            contentScrollRef.current.scrollTop = 0;
        }
        window.scrollTo(0, 0);
    }, [selectedCourse?.id]);

    // Record a lesson view so "Resume Last Lesson" on the dashboard can pick
    // up roadmap lessons the same way it already does for other lesson types.
    useEffect(() => {
        if (!selectedCourse) return;

        axios
            .post(
                `/member/course/${selectedCourse.id}/view`,
                {},
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            )
            .catch(() => {});
    }, [selectedCourse]);

    const goToLesson = (lesson) => {
        if (!lesson) return;
        setSelectedCourse(lesson);
        setExpandedCategories((prev) => ({
            ...prev,
            [lesson.category]: true,
        }));
    };

    const handleNextCourse = () => {
        if (currentIndex > -1 && currentIndex < flatCourses.length - 1) {
            goToLesson(flatCourses[currentIndex + 1]);
        }
    };
    const handlePrevCourse = () => {
        if (currentIndex > 0) {
            goToLesson(flatCourses[currentIndex - 1]);
        }
    };

    useEffect(() => {
        if (!selectedCourseId || courses.length === 0) return;

        // Find the course across all categories
        for (const categoryObj of courses) {
            const found = categoryObj.courses?.find(
                (course) => String(course.id) === String(selectedCourseId)
            );

            if (found) {
                setSelectedCourse(found);

                // auto-expand the category
                setExpandedCategories((prev) => ({
                    ...prev,
                    [categoryObj.category]: true,
                }));

                break;
            }
        }
    }, [courses, selectedCourseId]);

    useEffect(() => {
   
    }, [])

    return (
        <>
            <div className="flex flex-col md:flex-row">
                <div
                    className={`hidden md:block ${
                        sidebarCollapsed ? "w-20" : "w-1/3"
                    } transition-all duration-300 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800`}
                    style={{ height: "calc(100vh - 90px)", overflowY: "auto" }}
                >
                    <div className={`relative ${sidebarCollapsed ? "px-4 py-5" : "px-4 py-5 bg-blue-600"}`}>
                        <button
                            onClick={handleSidebarToggle}
                            className={
                                sidebarCollapsed
                                    ? "flex items-center justify-center w-11 h-11 mx-auto rounded-2xl text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none"
                                    : "absolute top-4 right-4 flex items-center justify-center w-7 h-7 rounded-full border border-white/80 text-white/80 hover:text-white hover:bg-white/10 transition focus:outline-none"
                            }
                            aria-label={sidebarCollapsed ? "Expand sidebar" : "Collapse sidebar"}
                        >
                            {sidebarCollapsed ? (
                                <svg className="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            ) : (
                                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M18 6 6 18M6 6l12 12" />
                                </svg>
                            )}
                        </button>

                        {!sidebarCollapsed && (
                            <>
                                <span className="block font-bold text-lg text-white truncate pr-8">
                                    {lastSegment.charAt(0).toUpperCase() +
                                        lastSegment.slice(1)}{" "}
                                    Piano Roadmap
                                </span>
                                <a
                                    href="/member/roadmap"
                                    className="inline-flex items-center gap-1.5 mt-2 text-sm font-medium text-white/80 hover:text-white transition"
                                >
                                    <svg className="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M15 18l-6-6 6-6" />
                                    </svg>
                                    Back
                                </a>
                            </>
                        )}
                    </div>
                    {!sidebarCollapsed && <CourseList />}
                </div>

                {/* Mobile Course List */}
                <div className="md:hidden w-full mb-4">
                    <div className="relative px-4 py-5 bg-blue-600">
                        <button
                            onClick={() =>
                                setExpandedCategories((prev) => ({
                                    ...prev,
                                    __mobile: !prev.__mobile,
                                }))
                            }
                            className="absolute top-4 right-4 flex items-center justify-center w-7 h-7 rounded-full border border-white/80 text-white/80 hover:text-white hover:bg-white/10 transition focus:outline-none"
                            aria-label={expandedCategories.__mobile ? "Collapse list" : "Expand list"}
                        >
                            {expandedCategories.__mobile ? (
                                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M18 6 6 18M6 6l12 12" />
                                </svg>
                            ) : (
                                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            )}
                        </button>

                        <span className="block font-bold text-lg text-white truncate pr-8">
                            {lastSegment.charAt(0).toUpperCase() +
                                lastSegment.slice(1)}{" "}
                            Piano Roadmap
                        </span>
                        <a
                            href="/member/roadmap"
                            className="inline-flex items-center gap-1.5 mt-2 text-sm font-medium text-white/80 hover:text-white transition"
                        >
                            <svg className="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                            Back
                        </a>
                    </div>
                    {expandedCategories.__mobile && <CourseList />}
                </div>

                {/* Course Details */}
                <div
                    ref={contentScrollRef}
                    className={`px-4 pb-4 pt-1 transition-all duration-300 ${
                        sidebarCollapsed ? "md:w-full" : "md:w-2/3"
                    }`}
                    style={{ height: "calc(100vh - 90px)", overflowY: "auto" }}
                >
                    <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-5 w-full max-w-7xl mx-auto mb-2">
                        <span className="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {calculateGeneralProgress()}% Completed
                        </span>
                        <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                            <div
                                className="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                style={{ width: `${generalProgress}%` }}
                            ></div>
                        </div>
                    </div>

                    {selectedCourse ? (
                        <CourseDetails
                            course={selectedCourse}
                            onComplete={handleCourseCompletion}
                            onSelectCourse={setSelectedCourse}
                            onPrevLesson={handlePrevCourse}
                            onNextLesson={handleNextCourse}
                            hasPrevLesson={currentIndex > 0}
                            hasNextLesson={
                                currentIndex > -1 &&
                                currentIndex < flatCourses.length - 1
                            }
                        />
                    ) : (
                        <div className="p-6 bg-white dark:bg-gray-500 rounded shadow-lg text-center">
                            <h2 className="text-xl font-bold mb-4">
                                Select a Course
                            </h2>
                            <p>
                                Please select a course from the list to view
                                details.
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
};

export default CoursesPage;

if (document.getElementById("course-details")) {
    const root = ReactDOM.createRoot(document.getElementById("course-details"));
    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <CoursesPage />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
