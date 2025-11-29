import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

const CourseDetails = ({ course, onComplete }) => {
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

    const handleMenuToggle = (commentId) => {
        setActiveMenuId(activeMenuId === commentId ? null : commentId);
    };

    const toggleBookmark = async () => {
        try {
            await axios.post(
                `/member/bookmark/toggle`,
                {
                    video_id: course.id,
                    source: "courses",
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
            return (
                <div className="mb-4 text-gray-500">No video available.</div>
            );
        }

        switch (course.video_type) {
            case "iframe":
                return (
                    <div
                        className="mb-4 w-full dark:bg-black"
                        dangerouslySetInnerHTML={{ __html: course.video_url }}
                    />
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

    return (
        <div className="p-6 bg-white dark:bg-black rounded shadow-lg">
            <div className="flex items-center justify-between mb-6">
                {/* Course Title */}
                <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                    {course.title}
                </h2>

                {/* Bookmark Button */}
                <button
                    onClick={toggleBookmark}
                    className={`px-4 py-2 rounded-full text-sm font-semibold flex items-center gap-2 shadow-md transition duration-300 ${
                        isBookmarked
                            ? "bg-yellow-400 text-black hover:bg-yellow-300"
                            : "bg-gray-200 text-gray-700 hover:bg-yellow-200"
                    }`}
                >
                    {isBookmarked ? (
                        <i className="fa fa-bookmark-o" aria-hidden="true"></i>
                    ) : (
                        <i className="fa fa-bookmark" aria-hidden="true"></i>
                    )}
                </button>
            </div>

           <div className="mb-4">
                {renderVideoPlayer()}
            </div>

            <div className="text-center mt-6">
                <button
                    onClick={handleMarkAsCompleted}
                    disabled={loading || course.completed}
                    className={`px-6 py-2 rounded-full text-sm font-semibold shadow-md transition duration-300 ${
                        course.completed
                            ? "bg-green-500 text-white cursor-not-allowed"
                            : "bg-gradient-to-r from-black to-gray-900 text-white hover:from-yellow-500 hover:to-yellow-400 hover:text-black"
                    }`}
                >
                    <span className="fa fa-check mr-2"></span>
                    {course.completed ? "Completed" : "Mark as Completed"}
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

const CoursesPage = () => {
    const [courses, setCourses] = useState([]);
    const [selectedCourse, setSelectedCourse] = useState(null);
    const [expandedCategories, setExpandedCategories] = useState({});
    const [showCourseModal, setShowCourseModal] = useState(false);
    const [darkMode, setDarkMode] = useState(false);
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

    useEffect(() => {
        const isDark = localStorage.getItem("darkMode") === "true";
        if (isDark) {
            document.documentElement.classList.add("dark");
            setDarkMode(true);
        }
    }, []);

    const toggleDarkMode = () => {
        const newMode = !darkMode;
        setDarkMode(newMode);
        if (newMode) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
        localStorage.setItem("darkMode", newMode);
    };

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
                setCourses(response.data);
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
        setCourses((prevCourses) => {
            const updatedCourses = { ...prevCourses };

            const category = completedCourse.category;

            if (!updatedCourses[category]) return prevCourses;

            updatedCourses[category] = updatedCourses[category].map((course) =>
                course.id === completedCourse.id
                    ? { ...course, completed: !course.completed }
                    : course
            );

            return updatedCourses;
        });
    };

    const calculateGeneralProgress = () => {
        if (!Array.isArray(courses)) return 0;
        const allCourses = courses.flatMap((cat) => cat.courses || []);
        const total = allCourses.length;
        const completed = allCourses.filter((course) => course.progress).length;
        return total === 0 ? 0 : Math.round((completed / total) * 100);
    };

    const CourseList = () => (
        <div className="p-2">
            {courses.map((categoryObj) => (
                <div
                    key={categoryObj.id || categoryObj.category}
                    className="mb-6"
                >
                    <div
                        className="px-4 py-1 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-t-lg font-bold text-lg text-gray-800 dark:text-gray-100 shadow cursor-pointer flex justify-between items-center"
                        onClick={() => toggleCategory(categoryObj.category)}
                    >
                        <span className="flex items-center gap-2 text-sm">
                            <i className="fa fa-folder text-blue-500"></i>
                            {categoryObj.category}
                        </span>
                        <i
                            className={`fa fa-chevron-${
                                expandedCategories[categoryObj.category]
                                    ? "up"
                                    : "down"
                            } text-gray-600 dark:text-gray-300 transition-transform`}
                        ></i>
                    </div>
                    {expandedCategories[categoryObj.category] && (
                        <div className="bg-white dark:bg-gray-900 rounded-b-lg shadow-inner border border-t-0 border-gray-200 dark:border-gray-700 p-2 space-y-2">
                            {categoryObj.courses &&
                            categoryObj.courses.length > 0 ? (
                                categoryObj.courses.map((course) => (
                                    <div
                                        key={course.id}
                                        className={`flex items-center justify-between px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700 shadow-sm transition cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 ${
                                            selectedCourse &&
                                            selectedCourse.id === course.id
                                                ? "ring-2 ring-blue-500 bg-blue-100 dark:bg-blue-900"
                                                : ""
                                        }`}
                                        onClick={() => {
                                            setSelectedCourse(course);
                                            setShowCourseModal(false);
                                            setExpandedCategories((prev) => ({
                                                ...prev,
                                                __mobile: false,
                                            }));
                                        }}
                                    >
                                        <div className="flex items-center gap-2">
                                            <i className="fa fa-book text-gray-400 dark:text-gray-500"></i>
                                            <span className="text-xs text-gray-800 dark:text-gray-100 truncate font-medium">
                                                {course.title}
                                            </span>
                                        </div>
                                        {course.completed && (
                                            <i className="fa fa-check-circle text-green-500 text-xs"></i>
                                        )}
                                    </div>
                                ))
                            ) : (
                                <div className="text-sm text-gray-500 dark:text-gray-400 italic p-2">
                                    No courses available in this category
                                </div>
                            )}
                        </div>
                    )}
                </div>
            ))}
        </div>
    );

    const generalProgress = calculateGeneralProgress();

    const [currentIndex, setCurrentIndex] = useState(null);
    useEffect(() => {
        if (selectedCourse && courses[selectedCourse.category]) {
            const idx = courses[selectedCourse.category].findIndex(
                (c) => c.id === selectedCourse.id
            );
            setCurrentIndex(idx);
        }
    }, [selectedCourse, courses]);

    const handleNextCourse = () => {
        if (
            selectedCourse &&
            courses[selectedCourse.category] &&
            currentIndex < courses[selectedCourse.category].length - 1
        ) {
            setSelectedCourse(
                courses[selectedCourse.category][currentIndex + 1]
            );
        }
    };
    const handlePrevCourse = () => {
        if (
            selectedCourse &&
            courses[selectedCourse.category] &&
            currentIndex > 0
        ) {
            setSelectedCourse(
                courses[selectedCourse.category][currentIndex - 1]
            );
        }
    };

    return (
        <>
            <div className="flex flex-col md:flex-row">
                <div
                    className={`hidden md:block ${
                        sidebarCollapsed ? "w-20" : "w-1/3"
                    } transition-all duration-300 bg-gray-100 dark:bg-black`}
                    style={{ height: "calc(100vh - 90px)", overflowY: "auto" }}
                >
                    <div className="flex justify-between items-center p-2 mb-2">
                        {!sidebarCollapsed && (
                            <h2 className="text-lg font-bold flex items-center gap-2">
                                <span className="fa fa-book"></span>{" "}
                                {lastSegment.charAt(0).toUpperCase() +
                                    lastSegment.slice(1)}{" "}
                                Piano Road-map
                            </h2>
                        )}
                        <button
                            onClick={handleSidebarToggle}
                            className="text-gray-600 hover:text-blue-500"
                        >
                            <i
                                className={`fa ${
                                    sidebarCollapsed
                                        ? "fa-chevron-right"
                                        : "fa-chevron-left"
                                }`}
                            ></i>
                        </button>
                    </div>
                    {!sidebarCollapsed && <CourseList />}
                </div>

                {/* Mobile Course List */}
                <div className="md:hidden w-full mb-4">
                    <div className="bg-gray-100 dark:bg-black rounded-lg shadow p-2">
                        <button
                            className="w-full flex justify-between items-center px-4 py-2 font-bold text-lg text-gray-800 dark:text-gray-100 focus:outline-none"
                            onClick={() =>
                                setExpandedCategories((prev) => ({
                                    ...prev,
                                    __mobile: !prev.__mobile,
                                }))
                            }
                        >
                            <span>
                                <i className="fa fa-book mr-2"></i>Select a
                                Course
                            </span>
                            <i
                                className={`fa fa-chevron-${
                                    expandedCategories.__mobile ? "up" : "down"
                                }`}
                            ></i>
                        </button>
                        {expandedCategories.__mobile && <CourseList />}
                    </div>
                </div>

                {/* Course Details */}
                <div
                    className={`p-4 transition-all duration-300 ${
                        sidebarCollapsed ? "md:w-full" : "md:w-2/3"
                    }`}
                    style={{ height: "calc(100vh - 90px)", overflowY: "auto" }}
                >
                    <div className="flex justify-between items-center bg-white dark:bg-gray-600 p-4 shadow rounded w-full max-w-7xl mx-auto my-2">
                        {/* Progress area - Centered */}
                        <div className="flex-1 flex justify-center pr-4 ">
                            <div className="w-full max-w-md">
                                <div className="flex items-center justify-between mb-1">
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {calculateGeneralProgress()}% Completed
                                    </span>
                                    {/* <span className="text-sm text-gray-500 dark:text-gray-400">
                                        {(() => {
                                            const allCourses =
                                                Object.values(courses).flat();
                                            const completed = allCourses.filter(
                                                (c) => c.completed
                                            ).length;
                                            return `${completed}/${allCourses.length}`;
                                        })()}
                                        <span className="mx-2 fa fa-info-circle"></span>
                                    </span> */}
                                </div>
                                <div className="w-full bg-gray-300 rounded-full h-2">
                                    <div
                                        className="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                        style={{ width: `${generalProgress}%` }}
                                    ></div>
                                </div>
                            </div>
                        </div>

                        {/* Night mode + avatar - Right aligned */}
                        <div className="flex items-center justify-end space-x-4">
                            <button
                                onClick={toggleDarkMode}
                                className="text-gray-600 dark:text-gray-300 focus:outline-none text-lg"
                                aria-label="Toggle Dark Mode"
                            >
                                <i
                                    className={`fas ${
                                        darkMode ? "fa-sun" : "fa-moon"
                                    }`}
                                ></i>
                            </button>

                            <a
                                href="/member/profile"
                                className="flex items-center"
                            >
                                <img
                                    src="/avatar1.jpg"
                                    alt="User Avatar"
                                    className="w-8 h-8 rounded-full border-2 border-gray-300 dark:border-gray-600"
                                />
                            </a>
                        </div>
                    </div>
                    {selectedCourse ? (
                        <>
                            <div className="flex justify-between items-center mb-2">
                                {/* <button
                                    onClick={handlePrevCourse}
                                    disabled={currentIndex === 0}
                                    className="px-3 py-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 disabled:opacity-50"
                                >
                                    <i className="fa fa-chevron-left"></i>{" "}
                                    Previous
                                </button> */}
                                {/* <button
                                    onClick={handleNextCourse}
                                    disabled={
                                        !selectedCourse ||
                                        !courses[selectedCourse.category] ||
                                        currentIndex ===
                                            courses[selectedCourse.category]
                                                .length -
                                                1
                                    }
                                    className="px-3 py-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 disabled:opacity-50"
                                >
                                    Next <i className="fa fa-chevron-right"></i>
                                </button> */}
                            </div>
                            <CourseDetails
                                course={selectedCourse}
                                onComplete={handleCourseCompletion}
                            />
                        </>
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
