import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const CourseDetails = ({ course, onComplete }) => {
    const [loading, setLoading] = useState(false);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    const { showMessage } = useFlashMessage();

    const handleMarkAsCompleted = async () => {
        setLoading(true);
        try {
            await axios.post(
                `/member/course/${course?.id}/complete`,
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

    return (
        <div className="p-6 bg-white rounded shadow-lg">
            <h2 className="text-xl font-bold mb-4">{course.title}</h2>
            {course.video_url ? (
                <div
                    className="mb-4 w-full"
                    dangerouslySetInnerHTML={{ __html: course.video_url }}
                />
            ) : (
                <div className="mb-4 text-gray-500">No video available.</div>
            )}

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
        </div>
    );
};

const CoursesPage = () => {
    const [courses, setCourses] = useState({});
    const [selectedCourse, setSelectedCourse] = useState(null);
    const [expandedCategories, setExpandedCategories] = useState({});
    const [showCourseModal, setShowCourseModal] = useState(false);

    const lastSegment = window.location.pathname
        .split("/")
        .filter(Boolean)
        .pop();

    useEffect(() => {
        const fetchCourses = async () => {
            try {
                const response = await axios.get(
                    `/api/member/courses/${lastSegment}`
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
            updatedCourses[category] = updatedCourses[category].map((course) =>
                course.id === completedCourse.id
                    ? { ...course, completed: true }
                    : course
            );
            return updatedCourses;
        });
    };

    const calculateProgress = (category) => {
        const courseList = courses[category] || [];
        const total = courseList.length;
        const completed = courseList.filter((c) => c.completed).length;
        return total === 0 ? 0 : Math.round((completed / total) * 100);
    };

    const calculateGeneralProgress = () => {
        const allCourses = Object.values(courses).flat();
        const total = allCourses.length;
        const completed = allCourses.filter((c) => c.completed).length;
        return total === 0 ? 0 : Math.round((completed / total) * 100);
    };

    const CourseList = () => (
        <div className="p-4">
            <h3 className="font-bold text-lg mb-4">Choose a Course</h3>
            {Object.entries(courses).map(([category, courseList]) => (
                <div key={category} className="mb-4">
                    <div
                        className="p-3 bg-gray-300 rounded cursor-pointer flex justify-between items-center"
                        onClick={() => toggleCategory(category)}
                    >
                        <span>{category}</span>
                        <span className="fa fa-chevron-down"></span>
                    </div>

                    <div className="mt-2 bg-gray-200 h-2 rounded-full">
                        <div
                            className="bg-blue-600 h-2 rounded-full"
                            style={{ width: `${calculateProgress(category)}%` }}
                        ></div>
                    </div>
                    <div className="text-xs text-right text-gray-500 mt-1">
                        {calculateProgress(category)}% completed
                    </div>

                    {expandedCategories[category] && (
                        <div className="mt-2">
                            {courseList.map((course) => (
                                <div
                                    key={course.id}
                                    className="p-2 bg-white rounded shadow-sm mb-2 flex justify-between items-center cursor-pointer"
                                    onClick={() => {
                                        setSelectedCourse(course);
                                        setShowCourseModal(false);
                                    }}
                                >
                                    <span>{course.title}</span>
                                    {course.completed && (
                                        <span className="fa fa-check-circle text-green-500"></span>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            ))}
        </div>
    );

    const generalProgress = calculateGeneralProgress();

    return (
        <div className="flex flex-col md:flex-row">
            {/* Mobile Title + General Progress */}
            <div className="block md:hidden p-4 flex flex-col gap-2">
                <div className="flex justify-between items-center">
                    <h2 className="text-lg font-bold">
                        {lastSegment.toUpperCase()} Courses
                    </h2>
                    <button
                        className="px-3 py-1 text-sm bg-black text-white rounded-full"
                        onClick={() => setShowCourseModal(true)}
                    >
                        Select <span className="fa fa-chevron-down"></span>
                    </button>
                </div>
                <div className="bg-gray-200 h-2 rounded-full mt-2">
                    <div
                        className="bg-green-500 h-2 rounded-full"
                        style={{ width: `${generalProgress}%` }}
                    ></div>
                </div>
                <p className="text-xs text-right text-gray-500">
                    {generalProgress}% overall progress
                </p>
            </div>

            {/* Desktop Sidebar */}
            <div className="hidden md:block w-1/3 p-4 bg-gray-100 h-screen overflow-y-auto">
                <h2 className="text-lg font-bold mb-4 flex items-center gap-2">
                    <span className="fa fa-book"></span>{" "}
                    {lastSegment.toUpperCase()} Courses
                </h2>
                <div className="mb-4">
                    <div className="bg-gray-200 h-2 rounded-full">
                        <div
                            className="bg-green-500 h-2 rounded-full"
                            style={{ width: `${generalProgress}%` }}
                        ></div>
                    </div>
                    <p className="text-xs text-right text-gray-500 mt-1">
                        {generalProgress}% overall progress
                    </p>
                </div>
                <CourseList />
            </div>

            {/* Course Details */}
            <div className="w-full md:w-2/3 p-4">
                {selectedCourse ? (
                    <CourseDetails
                        course={selectedCourse}
                        onComplete={handleCourseCompletion}
                    />
                ) : (
                    <div className="p-6 bg-white rounded shadow-lg text-center">
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

            {/* Mobile Modal */}
            {showCourseModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-lg w-full max-w-lg relative overflow-y-auto max-h-[90vh] p-4">
                        <button
                            className="absolute top-3 right-3 text-gray-500 hover:text-red-500"
                            onClick={() => setShowCourseModal(false)}
                        >
                            <i className="fa fa-times text-xl"></i>
                        </button>
                        <CourseList />
                    </div>
                </div>
            )}
        </div>
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
