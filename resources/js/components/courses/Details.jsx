import ReactDOM from "react-dom/client";
import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import { ArrowLeft, ArrowRight, Bookmark, BookmarkMinus, Check, Target } from "lucide-react";
import LessonDiscussion from "./LessonDiscussion";
import CheckpointDetails, {
    CheckpointCta,
    checkpointHasCta,
} from "./checkpoints/CheckpointDetails";

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
    const [isBookmarked, setIsBookmarked] = useState(
        course.isBookmarked || false
    );
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    const { showMessage } = useFlashMessage();

    const iframeWrapperRef = useRef(null);
    const [portraitSource, setPortraitSource] = useState(null);
    const isPortrait = Boolean(course.video_url && portraitSource === course.video_url);
    const mediaAspect = isPortrait ? "aspect-[9/16] max-w-xs mx-auto" : "aspect-video";
    const isGoogleDriveVideo = Boolean(course.video_url && (
        course.video_type === "google" ||
        (course.video_type === "iframe" && /\/\/drive\.google\.com\//i.test(course.video_url))
    ));
    // The injected iframe embed (e.g. VdoCipher) ships with fixed inline
    // width/height — override it so it fills the responsive aspect-ratio box.
    useEffect(() => {
        const iframe = iframeWrapperRef.current?.querySelector("iframe");
        if (!iframe) return;

        const width = Number(iframe.getAttribute("width")) ||
            (iframe.style.width.endsWith("px") ? parseFloat(iframe.style.width) : 0);
        const height = Number(iframe.getAttribute("height")) ||
            (iframe.style.height.endsWith("px") ? parseFloat(iframe.style.height) : 0);
        setPortraitSource(width > 0 && height > width ? course.video_url : null);
        Object.assign(iframe.style, {
            position: "absolute",
            inset: "0",
            width: "100%",
            height: "100%",
            border: "0",
        });
    }, [course.video_type, course.video_url]);

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

    // Records this course as the member's most recently viewed lesson so the
    // dashboard's "Resume Lesson" card points at whatever was opened last,
    // not whatever happened to be recorded before.
    useEffect(() => {
        axios
            .post(
                `/member/course/${course.id}/view`,
                {},
                { headers: { "X-CSRF-TOKEN": csrfToken }, withCredentials: true }
            )
            .catch((err) => console.error("Failed to record course view:", err));
    }, [course.id]);

    useEffect(() => {
        setIsBookmarked(course.isBookmarked || false);
    }, [course]);

    useEffect(() => {
        if (!course || !course.video_url) return;

        // Parse any script tags in the video_url and inject them into the document
        const parser = new DOMParser();
        const doc = parser.parseFromString(course.video_url, "text/html");
        const scripts = doc.querySelectorAll("script");

        scripts.forEach((script) => {
            const src = script.getAttribute("src");
            if (src) {
                // Check if script is already in document to avoid duplicates
                const existingScript = document.querySelector(`script[src="${src}"]`);
                if (!existingScript) {
                    const newScript = document.createElement("script");
                    newScript.src = src;
                    newScript.async = true;
                    
                    const type = script.getAttribute("type");
                    if (type) {
                        newScript.type = type;
                    }

                    newScript.onload = () => {
                        console.log(`Successfully loaded script: ${src}`);
                    };

                    newScript.onerror = (e) => {
                        console.error(`Failed to load script: ${src}`, e);
                    };
                    
                    document.head.appendChild(newScript);
                }
            }
        });
    }, [course?.video_url]);

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

    const renderVideoPlayer = () => {
        if (!course.video_type || !course.video_url) {
            return course.thumbnail_url || course.thumbnail ? (
                <div className="aspect-video w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800 md:aspect-auto md:h-[400px]">
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

        const playerClass = `block w-full rounded bg-black ${mediaAspect} ${isPortrait ? "" : "md:aspect-auto md:h-[400px]"}`;

        switch (course.video_type) {
            case "iframe":
                return (
                    <div className={`relative w-full overflow-hidden rounded bg-gray-100 md:shadow-lg ${mediaAspect}`}>
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
                        src={`https://drive.google.com/file/d/${course.video_url}/preview`}
                        title={course.title}
                        allow="autoplay"
                        allowFullScreen
                        className={playerClass}
                    />
                );

            case "youtube":
                return (
                    <iframe
                        src={`https://www.youtube.com/embed/${course.video_url}`}
                        title={course.title}
                        frameBorder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                        className={playerClass}
                    />
                );

            case "local":
                return (
                    <video
                        key={course.video_url}
                        controls
                        className={playerClass}
                        onLoadedMetadata={({ currentTarget }) => {
                            setPortraitSource(currentTarget.videoHeight > currentTarget.videoWidth ? course.video_url : null);
                        }}
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

    const lessonDescription = course.description && (
        <div className="my-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
            {course.description}
        </div>
    );

    return (
        <div className="rounded bg-white px-1 pb-4 pt-2 dark:bg-black md:p-6 md:shadow-lg">
            <div className="mb-3 md:mb-6">
                {/* Course Title */}
                <h2 className="text-xl font-bold leading-tight text-gray-900 dark:text-white md:text-2xl">
                    {course.title}
                </h2>
            </div>

            <div className={`mb-3 md:mb-4 ${isGoogleDriveVideo ? "-mx-4 md:mx-0" : ""}`}>
                {renderVideoPlayer()}
            </div>

            <div className="grid grid-cols-2 gap-2 md:mt-6 md:flex md:items-center md:justify-center md:gap-3">
                <button
                    onClick={toggleBookmark}
                    className={`px-2 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center justify-center gap-2 border transition duration-200 whitespace-nowrap ${
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
                    className={`px-2 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center justify-center gap-2 transition duration-200 whitespace-nowrap ${
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

            {lessonDescription}

            {course.image_urls && course.image_urls.length > 0 && (
                <div className="mt-6 mb-6">
                    <div className="grid grid-cols-1 gap-4">
                        {course.image_urls.map((url, idx) => (
                            <div key={idx} className="rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm bg-gray-50 dark:bg-gray-900">
                                <img src={url} alt="Description Highlight" className="w-full h-auto" />
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Downloads for this lesson */}
            {course.pdf_resource_url && (
                <div className="mt-8 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div className="bg-red-600 px-4 py-2.5 sm:px-5 sm:py-3">
                        <h3 className="text-white text-xs sm:text-sm font-bold tracking-wide uppercase whitespace-nowrap">
                            Downloads for this lesson
                        </h3>
                    </div>
                    <div className="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-5 sm:py-4 flex items-center gap-3 sm:gap-4">
                        <div className="w-12 h-9 sm:w-16 sm:h-10 rounded bg-gray-400 dark:bg-gray-600 flex items-center justify-center text-white text-[10px] sm:text-xs font-bold flex-shrink-0">
                            PDF
                        </div>
                        <a
                            href={course.pdf_resource_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-sm sm:text-base text-red-500 hover:text-red-600 font-bold truncate"
                        >
                            View the Chart
                        </a>
                    </div>
                </div>
            )}

            {/* Related Lessons Section */}
            {course.related && course.related.length > 0 && (
                <div className="mt-8 border-t pt-6 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/40 rounded-2xl p-4">
                    <div className="flex items-center gap-1.5 mb-3">
                        <i className="fa-solid fa-link text-[#1447A6] text-[10px]"></i>
                        <p className="text-[10px] font-bold text-gray-400 tracking-[0.14em] uppercase">
                            Related Lessons
                        </p>
                    </div>
                    <div className="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700 overflow-hidden shadow-sm">
                        {course.related.map((related, idx) => {
                            const content = (
                                <>
                                    <span className="flex items-center justify-center w-8 h-8 rounded-lg bg-[#1447A6]/10 dark:bg-[#1447A6]/20 text-[#1447A6] flex-shrink-0 group-hover:bg-[#1447A6] group-hover:text-white transition-colors">
                                        <i className="fa-solid fa-play text-[10px] ml-0.5"></i>
                                    </span>
                                    <p className="text-[13px] font-semibold text-gray-800 dark:text-gray-100 truncate flex-1 transition-colors">
                                        {related.title}
                                    </p>
                                    <i className="fa-solid fa-chevron-right text-gray-300 group-hover:text-[#1447A6] text-[10px] flex-shrink-0 transition-colors"></i>
                                </>
                            );
                            const className = "flex items-center gap-3 px-3.5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer group";

                            // Admin-curated related lessons carry a plain `url`
                            // (any link, not necessarily another course) and no
                            // `id`, so they navigate as a real link instead of
                            // switching the in-app selected course.
                            return related.url ? (
                                <a key={related.id ?? idx} href={related.url} className={className}>
                                    {content}
                                </a>
                            ) : (
                                <div key={related.id ?? idx} onClick={() => onSelectCourse(related)} className={className}>
                                    {content}
                                </div>
                            );
                        })}
                    </div>
                </div>
            )}

            <nav aria-label="Lesson navigation" className="mt-6 grid grid-cols-2 gap-3 border-t border-gray-200 pt-4 dark:border-gray-700 md:flex md:justify-between">
                <button
                    type="button"
                    onClick={onPrevLesson}
                    disabled={!hasPrevLesson}
                    className="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 focus-visible:ring-2 focus-visible:ring-[#1447A6] disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 md:min-w-32"
                >
                    <ArrowLeft className="h-4 w-4" aria-hidden="true" />
                    Previous
                </button>
                <button
                    type="button"
                    onClick={onNextLesson}
                    disabled={!hasNextLesson}
                    className="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#1447A6] px-4 text-sm font-semibold text-white transition-colors hover:bg-[#0F3A8A] focus-visible:ring-2 focus-visible:ring-[#1447A6] focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 md:min-w-32"
                >
                    Next
                    <ArrowRight className="h-4 w-4" aria-hidden="true" />
                </button>
            </nav>

            <LessonDiscussion key={course.id} topicId={course.id} category="course" />
        </div>
    );
};

const parseCoursePath = () => {
    const parts = window.location.pathname.split("/").filter(Boolean);
    const courseIndex = parts.indexOf("course");
    const level = parts[courseIndex + 1] || "";
    const pathId = parts[courseIndex + 2];
    const queryId = new URLSearchParams(window.location.search).get("selected_course");
    const courseId = pathId && /^\d+$/.test(pathId) ? pathId : queryId;

    return { level, courseId: courseId || null };
};

const CoursesPage = () => {
    const initialPath = parseCoursePath();
    const level = initialPath.level;
    const [courseId, setCourseId] = useState(initialPath.courseId);
    const [courses, setCourses] = useState([]);
    const [selectedCourse, setSelectedCourse] = useState(null);
    const [selectedCheckpoint, setSelectedCheckpoint] = useState(null);
    const [expandedCategories, setExpandedCategories] = useState({});
    const [showCourseModal, setShowCourseModal] = useState(false);
    const [darkMode, setDarkMode] = useState(false);
    const contentScrollRef = useRef(null);

    useEffect(() => {
        const isDark = localStorage.getItem("darkMode") === "true";
        if (isDark) {
            document.documentElement.classList.add("dark");
            setDarkMode(true);
        }
    }, []);

    useEffect(() => {
        const onPopState = () => {
            const next = parseCoursePath();
            setCourseId(next.courseId);
            if (!next.courseId) {
                setSelectedCourse(null);
            }
        };

        window.addEventListener("popstate", onPopState);
        return () => window.removeEventListener("popstate", onPopState);
    }, []);

    useEffect(() => {
        const fetchCourses = async () => {
            try {
                const response = await axios.get(
                    `/api/member/courses/${level}`,
                    {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        withCredentials: true,
                    }
                );
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
    }, [level]);

    const openCourse = (course) => {
        if (!course) return;

        setSelectedCourse(course);
        setSelectedCheckpoint(null);
        setShowCourseModal(false);
        setCourseId(String(course.id));
        setExpandedCategories((prev) => ({
            ...prev,
            [course.category]: true,
            __mobile: false,
        }));

        const url = `/member/course/${level}/${course.id}`;
        if (window.location.pathname !== url) {
            window.history.pushState({ courseId: course.id }, "", url);
        }
    };

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
                            className="px-4 py-3.5 bg-gray-100 dark:bg-gray-900 font-bold text-base text-gray-800 dark:text-gray-100 cursor-pointer flex justify-between items-center transition hover:bg-gray-200 dark:hover:bg-gray-800"
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
                        {isOpen && (() => {
                            const items = [
                                ...(categoryObj.courses || []).map((c) => ({
                                    ...c,
                                    item_type: "course",
                                })),
                                ...(categoryObj.checkpoints || []).map((c) => ({
                                    ...c,
                                    item_type: "checkpoint",
                                })),
                            ].sort((a, b) => {
                                const posA = a.position ?? Infinity;
                                const posB = b.position ?? Infinity;
                                return posA !== posB ? posA - posB : a.id - b.id;
                            });

                            let lessonNumber = 0;

                            return (
                                <div className="divide-y divide-gray-100 dark:divide-gray-700">
                                    {items.length > 0 ? (
                                        items.map((item) => {
                                            if (item.item_type === "checkpoint") {
                                                const isSelected =
                                                    selectedCheckpoint &&
                                                    selectedCheckpoint.id === item.id;
                                                return (
                                                    <div
                                                        key={`checkpoint-${item.id}`}
                                                        className={`relative flex items-center gap-3.5 pl-8 pr-4 py-4 cursor-pointer transition-colors ${
                                                            isSelected
                                                                ? "bg-[#1447A6]/10 dark:bg-[#1447A6]/20"
                                                                : "bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800"
                                                        }`}
                                                        onClick={() => {
                                                            setSelectedCheckpoint(item);
                                                            setSelectedCourse(null);
                                                            setCourseId(null);
                                                            setShowCourseModal(false);
                                                            setExpandedCategories(
                                                                (prev) => ({
                                                                    ...prev,
                                                                    __mobile: false,
                                                                }),
                                                            );
                                                            const url = `/member/course/${level}`;
                                                            if (window.location.pathname !== url) {
                                                                window.history.pushState({}, "", url);
                                                            }
                                                        }}
                                                    >
                                                        <span
                                                            className="absolute left-4 top-0 bottom-0 border-l-2 border-dashed border-gray-200 dark:border-gray-700"
                                                            aria-hidden="true"
                                                        />
                                                        <span
                                                            className="absolute left-[13px] top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-[#1447A6] ring-4 ring-white dark:ring-gray-900"
                                                            aria-hidden="true"
                                                        />
                                                        <span
                                                            className={`relative flex items-center justify-center w-10 h-10 rounded-full flex-shrink-0 shadow-sm transition-colors ${
                                                                isSelected
                                                                    ? "bg-[#1447A6]"
                                                                    : "bg-[#1447A6]/10 dark:bg-[#1447A6]/20"
                                                            }`}
                                                        >
                                                            <Target
                                                                className={`w-[18px] h-[18px] ${
                                                                    isSelected
                                                                        ? "text-white"
                                                                        : "text-[#1447A6] dark:text-[#1447A6]"
                                                                }`}
                                                                strokeWidth={2}
                                                            />
                                                        </span>
                                                        <div className="min-w-0">
                                                            <span className="block text-sm font-semibold text-[#1447A6] dark:text-[#1447A6] truncate">
                                                                {item.label || "Practice Checkpoint"}
                                                            </span>
                                                            <span className="block text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                                                {item.description}
                                                            </span>
                                                        </div>
                                                    </div>
                                                );
                                            }

                                            const course = item;
                                            lessonNumber += 1;
                                            const isSelected =
                                                selectedCourse &&
                                                selectedCourse.id === course.id;
                                            return (
                                                <a
                                                    key={course.id}
                                                    href={`/member/course/${level}/${course.id}`}
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        openCourse(course);
                                                    }}
                                                    className={`flex items-center justify-between gap-3 px-4 py-3.5 cursor-pointer transition no-underline ${
                                                        isSelected
                                                            ? "bg-[#1447A6]"
                                                            : "bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800"
                                                    }`}
                                                >
                                                    <div className="flex items-center gap-3 min-w-0">
                                                        <span
                                                            className={`flex items-center justify-center w-6 h-6 rounded-full text-[11px] font-bold flex-shrink-0 ${
                                                                isSelected
                                                                    ? "bg-white/20 text-white"
                                                                    : "bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400"
                                                            }`}
                                                        >
                                                            {lessonNumber}
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
                                                </a>
                                            );
                                        })
                                    ) : (
                                        <div className="text-sm text-gray-500 dark:text-gray-400 italic p-4">
                                            No courses available in this category
                                        </div>
                                    )}
                                </div>
                            );
                        })()}
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
        openCourse(lesson);
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
        if (!courseId || courses.length === 0) return;

        for (const categoryObj of courses) {
            const found = categoryObj.courses?.find(
                (course) => String(course.id) === String(courseId)
            );

            if (found) {
                setSelectedCourse((prev) =>
                    prev && String(prev.id) === String(found.id) ? prev : found
                );
                setSelectedCheckpoint(null);
                setExpandedCategories((prev) => ({
                    ...prev,
                    [categoryObj.category]: true,
                    __mobile: false,
                }));

                const canonical = `/member/course/${level}/${found.id}`;
                if (window.location.pathname !== canonical) {
                    window.history.replaceState({ courseId: found.id }, "", canonical);
                }

                break;
            }
        }
    }, [courses, courseId, level]);

    return (
        <>
            <div className="flex h-[100dvh] flex-col md:h-[calc(100dvh-3rem)] md:flex-row">
                <div
                    className={`hidden md:block w-1/3 transition-all duration-300 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800`}
                    style={{ overflowY: "auto" }}
                >
                    <div className="relative px-4 py-5 bg-[#1447A6]">
                        <span className="block font-bold text-lg text-white truncate">
                            {level.charAt(0).toUpperCase() +
                                level.slice(1)}{" "}
                            Piano Roadmap
                        </span>
                        <a
                            href="/member/roadmap"
                            className="inline-flex items-center gap-1.5 mt-2 text-sm font-medium text-white/80 hover:text-white transition"
                        >
                            <svg className="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                            Back to Roadmap
                        </a>
                    </div>
                    <CourseList />
                </div>

                <div className={`w-full md:hidden ${expandedCategories.__mobile ? "flex min-h-0 flex-1 flex-col" : "shrink-0"}`}>
                    <div className="bg-[#1447A6] px-4 pb-3 pt-2.5 text-white">
                        <a href="/member/roadmap" className="inline-flex min-h-8 items-center gap-1 text-xs font-medium text-white/90 hover:underline">
                            <span aria-hidden="true">‹</span> Back to Roadmap
                        </a>
                        <div className="flex items-center justify-between gap-3">
                            <h1 className="text-base font-bold leading-tight">
                                {level.charAt(0).toUpperCase() + level.slice(1)} Piano Roadmap
                            </h1>
                            <button
                                type="button"
                                onClick={() =>
                                    setExpandedCategories((prev) => ({ ...prev, __mobile: !prev.__mobile }))
                                }
                                className="inline-flex min-h-11 shrink-0 items-center gap-1.5 rounded-lg border border-white/50 px-2.5 text-xs font-semibold hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white"
                                aria-expanded={Boolean(expandedCategories.__mobile)}
                                aria-controls="mobile-roadmap-lessons"
                            >
                                Lessons
                                <svg className={`h-4 w-4 transition-transform ${expandedCategories.__mobile ? "rotate-180" : ""}`} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                        </div>
                        <div className="mt-1 flex items-center gap-3">
                            <div role="progressbar" aria-label="Roadmap progress" aria-valuenow={generalProgress} aria-valuemin={0} aria-valuemax={100}
                                 className="h-1.5 flex-1 overflow-hidden rounded-full bg-white/25">
                                <div className="h-full rounded-full bg-white" style={{ width: `${generalProgress}%` }} />
                            </div>
                            <span className="shrink-0 text-xs font-medium">{generalProgress}% complete</span>
                        </div>
                    </div>
                    <div id="mobile-roadmap-lessons" hidden={!expandedCategories.__mobile} className="min-h-0 flex-1 overflow-y-auto">
                        {expandedCategories.__mobile && <CourseList />}
                    </div>
                </div>

                {/* Course Details */}
                <div
                    className={`${expandedCategories.__mobile ? "hidden md:flex" : "flex"} min-h-0 min-w-0 flex-1 flex-col transition-all duration-300 md:w-2/3 md:flex-none`}
                >
                    <div
                        ref={contentScrollRef}
                        className="min-h-0 flex-1 overflow-y-auto px-3 pb-4 pt-3 md:px-4 md:pt-1"
                    >
                        <div className="mx-auto mb-2 hidden w-full max-w-7xl rounded-2xl bg-white p-5 shadow-sm dark:bg-gray-900 md:block">
                            <span className="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {calculateGeneralProgress()}% Completed
                            </span>
                            <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                                <div
                                    className="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style={{ width: `${generalProgress}%` }}
                                ></div>
                            </div>
                        </div>

                        {selectedCourse ? (
                            <CourseDetails
                                course={selectedCourse}
                                onComplete={handleCourseCompletion}
                                onSelectCourse={openCourse}
                                onPrevLesson={handlePrevCourse}
                                onNextLesson={handleNextCourse}
                                hasPrevLesson={currentIndex > 0}
                                hasNextLesson={
                                    currentIndex > -1 &&
                                    currentIndex < flatCourses.length - 1
                                }
                            />
                        ) : selectedCheckpoint ? (
                            <CheckpointDetails checkpoint={selectedCheckpoint} />
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

                    {/* Fixed action bar (outside the scroll area) so the
                        checkpoint's CTA stays visible without scrolling. */}
                    {selectedCheckpoint && checkpointHasCta(selectedCheckpoint) && (
                        <div className="sticky bottom-0 z-20 flex-shrink-0 px-4 py-3 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
                            <div className="max-w-7xl mx-auto">
                                <CheckpointCta
                                    checkpoint={selectedCheckpoint}
                                    onSelectCourse={openCourse}
                                />
                            </div>
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
