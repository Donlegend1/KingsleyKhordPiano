import ReactDOM from "react-dom/client";
import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import Select from "react-select";
import Modal from "../Modal/Modal";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const LearnSongsAdmin = () => {
    const [collapsedSections, setCollapsedSections] = useState({
        beginner: false,
        intermediate: false,
        advanced: false,
    });
    const [collapsedCategories, setCollapsedCategories] = useState({});
    const [songsData, setSongsData] = useState({
        beginner: { data: {} },
        intermediate: { data: {} },
        advanced: { data: {} },
    });
    const [allSongs, setAllSongs] = useState([]);
    const [loading, setLoading] = useState(false);
    
    // Modal states
    const [newCategoryModalOpen, setNewCategoryModalOpen] = useState(false);
    const [newCategoryLevel, setNewCategoryLevel] = useState(null);
    const [newCategoryName, setNewCategoryName] = useState("");

    // Edit Category States
    const [editCategoryModalOpen, setEditCategoryModalOpen] = useState(false);
    const [editingCategoryName, setEditingCategoryName] = useState("");
    const [originalCategoryName, setOriginalCategoryName] = useState("");

    const openEditCategoryModal = (categoryName) => {
        setOriginalCategoryName(categoryName);
        setEditingCategoryName(categoryName);
        setEditCategoryModalOpen(true);
    };

    const handleUpdateCategory = async () => {
        if (!editingCategoryName.trim()) return;
        setLoading(true);
        try {
            await axios.put(
                `/api/admin/learn-songs/category/${originalCategoryName}/update`,
                {
                    category: editingCategoryName,
                },
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            fetchSongs();
            setEditCategoryModalOpen(false);
            showMessage("Category Updated successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error updating category", "error");
        } finally {
            setLoading(false);
        }
    };

    const [isCreateSongModalOpen, setIsCreateSongModalOpen] = useState(false);
    const [selectedLevel, setSelectedLevel] = useState("");
    const [selectedCategoryName, setSelectedCategoryName] = useState("");
    const [newSong, setNewSong] = useState({
        title: "",
        author: "",
        description: "",
        video_type: "iframe",
        video_url: "",
        status: "active",
        tonal_center: "",
        related_songs: [],
    });

    const tonalCenters = [
        { value: "C", label: "C" },
        { value: "C#", label: "C# / Db" },
        { value: "D", label: "D" },
        { value: "D#", label: "D# / Eb" },
        { value: "E", label: "E" },
        { value: "F", label: "F" },
        { value: "F#", label: "F# / Gb" },
        { value: "G", label: "G" },
        { value: "G#", label: "G# / Ab" },
        { value: "A", label: "A" },
        { value: "A#", label: "A# / Bb" },
        { value: "B", label: "B" },
    ];

    const [isEditSongModalOpen, setIsEditSongModalOpen] = useState(false);
    const [editingSong, setEditingSong] = useState(null);

    const [isDeleteSongModalOpen, setIsDeleteSongModalOpen] = useState(false);
    const [songToDelete, setSongToDelete] = useState(null);

    const [thumbnailFile, setThumbnailFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(null);
    const [descriptionImageFiles, setDescriptionImageFiles] = useState([]);
    const [audioResourceFile, setAudioResourceFile] = useState(null);
    const [pdfResourceFile, setPdfResourceFile] = useState(null);
    const [editAudioResourceFile, setEditAudioResourceFile] = useState(null);
    const [editPdfResourceFile, setEditPdfResourceFile] = useState(null);
    const fileInputRef = useRef(null);

    const { showMessage } = useFlashMessage();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    const fetchSongs = async () => {
        setLoading(true);
        try {
            const response = await axios.get("/api/admin/learn-songs");
            setSongsData(response.data);
        } catch (error) {
            console.error("Error fetching songs:", error);
            showMessage("Error fetching songs data", "error");
        } finally {
            setLoading(false);
        }
    };

    const fetchAllSongsDropdown = async () => {
        try {
            const response = await axios.get("/api/admin/all-songs");
            setAllSongs(
                response.data.map((s) => ({
                    value: s.id,
                    label: `${s.title} (${s.level})`,
                }))
            );
        } catch (error) {
            console.error("Error fetching all songs:", error);
        }
    };

    useEffect(() => {
        fetchSongs();
        fetchAllSongsDropdown();
    }, []);

    const toggleSection = (level) => {
        setCollapsedSections((prev) => ({ ...prev, [level]: !prev[level] }));
    };

    const toggleCategory = (key) => {
        setCollapsedCategories((prev) => ({ ...prev, [key]: !prev[key] }));
    };

    // Category Handlers
    const handleNewCategoryModal = (level) => {
        setNewCategoryLevel(level);
        setNewCategoryName("");
        setNewCategoryModalOpen(true);
    };

    const handleCreateCategory = async () => {
        if (!newCategoryName.trim()) return;
        setLoading(true);
        try {
            await axios.post("/api/admin/learn-songs/category/create", {
                category: newCategoryName,
                level: newCategoryLevel,
            }, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            });
            showMessage("Category Created successfully", "success");
            setNewCategoryModalOpen(false);
            fetchSongs();
        } catch (error) {
            console.error("Error creating category:", error);
            showMessage("Error creating category", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCategory = async (categoryName) => {
        if (!confirm(`Are you sure you want to delete category "${categoryName}"?`)) return;
        setLoading(true);
        try {
            await axios.delete(`/api/admin/learn-songs/category/${categoryName}/delete`, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            });
            showMessage("Category Deleted successfully", "success");
            fetchSongs();
        } catch (error) {
            console.error("Error deleting category:", error);
            const msg = error.response?.data?.message || "Error deleting category";
            showMessage(msg, "error");
        } finally {
            setLoading(false);
        }
    };

    const handleOnDragEnd = async (result, level) => {
        if (!result.destination) return;

        if (result.source.droppableId === `droppable-${level}`) {
            const currentData = songsData[level]?.data || {};
            const items = Object.entries(currentData);
            const [reorderedItem] = items.splice(result.source.index, 1);
            items.splice(result.destination.index, 0, reorderedItem);

            const updatedData = {};
            items.forEach(([cat, list]) => {
                updatedData[cat] = list;
            });

            setSongsData((prev) => ({
                ...prev,
                [level]: { ...prev[level], data: updatedData }
            }));

            try {
                await axios.post("/api/admin/reorder/learn-songs", {
                    level,
                    categories: items.map(([category]) => category),
                }, {
                    headers: { "X-CSRF-TOKEN": csrfToken }
                });
            } catch (error) {
                console.error("Failed to persist category order:", error);
                showMessage("Failed to save category order", "error");
            }
        } else if (result.source.droppableId.startsWith("songs-")) {
            const categoryName = result.source.droppableId.replace("songs-", "");
            const currentData = songsData[level]?.data || {};
            const items = Object.entries(currentData);

            const updatedData = {};
            items.forEach(([cat, list]) => {
                if (cat === categoryName) {
                    const songsList = Array.from(list);
                    const [reorderedItem] = songsList.splice(result.source.index, 1);
                    songsList.splice(result.destination.index, 0, reorderedItem);
                    updatedData[cat] = songsList;
                } else {
                    updatedData[cat] = list;
                }
            });

            setSongsData((prev) => ({
                ...prev,
                [level]: { ...prev[level], data: updatedData }
            }));

            const targetSongs = updatedData[categoryName];
            if (targetSongs) {
                const songsIds = targetSongs.map(s => s.id);
                try {
                    await axios.post("/api/admin/reorder/learn-songs/items", {
                        songs: songsIds,
                    }, {
                        headers: { "X-CSRF-TOKEN": csrfToken }
                    });
                } catch (error) {
                    console.error("Failed to persist song order:", error);
                    showMessage("Failed to save song order", "error");
                }
            }
        }
    };

    // Song Handlers
    const openCreateSongModal = (level, category) => {
        setSelectedLevel(level);
        setSelectedCategoryName(category);
        setNewSong({
            title: "",
            author: "",
            description: "",
            video_type: "iframe",
            video_url: "",
            status: "active",
            related_songs: [],
        });
        setThumbnailFile(null);
        setPreviewUrl(null);
        setDescriptionImageFiles([]);
        setIsCreateSongModalOpen(true);
    };

    const handleCreateSongSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData();
        formData.append("title", newSong.title);
        formData.append("author", newSong.author || "");
        formData.append("description", newSong.description || "");
        formData.append("category", selectedCategoryName);
        formData.append("level", selectedLevel);
        formData.append("video_type", newSong.video_type);
        formData.append("video_url", newSong.video_url);
        formData.append("status", newSong.status);
        formData.append("tonal_center", newSong.tonal_center || "");
        if (thumbnailFile) {
            formData.append("thumbnail", thumbnailFile);
        }
        descriptionImageFiles.forEach((file, idx) => {
            formData.append(`images[${idx}]`, file);
        });
        newSong.related_songs.forEach((id, idx) => {
            formData.append(`related_songs[${idx}]`, id);
        });
        if (audioResourceFile) {
            formData.append("audio_resource", audioResourceFile);
        }
        if (pdfResourceFile) {
            formData.append("pdf_resource", pdfResourceFile);
        }

        try {
            await axios.post("/api/admin/learn-songs/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": csrfToken
                }
            });
            showMessage("Song added successfully", "success");
            setIsCreateSongModalOpen(false);
            setAudioResourceFile(null);
            setPdfResourceFile(null);
            fetchSongs();
            fetchAllSongsDropdown();
        } catch (error) {
            console.error("Error creating song:", error);
            showMessage("Error adding song", "error");
        } finally {
            setLoading(false);
        }
    };

    const openEditSongModal = (song) => {
        setEditingSong({
            ...song,
            related_songs: song.related_songs || []
        });
        setPreviewUrl(song.thumbnail_url);
        setThumbnailFile(null);
        setDescriptionImageFiles([]);
        setEditAudioResourceFile(null);
        setEditPdfResourceFile(null);
        setIsEditSongModalOpen(true);
    };

    const handleEditSongSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData();
        formData.append("title", editingSong.title);
        formData.append("author", editingSong.author || "");
        formData.append("description", editingSong.description || "");
        formData.append("video_type", editingSong.video_type);
        formData.append("video_url", editingSong.video_url);
        formData.append("status", editingSong.status);
        formData.append("tonal_center", editingSong.tonal_center || "");
        if (thumbnailFile) {
            formData.append("thumbnail", thumbnailFile);
        }
        descriptionImageFiles.forEach((file, idx) => {
            formData.append(`images[${idx}]`, file);
        });
        editingSong.related_songs.forEach((id, idx) => {
            formData.append(`related_songs[${idx}]`, id);
        });
        if (editAudioResourceFile) {
            formData.append("audio_resource", editAudioResourceFile);
        }
        if (editPdfResourceFile) {
            formData.append("pdf_resource", editPdfResourceFile);
        }

        try {
            await axios.post(`/api/admin/learn-songs/update/${editingSong.id}`, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": csrfToken
                }
            });
            showMessage("Song updated successfully", "success");
            setIsEditSongModalOpen(false);
            fetchSongs();
            fetchAllSongsDropdown();
        } catch (error) {
            console.error("Error updating song:", error);
            showMessage("Error updating song", "error");
        } finally {
            setLoading(false);
        }
    };

    const openDeleteSongModal = (song) => {
        setSongToDelete(song);
        setIsDeleteSongModalOpen(true);
    };

    const handleDeleteSong = async () => {
        setLoading(true);
        try {
            await axios.delete(`/api/admin/learn-songs/${songToDelete.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            });
            showMessage("Song deleted successfully", "success");
            setIsDeleteSongModalOpen(false);
            fetchSongs();
            fetchAllSongsDropdown();
        } catch (error) {
            console.error("Error deleting song:", error);
            showMessage("Error deleting song", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setThumbnailFile(file);
            setPreviewUrl(URL.createObjectURL(file));
        }
    };

    return (
        <div className="bg-white p-6 rounded-lg shadow-lg">
            <div className="flex justify-between items-center mb-6">
                <h2 className="text-2xl font-bold text-gray-800">Learn Songs Manager</h2>
            </div>

            {loading && !newCategoryModalOpen && !isCreateSongModalOpen && !isEditSongModalOpen && !isDeleteSongModalOpen ? (
                <div className="flex justify-center items-center h-64">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
                </div>
            ) : (
                <div className="space-y-8">
                    {["beginner", "intermediate", "advanced"].map((level) => {
                        const levelData = songsData[level]?.data || {};
                        const orderedCategories = Object.entries(levelData);

                        return (
                            <div key={level} className="mb-8 border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                {/* Level Header */}
                                <h3
                                    onClick={() => toggleSection(level)}
                                    className="text-lg font-semibold px-5 py-4 bg-gray-50 flex justify-between items-center cursor-pointer hover:bg-gray-100 select-none border-b border-gray-200 capitalize text-gray-800"
                                >
                                    <span>{level} Level</span>
                                    <div className="flex items-center gap-3">
                                        <button
                                            onClick={(e) => {
                                                e.stopPropagation();
                                                handleNewCategoryModal(level);
                                            }}
                                            className="px-3 py-1 bg-black text-white text-xs rounded-full hover:bg-gray-800 transition"
                                        >
                                            Add Category
                                        </button>
                                        <i className={`fa ${collapsedSections[level] ? "fa-chevron-down" : "fa-chevron-up"} text-sm text-gray-500`}></i>
                                    </div>
                                </h3>

                                {/* Level Content */}
                                <div className={collapsedSections[level] ? "hidden" : "p-5 bg-white space-y-6"}>
                                    {orderedCategories.length === 0 ? (
                                        <p className="text-gray-500 text-sm text-center py-6">No categories created yet in this level.</p>
                                    ) : (
                                        <DragDropContext onDragEnd={(res) => handleOnDragEnd(res, level)}>
                                            <Droppable droppableId={`droppable-${level}`}>
                                                {(provided) => (
                                                    <div ref={provided.innerRef} {...provided.droppableProps}>
                                                        {orderedCategories.map(([categoryName, songs], index) => {
                                                            const isCollapsed = collapsedCategories[`${level}-${categoryName}`];
                                                            return (
                                                                <Draggable key={categoryName} draggableId={`${level}-${categoryName}`} index={index}>
                                                                    {(provided, snapshot) => (
                                                                        <div
                                                                            ref={provided.innerRef}
                                                                            {...provided.draggableProps}
                                                                            className={`mb-6 p-4 rounded-xl border transition-all ${
                                                                                snapshot.isDragging
                                                                                    ? "bg-blue-50/70 border-blue-300 shadow-md scale-[1.01]"
                                                                                    : "bg-gray-50 border-gray-200"
                                                                            }`}
                                                                        >
                                                                            {/* Category Header */}
                                                                            <div
                                                                                {...provided.dragHandleProps}
                                                                                onClick={() => toggleCategory(`${level}-${categoryName}`)}
                                                                                className="flex justify-between items-center cursor-pointer select-none bg-white dark:bg-gray-700 p-3 rounded-lg border border-gray-200/80 shadow-sm hover:bg-gray-100/50 transition"
                                                                            >
                                                                                <span className="font-semibold text-gray-800">{categoryName}</span>
                                                                                <div className="flex items-center gap-4">
                                                                                    <button
                                                                                        onClick={(e) => {
                                                                                            e.stopPropagation();
                                                                                            openCreateSongModal(level, categoryName);
                                                                                        }}
                                                                                        className="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition"
                                                                                    >
                                                                                        Add Song
                                                                                    </button>
                                                                                    <i
                                                                                        className="fa fa-pencil text-blue-500 hover:text-blue-700 text-sm cursor-pointer"
                                                                                        onClick={(e) => {
                                                                                            e.stopPropagation();
                                                                                            openEditCategoryModal(categoryName);
                                                                                        }}
                                                                                    ></i>
                                                                                    <i
                                                                                        className="fa fa-trash text-red-500 hover:text-red-700 text-sm"
                                                                                        onClick={(e) => {
                                                                                            e.stopPropagation();
                                                                                            handleDeleteCategory(categoryName);
                                                                                        }}
                                                                                    ></i>
                                                                                    <i className={`fa ${isCollapsed ? "fa-chevron-down" : "fa-chevron-up"} text-sm text-gray-500`}></i>
                                                                                </div>
                                                                            </div>

                                                                            {/* Category Songs List */}
                                                                            <div className={`mt-4 overflow-hidden transition-all duration-300 ${isCollapsed ? "max-h-0 opacity-0" : "max-h-[3000px] opacity-100"}`}>
                                                                                {songs.length === 0 ? (
                                                                                    <p className="text-gray-500 text-xs text-center py-4">No songs in this category yet.</p>
                                                                                ) : (
                                                                                    <Droppable droppableId={`songs-${categoryName}`} type="song">
                                                                                        {(provided) => (
                                                                                            <div
                                                                                                ref={provided.innerRef}
                                                                                                {...provided.droppableProps}
                                                                                                className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                                                                                            >
                                                                                                {songs.map((song, songIndex) => (
                                                                                                    <Draggable key={song.id} draggableId={`song-${song.id}`} index={songIndex}>
                                                                                                        {(provided, snapshot) => (
                                                                                                            <div
                                                                                                                ref={provided.innerRef}
                                                                                                                {...provided.draggableProps}
                                                                                                                {...provided.dragHandleProps}
                                                                                                                className={`bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col justify-between hover:shadow-md transition ${
                                                                                                                    snapshot.isDragging
                                                                                                                        ? "ring-2 ring-blue-400 scale-[1.01]"
                                                                                                                        : "border-gray-100"
                                                                                                                }`}
                                                                                                            >
                                                                                                                <div>
                                                                                                                    <div className="h-40 bg-gray-100 relative">
                                                                                                                        {song.thumbnail_url ? (
                                                                                                                            <img src={song.thumbnail_url} alt={song.title} className="w-full h-full object-cover" />
                                                                                                                        ) : (
                                                                                                                            <div className="w-full h-full flex items-center justify-center text-gray-400">
                                                                                                                                <i className="fa fa-image text-3xl"></i>
                                                                                                                            </div>
                                                                                                                        )}
                                                                                                                        <span className={`absolute top-2 right-2 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full ${song.status === "active" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"}`}>
                                                                                                                            {song.status}
                                                                                                                        </span>
                                                                                                                    </div>
                                                                                                                    <div className="p-4">
                                                                                                                        <h4 className="font-bold text-gray-800 truncate mb-1">{song.title}</h4>
                                                                                                                        {song.author && (
                                                                                                                            <p className="text-xs text-blue-600 font-semibold mb-2 truncate">
                                                                                                                                by {song.author}
                                                                                                                            </p>
                                                                                                                        )}
                                                                                                                        <p className="text-gray-500 text-xs line-clamp-2 h-8 leading-relaxed mb-3">{song.description || "No description provided."}</p>
                                                                                                                        <span className="px-2 py-0.5 bg-gray-100 text-[10px] text-gray-600 rounded-md capitalize font-semibold">{song.video_type}</span>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div className="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                                                                                                                    <button onClick={() => openEditSongModal(song)} className="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md text-xs">
                                                                                                                        <i className="fa fa-edit"></i>
                                                                                                                    </button>
                                                                                                                    <button onClick={() => openDeleteSongModal(song)} className="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs">
                                                                                                                        <i className="fa fa-trash"></i>
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        )}
                                                                                                    </Draggable>
                                                                                                ))}
                                                                                                {provided.placeholder}
                                                                                            </div>
                                                                                        )}
                                                                                    </Droppable>
                                                                                )}
                                                                            </div>
                                                                        </div>
                                                                    )}
                                                                </Draggable>
                                                            );
                                                        })}
                                                        {provided.placeholder}
                                                    </div>
                                                )}
                                            </Droppable>
                                        </DragDropContext>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            {/* Create Category Modal */}
            <Modal isOpen={newCategoryModalOpen} onClose={() => setNewCategoryModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4 capitalize">Create Category in {newCategoryLevel}</h3>
                <div className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                        <input
                            type="text"
                            placeholder="e.g. Reckless Love"
                            value={newCategoryName}
                            onChange={(e) => setNewCategoryName(e.target.value)}
                            className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm transition"
                        />
                    </div>
                    <div className="flex justify-end gap-3 pt-2">
                        <button onClick={() => setNewCategoryModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button
                            onClick={handleCreateCategory}
                            disabled={loading}
                            className="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            {loading ? (
                                <>
                                    <i className="fa fa-spinner fa-spin"></i>
                                    <span>Creating...</span>
                                </>
                            ) : (
                                "Create"
                            )}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Create Song Modal */}
            <Modal isOpen={isCreateSongModalOpen} onClose={() => setIsCreateSongModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4 capitalize">Add Song to {selectedCategoryName}</h3>
                <form onSubmit={handleCreateSongSubmit} className="space-y-4 max-h-[80vh] overflow-y-auto px-1">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Song Title</label>
                            <input
                                type="text"
                                required
                                value={newSong.title}
                                onChange={(e) => setNewSong({ ...newSong, title: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Artist / Author (Optional)</label>
                            <input
                                type="text"
                                value={newSong.author || ""}
                                onChange={(e) => setNewSong({ ...newSong, author: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                            <select
                                value={newSong.video_type}
                                onChange={(e) => setNewSong({ ...newSong, video_type: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="youtube">YouTube</option>
                                <option value="vimeo">Vimeo</option>
                                <option value="google">Google Drive</option>
                                <option value="local">Local Video</option>
                                <option value="iframe">Iframe / Embed Code</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video Link / ID</label>
                            <textarea
                                required
                                value={newSong.video_url}
                                onChange={(e) => setNewSong({ ...newSong, video_url: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500"
                                rows="2"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                value={newSong.status}
                                onChange={(e) => setNewSong({ ...newSong, status: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Tonal Center (Key)</label>
                            <select
                                value={newSong.tonal_center}
                                onChange={(e) => setNewSong({ ...newSong, tonal_center: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                            >
                                <option value="">None</option>
                                {tonalCenters.map((key) => (
                                    <option key={key.value} value={key.value}>{key.label}</option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                            <input
                                type="file"
                                accept="image/*"
                                ref={fileInputRef}
                                onChange={handleFileChange}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Description Images (Optional)</label>
                            <input
                                type="file"
                                multiple
                                accept="image/*"
                                onChange={(e) => setDescriptionImageFiles(Array.from(e.target.files))}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                            />
                            {descriptionImageFiles.length > 0 && (
                                <div className="text-xs text-gray-500 mt-1">
                                    {descriptionImageFiles.length} file(s) selected
                                </div>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Audio Track (Optional)</label>
                            <input
                                type="file"
                                accept="audio/*"
                                onChange={(e) => setAudioResourceFile(e.target.files[0] || null)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">PDF File (Optional)</label>
                            <input
                                type="file"
                                accept="application/pdf"
                                onChange={(e) => setPdfResourceFile(e.target.files[0] || null)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                            />
                        </div>
                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea
                                value={newSong.description}
                                onChange={(e) => setNewSong({ ...newSong, description: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>

                    {previewUrl && (
                        <div className="mt-3">
                            <p className="text-xs font-semibold text-gray-500 mb-1">Preview Thumbnail:</p>
                            <div className="w-32 h-20 border rounded-lg overflow-hidden">
                                <img src={previewUrl} alt="Preview" className="w-full h-full object-cover" />
                            </div>
                        </div>
                    )}

                    <div className="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onClick={() => setIsCreateSongModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button
                            type="submit"
                            disabled={loading}
                            className="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            {loading ? (
                                <>
                                    <i className="fa fa-spinner fa-spin"></i>
                                    <span>Saving...</span>
                                </>
                            ) : (
                                "Save Song"
                            )}
                        </button>
                    </div>
                </form>
            </Modal>

            {/* Edit Song Modal */}
            <Modal isOpen={isEditSongModalOpen} onClose={() => setIsEditSongModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4">Edit Song Details</h3>
                {editingSong && (
                    <form onSubmit={handleEditSongSubmit} className="space-y-4 max-h-[80vh] overflow-y-auto px-1">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div className="col-span-1 sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Song Title</label>
                                <input
                                    type="text"
                                    required
                                    value={editingSong.title}
                                    onChange={(e) => setEditingSong({ ...editingSong, title: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                            </div>
                            <div className="col-span-1 sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Artist / Author (Optional)</label>
                                <input
                                    type="text"
                                    value={editingSong.author || ""}
                                    onChange={(e) => setEditingSong({ ...editingSong, author: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                                <select
                                    value={editingSong.video_type}
                                    onChange={(e) => setEditingSong({ ...editingSong, video_type: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                >
                                    <option value="youtube">YouTube</option>
                                    <option value="vimeo">Vimeo</option>
                                    <option value="google">Google Drive</option>
                                    <option value="local">Local Video</option>
                                    <option value="iframe">Iframe / Embed Code</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video Link / ID</label>
                                <textarea
                                    required
                                    value={editingSong.video_url}
                                    onChange={(e) => setEditingSong({ ...editingSong, video_url: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    rows="2"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select
                                    value={editingSong.status}
                                    onChange={(e) => setEditingSong({ ...editingSong, status: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Tonal Center (Key)</label>
                                <select
                                    value={editingSong.tonal_center || ""}
                                    onChange={(e) => setEditingSong({ ...editingSong, tonal_center: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                >
                                    <option value="">None</option>
                                    {tonalCenters.map((key) => (
                                        <option key={key.value} value={key.value}>{key.label}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Thumbnail File (Optional)</label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleFileChange}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description Images (Optional)</label>
                                <input
                                    type="file"
                                    multiple
                                    accept="image/*"
                                    onChange={(e) => setDescriptionImageFiles(Array.from(e.target.files))}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                                {descriptionImageFiles.length > 0 && (
                                    <div className="text-xs text-gray-500 mt-1">
                                        {descriptionImageFiles.length} file(s) selected
                                    </div>
                                )}
                                {editingSong.image_urls && editingSong.image_urls.length > 0 && (
                                    <div className="mt-2 flex gap-2 flex-wrap">
                                        {editingSong.image_urls.map((url, idx) => (
                                            <div key={idx} className="w-16 h-12 border rounded overflow-hidden">
                                                <img src={url} className="w-full h-full object-cover" />
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Audio Track (Optional)</label>
                                <input
                                    type="file"
                                    accept="audio/*"
                                    onChange={(e) => setEditAudioResourceFile(e.target.files[0] || null)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                                {editingSong.audio_resource_url && (
                                    <div className="text-xs text-gray-500 mt-1">Audio track already uploaded.</div>
                                )}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">PDF File (Optional)</label>
                                <input
                                    type="file"
                                    accept="application/pdf"
                                    onChange={(e) => setEditPdfResourceFile(e.target.files[0] || null)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                                {editingSong.pdf_resource_url && (
                                    <div className="text-xs text-gray-500 mt-1">PDF already uploaded.</div>
                                )}
                            </div>
                            <div className="col-span-1 sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea
                                    value={editingSong.description || ""}
                                    onChange={(e) => setEditingSong({ ...editingSong, description: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>

                        {previewUrl && (
                            <div className="mt-3">
                                <p className="text-xs font-semibold text-gray-500 mb-1">Preview Thumbnail:</p>
                                <div className="w-32 h-20 border rounded-lg overflow-hidden">
                                    <img src={previewUrl} alt="Preview" className="w-full h-full object-cover" />
                                </div>
                            </div>
                        )}

                        <div className="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" onClick={() => setIsEditSongModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                            <button
                                type="submit"
                                disabled={loading}
                                className="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                            >
                                {loading ? (
                                    <>
                                        <i className="fa fa-spinner fa-spin"></i>
                                        <span>Updating...</span>
                                    </>
                                ) : (
                                    "Update Song"
                                )}
                            </button>
                        </div>
                    </form>
                )}
            </Modal>

            {/* Delete Song Modal */}
            <Modal isOpen={isDeleteSongModalOpen} onClose={() => setIsDeleteSongModalOpen(false)}>
                <div className="text-center p-3">
                    <h3 className="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h3>
                    <p className="text-gray-500 text-sm mb-6">Are you sure you want to delete song <span className="font-semibold text-red-600">"{songToDelete?.title}"</span>? This action is permanent.</p>
                    <div className="flex justify-center gap-3">
                        <button onClick={() => setIsDeleteSongModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button
                            onClick={handleDeleteSong}
                            disabled={loading}
                            className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            {loading ? (
                                <>
                                    <i className="fa fa-spinner fa-spin"></i>
                                    <span>Deleting...</span>
                                </>
                            ) : (
                                "Yes, Delete"
                            )}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Edit Category Modal */}
            <Modal isOpen={editCategoryModalOpen} onClose={() => setEditCategoryModalOpen(false)}>
                <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-800 mb-4">Edit Category Name</h3>
                    <div className="mb-4">
                        <label className="block mb-2 font-medium text-gray-700">Category Name:</label>
                        <input
                            type="text"
                            value={editingCategoryName}
                            onChange={(e) => setEditingCategoryName(e.target.value)}
                            className="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    <div className="flex justify-end gap-3">
                        <button onClick={() => setEditCategoryModalOpen(false)} className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">Cancel</button>
                        <button
                            onClick={handleUpdateCategory}
                            disabled={loading}
                            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            {loading ? (
                                <>
                                    <i className="fa fa-spinner fa-spin"></i>
                                    <span>Saving...</span>
                                </>
                            ) : (
                                "Save Changes"
                            )}
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    );
};

export default LearnSongsAdmin;

if (document.getElementById("learn-songs-admin")) {
    const root = ReactDOM.createRoot(document.getElementById("learn-songs-admin"));
    root.render(
        <FlashMessageProvider>
            <LearnSongsAdmin />
        </FlashMessageProvider>
    );
}
