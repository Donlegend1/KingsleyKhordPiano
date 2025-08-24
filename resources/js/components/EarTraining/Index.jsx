import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import CustomPagination from "../Pagination/CustomPagination";


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

    const [newQuestions, setNewQuestions] = useState([]);

    const perPage = 10;
    const [selectedQuiz, setSelectedQuiz] = useState({
        title: "",
        description: "",
        main_video: "",
        main_audio: "",
        thumbnail: "",
        main_audio_path: "",
    });

    const RELATIVE_OPTIONS = ["DOH", "REH", "MI", "FAH", "SOH", "LAH", "TI"];
    const DITONE_OPTIONS = [
        "DOH MI",
        "REH FAH",
        "MI SOH",
        "FAH LAH",
        "SOH TI",
        "LAH DOH",
        "TI REH",
    ];
    const DIATOMIC_INTERVALS = [
        "Major 2nd",
        "Major 3rd",
        "Perfect 4th",
        "Perfect 5th",
        "Major 6th",
        "Major 7th",
        "Octave",
    ];
    const NONDIATOMIC_INTERVALS = [
        "Minor 2nd",
        "Minor 3rd",
        "Tri tone",
        "Minor 6th",
        "Minor 7th",
    ];
    const BASICTRIADS = ["Augmented", "Diminished", "Major", "Minor", "Sus"];

    const INTERVALS = [
        "Minor 2nd",
        "Major 2nd",
        "Minor 3rd",
        "Major 3rd",
        "Perfect 4th",
        "Tri tone",
        "Perfect 5th",
        "Minor 6th",
        "Major 6th",
        "Minor 7th",
        "Major 7th",
        "Octave",
    ];

    const SEVENDEGREECHORD = [
        "Diminished 7th",
        "Dominant 7th",
        "Minor 7b5",
        "Major 7th",
        "Minor 7th",
    ];

    const SEVENDEGREECHORDSECONDARY = [
        "Dim (Maj7)",
        "Dom7#5",
        "Dom7b5",
        "Maj7#5",
        "Maj7b5",
        "minMaj7",
    ];
    const SEVENDEGREECHORDEGENERAL = [
        "Diminished 7th",
        "Dominant 7th",
        "Minor 7b5",
        "Major 7th",
        "Minor 7th",
        "Dim (Maj7)",
        "Dom7#5",
        "Dom7b5",
        "Maj7#5",
        "Maj7b5",
        "minMaj7",
    ];

    const NINEDEGREECHORD = [
        "Dim7 (9)",
        "Dom9",
        "Dom7 (b9)",
        "Maj 6/9",
        "min 6/9",
        "min9",
        "min9 (b5)",
    ];

    const NINEDEGREECHORDSECONDARY = [
        "DimMaj7 (9)",
        "Dom9 (b5)",
        "Dom9 (#5)",
        "Maj9 (b5)",
        "Maj9 (#5)",
        "min (Maj9)",
    ];

    const NINEDEGREECHORDGENERAL = [
        "DimMaj7 (9)",
        "Dom9 (b5)",
        "Dom9 (#5)",
        "Maj9 (b5)",
        "Maj9 (#5)",
        "min (Maj9)",
        "Dim7 (9)",
        "Dom9",
        "Dom7 (b9)",
        "Maj 6/9",
        "min 6/9",
        "min9",
        "min9 (b5)",
    ];

    const ELEVENDEGREE = [
        "6/9 (#11)",
        "Dom9 (#11)",
        "Dom7 (b9#11)",
        "Maj9 (#11)",
        "min6/9 (11)",
        "min 9 (11)",
    ];

    const THIRTEENDEGREE = [
        "13sus4",
        "Dom 9 (13) #11",
        "Dom 13 (b9#11)",
        "Maj13 (#11)",
        "min13 (9,11)",
    ];

    const OTHERS = [
        "9sus4",
        "DimM9 (#5)",
        "Dom9 (#5b5)",
        "Maj9sus4",
        "Maj9 (b5#5)",
        "min9/11 (Maj7)",
    ];

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

    const getOptionsByCategory = (category) => {
        switch (category) {
            case "Relative Pitch":
                return RELATIVE_OPTIONS;
            case "Di-tone Pitch":
                return DITONE_OPTIONS;
            case "Diatonic Intervals":
                return DIATOMIC_INTERVALS;
            case "Non-diatonic Intervals":
                return NONDIATOMIC_INTERVALS;
            case "Intervals":
                return INTERVALS;
            case "Basic Triad":
                return BASICTRIADS;
            case "7th Degree Chords (Basic)":
                return SEVENDEGREECHORD;
            case "7th Degree Chords (Gecondary)":
                return SEVENDEGREECHORDSECONDARY;
            case "7th Degree Chords (General)":
                return SEVENDEGREECHORDEGENERAL;
            case "9th degree Chords (Basic)":
                return NINEDEGREECHORD;
            case "9th Degree Chords (Secondary)":
                return NINEDEGREECHORDSECONDARY;
            case "9th Degree Chords (General)":
                return NINEDEGREECHORDGENERAL;
            case "11th Degree Chords":
                return ELEVENDEGREE;
            case "13th Degree Chords":
                return THIRTEENDEGREE;
            case "Others":
                return OTHERS;
            default:
                return [];
        }
    };

    const handleDeleteQuestion = async (id, index) => {
        try {
            // If the question has an ID (i.e., it's saved in the DB), delete it via API
            if (id) {
                await axios.delete(`/admin/ear-training/question/${id}`, {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                });
            }

            // Remove from local state
            const updated = selectedQuiz?.questions.filter(
                (_, i) => i !== index
            );
            setSelectedQuiz({
                ...selectedQuiz,
                questions: updated,
            });
        } catch (error) {
            console.error("Error deleting question:", error);
        }
    };

    const addNewQuestionField = () => {
        setNewQuestions([...newQuestions, { audio: null, correct_option: "" }]);
    };

    const removeNewQuestion = (index) => {
        const updated = [...newQuestions];
        updated.splice(index, 1);
        setNewQuestions(updated);
    };

    const handleNewQuestionChange = (index, field, value) => {
        const updated = [...newQuestions];
        updated[index][field] = value;
        setNewQuestions(updated);
    };

    const submitNewQuestions = async () => {
        if (!selectedQuiz?.id) return;

        const formData = new FormData();
        newQuestions.forEach((q, i) => {
            formData.append(`questions[${i}][audio]`, q.audio);
            formData.append(
                `questions[${i}][correct_option]`,
                q.correct_option
            );
        });

        try {
            await axios.post(
                `/admin/ear-training/${selectedQuiz.id}/questions`,
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                }
            );

            setNewQuestions([]); // reset new question form
            fetchQuizzes(); // refetch updated quiz
        } catch (err) {
            console.error("Error adding questions:", err);
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
                                                src={user.thumbnail_path}
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
                        <CustomPagination
                                currentPage={currentPage}   
                                totalPages={totalPages}
                                onPageChange={handlePageChange}
                        />
                    </div>
                </>
            )}

            <Modal isOpen={isEditModalOpen} onClose={closeEditModal}>
                <div className="max-h-[80vh] overflow-y-auto p-6">
                    <h2 className="text-lg font-bold mb-4">
                        Edit Quiz: {selectedQuiz?.title}
                    </h2>

                    <form onSubmit={handleSubmit} className="space-y-8">
                        {/* Thumbnail & Title/Video URL */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">
                            {/* Thumbnail Preview */}
                            <div className="flex flex-col items-center gap-3">
                                <label className="block mb-1 font-medium">
                                    Thumbnail
                                </label>
                                <img
                                    src={selectedQuiz?.thumbnail_path}
                                    alt="Thumbnail"
                                    className="w-48 h-32 object-cover rounded border"
                                />
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={(e) =>
                                        setSelectedQuiz({
                                            ...selectedQuiz,
                                            thumbnail: e.target.files[0],
                                        })
                                    }
                                    className="text-sm"
                                />
                            </div>

                            {/* Title and Video URL */}
                            <div className="space-y-4">
                                <div>
                                    <label className="block mb-1 font-medium">
                                        Title
                                    </label>
                                    <input
                                        name="title"
                                        placeholder="Quiz Title"
                                        value={selectedQuiz?.title || ""}
                                        onChange={handleChange}
                                        className="w-full p-3 border rounded-lg"
                                    />
                                </div>

                                <div>
                                    <label className="block mb-1 font-medium">
                                        Video URL
                                    </label>
                                    <input
                                        name="video_url"
                                        placeholder="Embedded Video URL"
                                        value={selectedQuiz?.video_url || ""}
                                        onChange={handleChange}
                                        className="w-full p-3 border rounded-lg"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Description */}
                        <div>
                            <textarea
                                name="description"
                                placeholder="Quiz Description"
                                value={selectedQuiz?.description || ""}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                                rows="4"
                            ></textarea>
                        </div>

                        {/* Main Audio Upload */}
                        <div>
                            <label className="block mb-1 font-medium">
                                Main Audio 
                            </label>
                            <audio
                                controls
                                src={`${selectedQuiz?.main_audio_path}`}
                                className="mb-2 w-full"
                            />
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

                        {/* Questions Section */}
                        <div>
                            <h3 className="text-lg font-semibold mb-4">
                                Quiz Questions
                            </h3>
                            <div className="space-y-4">
                                {selectedQuiz?.questions?.map((q, index) => (
                                    <div
                                        key={index}
                                        className="border rounded-lg p-4 relative bg-gray-50 shadow-sm"
                                    >
                                        <div className="mb-3">
                                            <label className="block text-sm font-medium mb-1">
                                                Question Audio
                                            </label>
                                            {q.audio_path && (
                                                <audio
                                                    controls
                                                    src={`${q.audio_path}`}
                                                    className="mb-2 w-full"
                                                />
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium mb-1">
                                                Correct Option
                                            </label>
                                            <select
                                                value={q.correct_option ?? ""}
                                                onChange={(e) => {
                                                    const updated = [
                                                        ...selectedQuiz.questions,
                                                    ];
                                                    updated[
                                                        index
                                                    ].correct_option = Number(
                                                        e.target.value
                                                    );
                                                    setSelectedQuiz({
                                                        ...selectedQuiz,
                                                        questions: updated,
                                                    });
                                                }}
                                                className="w-full p-2 border rounded"
                                            >
                                                <option value="">
                                                    Select Option
                                                </option>
                                                {getOptionsByCategory(
                                                    selectedQuiz.category
                                                ).map((option, idx) => (
                                                    <option
                                                        key={idx}
                                                        value={idx}
                                                    >
                                                        {option}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>

                                        {/* Delete Question */}
                                        <button
                                            type="button"
                                            onClick={() =>
                                                handleDeleteQuestion(
                                                    q.id,
                                                    index
                                                )
                                            }
                                            className="absolute top-2 right-2 text-red-500 hover:text-red-700"
                                            title="Delete Question"
                                        >
                                            ❌
                                        </button>
                                    </div>
                                ))}
                            </div>

                            <div className="mt-10">
                                <h4 className="font-semibold text-md mb-2">
                                    Add New Questions
                                </h4>
                                {newQuestions.map((q, i) => (
                                    <div
                                        key={i}
                                        className="border p-4 rounded mb-4 bg-white shadow"
                                    >
                                        <input
                                            type="file"
                                            accept="audio/*"
                                            onChange={(e) =>
                                                handleNewQuestionChange(
                                                    i,
                                                    "audio",
                                                    e.target.files[0]
                                                )
                                            }
                                            className="mb-2 w-full"
                                        />
                                        <select
                                            value={q.correct_option}
                                            onChange={(e) =>
                                                handleNewQuestionChange(
                                                    i,
                                                    "correct_option",
                                                    e.target.value
                                                )
                                            }
                                            className="w-full p-2 border rounded"
                                        >
                                            <option value="">
                                                Select Correct Option
                                            </option>
                                            {getOptionsByCategory(
                                                selectedQuiz.category
                                            ).map((opt, idx) => (
                                                <option key={idx} value={idx}>
                                                    {opt}
                                                </option>
                                            ))}
                                        </select>
                                        <button
                                            type="button"
                                            className="text-sm text-red-600 mt-2"
                                            onClick={() => removeNewQuestion(i)}
                                        >
                                            Remove
                                        </button>
                                    </div>
                                ))}

                                <button
                                    type="button"
                                    onClick={addNewQuestionField}
                                    className="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                                >
                                    ➕ Add Another Question
                                </button>

                                <button
                                    type="button"
                                    onClick={submitNewQuestions}
                                    className="mt-4 ml-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                                >
                                    ✅ Save New Questions
                                </button>
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div className="flex justify-end pt-6">
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
