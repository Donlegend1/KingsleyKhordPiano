import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";

const Modal = ({ isOpen, onClose, children }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
            <div className="relative bg-white rounded-lg shadow-lg max-w-3xl w-full mx-4 p-6">
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold"
                    aria-label="Close"
                >
                    &times;
                </button>
                <div className="mt-2">{children}</div>
            </div>
        </div>
    );
};

const EarTraining = () => {
    const [quizzes, setquizzes] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [loading, setLoading] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    // const [selectedQuiz, setSelectedQuiz] = useState(null);
    const perPage = 10;
    const [selectedQuiz, setSelectedQuiz] = useState({
        title: "",
        description: "",
        main_video: "",
        main_audio: "",
        thumbnail: "",
    });
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const fetchQuizzes = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(
                `/admin/ear-training/list?page=${page}&perPage=${perPage}`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            console.log(response.data);
            setquizzes(response.data.data);
            setCurrentPage(response.data.current_page);
            setTotalPages(response.data.last_page);
        } catch (error) {
            console.error("Error fetching users:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteQuiz = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.delete(
                `/api/admin/quizzes/${selectedQuiz.id}`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            closeDeleteModal();
            console.log(response.data);
            fetchquizzes();
        } catch (error) {
            console.error("Error fetching users:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setSelectedQuiz({ ...selectedQuiz, [name]: value });
    };

    useEffect(() => {
        fetchQuizzes();
    }, []);

    const handlePageChange = (page) => {
        fetchQuizzes(page);
    };

    const openEditModal = (course) => {
        setSelectedQuiz(course);
        setIsEditModalOpen(true);
    };

    const openDeleteModal = (course) => {
        setSelectedQuiz(course);
        setIsDeleteModalOpen(true);
    };

    const closeEditModal = () => {
        setIsEditModalOpen(false);
        setSelectedQuiz(null);
    };

    const closeDeleteModal = () => {
        setIsDeleteModalOpen(false);
        setSelectedQuiz(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            const response = await axios.patch(
                `/api/admin/quizzes/${selectedQuiz.id}`,
                selectedQuiz,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            console.log(response.data);
            closeEditModal();
            fetchQuizzes();
        } catch (error) {
            console.error("Error creating course:", error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 className="text-lg font-bold mb-4">Ear Training Quiz List</h2>
            <div className="flex justify-end items-center mb-4 ">
                <a
                    className="px-4 py-2 bg-black text-white rounded-full"
                    href="/admin/ear-training/create"
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
                                <th className="py-2 px-4 text-left">
                                    Quiz Title
                                </th>
                                <th className="py-2 px-4 text-left">
                                    Description
                                </th>
                                <th className="py-2 px-4 text-left">
                                    Thumbnail
                                </th>
                                <th className="py-2 px-4 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {quizzes && quizzes.length > 0 ? (
                                quizzes.map((user, index) => (
                                    <tr key={user.id} className="border-b">
                                        <td className="py-2 px-4">
                                            {index + 1}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.title}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.title}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.thumbnail}
                                        </td>
                                        <td className="py-2 px-4">
                                            <button
                                                onClick={() =>
                                                    openEditModal(user)
                                                }
                                                className="bg-blue-500 text-white px-2 py-1 rounded"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                onClick={() =>
                                                    openDeleteModal(user)
                                                }
                                                className="bg-red-500 text-white px-2 py-1 rounded ml-2"
                                            >
                                                Delete
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
                                        No Quiz found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>

                    <div className="flex justify-between items-center">
                        <button
                            disabled={currentPage === 1}
                            onClick={() => handlePageChange(currentPage - 1)}
                            className="px-4 py-2 bg-gray-300 rounded disabled:bg-gray-200"
                        >
                            Previous
                        </button>
                        <span>
                            Page {currentPage} of {totalPages}
                        </span>
                        <button
                            disabled={currentPage === totalPages}
                            onClick={() => handlePageChange(currentPage + 1)}
                            className="px-4 py-2 bg-gray-300 rounded disabled:bg-gray-200"
                        >
                            Next
                        </button>
                    </div>
                </>
            )}

            <Modal
                isOpen={isEditModalOpen}
                onClose={() => setIsEditModalOpen(false)}
            >
                <h2 className="text-lg font-bold mb-2">Edit Course</h2>
                <p>Editing Course: {selectedQuiz?.title}</p>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input
                            name="title"
                            placeholder="Title"
                            defaultValue={selectedQuiz?.title}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            name="category"
                            placeholder="Category"
                            defaultValue={selectedQuiz?.category}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            name="video_url"
                            placeholder="Video URL"
                            defaultValue={selectedQuiz?.video_url}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />

                        <select
                            name="status"
                            defaultValue={selectedQuiz?.status}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>

                        <select
                            name="level"
                            defaultValue={selectedQuiz?.level}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>

                    <textarea
                        name="description"
                        placeholder="Description"
                        defaultValue={selectedQuiz?.description}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                        rows="4"
                    ></textarea>

                    <button
                        type="submit"
                        className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300"
                    >
                        Save Course
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
                            {selectedQuiz?.title}
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
                            onClick={handleDeleteQuiz} // Make sure to define this function
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

export default EarTraining;

if (document.getElementById("ear-training")) {
    const Index = ReactDOM.createRoot(document.getElementById("ear-training"));

    Index.render(
        <React.StrictMode>
            <EarTraining />
        </React.StrictMode>
    );
}
