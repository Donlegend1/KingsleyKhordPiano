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

const EtudesAdmin = () => {
    const [collapsedCategories, setCollapsedCategories] = useState({});
    const [etudesData, setEtudesData] = useState({ data: {} });
    const [allEtudes, setAllEtudes] = useState([]);
    const [loading, setLoading] = useState(false);
    
    // Modal states
    const [newCategoryModalOpen, setNewCategoryModalOpen] = useState(false);
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
                `/api/admin/etudes/category/${originalCategoryName}/update`,
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
            fetchEtudes();
            setEditCategoryModalOpen(false);
            showMessage("Category Updated successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error updating category", "error");
        } finally {
            setLoading(false);
        }
    };

    const [isCreateEtudeModalOpen, setIsCreateEtudeModalOpen] = useState(false);
    const [selectedCategoryName, setSelectedCategoryName] = useState("");
    const [newEtude, setNewEtude] = useState({
        title: "",
        author: "",
        description: "",
        video_type: "iframe",
        video_url: "",
        status: "active",
        related_etudes: [],
    });

    const [isEditEtudeModalOpen, setIsEditEtudeModalOpen] = useState(false);
    const [editingEtude, setEditingEtude] = useState(null);

    const [isDeleteCourseModalOpen, setIsDeleteCourseModalOpen] = useState(false);
    const [courseToDelete, setCourseToDelete] = useState(null);

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

    const fetchEtudes = async () => {
        setLoading(true);
        try {
            const response = await axios.get("/api/admin/etudes-list");
            setEtudesData(response.data);
        } catch (error) {
            console.error("Error fetching etudes:", error);
            showMessage("Error fetching etudes data", "error");
        } finally {
            setLoading(false);
        }
    };

    const fetchAllEtudesDropdown = async () => {
        try {
            const response = await axios.get("/api/admin/all-etudes");
            setAllEtudes(
                response.data.map((c) => ({
                    value: c.id,
                    label: c.title,
                }))
            );
        } catch (error) {
            console.error("Error fetching all etudes:", error);
        }
    };

    useEffect(() => {
        fetchEtudes();
        fetchAllEtudesDropdown();
    }, []);

    const toggleCategory = (key) => {
        setCollapsedCategories((prev) => ({ ...prev, [key]: !prev[key] }));
    };

    const handleCreateCategory = async () => {
        if (!newCategoryName.trim()) return;
        setLoading(true);
        try {
            await axios.post("/api/admin/etudes/category/create", {
                category: newCategoryName,
            }, {
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });
            fetchEtudes();
            setNewCategoryModalOpen(false);
            setNewCategoryName("");
            showMessage("Category created successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error creating category", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCategory = async (categoryName) => {
        if (!confirm(`Are you sure you want to delete category "${categoryName}"?`)) return;
        setLoading(true);
        try {
            await axios.delete(`/api/admin/etudes/category/${categoryName}/delete`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });
            fetchEtudes();
            showMessage("Category deleted successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error deleting category", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setThumbnailFile(file);
            setPreviewUrl(URL.createObjectURL(file));
        } else {
            setThumbnailFile(null);
            setPreviewUrl(null);
        }
    };

    const handleCreateEtude = async (e) => {
        e.preventDefault();
        setLoading(true);
        const formData = new FormData();
        formData.append("title", newEtude.title);
        formData.append("author", newEtude.author);
        formData.append("category", selectedCategoryName);
        formData.append("video_type", newEtude.video_type);
        formData.append("video_url", newEtude.video_url);
        formData.append("status", newEtude.status);
        formData.append("description", newEtude.description);

        if (thumbnailFile) {
            formData.append("thumbnail", thumbnailFile);
        }
        if (audioResourceFile) {
            formData.append("audio_resource", audioResourceFile);
        }
        if (pdfResourceFile) {
            formData.append("pdf_resource", pdfResourceFile);
        }
        descriptionImageFiles.forEach((f) => {
            formData.append("images[]", f);
        });
        newEtude.related_etudes.forEach((id) => {
            formData.append("related_etudes[]", id);
        });

        try {
            await axios.post("/api/admin/etudes/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": csrfToken,
                },
                withCredentials: true,
            });
            fetchEtudes();
            fetchAllEtudesDropdown();
            setIsCreateEtudeModalOpen(false);
            setNewEtude({
                title: "",
                author: "",
                description: "",
                video_type: "iframe",
                video_url: "",
                status: "active",
                related_etudes: [],
            });
            setThumbnailFile(null);
            setPreviewUrl(null);
            setDescriptionImageFiles([]);
            setAudioResourceFile(null);
            setPdfResourceFile(null);
            showMessage("Etude/Piece created successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error creating etude", "error");
        } finally {
            setLoading(false);
        }
    };

    const openEditModal = (etude) => {
        setEditingEtude({
            ...etude,
            related_etudes: etude.related_etudes || [],
        });
        setPreviewUrl(etude.thumbnail_url);
        setEditAudioResourceFile(null);
        setEditPdfResourceFile(null);
        setDescriptionImageFiles([]);
        setThumbnailFile(null);
        setIsEditEtudeModalOpen(true);
    };

    const handleUpdateEtude = async (e) => {
        e.preventDefault();
        setLoading(true);
        const formData = new FormData();
        formData.append("title", editingEtude.title);
        formData.append("author", editingEtude.author || "");
        formData.append("video_type", editingEtude.video_type);
        formData.append("video_url", editingEtude.video_url);
        formData.append("status", editingEtude.status);
        formData.append("description", editingEtude.description || "");

        if (thumbnailFile) {
            formData.append("thumbnail", thumbnailFile);
        }
        if (editAudioResourceFile) {
            formData.append("audio_resource", editAudioResourceFile);
        }
        if (editPdfResourceFile) {
            formData.append("pdf_resource", editPdfResourceFile);
        }
        descriptionImageFiles.forEach((f) => {
            formData.append("images[]", f);
        });
        editingEtude.related_etudes.forEach((id) => {
            formData.append("related_etudes[]", id);
        });

        try {
            await axios.post(`/api/admin/etudes/update/${editingEtude.id}`, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": csrfToken,
                },
                withCredentials: true,
            });
            fetchEtudes();
            fetchAllEtudesDropdown();
            setIsEditEtudeModalOpen(false);
            setEditingEtude(null);
            setThumbnailFile(null);
            setPreviewUrl(null);
            setEditAudioResourceFile(null);
            setEditPdfResourceFile(null);
            setDescriptionImageFiles([]);
            showMessage("Etude/Piece updated successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error updating etude", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteEtude = async () => {
        if (!courseToDelete) return;
        setLoading(true);
        try {
            await axios.delete(`/api/admin/etudes/${courseToDelete.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });
            fetchEtudes();
            fetchAllEtudesDropdown();
            setIsDeleteCourseModalOpen(false);
            setCourseToDelete(null);
            showMessage("Etude deleted successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error deleting etude", "error");
        } finally {
            setLoading(false);
        }
    };

    const onDragEnd = async (result) => {
        const { source, destination, type } = result;
        if (!destination) return;

        if (type === "category") {
            const keys = Object.keys(etudesData.data || {});
            const [moved] = keys.splice(source.index, 1);
            keys.splice(destination.index, 0, moved);

            const updatedData = {};
            keys.forEach((k) => {
                updatedData[k] = etudesData.data[k];
            });
            setEtudesData({ ...etudesData, data: updatedData });

            try {
                await axios.post("/api/admin/reorder/etudes", {
                    categories: keys,
                }, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                });
                showMessage("Category positions updated", "success");
            } catch (error) {
                showMessage("Error updating category positions", "error");
            }
        } else {
            const sourceCategory = source.droppableId;
            const destCategory = destination.droppableId;

            if (sourceCategory !== destCategory) {
                showMessage("Cannot drag lessons between different categories", "error");
                return;
            }

            const list = Array.from(etudesData.data[sourceCategory]);
            const [moved] = list.splice(source.index, 1);
            list.splice(destination.index, 0, moved);

            const updatedData = { ...etudesData.data, [sourceCategory]: list };
            setEtudesData({ ...etudesData, data: updatedData });

            try {
                await axios.post("/api/admin/reorder/etudes/items", {
                    etudes: list.map((i) => i.id),
                }, {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                });
                showMessage("Lesson positions updated", "success");
            } catch (error) {
                showMessage("Error updating lesson positions", "error");
            }
        }
    };

    const dataObj = etudesData.data || {};
    const categoryKeys = Object.keys(dataObj);

    return (
        <div className="bg-gray-50 min-h-screen pb-12">
            <div className="max-w-6xl mx-auto">
                <div className="flex items-center justify-between mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">Etudes & Pieces Management</h1>
                        <p className="text-sm text-gray-400 mt-1">Manage, arrange, and drag-and-drop lessons and categories</p>
                    </div>
                    <button
                        onClick={() => {
                            setNewCategoryName("");
                            setNewCategoryModalOpen(true);
                        }}
                        className="bg-black text-white px-5 py-3 rounded-xl hover:bg-gray-800 transition text-sm font-semibold flex items-center gap-2"
                    >
                        <span className="text-lg">+</span> Add Category
                    </button>
                </div>

                <DragDropContext onDragEnd={onDragEnd}>
                    <Droppable droppableId="categories-wrapper" type="category">
                        {(provided) => (
                            <div {...provided.droppableProps} ref={provided.innerRef} className="space-y-6">
                                {categoryKeys.map((catName, index) => {
                                    const lessons = dataObj[catName] || [];
                                    const collapsedKey = catName;
                                    const isCollapsed = collapsedCategories[collapsedKey];

                                    return (
                                        <Draggable key={catName} draggableId={`cat-${catName}`} index={index}>
                                            {(dragProvided) => (
                                                <div
                                                    ref={dragProvided.innerRef}
                                                    {...dragProvided.draggableProps}
                                                    className="bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden"
                                                >
                                                    <div className="flex items-center justify-between p-5 bg-gray-50/50 border-b border-gray-100">
                                                        <div className="flex items-center gap-3">
                                                            <div {...dragProvided.dragHandleProps} className="text-gray-400 hover:text-gray-600 cursor-grab p-1">
                                                                <i className="fa-solid fa-bars-staggered"></i>
                                                            </div>
                                                            <button
                                                                onClick={() => toggleCategory(collapsedKey)}
                                                                className="text-left font-bold text-gray-800 hover:text-indigo-600 transition flex items-center gap-2"
                                                            >
                                                                <span className="text-sm text-gray-400">
                                                                    {isCollapsed ? "▶" : "▼"}
                                                                </span>
                                                                {catName}
                                                                <span className="ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full font-medium">
                                                                    {lessons.length} {lessons.length === 1 ? "lesson" : "lessons"}
                                                                </span>
                                                            </button>
                                                        </div>

                                                        <div className="flex items-center gap-2">
                                                            <button
                                                                onClick={() => {
                                                                    setSelectedCategoryName(catName);
                                                                    setIsCreateEtudeModalOpen(true);
                                                                }}
                                                                className="text-xs bg-black text-white hover:bg-gray-800 transition px-3 py-1.5 rounded-lg font-semibold"
                                                            >
                                                                + Add Lesson
                                                            </button>
                                                            <button
                                                                onClick={() => openEditCategoryModal(catName)}
                                                                className="text-xs bg-gray-200 text-gray-700 hover:bg-gray-300 transition px-3 py-1.5 rounded-lg font-semibold"
                                                            >
                                                                Rename
                                                            </button>
                                                            <button
                                                                onClick={() => handleDeleteCategory(catName)}
                                                                className="text-xs bg-rose-50 text-rose-600 hover:bg-rose-100 transition px-3 py-1.5 rounded-lg font-semibold"
                                                            >
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>

                                                    {!isCollapsed && (
                                                        <Droppable droppableId={catName} type="lesson">
                                                            {(dropProvided) => (
                                                                <div
                                                                    {...dropProvided.droppableProps}
                                                                    ref={dropProvided.innerRef}
                                                                    className="p-4 space-y-2 bg-white"
                                                                >
                                                                    {lessons.map((lesson, idx) => (
                                                                        <Draggable key={lesson.id} draggableId={`lesson-${lesson.id}`} index={idx}>
                                                                            {(lessonDragProvided) => (
                                                                                <div
                                                                                    ref={lessonDragProvided.innerRef}
                                                                                    {...lessonDragProvided.draggableProps}
                                                                                    className="flex items-center justify-between p-3.5 bg-white border border-gray-100 rounded-xl hover:shadow-sm transition group"
                                                                                >
                                                                                    <div className="flex items-center gap-3">
                                                                                        <div {...lessonDragProvided.dragHandleProps} className="text-gray-300 hover:text-gray-500 cursor-grab p-1">
                                                                                            <i className="fa-solid fa-grip-vertical"></i>
                                                                                        </div>
                                                                                        {lesson.thumbnail && (
                                                                                            <div className="w-12 h-8 rounded overflow-hidden bg-gray-100 border">
                                                                                                <img src={lesson.thumbnail_url} className="w-full h-full object-cover" />
                                                                                            </div>
                                                                                        )}
                                                                                        <div>
                                                                                            <p className="text-sm font-bold text-gray-800">{lesson.title}</p>
                                                                                            {lesson.author && (
                                                                                                <p className="text-xs text-gray-400">By {lesson.author}</p>
                                                                                            )}
                                                                                        </div>
                                                                                    </div>

                                                                                    <div className="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition duration-150">
                                                                                        <button
                                                                                            onClick={() => openEditModal(lesson)}
                                                                                            className="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 rounded-md font-semibold"
                                                                                        >
                                                                                            Edit
                                                                                        </button>
                                                                                        <button
                                                                                            onClick={() => {
                                                                                                setCourseToDelete(lesson);
                                                                                                setIsDeleteCourseModalOpen(true);
                                                                                            }}
                                                                                            className="text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 px-2.5 py-1 rounded-md font-semibold"
                                                                                        >
                                                                                            Delete
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            )}
                                                                        </Draggable>
                                                                    ))}
                                                                    {dropProvided.placeholder}
                                                                    {lessons.length === 0 && (
                                                                        <div className="text-center py-8 text-sm text-gray-400">
                                                                            No lessons in this category yet. Click "+ Add Lesson" to create one.
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            )}
                                                        </Droppable>
                                                    )}
                                                </div>
                                            )}
                                        </Draggable>
                                    );
                                })}
                                {provided.placeholder}
                                {categoryKeys.length === 0 && (
                                    <div className="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300 text-gray-400">
                                        No categories found. Click "+ Add Category" to get started.
                                    </div>
                                )}
                            </div>
                        )}
                    </Droppable>
                </DragDropContext>
            </div>

            {/* Create Category Modal */}
            <Modal isOpen={newCategoryModalOpen} onClose={() => setNewCategoryModalOpen(false)}>
                <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-800 mb-4">Create New Category</h3>
                    <div className="mb-4">
                        <label className="block mb-2 font-medium text-gray-700">Category Name:</label>
                        <input
                            type="text"
                            placeholder="e.g., Classical Pieces"
                            value={newCategoryName}
                            onChange={(e) => setNewCategoryName(e.target.value)}
                            className="w-full px-3.5 py-2.5 border rounded-xl outline-none text-sm border-gray-300 focus:ring-2 focus:ring-black"
                        />
                    </div>
                    <div className="flex justify-end gap-3">
                        <button onClick={() => setNewCategoryModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button
                            onClick={handleCreateCategory}
                            disabled={loading}
                            className="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm font-semibold disabled:opacity-50 flex items-center gap-2"
                        >
                            {loading ? <i className="fa fa-spinner fa-spin"></i> : "Create Category"}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Create Etude Modal */}
            <Modal isOpen={isCreateEtudeModalOpen} onClose={() => setIsCreateEtudeModalOpen(false)}>
                <div className="p-6 max-h-[85vh] overflow-y-auto">
                    <h3 className="text-xl font-bold text-gray-800 mb-4">Add New Lesson under "{selectedCategoryName}"</h3>
                    <form onSubmit={handleCreateEtude} className="space-y-4">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input
                                    type="text"
                                    required
                                    value={newEtude.title}
                                    onChange={(e) => setNewEtude({ ...newEtude, title: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Composer/Author (Optional)</label>
                                <input
                                    type="text"
                                    value={newEtude.author}
                                    onChange={(e) => setNewEtude({ ...newEtude, author: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video Source</label>
                                <select
                                    value={newEtude.video_type}
                                    onChange={(e) => setNewEtude({ ...newEtude, video_type: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                >
                                    <option value="iframe">Raw Link/Iframe</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="google">Google Drive</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                                <input
                                    type="text"
                                    required
                                    value={newEtude.video_url}
                                    onChange={(e) => setNewEtude({ ...newEtude, video_url: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select
                                    value={newEtude.status}
                                    onChange={(e) => setNewEtude({ ...newEtude, status: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Thumbnail File</label>
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
                                    value={newEtude.description}
                                    onChange={(e) => setNewEtude({ ...newEtude, description: e.target.value })}
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
                            <button type="button" onClick={() => setIsCreateEtudeModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                            <button
                                type="submit"
                                disabled={loading}
                                className="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm disabled:opacity-50 flex items-center gap-2"
                            >
                                {loading ? <i className="fa fa-spinner fa-spin"></i> : "Add Lesson"}
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

            {/* Edit Etude Modal */}
            <Modal isOpen={isEditEtudeModalOpen} onClose={() => setIsEditEtudeModalOpen(false)}>
                <div className="p-6 max-h-[85vh] overflow-y-auto">
                    <h3 className="text-xl font-bold text-gray-800 mb-4">Edit Lesson</h3>
                    {editingEtude && (
                        <form onSubmit={handleUpdateEtude} className="space-y-4">
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input
                                        type="text"
                                        required
                                        value={editingEtude.title}
                                        onChange={(e) => setEditingEtude({ ...editingEtude, title: e.target.value })}
                                        className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Composer/Author (Optional)</label>
                                    <input
                                        type="text"
                                        value={editingEtude.author || ""}
                                        onChange={(e) => setEditingEtude({ ...editingEtude, author: e.target.value })}
                                        className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Video Source</label>
                                    <select
                                        value={editingEtude.video_type}
                                        onChange={(e) => setEditingEtude({ ...editingEtude, video_type: e.target.value })}
                                        className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    >
                                        <option value="iframe">Raw Link/Iframe</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="google">Google Drive</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                                    <input
                                        type="text"
                                        required
                                        value={editingEtude.video_url}
                                        onChange={(e) => setEditingEtude({ ...editingEtude, video_url: e.target.value })}
                                        className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select
                                        value={editingEtude.status}
                                        onChange={(e) => setEditingEtude({ ...editingEtude, status: e.target.value })}
                                        className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    >
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
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
                                    {editingEtude.image_urls && editingEtude.image_urls.length > 0 && (
                                        <div className="mt-2 flex gap-2 flex-wrap">
                                            {editingEtude.image_urls.map((url, idx) => (
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
                                    {editingEtude.audio_resource_url && (
                                        <div className="text-xs text-gray-400 mt-1">Audio track already uploaded.</div>
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
                                    {editingEtude.pdf_resource_url && (
                                        <div className="text-xs text-gray-400 mt-1">PDF already uploaded.</div>
                                    )}
                                </div>
                                <div className="col-span-1 sm:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea
                                        value={editingEtude.description || ""}
                                        onChange={(e) => setEditingEtude({ ...editingEtude, description: e.target.value })}
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
                                <button type="button" onClick={() => setIsEditEtudeModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm disabled:opacity-50 flex items-center gap-2"
                                >
                                    {loading ? <i className="fa fa-spinner fa-spin"></i> : "Update Lesson"}
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            </Modal>

            {/* Delete Lesson Modal */}
            <Modal isOpen={isDeleteCourseModalOpen} onClose={() => setIsDeleteCourseModalOpen(false)}>
                <div className="text-center p-6">
                    <h3 className="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h3>
                    <p className="text-gray-500 text-sm mb-6">Are you sure you want to delete lesson <span className="font-semibold text-red-600">"{courseToDelete?.title}"</span>? This action is permanent.</p>
                    <div className="flex justify-center gap-3">
                        <button onClick={() => setIsDeleteCourseModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button
                            onClick={handleDeleteEtude}
                            disabled={loading}
                            className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold disabled:opacity-50 flex items-center gap-2"
                        >
                            {loading ? <i className="fa fa-spinner fa-spin"></i> : "Yes, Delete"}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Edit Category Name Modal */}
            <Modal isOpen={editCategoryModalOpen} onClose={() => setEditCategoryModalOpen(false)}>
                <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-800 mb-4">Edit Category Name</h3>
                    <div className="mb-4">
                        <label className="block mb-2 font-medium text-gray-700">Category Name:</label>
                        <input
                            type="text"
                            value={editingCategoryName}
                            onChange={(e) => setEditingCategoryName(e.target.value)}
                            className="w-full px-3.5 py-2.5 border rounded-xl outline-none text-sm border-gray-300 focus:ring-2 focus:ring-black"
                        />
                    </div>
                    <div className="flex justify-end gap-3">
                        <button onClick={() => setEditCategoryModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button
                            onClick={handleUpdateCategory}
                            disabled={loading}
                            className="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm font-semibold disabled:opacity-50 flex items-center gap-2"
                        >
                            {loading ? <i className="fa fa-spinner fa-spin"></i> : "Save Changes"}
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    );
};

export default EtudesAdmin;

if (document.getElementById("etudes-admin")) {
    const root = ReactDOM.createRoot(document.getElementById("etudes-admin"));
    root.render(
        <FlashMessageProvider>
            <EtudesAdmin />
        </FlashMessageProvider>
    );
}
