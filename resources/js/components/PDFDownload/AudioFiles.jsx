import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import Modal from "../Modal/Modal";
import { LoaderCircleIcon } from "lucide-react";

const AudioFiles = () => {
    const [audioFiles, setAudioFiles] = useState([]);
    const [loading, setLoading] = useState(false);

    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [editModalOpen, setEditModalOpen] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);

    const [selectedFile, setSelectedFile] = useState({});

    const [form, setForm] = useState({
        title: "",
        category: "",
        audio_file: null,
        duration: "",
    });

    const { showMessage } = useFlashMessage();

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    /** ----------------------------
     * FETCH ALL AUDIO FILES
     * ---------------------------- */
    const fetchAudioFiles = async () => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/admin/audio-downloads`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });
            setAudioFiles(response.data);
        } catch (error) {
            console.error("Error fetching audio files:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchAudioFiles();
    }, []);

    /** ----------------------------
     * HANDLE CREATE
     * ---------------------------- */
    const handleCreate = async () => {
        const fd = new FormData();
        fd.append("title", form.title);
        fd.append("category", form.category);

        if (form.audio_file instanceof File) {
            fd.append("audio_file", form.audio_file);
        }

        if (form.duration) {
            fd.append("duration", form.duration);
        }

        try {
            setLoading(true);
            await axios.post("/api/admin/audio-download/store", fd, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "multipart/form-data",
                },
            });

            showMessage("Audio file added successfully!", "success");
            setCreateModalOpen(false);
            setForm({
                title: "",
                category: "",
                audio_file: null,
                duration: "",
            });
            fetchAudioFiles();
        } catch (error) {
            console.error(error);
            showMessage("Failed to create audio file", "error");
        } finally {
            setLoading(false);
        }
    };

    /** ----------------------------
     * HANDLE EDIT
     * ---------------------------- */
    const openEditModal = (file) => {
        setSelectedFile(file);
        setEditModalOpen(true);
    };

    const handleUpdate = async () => {
        const fd = new FormData();
        fd.append("title", selectedFile.title);
        fd.append("category", selectedFile.category);

        if (selectedFile.audio_file instanceof File) {
            fd.append("audio_file", selectedFile.audio_file);
        }

        if (selectedFile.duration) {
            fd.append("duration", selectedFile.duration);
        }

        try {
            setLoading(true);
            await axios.post(
                `/api/admin/audio-downloads/${selectedFile.id}/update`,
                fd,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "multipart/form-data",
                    },
                },
            );

            showMessage("Audio file updated successfully!", "success");
            setEditModalOpen(false);
            setSelectedFile({});
            fetchAudioFiles();
        } catch (error) {
            console.error(error);
            showMessage("Failed to update audio file", "error");
        } finally {
            setLoading(false);
        }
    };

    /** ----------------------------
     * HANDLE DELETE
     * ---------------------------- */
    const openDeleteModal = (file) => {
        setSelectedFile(file);
        setDeleteModalOpen(true);
    };

    const handleDelete = async () => {
        try {
            setLoading(true);
            await axios.delete(
                `/api/admin/audio-downloads/${selectedFile.id}`,
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                },
            );

            showMessage("Audio file deleted!", "success");
            setDeleteModalOpen(false);
            fetchAudioFiles();
        } catch (error) {
            console.error(error);
            showMessage("Failed to delete audio file", "error");
        } finally {
            setLoading(false);
        }
    };

    /** ----------------------------
     * RENDER
     * ---------------------------- */
    return (
        <div className="bg-white p-6 rounded-lg shadow-lg">
            <div className="flex justify-between items-center mb-6">
                <h2 className="text-2xl font-bold text-gray-800">
                    Audio Downloads
                </h2>
                <button
                    onClick={() => setCreateModalOpen(true)}
                    className="px-4 py-2 bg-black text-white rounded-lg"
                >
                    Add New Audio
                </button>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-64">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {audioFiles.map((file) => (
                        <div
                            key={file.id}
                            className="bg-white p-4 rounded-lg shadow"
                        >
                            <div className="bg-gradient-to-br from-blue-100 to-purple-100 w-full h-48 flex items-center justify-center rounded mb-3">
                                <i className="fa fa-music text-5xl text-purple-500"></i>
                            </div>

                            <h3 className="font-semibold text-lg">
                                {file.title}
                            </h3>

                            <p className="text-sm text-gray-500 mb-3">
                                <span className="inline-block bg-gray-200 px-2 py-1 rounded text-xs font-medium capitalize">
                                    {file.category === "tracks_loops"
                                        ? "Track & Loops"
                                        : "Piano Plays"}
                                </span>
                            </p>

                            <audio controls className="w-full mb-3 h-8">
                                <source
                                    src={`/${file.audio_file}`}
                                    type="audio/mpeg"
                                />
                                Your browser does not support the audio element.
                            </audio>

                            <div className="mt-4 flex justify-end gap-2">
                                <button
                                    onClick={() => openEditModal(file)}
                                    className="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                    title="Edit"
                                >
                                    <i className="fa fa-edit"></i>
                                </button>

                                <button
                                    onClick={() => openDeleteModal(file)}
                                    className="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                    title="Delete"
                                >
                                    <i className="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* ------------------- CREATE MODAL ------------------- */}
            <Modal
                isOpen={createModalOpen}
                onClose={() => setCreateModalOpen(false)}
            >
                <div className="my-5">
                    <h2 className="text-2xl font-bold mb-3 text-gray-800">
                        Add New Audio
                    </h2>

                    <div className="space-y-5">
                        {/* Title Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Audio Title *
                            </label>
                            <input
                                type="text"
                                placeholder="Enter audio title"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                value={form.title}
                                onChange={(e) =>
                                    setForm({ ...form, title: e.target.value })
                                }
                            />
                        </div>

                        {/* Category Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Category *
                            </label>
                            <select
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition bg-white"
                                value={form.category}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        category: e.target.value,
                                    })
                                }
                            >
                                <option value="">Select Category</option>
                                <option value="tracks_loops">
                                    Track & Loops
                                </option>
                                <option value="piano_plays">Piano Plays</option>
                            </select>
                        </div>

                        {/* Audio File Upload */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Audio File *
                            </label>
                            <input
                                type="file"
                                accept="audio/mp3,audio/wav,audio/aac,audio/x-m4a"
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        audio_file: e.target.files[0],
                                    })
                                }
                            />
                        </div>

                        {/* Duration Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Duration (optional)
                            </label>
                            <input
                                type="text"
                                placeholder="e.g., 3:45"
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                value={form.duration}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        duration: e.target.value,
                                    })
                                }
                            />
                        </div>

                        {/* Button */}
                        <button
                            onClick={handleCreate}
                            disabled={loading}
                            className="w-full bg-black text-white py-3 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 hover:bg-gray-800 transition mt-6"
                        >
                            {loading ? (
                                <>
                                    <LoaderCircleIcon
                                        className="animate-spin"
                                        size={18}
                                    />
                                    Creating...
                                </>
                            ) : (
                                "Create Audio"
                            )}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* ------------------- EDIT MODAL ------------------- */}
            <Modal
                isOpen={editModalOpen}
                onClose={() => setEditModalOpen(false)}
            >
                <div className="my-5">
                    <h2 className="text-2xl font-bold mb-2 text-gray-800">
                        Edit Audio
                    </h2>

                    <div className="space-y-5">
                        {/* Title Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Audio Title *
                            </label>
                            <input
                                type="text"
                                value={selectedFile.title || ""}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        title: e.target.value,
                                    })
                                }
                            />
                        </div>

                        {/* Category Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Category *
                            </label>
                            <select
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition bg-white"
                                value={selectedFile.category || ""}
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        category: e.target.value,
                                    })
                                }
                            >
                                <option value="">Select Category</option>
                                <option value="tracks_loops">
                                    Track & Loops
                                </option>
                                <option value="piano_plays">Piano Plays</option>
                            </select>
                        </div>

                        {/* Audio File Upload */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Audio File
                            </label>
                            <input
                                type="file"
                                accept="audio/mp3,audio/wav,audio/aac,audio/x-m4a"
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        audio_file: e.target.files[0],
                                    })
                                }
                            />
                        </div>

                        {/* Duration Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Duration (optional)
                            </label>
                            <input
                                type="text"
                                value={selectedFile.duration || ""}
                                placeholder="e.g., 3:45"
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        duration: e.target.value,
                                    })
                                }
                            />
                        </div>

                        {/* Button */}
                        <button
                            onClick={handleUpdate}
                            disabled={loading}
                            className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 hover:bg-blue-700 transition mt-6"
                        >
                            {loading ? (
                                <>
                                    <LoaderCircleIcon
                                        className="animate-spin"
                                        size={18}
                                    />
                                    Updating...
                                </>
                            ) : (
                                "Update Audio"
                            )}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* ------------------- DELETE MODAL ------------------- */}
            <Modal
                isOpen={deleteModalOpen}
                onClose={() => setDeleteModalOpen(false)}
            >
                <h2 className="text-xl font-bold mb-4">Delete Audio</h2>

                <p>
                    Are you sure you want to delete:{" "}
                    <strong>{selectedFile?.title}</strong>?
                </p>

                <div className="flex gap-4 mt-6">
                    <button
                        onClick={() => setDeleteModalOpen(false)}
                        disabled={loading}
                        className="flex-1 py-2 border rounded disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Cancel
                    </button>

                    <button
                        onClick={handleDelete}
                        disabled={loading}
                        className="flex-1 py-2 bg-red-600 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        {loading ? (
                            <>
                                <LoaderCircleIcon
                                    className="animate-spin"
                                    size={18}
                                />
                                Deleting...
                            </>
                        ) : (
                            "Delete"
                        )}
                    </button>
                </div>
            </Modal>
        </div>
    );
};

export default AudioFiles;

if (document.getElementById("audio-download")) {
    const Index = ReactDOM.createRoot(
        document.getElementById("audio-download"),
    );

    Index.render(
        <FlashMessageProvider>
            <AudioFiles />
        </FlashMessageProvider>,
    );
}
