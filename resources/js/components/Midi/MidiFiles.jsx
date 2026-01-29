import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import Modal from "../Modal/Modal";
import { LoaderCircleIcon } from "lucide-react";

const MidiFiles = () => {
    const [midiFiles, setMidiFiles] = useState([]);
    const [loading, setLoading] = useState(false);

    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [editModalOpen, setEditModalOpen] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);

    const [selectedFile, setSelectedFile] = useState({});

    const [form, setForm] = useState({
        name: "",
        video_path: "",
        video_type: "",
        midi_file: null,
        lmv_file: null,
        thumbnail: "",
        description: "",
    });

    const { showMessage } = useFlashMessage();

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    /** ----------------------------
     * FETCH ALL MIDI FILES
     * ---------------------------- */
    const fetchMidiFiles = async () => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/admin/midi-files`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });
            setMidiFiles(response.data);
        } catch (error) {
            console.error("Error fetching midi files:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchMidiFiles();
    }, []);

    /** ----------------------------
     * HANDLE CREATE
     * ---------------------------- */
    const handleCreate = async () => {
        const fd = new FormData();
        fd.append("name", form.name);
        fd.append("video_path", form.video_path);
        fd.append("video_type", form.video_type);
        fd.append("description", form.description);

        if (form.thumbnail instanceof File) {
            fd.append("thumbnail", form.thumbnail);
        }

        if (form.lmv_file instanceof File) {
            fd.append("lmv_file", form.lmv_file);
        }

        if (form.midi_file instanceof File) {
            fd.append("midi_file", form.midi_file);
        }
        try {
            setLoading(true);
            await axios.post("/api/admin/midi-file/create", fd, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "multipart/form-data",
                },
            });

            showMessage("MIDI file added successfully!", "success");
            setCreateModalOpen(false);
            setForm({
                name: "",
                video_path: "",
                video_type: "",
                midi_file: null,
                lmv_file: null,
                thumbnail: "",
                description: "",
            });
            fetchMidiFiles();
        } catch (error) {
            console.error(error);
            showMessage("Failed to create file", "error");
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
        fd.append("name", selectedFile.name);
        fd.append("video_path", selectedFile.video_path);
        fd.append("video_type", selectedFile.video_type);
        fd.append("description", selectedFile.description);

        if (selectedFile.thumbnail instanceof File) {
            fd.append("thumbnail", selectedFile.thumbnail);
        }

        if (selectedFile.lmv_file instanceof File) {
            fd.append("lmv_file", selectedFile.lmv_file);
        }

        if (selectedFile.midi_file instanceof File) {
            fd.append("midi_file", selectedFile.midi_file);
        }

        try {
            setLoading(true);
            await axios.post(
                `/api/admin/midi-files/update/${selectedFile.id}`,
                fd,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "multipart/form-data",
                    },
                },
            );

            showMessage("MIDI file updated successfully!", "success");
            setEditModalOpen(false);
            setSelectedFile({});
            fetchMidiFiles();
        } catch (error) {
            console.error(error);
            showMessage("Failed to update file", "error");
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
            await axios.delete(`/api/admin/midi-files/${selectedFile.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });

            showMessage("File deleted!", "success");
            setDeleteModalOpen(false);
            fetchMidiFiles();
        } catch (error) {
            console.error(error);
            showMessage("Failed to delete file", "error");
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
                <h2 className="text-2xl font-bold text-gray-800">MIDI Files</h2>
                <button
                    onClick={() => setCreateModalOpen(true)}
                    className="px-4 py-2 bg-black text-white rounded-lg"
                >
                    Add New File
                </button>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-64">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {midiFiles.map((file) => (
                        <div
                            key={file.id}
                            className="bg-white p-4 rounded-lg shadow"
                        >
                            <div className="h-48 overflow-hidden rounded mb-3">
                                {file.thumbnail_path ? (
                                    <img
                                        src={`/${file.thumbnail_path}`}
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="bg-gray-200 w-full h-full flex items-center justify-center">
                                        <i className="fa fa-image text-4xl text-gray-400"></i>
                                    </div>
                                )}
                            </div>

                            <h3 className="font-semibold text-lg">
                                {file.name}
                            </h3>

                            {/* <p className="text-sm text-gray-600 truncate">
                                {file.video_path}
                            </p> */}

                            <div className="mt-4 flex justify-end gap-2">
                                <button
                                    onClick={() => openEditModal(file)}
                                    className="p-2 bg-blue-50 text-blue-600 rounded-lg"
                                >
                                    <i className="fa fa-edit"></i>
                                </button>

                                <button
                                    onClick={() => openDeleteModal(file)}
                                    className="p-2 bg-red-50 text-red-600 rounded-lg"
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
                        Add New MIDI File
                    </h2>

                    <div className="space-y-5">
                        {/* Name Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                File Name *
                            </label>
                            <input
                                type="text"
                                placeholder="Enter file name"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                value={form.name}
                                onChange={(e) =>
                                    setForm({ ...form, name: e.target.value })
                                }
                            />
                        </div>

                        {/* Video URL Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Video URL *
                            </label>
                            <input
                                type="text"
                                placeholder="https://example.com/video"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                value={form.video_path}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        video_path: e.target.value,
                                    })
                                }
                            />
                        </div>

                        {/* Video Type Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Video Type *
                            </label>
                            <select
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition bg-white"
                                value={form.video_type}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        video_type: e.target.value,
                                    })
                                }
                            >
                                <option value="">Select Video Type</option>
                                <option value="youtube">YouTube</option>
                                <option value="google">Google</option>
                                <option value="local">Local</option>
                                <option value="iframe">Iframe</option>
                            </select>
                        </div>

                        {/* File Upload Fields - Two Column Grid */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-2">
                                    MIDI File *
                                </label>
                                <input
                                    type="file"
                                    accept=".mid,.midi"
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            midi_file: e.target.files[0],
                                        })
                                    }
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-2">
                                    LMV File *
                                </label>
                                <input
                                    type="file"
                                    accept=".lmv"
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            lmv_file: e.target.files[0],
                                        })
                                    }
                                />
                            </div>
                        </div>

                        {/* Thumbnail Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Thumbnail
                            </label>
                            <input
                                type="file"
                                accept="image/*"
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition"
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        thumbnail: e.target.files[0],
                                    })
                                }
                            />
                        </div>

                        {/* Description Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                placeholder="Enter file description"
                                rows="2"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition resize-none"
                                value={form.description}
                                onChange={(e) =>
                                    setForm({
                                        ...form,
                                        description: e.target.value,
                                    })
                                }
                            ></textarea>
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
                                "Create File"
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
                        Edit MIDI File
                    </h2>

                    <div className="space-y-5">
                        {/* Name Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                File Name *
                            </label>
                            <input
                                type="text"
                                value={selectedFile.name || ""}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        name: e.target.value,
                                    })
                                }
                            />
                        </div>

                        {/* Video URL Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Video URL *
                            </label>
                            <input
                                type="text"
                                value={selectedFile.video_path || ""}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        video_path: e.target.value,
                                    })
                                }
                            />
                        </div>

                        {/* Video Type Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Video Type *
                            </label>
                            <select
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition bg-white"
                                value={selectedFile.video_type || ""}
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        video_type: e.target.value,
                                    })
                                }
                            >
                                <option value="">Select Video Type</option>
                                <option value="youtube">YouTube</option>
                                <option value="google">Google</option>
                                <option value="local">Local</option>
                                <option value="iframe">Iframe</option>
                            </select>
                        </div>

                        {/* File Upload Fields - Two Column Grid */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-2">
                                    MIDI File
                                </label>
                                <input
                                    type="file"
                                    accept=".mid,.midi"
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                    onChange={(e) =>
                                        setSelectedFile({
                                            ...selectedFile,
                                            midi_file: e.target.files[0],
                                        })
                                    }
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-2">
                                    LMV File
                                </label>
                                <input
                                    type="file"
                                    accept=".lmv"
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                    onChange={(e) =>
                                        setSelectedFile({
                                            ...selectedFile,
                                            lmv_file: e.target.files[0],
                                        })
                                    }
                                />
                            </div>
                        </div>

                        {/* Thumbnail Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Thumbnail
                            </label>
                            <input
                                type="file"
                                accept="image/*"
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        thumbnail: e.target.files[0],
                                    })
                                }
                            />
                        </div>

                        {/* Description Field */}
                        <div>
                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea
                                value={selectedFile.description || ""}
                                rows="2"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition resize-none"
                                onChange={(e) =>
                                    setSelectedFile({
                                        ...selectedFile,
                                        description: e.target.value,
                                    })
                                }
                            ></textarea>
                        </div>

                        {/* Button */}
                        <button
                            onClick={handleUpdate}
                            disabled={loading}
                            className="w-full bg-black text-white py-3 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 hover:bg-blue-700 transition mt-6"
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
                                "Update File"
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
                <h2 className="text-xl font-bold mb-4">Delete MIDI File</h2>

                <p>
                    Are you sure you want to delete:{" "}
                    <strong>{selectedFile?.name}</strong>?
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

export default MidiFiles;

if (document.getElementById("midi-file")) {
    const Index = ReactDOM.createRoot(document.getElementById("midi-file"));

    Index.render(
        <FlashMessageProvider>
            <MidiFiles />
        </FlashMessageProvider>,
    );
}
