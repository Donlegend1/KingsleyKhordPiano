import ReactDOM from "react-dom/client";
import React, { useState } from "react";
import axios from "axios";
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
        // image_path: '',
        level: "beginner",
        enrollment_count: 0,
        status: "active",
        prerequisites: "",
        // what_you_will_learn: '',

        rating_count: 0,
        average_rating: 0,
        // resources: [],
        // requirements: '',
        likes: 0,
        dislikes: 0,
    });

    const [loading, setLoading] = useState(false);

    const { showMessage } = useFlashMessage();

    const handleChange = (e) => {
        const { name, value } = e.target;
        setCourse({ ...course, [name]: value });
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
                // image_path: '',
                level: "beginner",
                enrollment_count: 0,
                status: "active",
                prerequisites: "",
                // what_you_will_learn: '',

                rating_count: 0,
                average_rating: 0,
                // resources: [],
                // requirements: '',
                likes: 0,
                dislikes: 0,
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
                    <div>
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
                            className="w-full p-3 border rounded-lg"
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
                            className="w-full p-3 border rounded-lg"
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="video_url"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Video URL
                        </label>
                        <input
                            id="video_url"
                            name="video_url"
                            value={course.video_url}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
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
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
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
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
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
                        className="w-full p-3 border rounded-lg"
                        rows="4"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    disabled={loading}
                    className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {loading ? (
                        <span className="fa fa-spinner fa-spin"></span>
                    ) : (
                        "Save Course"
                    )}
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
