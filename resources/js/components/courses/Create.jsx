import ReactDOM from "react-dom/client";
import React, { useState, useEffect } from "react";
import axios from "axios";
import Select from "react-select";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const CourseForm = () => {
    const [course, setCourse] = useState({
        title: "",
        category: "",
        description: "",
        video_url: "",
        video_type: "youtube",
        level: "beginner",
        enrollment_count: 0,
        status: "active",
        prerequisites: "",
        rating_count: 0,
        average_rating: 0,
        likes: 0,
        dislikes: 0,
        related_courses: [],
    });

    const [allCourses, setAllCourses] = useState([]);
    const [loading, setLoading] = useState(false);

    const { showMessage } = useFlashMessage();

    useEffect(() => {
        const fetchAllCourses = async () => {
            try {
                const response = await axios.get("/api/admin/all-courses");
                setAllCourses(
                    response.data.map((c) => ({
                        value: c.id,
                        label: `${c.title} (${c.level})`,
                    }))
                );
            } catch (error) {
                console.error("Error fetching courses:", error);
            }
        };
        fetchAllCourses();
    }, []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setCourse({ ...course, [name]: value });
    };

    const handleRelatedCoursesChange = (selectedOptions) => {
        setCourse({
            ...course,
            related_courses: selectedOptions
                ? selectedOptions.map((opt) => opt.value)
                : [],
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            setLoading(true);
            const response = await axios.post(
                "/api/admin/course/store",
                course
            );
            showMessage("Course saved", "success");
            setCourse({
                title: "",
                category: "",
                description: "",
                video_url: "",
                video_type: "youtube",
                level: "beginner",
                enrollment_count: 0,
                status: "active",
                prerequisites: "",
                rating_count: 0,
                average_rating: 0,
                likes: 0,
                dislikes: 0,
                related_courses: [],
            });
        } catch (error) {
            showMessage("Error creating course", "error");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-8 bg-white rounded-lg shadow-lg max-w-2xl mx-auto">
            <h2 className="text-2xl font-semibold mb-6 text-gray-800">
                Create New Course
            </h2>
            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div className="col-span-1 sm:col-span-2">
                        <label
                            htmlFor="title"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Title
                        </label>
                        <input
                            id="title"
                            name="title"
                            value={course.title}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            required
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="category"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Category
                        </label>
                        <input
                            id="category"
                            name="category"
                            value={course.category}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            required
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="level"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Level
                        </label>
                        <select
                            id="level"
                            name="level"
                            value={course.level}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        >
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <div>
                        <label
                            htmlFor="video_type"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Video Type
                        </label>
                        <select
                            id="video_type"
                            name="video_type"
                            value={course.video_type}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        >
                            <option value="youtube">YouTube</option>
                            <option value="google">Google Drive</option>
                            <option value="local">Local</option>
                            <option value="iframe">Iframe</option>
                        </select>
                    </div>

                    <div>
                        <label
                            htmlFor="video_url"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Video URL / ID
                        </label>
                        <input
                            id="video_url"
                            name="video_url"
                            value={course.video_url}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            required
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="status"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            value={course.status}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                <div className="col-span-1 sm:col-span-2">
                        <label
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Related Courses
                        </label>
                        <Select
                            isMulti
                            options={allCourses}
                            className="basic-multi-select"
                            classNamePrefix="select"
                            onChange={handleRelatedCoursesChange}
                            value={allCourses.filter(opt => course.related_courses.includes(opt.value))}
                            placeholder="Select related courses..."
                        />
                    </div>
                </div>

                <div>
                    <label
                        htmlFor="description"
                        className="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        value={course.description}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        rows="4"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    disabled={loading}
                    className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto"
                >
                    {loading ? (
                        <span className="fa fa-spinner fa-spin mr-2"></span>
                    ) : (
                        <span className="fa fa-save mr-2"></span>
                    )}
                    {loading ? "Saving..." : "Save Course"}
                </button>
            </form>
        </div>
    );
};

export default CourseForm;

if (document.getElementById("courses-create")) {
    const Index = ReactDOM.createRoot(
        document.getElementById("courses-create")
    );

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <CourseForm />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
