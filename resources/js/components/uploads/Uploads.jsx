import ReactDOM from "react-dom/client";
import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import CustomPagination from "../Pagination/CustomPagination";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import Select from "react-select";

const Modal = ({ isOpen, onClose, children }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
            <div className="relative bg-white rounded-lg shadow-lg max-w-3xl w-full mx-4 p-6">
                {/* Close Button (X) */}
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold"
                    aria-label="Close"
                >
                    &times;
                </button>

                {/* Modal Content */}
                <div className="mt-2">{children}</div>
            </div>
        </div>
    );
};

const UploadList = () => {
    const [courses, setCourses] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [loading, setLoading] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
    const perPage = 10;
    const slug = window.location.pathname.split("/").pop();
    const category = slug.replace(/-/g, " ");
    const [thumbnailFile, setThumbnailFile] = useState(null);
    const [tagOptions, setUploadList] = useState([]);
    const [selectedTags, setSelectedTags] = useState([]);
    const { showMessage } = useFlashMessage();

    const levels = ["Beginner", "Intermediate", "Advanced"];
    const pianoLevels = [
        "Independence",
        "Coordination",
        "Flexibility",
        "Strength",
        "Dexterity",
    ];

    const pianoGroup = ["Basic", "Competent", "Challenging"];
    const [saving, setSaving] = useState(false);
    const [selectedCourse, setSelectedCourse] = useState({
        title: "",
        category: "",
        description: "",
        video_url: "",
        level: "beginner",
        enrollment_count: 0,
        status: "active",
        prerequisites: "",
        published_at: "",
        rating_count: 0,
        average_rating: 0,
        // resources: [],
        requirements: "",
    });

    const [upload, setUpload] = useState({
        // tumbnail: null,
        title: "",
        category: category,
        description: "",
        video_url: "",
        level: "",
        skill_level: "",
        status: "active",
    });

    const [preview, setPreview] = useState(
        selectedCourse?.thumbnail_url || null
    );
    const [thumbnail, setThumbnail] = useState(null);
    const fileInputRef = useRef(null);
    const handleChangeCreate = (e) => {
        const { name, value } = e.target;
        setUpload({ ...upload, [name]: value });
    };

    const handleTagsChange = (selectedOptions) => {
        setSelectedTags(selectedOptions);
    };
    const handleImageClick = () => {
        fileInputRef.current.click();
    };

    const handleFileChange = (e) => {
        setThumbnailFile(e.target.files[0]);
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setThumbnail(file);
            setPreview(URL.createObjectURL(file));
            handleChange({ target: { name: "thumbnail", value: file } });
        }
    };

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const fetchCourses = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(
                `/admin/upload-list?page=${page}&perPage=${perPage}&category=${category}`,
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );

            setCourses(response.data.data);
            setCurrentPage(response.data.current_page);
            setTotalPages(response.data.last_page);
        } catch (error) {
            console.error("Error fetching upload:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCourse = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.delete(
                `/admin/upload/${selectedCourse.id}`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            closeDeleteModal();
            showMessage("Course deleted successfully.", "success");
            fetchCourses();
        } catch (error) {
            console.error("Error fetching uploads:", error);
            showMessage("Error deleting course.", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setSelectedCourse({ ...selectedCourse, [name]: value });
    };

    useEffect(() => {
        fetchCourses();
    }, []);

    const handlePageChange = (page) => {
        fetchCourses(page);
    };

    const openEditModal = (course) => {
        setSelectedCourse(course);
        setIsEditModalOpen(true);
    };

    const openDeleteModal = (course) => {
        setSelectedCourse(course);
        setIsDeleteModalOpen(true);
    };

    const closeEditModal = () => {
        setIsEditModalOpen(false);
        setSelectedCourse(null);
    };

    const closeDeleteModal = () => {
        setIsDeleteModalOpen(false);
        setSelectedCourse(null);
    };

    const openCreateModal = () => {
        setIsCreateModalOpen(true);
    };

    const closeCreateModal = () => {
        setIsCreateModalOpen(false);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData();
        formData.append("title", selectedCourse.title);
        formData.append("category", selectedCourse.category);
        formData.append("video_url", selectedCourse.video_url);
        formData.append("status", selectedCourse.status);
        formData.append("level", selectedCourse.level);
        formData.append("description", selectedCourse.description);

        if (thumbnail instanceof File) {
            formData.append("thumbnail", thumbnail);
        }

        try {
            const response = await axios.post(
                `/api/admin/upload/${selectedCourse.id}`,
                formData,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "multipart/form-data",
                    },
                    withCredentials: true,
                }
            );
            closeEditModal();
            showMessage("Course updated successfully.", "success");
            fetchCourses();
        } catch (error) {
            console.error(
                "Error updating course:",
                error?.response?.data || error
            );
            showMessage("Error updating course.", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleCreateCourse = async (e) => {
        e.preventDefault();
        setSaving(true);

        const formData = new FormData();
        formData.append("thumbnail", thumbnailFile);

        // Upload fields
        Object.entries(upload).forEach(([key, value]) =>
            formData.append(key, value)
        );
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
                description: "",
                video_url: "",
                level: "",
                skill_level: "",
                status: "active",
            });
            setSelectedTags([]);
            fetchCourses();
            closeCreateModal();
        } catch (error) {
            showMessage("Error creating upload.", "error");
            console.error("Error creating upload:", error);
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 className="text-lg font-bold mb-4">
                {category.toLocaleUpperCase()} Course List
            </h2>
            <div className="flex justify-end items-center mb-4 ">
                <button
                    className="px-4 py-2 bg-black text-white rounded-full"
                    onClick={openCreateModal}
                >
                    <span className="fa fa-plus"></span>
                </button>
            </div>
            {loading ? (
                <p>Loading...</p>
            ) : (
                <>
                    <table className="min-w-full bg-white mb-4">
                        <thead className="bg-gray-800 text-white">
                            <tr>
                                <th className="py-2 px-4 text-left">S/N</th>
                                <th className="py-2 px-4 text-left">
                                    Course Title
                                </th>
                                <th className="py-2 px-4 text-left">
                                    Banner Image
                                </th>
                                <th className="py-2 px-4 text-left">
                                    Category
                                </th>
                                <th className="py-2 px-4 text-left">Level</th>
                                <th className="py-2 px-4 text-left">Status</th>
                                <th className="py-2 px-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {courses && courses.length > 0 ? (
                                courses.map((upload, index) => (
                                    <tr key={upload.id} className="border-b">
                                        <td className="py-2 px-4">
                                            {index + 1}
                                        </td>
                                        <td className="py-2 px-4">
                                            {upload.title}
                                        </td>
                                        <td className="py-2 px-4">
                                            <div className="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <img
                                                    src={upload.thumbnail_url}
                                                    alt=""
                                                />
                                            </div>
                                        </td>
                                        <td className="py-2 px-4">
                                            {upload.category}
                                        </td>
                                        <td className="py-2 px-4">
                                            {upload.level}
                                        </td>
                                        <td className="py-2 px-4">
                                            {upload.status}
                                        </td>
                                        <td className="py-2 px-4 flex justify-center text-center items-center">
                                            <button
                                                onClick={() =>
                                                    openEditModal(upload)
                                                }
                                                className="bg-blue-500 text-white px-2 py-1 rounded"
                                            >
                                                <span className="fa fa-edit"></span>
                                            </button>
                                            <button
                                                onClick={() =>
                                                    openDeleteModal(upload)
                                                }
                                                className="bg-red-500 text-white px-2 py-1 rounded ml-2"
                                            >
                                                <span className="fa fa-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td
                                        colSpan="6"
                                        className="py-2 px-4 text-center"
                                    >
                                        No Upload found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>

                    <div className="flex items-center justify-center gap-6 mt-6">
                        <CustomPagination
                            currentPage={currentPage}
                            totalPages={totalPages}
                            onPageChange={handlePageChange}
                        />
                    </div>
                </>
            )}

            <Modal
                isOpen={isEditModalOpen}
                onClose={() => setIsEditModalOpen(false)}
            >
                <h2 className="text-lg font-bold mb-2">Edit Course</h2>
                <p>Editing Course: {selectedCourse?.title}</p>

                <div
                    className="
                    max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-lg"
                >
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div
                                className="w-full h-48 bg-gray-100 border rounded-lg flex items-center justify-center cursor-pointer overflow-hidden"
                                onClick={handleImageClick}
                            >
                                {preview ? (
                                    <img
                                        src={preview}
                                        alt="Thumbnail Preview"
                                        className="object-cover w-full h-full"
                                    />
                                ) : (
                                    <span className="text-gray-500">
                                        <img
                                            src={selectedCourse?.thumbnail_url}
                                            alt="Thumbnail Preview"
                                            className="object-cover w-full h-full"
                                        />
                                    </span>
                                )}
                                <input
                                    type="file"
                                    name="thumbnail"
                                    accept="image/*"
                                    onChange={handleImageChange}
                                    ref={fileInputRef}
                                    className="hidden"
                                />
                            </div>

                            {/* Course Info Fields */}
                            <div className="flex flex-col space-y-4">
                                <input
                                    name="title"
                                    placeholder="Title"
                                    defaultValue={selectedCourse?.title}
                                    onChange={handleChange}
                                    className="w-full p-3 border rounded-lg"
                                />
                                <input
                                    name="category"
                                    placeholder="Category"
                                    disabled
                                    defaultValue={selectedCourse?.category}
                                    onChange={handleChange}
                                    className="w-full p-3 border rounded-lg"
                                />
                                <input
                                    name="video_url"
                                    placeholder="Video URL"
                                    defaultValue={selectedCourse?.video_url}
                                    onChange={handleChange}
                                    className="w-full p-3 border rounded-lg"
                                />
                            </div>

                            {/* Select Fields */}
                            <select
                                name="status"
                                defaultValue={selectedCourse?.status}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>

                            <select
                                name="level"
                                defaultValue={selectedCourse?.level}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                            >
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">
                                    Intermediate
                                </option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        {/* Description Field */}
                        <textarea
                            name="description"
                            placeholder="Description"
                            defaultValue={selectedCourse?.description}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                            rows="4"
                        ></textarea>

                        {/* Submit Button */}
                        <div className="text-right">
                            <button
                                type="submit"
                                className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300"
                            >
                                Save Course
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

            <Modal
                isOpen={isDeleteModalOpen}
                onClose={() => setIsDeleteModalOpen(false)}
            >
                <div className="text-center p-6">
                    <h2 className="text-2xl font-bold text-gray-800 mb-4">
                        Confirm Deletion
                    </h2>
                    <p className="text-gray-600 mb-6">
                        Are you sure you want to delete{" "}
                        <span className="font-semibold text-red-600">
                            {selectedCourse?.title}
                        </span>
                        ?
                    </p>
                    <small>This action cannot be undone.</small>

                    <div className="flex justify-center space-x-4">
                        <button
                            onClick={() => setIsDeleteModalOpen(false)}
                            className="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded"
                        >
                            Cancel
                        </button>
                        <button
                            onClick={handleDeleteCourse} // Make sure to define this function
                            className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded"
                        >
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </Modal>

            <Modal isOpen={isCreateModalOpen} onClose={closeCreateModal}>
                <h2 className="text-lg font-bold mb-2">Add Course to {category.toLocaleUpperCase()}</h2>

                <div
                    className="
                    max-w-3xl mx-auto bg-white p-5 rounded-lg shadow-lg"
                >
                    <form onSubmit={handleCreateCourse} className="space-y-3">
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
                                    onChange={handleChangeCreate}
                                    className="w-full p-3 border rounded-lg"
                                />
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
                                    onChange={handleChangeCreate}
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
                                    onChange={handleChangeCreate}
                                    className="w-full p-3 border rounded-lg"
                                >
                                    <option value="">Select</option>
                                    {upload.category === "piano exercise"
                                        ? pianoLevels.map((level) => (
                                              <option key={level} value={level}>
                                                  {level
                                                      .charAt(0)
                                                      .toUpperCase() +
                                                      level.slice(1)}
                                              </option>
                                          ))
                                        : levels.map((level) => (
                                              <option key={level} value={level}>
                                                  {level
                                                      .charAt(0)
                                                      .toUpperCase() +
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
                                        onChange={handleChangeCreate}
                                        className="w-full p-3 border rounded-lg"
                                    >
                                        <option value="">Select</option>

                                        {upload.category === "piano exercise"
                                            ? pianoGroup.map((level) => (
                                                  <option
                                                      key={level}
                                                      value={level}
                                                  >
                                                      {level
                                                          .charAt(0)
                                                          .toUpperCase() +
                                                          level.slice(1)}
                                                  </option>
                                              ))
                                            : levels.map((level) => (
                                                  <option
                                                      key={level}
                                                      value={level}
                                                  >
                                                      {level
                                                          .charAt(0)
                                                          .toUpperCase() +
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
                                    onChange={handleChangeCreate}
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
                                onChange={handleChangeCreate}
                                className="w-full p-1 border rounded-lg"
                                rows="2"
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
            </Modal>
        </div>
    );
};

export default UploadList;

if (document.getElementById("uploads")) {
    const Index = ReactDOM.createRoot(document.getElementById("uploads"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <UploadList />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
