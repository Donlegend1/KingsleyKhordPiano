import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

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

const LiveShow = () => {
    const [courses, setCourses] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [loading, setLoading] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
     const { showMessage } = useFlashMessage();
    // const [liveShow, setliveShow] = useState(null);
    const perPage = 10;
    const [liveShow, setLiveShow] = useState({
        title: "",
        recording_url: "",
        zoom_link: "",
        access_type: "all",
        start_time: "",
    });
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const fetchCourses = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/live-shows`, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                withCredentials: true,
            });
            setCourses(response.data);
            setCurrentPage(response.data.current_page);
            setTotalPages(response.data.last_page);
        } catch (error) {
            console.error("Error fetching users:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCourse = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.delete(
                `/api/admin/live-show/${liveShow.id}/delete`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            closeDeleteModal();
            showMessage("Live show deleted successfully!", "success");
            fetchCourses();
        } catch (error) {
            console.error("Error deleting liveshow:", error);
            showMessage("Error deleting!", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setLiveShow({ ...liveShow, [name]: value });
    };

    useEffect(() => {
        fetchCourses();
    }, []);

    const handlePageChange = (page) => {
        fetchCourses(page);
    };

    const openEditModal = (course) => {
        setLiveShow(course);
        setIsEditModalOpen(true);
    };

    const openDeleteModal = (course) => {
        setLiveShow(course);
        setIsDeleteModalOpen(true);
    };

    const closeEditModal = () => {
        setIsEditModalOpen(false);
        setLiveShow(null);
    };

    const closeDeleteModal = () => {
        setIsDeleteModalOpen(false);
        setLiveShow(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            const response = await axios.patch(
                `/api/admin/live-shows/${liveShow.id}`,
                liveShow,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            showMessage("Live show updated successfully!", "success");
            closeEditModal();
            fetchCourses();
        } catch (error) {
            console.error("Error creating course:", error);
            showMessage("Error updating live show!", "error");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 className="text-lg font-bold mb-4">Live Show List</h2>
            <div className="flex justify-end items-center mb-4 ">
                <a
                    className="px-4 py-2 bg-black text-white rounded-full"
                    href="/admin/live-show/create"
                >
                    <span className="fa fa-plus"></span>
                </a>
            </div>
            {loading ? (
                <p>Loading...</p>
            ) : (
                <>
                    <table className="min-w-full bg-white mb-4">
                        <thead className="bg-gray-800 text-white">
                            <tr>
                                <th className="py-2 px-4 text-left">S/N</th>
                                <th className="py-2 px-4 text-left">Title</th>
                                <th className="py-2 px-4 text-left">Date</th>
                                <th className="py-2 px-4 text-left">
                                    Access Level
                                </th>
                                <th className="py-2 px-4 text-left">
                                    Video Url
                                </th>

                                <th className="py-2 px-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {courses && courses.length > 0 ? (
                                courses.map((user, index) => (
                                    <tr key={user.id} className="border-b">
                                        <td className="py-2 px-4">
                                            {index + 1}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.title}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.start_time}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.access_type}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.recording_url}
                                        </td>
                                        <td className="py-2 px-4 flex">
                                            <button
                                                onClick={() =>
                                                    openEditModal(user)
                                                }
                                                className="bg-blue-500 text-white px-2 py-1 rounded"
                                            >
                                                <span className="fa fa-edit"></span>
                                            </button>
                                            <button
                                                onClick={() =>
                                                    openDeleteModal(user)
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
                                        No users found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>

                    <div className="flex items-center justify-center gap-6 mt-6">
                        <button
                            disabled={currentPage === 1}
                            onClick={() => handlePageChange(currentPage - 1)}
                            className="p-3 rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            <i className="fas fa-chevron-left"></i>
                        </button>

                        <span className="text-gray-700 text-sm font-medium">
                            Page {currentPage} of {totalPages}
                        </span>

                        <button
                            disabled={currentPage === totalPages}
                            onClick={() => handlePageChange(currentPage + 1)}
                            className="p-3 rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            <i className="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </>
            )}

            <Modal
                isOpen={isEditModalOpen}
                onClose={() => setIsEditModalOpen(false)}
            >
                <h2 className="text-lg font-bold mb-4">Edit Live Show</h2>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input
                            type="text"
                            name="title"
                            placeholder="Title"
                            defaultValue={liveShow?.title}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            type="text"
                            name="zoom_link"
                            placeholder="Zoom Link"
                            defaultValue={liveShow?.zoom_link}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            type="text"
                            name="recording_url"
                            placeholder="Recording URL"
                            defaultValue={liveShow?.recording_url}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            type="datetime-local"
                            name="start_time"
                            defaultValue={liveShow?.start_time}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <select
                            name="access_type"
                            defaultValue={liveShow?.access_type}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="all">All Users</option>
                            <option value="premium">Premium Only</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        className="w-full px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300"
                    >
                        Save Changes
                    </button>
                </form>
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
                            {liveShow?.title}
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
        </div>
    );
};

export default LiveShow;

if (document.getElementById("live")) {
    const Index = ReactDOM.createRoot(document.getElementById("live"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <LiveShow />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
