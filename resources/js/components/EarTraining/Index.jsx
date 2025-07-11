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
                `/admin/ear-training/delete/${selectedQuiz.id}`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            fetchQuizzes();
            closeDeleteModal();

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

        const formData = new FormData();

        formData.append("title", selectedQuiz.title || "");
        formData.append("video_url", selectedQuiz.video_url || "");
        formData.append("description", selectedQuiz.description || "");

        // Files
        if (selectedQuiz.thumbnail instanceof File) {
            formData.append("thumbnail", selectedQuiz.thumbnail);
        }

        if (selectedQuiz.main_audio instanceof File) {
            formData.append("main_audio", selectedQuiz.main_audio);
        }

        // Questions (nested)
        selectedQuiz.questions.forEach((q, index) => {
            formData.append(`questions[${index}][audio]`, q.audio);
            formData.append(
                `questions[${index}][correct_option]`,
                q.correct_option
            );
        });

        try {
            const response = await axios.post(
                `/admin/ear-training/update/${selectedQuiz.id}`,
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
            fetchQuizzes();
        } catch (error) {
            console.error("Error submitting quiz:", error);
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
                                            <img
                                                className="object-cover h-10 w-10"
                                                src={
                                                    "/storage/" +
                                                    user.thumbnail_path
                                                }
                                                alt=""
                                            />
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
                                        No Quiz found.
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

            <Modal isOpen={isEditModalOpen} onClose={closeEditModal}>
                <div className="max-h-[80vh] overflow-y-auto p-6">
                    <h2 className="text-lg font-bold mb-4">
                        Edit Quiz: {selectedQuiz?.title}
                    </h2>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input
                                name="title"
                                placeholder="Title"
                                value={selectedQuiz?.title || ""}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                            />

                            <input
                                name="video_url"
                                placeholder="Video URL"
                                value={selectedQuiz?.video_url || ""}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                            />
                        </div>

                        <textarea
                            name="description"
                            placeholder="Description"
                            value={selectedQuiz?.description || ""}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                            rows="4"
                        ></textarea>

                        {/* File Uploads */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block mb-1 font-medium">
                                    Thumbnail
                                </label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) =>
                                        setSelectedQuiz({
                                            ...selectedQuiz,
                                            thumbnail: e.target.files[0],
                                        })
                                    }
                                    className="w-full"
                                />
                            </div>

                            <div>
                                <label className="block mb-1 font-medium">
                                    Main Audio
                                </label>
                                <input
                                    type="file"
                                    accept="audio/*"
                                    onChange={(e) =>
                                        setSelectedQuiz({
                                            ...selectedQuiz,
                                            main_audio: e.target.files[0],
                                        })
                                    }
                                    className="w-full"
                                />
                            </div>
                        </div>

                        {/* Questions Section */}
                        <div>
                            <h3 className="text-md font-semibold mb-2">
                                Quiz Questions
                            </h3>

                            {selectedQuiz?.questions?.map((q, index) => (
                                <div
                                    key={index}
                                    className="border rounded p-4 mb-3"
                                >
                                    <div className="mb-2">
                                        <label className="block mb-1 text-sm">
                                            Question Audio
                                        </label>
                                        <input
                                            type="file"
                                            accept="audio/*"
                                            onChange={(e) => {
                                                const updatedQuestions = [
                                                    ...selectedQuiz.questions,
                                                ];
                                                updatedQuestions[index].audio =
                                                    e.target.files[0];
                                                setSelectedQuiz({
                                                    ...selectedQuiz,
                                                    questions: updatedQuestions,
                                                });
                                            }}
                                            className="w-full"
                                        />
                                    </div>

                                    <div className="mb-2">
                                        <label className="block mb-1 text-sm">
                                            Correct Option
                                        </label>
                                        <select
                                            value={q.correct_option ?? ""}
                                            onChange={(e) => {
                                                const updatedQuestions = [
                                                    ...selectedQuiz.questions,
                                                ];
                                                updatedQuestions[
                                                    index
                                                ].correct_option =
                                                    e.target.value;
                                                setSelectedQuiz({
                                                    ...selectedQuiz,
                                                    questions: updatedQuestions,
                                                });
                                            }}
                                            className="w-full p-2 border rounded"
                                        >
                                            <option value="">
                                                Select Option
                                            </option>
                                            <option value="0">DOH</option>
                                            <option value="1">REH</option>
                                            <option value="2">MI</option>
                                            <option value="3">FAH</option>
                                            <option value="4">SOH</option>
                                            <option value="5">LAH</option>
                                            <option value="6">TI</option>
                                        </select>
                                    </div>

                                    <button
                                        type="button"
                                        onClick={() => {
                                            const updatedQuestions =
                                                selectedQuiz.questions.filter(
                                                    (_, i) => i !== index
                                                );
                                            setSelectedQuiz({
                                                ...selectedQuiz,
                                                questions: updatedQuestions,
                                            });
                                        }}
                                        className="text-red-600 text-sm"
                                    >
                                        Remove Question
                                    </button>
                                </div>
                            ))}

                            <button
                                type="button"
                                onClick={() => {
                                    const updatedQuestions =
                                        selectedQuiz.questions
                                            ? [...selectedQuiz.questions]
                                            : [];
                                    updatedQuestions.push({
                                        audio: null,
                                        correct_option: "",
                                    });
                                    setSelectedQuiz({
                                        ...selectedQuiz,
                                        questions: updatedQuestions,
                                    });
                                }}
                                className="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                                Add Question
                            </button>
                        </div>

                        <div className="flex justify-end mt-6">
                            <button
                                type="submit"
                                className="px-6 py-3 bg-black text-white rounded-lg hover:bg-green-600"
                            >
                                Save Quiz
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
