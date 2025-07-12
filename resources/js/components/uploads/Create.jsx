import ReactDOM from "react-dom/client";
import React, { useState, useEffect } from "react";
import Select from "react-select";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const UploadForm = () => {
    const [thumbnailFile, setThumbnailFile] = useState(null);
    const { showMessage } = useFlashMessage();
    const [tagOptions, setUploadList] = useState([]);

    const [selectedTags, setSelectedTags] = useState([]);

    const handleTagsChange = (selectedOptions) => {
        setSelectedTags(selectedOptions);
    };

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const [upload, setUpload] = useState({
        // tumbnail: null,
        title: "",
        category: "",
        description: "",
        video_url: "",
        level: "",
        skill_level: "",
        status: "active",
    });
    const [saving, setSaving] = useState(false);

    const categories = [
        "piano exercise",
        "extra courses",
        "quick lessons",
        "learn songs",
    ];

    const levels = ["Beginner", "Intermediate", "Advanced"];
    const pianoLevels = [
        "Independence",
        "Coordination",
        "Flexibility",
        "Strength",
        "Dexterity",
    ];

    const pianoGroup = ["Basic", "Competent", "Challenging"];

    const handleChange = (e) => {
        const { name, value } = e.target;
        setUpload({ ...upload, [name]: value });
    };

    const handleFileChange = (e) => {
        setThumbnailFile(e.target.files[0]);
    };

    const fetchCourses = async () => {
    try {
        const response = await axios.get(`/admin/upload-list`, {
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
            withCredentials: true,
        });
        const formatted = response?.data.map((item) => ({
            value: item.id,
            label: item.title, 
        }));

        setUploadList(formatted);
    } catch (error) {
        console.error("Error fetching courses:", error);
    }
    };


    const handleSubmit = async (e) => {
    e.preventDefault();
    setSaving(true);

    const formData = new FormData();
    formData.append("thumbnail", thumbnailFile);

    // Upload fields
    Object.entries(upload).forEach(([key, value]) =>
        formData.append(key, value)
    );

    // Append selected tag IDs as an array
    selectedTags.forEach((tag, index) => {
        formData.append(`tags[${index}]`, tag.value);
    });

    try {
        const response = await axios.post("/admin/upload/store", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        showMessage("Record Saved successfully.", "success");
        setUpload({
            title: "",
            category: "",
            description: "",
            video_url: "",
            level: "",
            skill_level: "",
            status: "active",
        });
        setSelectedTags([]);
    } catch (error) {
        showMessage("Error creating upload.", "error");
        console.error("Error creating upload:", error);
    } finally {
        setSaving(false);
    }
};


    useEffect(() => {
        fetchCourses();
    }, []);

    return (
        <div className="p-8 bg-white rounded-lg shadow-lg max-w-2xl mx-auto">
            <h2 className="text-2xl font-semibold mb-6 text-gray-800">
                Add a New Upload
            </h2>
            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {/* Thumbnail Upload */}
                    <div>
                        <label
                            htmlFor="thumbnail"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Thumbnail
                        </label>
                        <input
                            type="file"
                            id="thumbnail"
                            name="thumbnail"
                            accept="image/*"
                            onChange={handleFileChange}
                            className="w-full p-3 border rounded-lg"
                        />
                    </div>

                    {/* Title */}
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
                            placeholder="Title"
                            value={upload.title}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                    </div>

                    {/* Category */}
                    <div>
                        <label
                            htmlFor="category"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Category
                        </label>
                        <select
                            id="category"
                            name="category"
                            value={upload.category}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="">Select</option>
                            {categories.map((category) => (
                                <option key={category} value={category}>
                                    {category.charAt(0).toUpperCase() +
                                        category.slice(1)}
                                </option>
                            ))}
                        </select>
                    </div>

                    {/* Video URL */}
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
                            placeholder="Video URL"
                            value={upload.video_url}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                    </div>

                    {/* Level */}
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
                            value={upload.level}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="">Select</option>
                            {upload.category === "piano exercise"
                                ? pianoLevels.map((level) => (
                                      <option key={level} value={level}>
                                          {level.charAt(0).toUpperCase() +
                                              level.slice(1)}
                                      </option>
                                  ))
                                : levels.map((level) => (
                                      <option key={level} value={level}>
                                          {level.charAt(0).toUpperCase() +
                                              level.slice(1)}
                                      </option>
                                  ))}
                        </select>
                    </div>
                    {upload.category === "piano exercise" && (
                        <div>
                            <label
                                htmlFor="skill_level"
                                className="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Skil Level
                            </label>
                            <select
                                id="skill_level"
                                name="skill_level"
                                value={upload.skill_level}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                            >
                                <option value="">Select</option>

                                {upload.category === "piano exercise"
                                    ? pianoGroup.map((level) => (
                                          <option key={level} value={level}>
                                              {level.charAt(0).toUpperCase() +
                                                  level.slice(1)}
                                          </option>
                                      ))
                                    : levels.map((level) => (
                                          <option key={level} value={level}>
                                              {level.charAt(0).toUpperCase() +
                                                  level.slice(1)}
                                          </option>
                                      ))}
                            </select>
                        </div>
                    )}
                    <div>
                        <label
                            htmlFor="tags"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Related Courses
                        </label>
                        <Select
                            id="tags"
                            isMulti
                            name="tags"
                            options={tagOptions}
                            value={selectedTags}
                            onChange={handleTagsChange}
                            className="basic-multi-select"
                            classNamePrefix="select"
                        />
                    </div>

                    {/* Status */}
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
                            value={upload.status}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                {/* Description */}
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
                        placeholder="Description"
                        value={upload.description}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                        rows="4"
                    ></textarea>
                </div>

                {/* Submit Button */}
                <button
                    disabled={saving}
                    type="submit"
                    className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300 flex items-center justify-center gap-2"
                >
                    {saving ? (
                        <>
                            <svg
                                className="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    className="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    strokeWidth="4"
                                ></circle>
                                <path
                                    className="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"
                                ></path>
                            </svg>
                            <span>Saving...</span>
                        </>
                    ) : (
                        <span>Save Upload</span>
                    )}
                </button>
            </form>
        </div>
    );
};

export default UploadForm;

if (document.getElementById("upload-form")) {
    const Index = ReactDOM.createRoot(document.getElementById("upload-form"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <UploadForm />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
