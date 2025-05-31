import ReactDOM from "react-dom/client";
import React, { useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const UploadForm = () => {
    const [thumbnailFile, setThumbnailFile] = useState(null);
    const { showMessage } = useFlashMessage();
    const [upload, setUpload] = useState({
        // tumbnail: null,
        title: "",
        category: "",
        description: "",
        video_url: "",
        level: "",
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

    const handleChange = (e) => {
        const { name, value } = e.target;
        setUpload({ ...upload, [name]: value });
    };

    const handleFileChange = (e) => {
        setThumbnailFile(e.target.files[0]);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);

        const formData = new FormData();
        formData.append("thumbnail", thumbnailFile);
        Object.entries(upload).forEach(([key, value]) =>
            formData.append(key, value)
        );

        try {
            const response = await axios.post("/admin/upload/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            showMessage("Record Saved successfully.", "success");
            setUpload({})
        } catch (error) {
            showMessage("Error creating upload.", "error");
            console.error("Error creating upload:", error);
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="p-8 bg-white rounded-lg shadow-lg max-w-2xl mx-auto">
            <h2 className="text-2xl font-semibold mb-6 text-gray-800">
                Add a New Upload
            </h2>
            <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input
                        type="file"
                        name="thumbnail"
                        accept="image/*"
                        onChange={handleFileChange}
                        className="w-full p-3 border rounded-lg"
                    />
                    <input
                        name="title"
                        placeholder="Title"
                        value={upload.title}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                    />
                    <select
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
                    <input
                        name="video_url"
                        placeholder="Video URL"
                        value={upload.video_url}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                    />
                    <select
                        name="level"
                        value={upload.level}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                    >
                        <option value="">select</option>
                        {upload.category === "piano exercise" ? (
                            <>
                                {pianoLevels.map((level) => (
                                    <option key={level} value={level}>
                                        {level.charAt(0).toUpperCase() +
                                            level.slice(1)}
                                    </option>
                                ))}
                            </>
                        ) : (
                            <>
                                {levels.map((level) => (
                                    <option key={level} value={level}>
                                        {level.charAt(0).toUpperCase() +
                                            level.slice(1)}
                                    </option>
                                ))}
                            </>
                        )}
                    </select>

                    <select
                        name="status"
                        value={upload.status}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <textarea
                    name="description"
                    placeholder="Description"
                    value={upload.description}
                    onChange={handleChange}
                    className="w-full p-3 border rounded-lg"
                    rows="4"
                ></textarea>
                {/* <textarea name="what_you_will_learn" placeholder="What You Will Learn" value={upload.what_you_will_learn} onChange={handleChange} className="w-full p-3 border rounded-lg" rows="4"></textarea> */}

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
                        <span>Save upload</span>
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
