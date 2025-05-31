import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const CourseDetails = ({ course }) => {
    const [loading, setLoading] = useState(false);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
    const { showMessage } = useFlashMessage();

    const handleMarkAsCompleted = async () => {
        setLoading(true);
        try {
            const response = await axios.post(
                `/member/course/${course?.id}/complete`,
                course,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            showMessage("Mark as completed.", "success");

            console.log(response.data);
        } catch (error) {
            console.error("Error creating course:", error);
            showMessage(" Failed to mark as completed.", "error");
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
                <div className="mb-4">
                    <p>No video available.</p>
                </div>
            )}

           <div className="text-center mt-6">
            <button
                onClick={handleMarkAsCompleted}
                className="px-6 py-2 bg-gradient-to-r from-black to-gray-900 text-white text-sm font-semibold rounded-full shadow-md hover:from-yellow-500 hover:to-yellow-400 hover:text-black transition duration-300"
            >
                <span className="fa fa-check"></span> Mark as Completed
            </button>
            </div>

        </div>
    );
};

const CoursesPage = () => {
    const [courses, setCourses] = useState({});
    const [selectedCourse, setSelectedCourse] = useState(null);
    const [expandedCategories, setExpandedCategories] = useState({});

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

    return (
        <div className="flex">
            {/* Left Side: Course List */}
            <div className="w-1/3 p-4 bg-gray-100 h-screen overflow-y-auto">
                <h2 className="text-lg font-bold mb-4 flex items-center gap-2">
                    <span className="fa fa-book"></span>{" "}
                    {lastSegment.toUpperCase()} Courses
                </h2>

                {Object.keys(courses).length > 0 ? (
                    Object.entries(courses).map(([category, courseList]) => (
                        <div key={category} className="mb-4">
                            {/* Category Header */}
                            <div
                                className="p-3 bg-gray-300 cursor-pointer hover:bg-gray-400 rounded flex justify-between items-center"
                                onClick={() => toggleCategory(category)}
                            >
                                <span className="font-semibold">
                                    {category}
                                </span>
                                <span
                                    className={`fa ${
                                        expandedCategories[category]
                                            ? "fa-chevron-down"
                                            : "fa-chevron-right"
                                    }`}
                                ></span>
                            </div>

                            {/* Courses List */}
                            {expandedCategories[category] && (
                                <div className="mt-2">
                                    {courseList.length > 0 ? (
                                        courseList.map((course) => (
                                            <div
                                                key={course.id}
                                                className="p-3 bg-white mb-2 flex items-center justify-between cursor-pointer hover:bg-gray-200 rounded shadow-sm"
                                                onClick={() =>
                                                    setSelectedCourse(course)
                                                }
                                            >
                                                <div className="flex items-center gap-2">
                                                    <span className="fa fa-chevron-right text-gray-500"></span>
                                                    <span className="text-gray-700 font-medium">
                                                        {course.title}
                                                    </span>
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="text-gray-500 p-3">
                                            No courses in this category.
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    ))
                ) : (
                    <p className="text-gray-500">No courses available.</p>
                )}
            </div>

            {/* Right Side: Course Details */}
            <div className="w-2/3 p-4">
                {selectedCourse ? (
                    <CourseDetails course={selectedCourse} />
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
