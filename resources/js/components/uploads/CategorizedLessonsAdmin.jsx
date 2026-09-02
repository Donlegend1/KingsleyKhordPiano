import ReactDOM from "react-dom/client";
import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import Modal from "../Modal/Modal";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const extraFieldDefaults = (fields = []) =>
    Object.fromEntries((fields || []).map((field) => [field.name, field.default ?? ""]));

const CategorizedLessonsAdmin = ({ config }) => {
    const emptyCollapsed = Object.fromEntries(config.levels.map((level) => [level, false]));
    const emptyData = Object.fromEntries(config.levels.map((level) => [level, { data: {} }]));

    const [collapsedSections, setCollapsedSections] = useState(emptyCollapsed);
    const [collapsedCategories, setCollapsedCategories] = useState({});
    const [lessonsData, setLessonsData] = useState(emptyData);
    const [loading, setLoading] = useState(false);

    const [newCategoryModalOpen, setNewCategoryModalOpen] = useState(false);
    const [newCategoryLevel, setNewCategoryLevel] = useState(null);
    const [newCategoryName, setNewCategoryName] = useState("");

    const [editCategoryModalOpen, setEditCategoryModalOpen] = useState(false);
    const [editingCategoryName, setEditingCategoryName] = useState("");
    const [originalCategoryName, setOriginalCategoryName] = useState("");
    const [editingCategoryLevel, setEditingCategoryLevel] = useState("");

    const blankLesson = () => ({
        title: "",
        description: "",
        video_type: config.defaultVideoType || "iframe",
        video_url: "",
        status: "active",
        ...extraFieldDefaults(config.extraFields),
    });

    const [isCreateLessonModalOpen, setIsCreateLessonModalOpen] = useState(false);
    const [selectedLevel, setSelectedLevel] = useState("");
    const [selectedCategoryName, setSelectedCategoryName] = useState("");
    const [newLesson, setNewLesson] = useState(blankLesson());

    const [isEditLessonModalOpen, setIsEditLessonModalOpen] = useState(false);
    const [editingLesson, setEditingLesson] = useState(null);

    const [isDeleteLessonModalOpen, setIsDeleteLessonModalOpen] = useState(false);
    const [lessonToDelete, setLessonToDelete] = useState(null);

    const [thumbnailFile, setThumbnailFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(null);
    const [descriptionImageFiles, setDescriptionImageFiles] = useState([]);
    const [audioResourceFile, setAudioResourceFile] = useState(null);
    const [pdfResourceFile, setPdfResourceFile] = useState(null);
    const [midiResourceFile, setMidiResourceFile] = useState(null);
    const [editAudioResourceFile, setEditAudioResourceFile] = useState(null);
    const [editPdfResourceFile, setEditPdfResourceFile] = useState(null);
    const [editMidiResourceFile, setEditMidiResourceFile] = useState(null);
    const fileInputRef = useRef(null);

    const { showMessage } = useFlashMessage();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    const fetchLessons = async () => {
        setLoading(true);
        try {
            const response = await axios.get(config.endpoints.list);
            setLessonsData(response.data);
        } catch (error) {
            showMessage("Error fetching lessons data", "error");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchLessons();
    }, []);

    const toggleSection = (level) => {
        setCollapsedSections((prev) => ({ ...prev, [level]: !prev[level] }));
    };

    const toggleCategory = (key) => {
        setCollapsedCategories((prev) => ({ ...prev, [key]: !prev[key] }));
    };

    const encodeName = (name) => encodeURIComponent(name);

    const handleCreateCategory = async () => {
        if (!newCategoryName.trim()) return;
        setLoading(true);
        try {
            await axios.post(config.endpoints.createCategory, {
                category: newCategoryName,
                level: newCategoryLevel,
            }, { headers: { "X-CSRF-TOKEN": csrfToken } });
            showMessage("Category created successfully", "success");
            setNewCategoryModalOpen(false);
            fetchLessons();
        } catch (error) {
            showMessage(error.response?.data?.message || "Error creating category", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleUpdateCategory = async () => {
        if (!editingCategoryName.trim()) return;
        setLoading(true);
        try {
            await axios.put(
                `${config.endpoints.updateCategory}/${encodeName(originalCategoryName)}/update`,
                { category: editingCategoryName, level: editingCategoryLevel },
                { headers: { "X-CSRF-TOKEN": csrfToken }, withCredentials: true }
            );
            fetchLessons();
            setEditCategoryModalOpen(false);
            showMessage("Category updated successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error updating category", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCategory = async (categoryName, level) => {
        if (!confirm(`Delete category "${categoryName}"?`)) return;
        setLoading(true);
        try {
            await axios.delete(
                `${config.endpoints.deleteCategory}/${encodeName(categoryName)}/delete`,
                { headers: { "X-CSRF-TOKEN": csrfToken }, params: { level } }
            );
            showMessage("Category deleted successfully", "success");
            fetchLessons();
        } catch (error) {
            showMessage(error.response?.data?.message || "Error deleting category", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleOnDragEnd = async (result, level) => {
        if (!result.destination) return;
        const itemPrefix = `items-${level}::`;

        if (result.source.droppableId === `droppable-${level}`) {
            const currentData = lessonsData[level]?.data || {};
            const items = Object.entries(currentData);
            const [moved] = items.splice(result.source.index, 1);
            items.splice(result.destination.index, 0, moved);
            const updatedData = Object.fromEntries(items);
            setLessonsData((prev) => ({ ...prev, [level]: { ...prev[level], data: updatedData } }));
            try {
                await axios.post(config.endpoints.reorderCategories, {
                    level,
                    categories: items.map(([category]) => category),
                }, { headers: { "X-CSRF-TOKEN": csrfToken } });
            } catch (error) {
                showMessage("Failed to save category order", "error");
            }
        } else if (result.source.droppableId.startsWith(itemPrefix)) {
            const categoryName = result.source.droppableId.slice(itemPrefix.length);
            const currentData = lessonsData[level]?.data || {};
            const updatedData = {};
            Object.entries(currentData).forEach(([cat, list]) => {
                if (cat === categoryName) {
                    const lessonList = Array.from(list);
                    const [moved] = lessonList.splice(result.source.index, 1);
                    lessonList.splice(result.destination.index, 0, moved);
                    updatedData[cat] = lessonList;
                } else {
                    updatedData[cat] = list;
                }
            });
            setLessonsData((prev) => ({ ...prev, [level]: { ...prev[level], data: updatedData } }));
            try {
                await axios.post(config.endpoints.reorderItems, {
                    items: (updatedData[categoryName] || []).map((lesson) => lesson.id),
                }, { headers: { "X-CSRF-TOKEN": csrfToken } });
            } catch (error) {
                showMessage("Failed to save lesson order", "error");
            }
        }
    };

    const appendExtraFields = (formData, source) => {
        (config.extraFields || []).forEach((field) => {
            if (source[field.name] != null) {
                formData.append(field.name, source[field.name]);
            }
        });
    };

    const handleCreateLessonSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        const formData = new FormData();
        formData.append("title", newLesson.title);
        formData.append("description", newLesson.description || "");
        formData.append("category", selectedCategoryName);
        formData.append("level", selectedLevel);
        formData.append("video_type", newLesson.video_type);
        formData.append("video_url", newLesson.video_url);
        formData.append("status", newLesson.status);
        appendExtraFields(formData, newLesson);
        if (thumbnailFile) formData.append("thumbnail", thumbnailFile);
        descriptionImageFiles.forEach((file, idx) => formData.append(`images[${idx}]`, file));
        if (audioResourceFile) formData.append("audio_resource", audioResourceFile);
        if (pdfResourceFile) formData.append("pdf_resource", pdfResourceFile);
        if (midiResourceFile) formData.append("midi_resource", midiResourceFile);
        try {
            await axios.post(config.endpoints.store, formData, {
                headers: { "Content-Type": "multipart/form-data", "X-CSRF-TOKEN": csrfToken },
            });
            showMessage(`${config.itemLabel} added successfully`, "success");
            setIsCreateLessonModalOpen(false);
            fetchLessons();
        } catch (error) {
            showMessage(error.response?.data?.message || `Error adding ${config.itemLabel.toLowerCase()}`, "error");
        } finally {
            setLoading(false);
        }
    };

    const handleEditLessonSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        const formData = new FormData();
        formData.append("title", editingLesson.title);
        formData.append("description", editingLesson.description || "");
        formData.append("video_type", editingLesson.video_type);
        formData.append("video_url", editingLesson.video_url);
        formData.append("status", editingLesson.status);
        appendExtraFields(formData, editingLesson);
        if (thumbnailFile) formData.append("thumbnail", thumbnailFile);
        descriptionImageFiles.forEach((file, idx) => formData.append(`images[${idx}]`, file));
        if (editAudioResourceFile) formData.append("audio_resource", editAudioResourceFile);
        if (editPdfResourceFile) formData.append("pdf_resource", editPdfResourceFile);
        if (editMidiResourceFile) formData.append("midi_resource", editMidiResourceFile);
        try {
            await axios.post(`${config.endpoints.update}/${editingLesson.id}`, formData, {
                headers: { "Content-Type": "multipart/form-data", "X-CSRF-TOKEN": csrfToken },
            });
            showMessage(`${config.itemLabel} updated successfully`, "success");
            setIsEditLessonModalOpen(false);
            fetchLessons();
        } catch (error) {
            showMessage(error.response?.data?.message || `Error updating ${config.itemLabel.toLowerCase()}`, "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteLesson = async () => {
        setLoading(true);
        try {
            await axios.delete(`${config.endpoints.destroy}/${lessonToDelete.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });
            showMessage(`${config.itemLabel} deleted successfully`, "success");
            setIsDeleteLessonModalOpen(false);
            fetchLessons();
        } catch (error) {
            showMessage(`Error deleting ${config.itemLabel.toLowerCase()}`, "error");
        } finally {
            setLoading(false);
        }
    };

    const extraFieldInputs = (source, setSource) =>
        (config.extraFields || []).map((field) => (
            <div key={field.name}>
                <label className="block text-sm font-medium text-gray-700 mb-1">{field.label}</label>
                <select
                    value={source[field.name] || field.default || ""}
                    onChange={(e) => setSource({ ...source, [field.name]: e.target.value })}
                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                >
                    {(field.options || []).map((option) => (
                        <option key={option} value={option}>{option}</option>
                    ))}
                </select>
            </div>
        ));

    const sectionTitle = (level) =>
        config.sectionTitle ? config.sectionTitle(level) : `${level} ${config.sectionSuffix || "Level"}`;

    const videoTypeSelect = (value, onChange) => (
        <select value={value} onChange={onChange} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
            <option value="youtube">YouTube</option>
            <option value="vimeo">Vimeo</option>
            <option value="google">Google Drive</option>
            <option value="local">Local Video</option>
            <option value="iframe">Iframe / Embed Code</option>
        </select>
    );

    return (
        <div className="bg-white p-6 rounded-lg shadow-lg">
            <div className="flex justify-between items-center mb-6">
                <h2 className="text-2xl font-bold text-gray-800">{config.title}</h2>
            </div>

            {loading && !newCategoryModalOpen && !isCreateLessonModalOpen && !isEditLessonModalOpen && !isDeleteLessonModalOpen ? (
                <div className="flex justify-center items-center h-64">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
                </div>
            ) : (
                <div className="space-y-8">
                    {config.levels.map((level) => {
                        const orderedCategories = Object.entries(lessonsData[level]?.data || {});
                        return (
                            <div key={level} className="mb-8 border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                <h3
                                    onClick={() => toggleSection(level)}
                                    className="text-lg font-semibold px-5 py-4 bg-gray-50 flex justify-between items-center cursor-pointer hover:bg-gray-100 select-none border-b border-gray-200 capitalize text-gray-800"
                                >
                                    <span>{sectionTitle(level)}</span>
                                    <div className="flex items-center gap-3">
                                        <button
                                            onClick={(e) => {
                                                e.stopPropagation();
                                                setNewCategoryLevel(level);
                                                setNewCategoryName("");
                                                setNewCategoryModalOpen(true);
                                            }}
                                            className="px-3 py-1 bg-black text-white text-xs rounded-full hover:bg-gray-800 transition"
                                        >
                                            Add Category
                                        </button>
                                        <i className={`fa ${collapsedSections[level] ? "fa-chevron-down" : "fa-chevron-up"} text-sm text-gray-500`}></i>
                                    </div>
                                </h3>
                                <div className={collapsedSections[level] ? "hidden" : "p-5 bg-white space-y-6"}>
                                    {orderedCategories.length === 0 ? (
                                        <p className="text-gray-500 text-sm text-center py-6">No categories created yet in this section.</p>
                                    ) : (
                                        <DragDropContext onDragEnd={(res) => handleOnDragEnd(res, level)}>
                                            <Droppable droppableId={`droppable-${level}`}>
                                                {(provided) => (
                                                    <div ref={provided.innerRef} {...provided.droppableProps}>
                                                        {orderedCategories.map(([categoryName, lessons], index) => {
                                                            const isCollapsed = collapsedCategories[`${level}-${categoryName}`];
                                                            return (
                                                                <Draggable key={categoryName} draggableId={`${level}-${categoryName}`} index={index}>
                                                                    {(provided, snapshot) => (
                                                                        <div
                                                                            ref={provided.innerRef}
                                                                            {...provided.draggableProps}
                                                                            className={`mb-6 p-4 rounded-xl border transition-all ${snapshot.isDragging ? "bg-blue-50/70 border-blue-300 shadow-md scale-[1.01]" : "bg-gray-50 border-gray-200"}`}
                                                                        >
                                                                            <div
                                                                                {...provided.dragHandleProps}
                                                                                onClick={() => toggleCategory(`${level}-${categoryName}`)}
                                                                                className="flex justify-between items-center cursor-pointer select-none bg-white p-3 rounded-lg border border-gray-200/80 shadow-sm hover:bg-gray-100/50 transition"
                                                                            >
                                                                                <span className="font-semibold text-gray-800">{categoryName}</span>
                                                                                <div className="flex items-center gap-4">
                                                                                    <button
                                                                                        onClick={(e) => {
                                                                                            e.stopPropagation();
                                                                                            setSelectedLevel(level);
                                                                                            setSelectedCategoryName(categoryName);
                                                                                            setNewLesson(blankLesson());
                                                                                            setThumbnailFile(null);
                                                                                            setPreviewUrl(null);
                                                                                            setDescriptionImageFiles([]);
                                                                                            setAudioResourceFile(null);
                                                                                            setPdfResourceFile(null);
                                                                                            setMidiResourceFile(null);
                                                                                            setIsCreateLessonModalOpen(true);
                                                                                        }}
                                                                                        className="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition"
                                                                                    >
                                                                                        Add {config.itemLabel}
                                                                                    </button>
                                                                                    <i
                                                                                        className="fa fa-pencil text-blue-500 hover:text-blue-700 text-sm cursor-pointer"
                                                                                        onClick={(e) => {
                                                                                            e.stopPropagation();
                                                                                            setOriginalCategoryName(categoryName);
                                                                                            setEditingCategoryName(categoryName);
                                                                                            setEditingCategoryLevel(level);
                                                                                            setEditCategoryModalOpen(true);
                                                                                        }}
                                                                                    ></i>
                                                                                    <i
                                                                                        className="fa fa-trash text-red-500 hover:text-red-700 text-sm"
                                                                                        onClick={(e) => {
                                                                                            e.stopPropagation();
                                                                                            handleDeleteCategory(categoryName, level);
                                                                                        }}
                                                                                    ></i>
                                                                                    <i className={`fa ${isCollapsed ? "fa-chevron-down" : "fa-chevron-up"} text-sm text-gray-500`}></i>
                                                                                </div>
                                                                            </div>
                                                                            <div className={`mt-4 overflow-hidden transition-all duration-300 ${isCollapsed ? "max-h-0 opacity-0" : "max-h-[3000px] opacity-100"}`}>
                                                                                {lessons.length === 0 ? (
                                                                                    <p className="text-gray-500 text-xs text-center py-4">No {config.itemLabel.toLowerCase()}s in this category yet.</p>
                                                                                ) : (
                                                                                    <Droppable droppableId={`items-${level}::${categoryName}`} type="lesson">
                                                                                        {(provided) => (
                                                                                            <div ref={provided.innerRef} {...provided.droppableProps} className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                                                                                {lessons.map((lesson, lessonIndex) => (
                                                                                                    <Draggable key={lesson.id} draggableId={`lesson-${lesson.id}`} index={lessonIndex}>
                                                                                                        {(provided, snapshot) => (
                                                                                                            <div
                                                                                                                ref={provided.innerRef}
                                                                                                                {...provided.draggableProps}
                                                                                                                {...provided.dragHandleProps}
                                                                                                                className={`bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col justify-between hover:shadow-md transition ${snapshot.isDragging ? "ring-2 ring-blue-400 scale-[1.01]" : "border-gray-100"}`}
                                                                                                            >
                                                                                                                <div>
                                                                                                                    <div className="h-40 bg-gray-100 relative">
                                                                                                                        {lesson.thumbnail_url ? (
                                                                                                                            <img src={lesson.thumbnail_url} alt={lesson.title} className="w-full h-full object-cover" />
                                                                                                                        ) : (
                                                                                                                            <div className="w-full h-full flex items-center justify-center text-gray-400">
                                                                                                                                <i className="fa fa-image text-3xl"></i>
                                                                                                                            </div>
                                                                                                                        )}
                                                                                                                        <span className={`absolute top-2 right-2 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full ${lesson.status === "active" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"}`}>
                                                                                                                            {lesson.status}
                                                                                                                        </span>
                                                                                                                    </div>
                                                                                                                    <div className="p-4">
                                                                                                                        <h4 className="font-bold text-gray-800 truncate mb-1">{lesson.title}</h4>
                                                                                                                        <p className="text-gray-500 text-xs line-clamp-2 h-8 leading-relaxed mb-3">{lesson.description || "No description provided."}</p>
                                                                                                                        <div className="flex flex-wrap gap-1">
                                                                                                                            <span className="px-2 py-0.5 bg-gray-100 text-[10px] text-gray-600 rounded-md capitalize font-semibold">{lesson.video_type}</span>
                                                                                                                            {lesson.skill_level && (
                                                                                                                                <span className="px-2 py-0.5 bg-indigo-50 text-[10px] text-indigo-700 rounded-md font-semibold">{lesson.skill_level}</span>
                                                                                                                            )}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div className="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                                                                                                                    <button
                                                                                                                        onClick={() => {
                                                                                                                            setEditingLesson({ ...blankLesson(), ...lesson });
                                                                                                                            setPreviewUrl(lesson.thumbnail_url);
                                                                                                                            setThumbnailFile(null);
                                                                                                                            setDescriptionImageFiles([]);
                                                                                                                            setEditAudioResourceFile(null);
                                                                                                                            setEditPdfResourceFile(null);
                                                                                                                            setEditMidiResourceFile(null);
                                                                                                                            setIsEditLessonModalOpen(true);
                                                                                                                        }}
                                                                                                                        className="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md text-xs"
                                                                                                                    >
                                                                                                                        <i className="fa fa-edit"></i>
                                                                                                                    </button>
                                                                                                                    <button
                                                                                                                        onClick={() => {
                                                                                                                            setLessonToDelete(lesson);
                                                                                                                            setIsDeleteLessonModalOpen(true);
                                                                                                                        }}
                                                                                                                        className="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs"
                                                                                                                    >
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

            <Modal isOpen={newCategoryModalOpen} onClose={() => setNewCategoryModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4 capitalize">Create Category in {newCategoryLevel}</h3>
                <div className="space-y-4">
                    <input
                        type="text"
                        placeholder="Category name"
                        value={newCategoryName}
                        onChange={(e) => setNewCategoryName(e.target.value)}
                        className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg outline-none text-sm"
                    />
                    <div className="flex justify-end gap-3">
                        <button type="button" onClick={() => setNewCategoryModalOpen(false)} className="px-4 py-2 bg-gray-100 rounded-lg text-sm">Cancel</button>
                        <button type="button" onClick={handleCreateCategory} disabled={loading} className="px-4 py-2 bg-black text-white rounded-lg text-sm disabled:opacity-50">Create</button>
                    </div>
                </div>
            </Modal>

            <Modal isOpen={isCreateLessonModalOpen} onClose={() => setIsCreateLessonModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4">Add {config.itemLabel} to {selectedCategoryName}</h3>
                <form onSubmit={handleCreateLessonSubmit} className="space-y-4 max-h-[80vh] overflow-y-auto px-1">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" required value={newLesson.title} onChange={(e) => setNewLesson({ ...newLesson, title: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                            {videoTypeSelect(newLesson.video_type, (e) => setNewLesson({ ...newLesson, video_type: e.target.value }))}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video Link / ID</label>
                            <textarea required value={newLesson.video_url} onChange={(e) => setNewLesson({ ...newLesson, video_url: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" rows="2" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select value={newLesson.status} onChange={(e) => setNewLesson({ ...newLesson, status: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        {extraFieldInputs(newLesson, setNewLesson)}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                            <input type="file" accept="image/*" ref={fileInputRef} onChange={(e) => { const file = e.target.files[0]; if (file) { setThumbnailFile(file); setPreviewUrl(URL.createObjectURL(file)); } }} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                        </div>
                        {config.showImages !== false && (
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description Images</label>
                                <input type="file" multiple accept="image/*" onChange={(e) => setDescriptionImageFiles(Array.from(e.target.files))} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                        )}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Audio Track</label>
                            <input type="file" accept="audio/*" onChange={(e) => setAudioResourceFile(e.target.files[0] || null)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">PDF File</label>
                            <input type="file" accept="application/pdf" onChange={(e) => setPdfResourceFile(e.target.files[0] || null)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                        </div>
                        {config.showMidi && (
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">MIDI File</label>
                                <input type="file" accept=".mid,.midi,audio/midi,audio/x-midi" onChange={(e) => setMidiResourceFile(e.target.files[0] || null)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                        )}
                        <div className="sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea value={newLesson.description} onChange={(e) => setNewLesson({ ...newLesson, description: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" rows="3" />
                        </div>
                    </div>
                    {previewUrl && <img src={previewUrl} alt="Preview" className="w-32 h-20 object-cover rounded-lg border" />}
                    <div className="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onClick={() => setIsCreateLessonModalOpen(false)} className="px-4 py-2 bg-gray-100 rounded-lg text-sm">Cancel</button>
                        <button type="submit" disabled={loading} className="px-4 py-2 bg-black text-white rounded-lg text-sm disabled:opacity-50">Save {config.itemLabel}</button>
                    </div>
                </form>
            </Modal>

            <Modal isOpen={isEditLessonModalOpen} onClose={() => setIsEditLessonModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4">Edit {config.itemLabel}</h3>
                {editingLesson && (
                    <form onSubmit={handleEditLessonSubmit} className="space-y-4 max-h-[80vh] overflow-y-auto px-1">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div className="sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" required value={editingLesson.title} onChange={(e) => setEditingLesson({ ...editingLesson, title: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                                {videoTypeSelect(editingLesson.video_type, (e) => setEditingLesson({ ...editingLesson, video_type: e.target.value }))}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video Link / ID</label>
                                <textarea required value={editingLesson.video_url} onChange={(e) => setEditingLesson({ ...editingLesson, video_url: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" rows="2" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select value={editingLesson.status} onChange={(e) => setEditingLesson({ ...editingLesson, status: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            {extraFieldInputs(editingLesson, setEditingLesson)}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                                <input type="file" accept="image/*" onChange={(e) => { const file = e.target.files[0]; if (file) { setThumbnailFile(file); setPreviewUrl(URL.createObjectURL(file)); } }} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                            {config.showImages !== false && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Description Images</label>
                                    <input type="file" multiple accept="image/*" onChange={(e) => setDescriptionImageFiles(Array.from(e.target.files))} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                                </div>
                            )}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Audio Track</label>
                                <input type="file" accept="audio/*" onChange={(e) => setEditAudioResourceFile(e.target.files[0] || null)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">PDF File</label>
                                <input type="file" accept="application/pdf" onChange={(e) => setEditPdfResourceFile(e.target.files[0] || null)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                            {config.showMidi && (
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">MIDI File</label>
                                    {editingLesson.midi_resource_url && (
                                        <p className="text-xs text-gray-500 mb-1 truncate">Current: {editingLesson.midi_resource_url.split("/").pop()}</p>
                                    )}
                                    <input type="file" accept=".mid,.midi,audio/midi,audio/x-midi" onChange={(e) => setEditMidiResourceFile(e.target.files[0] || null)} className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                                </div>
                            )}
                            <div className="sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea value={editingLesson.description || ""} onChange={(e) => setEditingLesson({ ...editingLesson, description: e.target.value })} className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none" rows="3" />
                            </div>
                        </div>
                        {previewUrl && <img src={previewUrl} alt="Preview" className="w-32 h-20 object-cover rounded-lg border" />}
                        <div className="flex justify-end gap-3 pt-4 border-t">
                            <button type="button" onClick={() => setIsEditLessonModalOpen(false)} className="px-4 py-2 bg-gray-100 rounded-lg text-sm">Cancel</button>
                            <button type="submit" disabled={loading} className="px-4 py-2 bg-black text-white rounded-lg text-sm disabled:opacity-50">Update {config.itemLabel}</button>
                        </div>
                    </form>
                )}
            </Modal>

            <Modal isOpen={isDeleteLessonModalOpen} onClose={() => setIsDeleteLessonModalOpen(false)}>
                <div className="text-center p-3">
                    <h3 className="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h3>
                    <p className="text-gray-500 text-sm mb-6">Delete <span className="font-semibold text-red-600">"{lessonToDelete?.title}"</span>?</p>
                    <div className="flex justify-center gap-3">
                        <button type="button" onClick={() => setIsDeleteLessonModalOpen(false)} className="px-4 py-2 bg-gray-100 rounded-lg text-sm">Cancel</button>
                        <button type="button" onClick={handleDeleteLesson} disabled={loading} className="px-4 py-2 bg-red-600 text-white rounded-lg text-sm disabled:opacity-50">Yes, Delete</button>
                    </div>
                </div>
            </Modal>

            <Modal isOpen={editCategoryModalOpen} onClose={() => setEditCategoryModalOpen(false)}>
                <h3 className="text-xl font-bold text-gray-800 mb-4">Edit Category Name</h3>
                <input type="text" value={editingCategoryName} onChange={(e) => setEditingCategoryName(e.target.value)} className="w-full px-3 py-2 border rounded-md mb-4" />
                <div className="flex justify-end gap-3">
                    <button type="button" onClick={() => setEditCategoryModalOpen(false)} className="px-4 py-2 bg-gray-200 rounded-lg text-sm">Cancel</button>
                    <button type="button" onClick={handleUpdateCategory} disabled={loading} className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm disabled:opacity-50">Save Changes</button>
                </div>
            </Modal>
        </div>
    );
};

const pianoConfig = {
    title: "Piano Exercise Manager",
    levels: ["independence", "technique", "flexibility", "strength", "dexterity"],
    sectionTitle: (level) => level,
    itemLabel: "Lesson",
    defaultVideoType: "iframe",
    showImages: true,
    extraFields: [
        { name: "skill_level", label: "Skill Group", options: ["Basic", "Competent", "Challenging"], default: "Basic" },
    ],
    endpoints: {
        list: "/api/admin/piano-exercises-list",
        store: "/api/admin/piano-exercises/store",
        update: "/api/admin/piano-exercises/update",
        destroy: "/api/admin/piano-exercises",
        createCategory: "/api/admin/piano-exercises/category/create",
        updateCategory: "/api/admin/piano-exercises/category",
        deleteCategory: "/api/admin/piano-exercises/category",
        reorderCategories: "/api/admin/reorder/piano-exercises",
        reorderItems: "/api/admin/reorder/piano-exercises/items",
    },
};

const musicalConfig = {
    title: "Musical Application Manager",
    levels: ["beginner", "intermediate", "advanced"],
    sectionSuffix: "Level",
    itemLabel: "Lesson",
    defaultVideoType: "vimeo",
    showImages: false,
    showMidi: true,
    extraFields: [],
    endpoints: {
        list: "/api/admin/musical-applications-list",
        store: "/api/admin/musical-applications/store",
        update: "/api/admin/musical-applications/update",
        destroy: "/api/admin/musical-applications",
        createCategory: "/api/admin/musical-applications/category/create",
        updateCategory: "/api/admin/musical-applications/category",
        deleteCategory: "/api/admin/musical-applications/category",
        reorderCategories: "/api/admin/reorder/musical-applications",
        reorderItems: "/api/admin/reorder/musical-applications/items",
    },
};

const mountAdmin = (elementId, config) => {
    const el = document.getElementById(elementId);
    if (!el) return;
    ReactDOM.createRoot(el).render(
        <FlashMessageProvider>
            <CategorizedLessonsAdmin config={config} />
        </FlashMessageProvider>
    );
};

mountAdmin("piano-exercise-admin", pianoConfig);
mountAdmin("musical-application-admin", musicalConfig);

export default CategorizedLessonsAdmin;
