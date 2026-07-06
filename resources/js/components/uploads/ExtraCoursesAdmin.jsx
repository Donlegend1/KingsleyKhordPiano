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

const ExtraCoursesAdmin = () => {
    const [collapsedSections, setCollapsedSections] = useState({
        beginner: false,
        intermediate: false,
        advanced: false,
    });
    const [collapsedCategories, setCollapsedCategories] = useState({});
    const [coursesData, setCoursesData] = useState({
        beginner: { data: {} },
        intermediate: { data: {} },
        advanced: { data: {} },
    });
    const [allCourses, setAllCourses] = useState([]);
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
                `/api/admin/extra-courses/category/${originalCategoryName}/update`,
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
            fetchCourses();
            setEditCategoryModalOpen(false);
            showMessage("Category Updated successfully", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error updating category", "error");
        } finally {
            setLoading(false);
        }
    };

    const [isCreateCourseModalOpen, setIsCreateCourseModalOpen] = useState(false);
    const [selectedLevel, setSelectedLevel] = useState("");
    const [selectedCategoryName, setSelectedCategoryName] = useState("");
    const [newCourse, setNewCourse] = useState({
        title: "",
        description: "",
        video_type: "iframe",
        video_url: "",
        status: "active",
        related_courses: [],
    });

    const [isEditCourseModalOpen, setIsEditCourseModalOpen] = useState(false);
    const [editingCourse, setEditingCourse] = useState(null);

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

    const fetchCourses = async () => {
        setLoading(true);
        try {
            const response = await axios.get("/api/admin/extra-courses-list");
            setCoursesData(response.data);
        } catch (error) {
            console.error("Error fetching courses:", error);
            showMessage("Error fetching courses data", "error");
        } finally {
            setLoading(false);
        }
    };

    const fetchAllCoursesDropdown = async () => {
        try {
            const response = await axios.get("/api/admin/all-extra-courses");
            setAllCourses(
                response.data.map((c) => ({
                    value: c.id,
                    label: `${c.title} (${c.level})`,
                }))
            );
        } catch (error) {
            console.error("Error fetching all extra courses:", error);
        }
    };

    useEffect(() => {
        fetchCourses();
        fetchAllCoursesDropdown();
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
            await axios.post("/api/admin/extra-courses/category/create", {
                category: newCategoryName,
                level: newCategoryLevel,
            }, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            });
            showMessage("Category Created successfully", "success");
            setNewCategoryModalOpen(false);
            fetchCourses();
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
            await axios.delete(`/api/admin/extra-courses/category/${categoryName}/delete`, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            });
            showMessage("Category Deleted successfully", "success");
            fetchCourses();
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
        const currentData = coursesData[level]?.data || {};
        const items = Object.entries(currentData);
        const [reorderedItem] = items.splice(result.source.index, 1);
        items.splice(result.destination.index, 0, reorderedItem);

        const updatedData = {};
        items.forEach(([cat, list]) => {
            updatedData[cat] = list;
        });

        setCoursesData((prev) => ({
            ...prev,
            [level]: { ...prev[level], data: updatedData }
        }));

        try {
            await axios.post("/api/admin/reorder/extra-courses", {
                level,
                categories: items.map(([category]) => category),
            }, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            });
        } catch (error) {
            console.error("Failed to persist category order:", error);
            showMessage("Failed to save category order", "error");
        }
    };

    // Course Handlers
    const openCreateCourseModal = (level, category) => {
        setSelectedLevel(level);
        setSelectedCategoryName(category);
        setNewCourse({
            title: "",
            description: "",
            video_type: "iframe",
            video_url: "",
            status: "active",
            related_courses: [],
        });
        setThumbnailFile(null);
        setPreviewUrl(null);
        setDescriptionImageFiles([]);
        setIsCreateCourseModalOpen(true);
    };

    const handleCreateCourseSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData();
        formData.append("title", newCourse.title);
        formData.append("description", newCourse.description || "");
        formData.append("category", selectedCategoryName);
        formData.append("level", selectedLevel);
        formData.append("video_type", newCourse.video_type);
        formData.append("video_url", newCourse.video_url);
        formData.append("status", newCourse.status);
        if (thumbnailFile) {
            formData.append("thumbnail", thumbnailFile);
        }
        descriptionImageFiles.forEach((file, idx) => {
            formData.append(`images[${idx}]`, file);
        });
        newCourse.related_courses.forEach((id, idx) => {
            formData.append(`related_courses[${idx}]`, id);
        });
        if (audioResourceFile) {
            formData.append("audio_resource", audioResourceFile);
        }
        if (pdfResourceFile) {
            formData.append("pdf_resource", pdfResourceFile);
        }

        try {
            await axios.post("/api/admin/extra-courses/store", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": csrfToken
                }
            });
            showMessage("Course added successfully", "success");
            setIsCreateCourseModalOpen(false);
            setAudioResourceFile(null);
            setPdfResourceFile(null);
            fetchCourses();
            fetchAllCoursesDropdown();
        } catch (error) {
            console.error("Error creating course:", error);
            showMessage("Error adding course", "error");
        } finally {
            setLoading(false);
        }
    };

    const openEditCourseModal = (course) => {
        setEditingCourse({
            ...course,
            related_courses: course.related_courses || []
        });
        setPreviewUrl(course.thumbnail_url);
        setThumbnailFile(null);
        setDescriptionImageFiles([]);
        setEditAudioResourceFile(null);
        setEditPdfResourceFile(null);
        setIsEditCourseModalOpen(true);
    };

    const handleEditCourseSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData();
        formData.append("title", editingCourse.title);
        formData.append("description", editingCourse.description || "");
        formData.append("video_type", editingCourse.video_type);
        formData.append("video_url", editingCourse.video_url);
        formData.append("status", editingCourse.status);
        if (thumbnailFile) {
            formData.append("thumbnail", thumbnailFile);
        }
        descriptionImageFiles.forEach((file, idx) => {
            formData.append(`images[${idx}]`, file);
        });
        editingCourse.related_courses.forEach((id, idx) => {
            formData.append(`related_courses[${idx}]`, id);
        });
        if (editAudioResourceFile) {
            formData.append("audio_resource", editAudioResourceFile);
        }
        if (editPdfResourceFile) {
            formData.append("pdf_resource", editPdfResourceFile);
        }

        try {
            await axios.post(`/api/admin/extra-courses/update/${editingCourse.id}`, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": csrfToken
                }
            });
            showMessage("Course updated successfully", "success");
            setIsEditCourseModalOpen(false);
            fetchCourses();
            fetchAllCoursesDropdown();
        } catch (error) {
            console.error("Error updating course:", error);
            showMessage("Error updating course", "error");
        } finally {
            setLoading(false);
        }
    };

    const openDeleteCourseModal = (course) => {
        setCourseToDelete(course);
        setIsDeleteCourseModalOpen(true);
    };

    const handleDeleteCourse = async () => {
        setLoading(true);
        try {
            await axios.delete(`/api/admin/extra-courses/${courseToDelete.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            });
            showMessage("Course deleted successfully", "success");
            setIsDeleteCourseModalOpen(false);
            fetchCourses();
            fetchAllCoursesDropdown();
        } catch (error) {
            console.error("Error deleting course:", error);
            showMessage("Error deleting course", "error");
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
                <h2 className="text-2xl font-bold text-gray-800">Extra Courses Manager</h2>
            </div>

            {loading && !newCategoryModalOpen && !isCreateCourseModalOpen && !isEditCourseModalOpen && !isDeleteCourseModalOpen ? (
                <div className="flex justify-center items-center h-64">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
                </div>
            ) : (
                <div className="space-y-8">
                    {["beginner", "intermediate", "advanced"].map((level) => {
                        const levelData = coursesData[level]?.data || {};
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
                                                        {orderedCategories.map(([categoryName, courses], index) => {
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
                                                                                            openCreateCourseModal(level, categoryName);
                                                                                        }}
                                                                                        className="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full hover:bg-blue-700 transition"
                                                                                    >
                                                                                        Add Course
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

                                                                            {/* Category Courses List */}
                                                                            <div className={`mt-4 overflow-hidden transition-all duration-300 ${isCollapsed ? "max-h-0 opacity-0" : "max-h-[3000px] opacity-100"}`}>
                                                                                {courses.length === 0 ? (
                                                                                    <p className="text-gray-500 text-xs text-center py-4">No courses in this category yet.</p>
                                                                                ) : (
                                                                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                                                                        {courses.map((course) => (
                                                                                            <div key={course.id} className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                                                                                                <div>
                                                                                                    <div className="h-40 bg-gray-100 relative">
                                                                                                        {course.thumbnail_url ? (
                                                                                                            <img src={course.thumbnail_url} alt={course.title} className="w-full h-full object-cover" />
                                                                                                        ) : (
                                                                                                            <div className="w-full h-full flex items-center justify-center text-gray-400">
                                                                                                                <i className="fa fa-image text-3xl"></i>
                                                                                                            </div>
                                                                                                        )}
                                                                                                        <span className={`absolute top-2 right-2 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full ${course.status === "active" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"}`}>
                                                                                                            {course.status}
                                                                                                        </span>
                                                                                                    </div>
                                                                                                    <div className="p-4">
                                                                                                        <h4 className="font-bold text-gray-800 truncate mb-1">{course.title}</h4>
                                                                                                        <p className="text-gray-500 text-xs line-clamp-2 h-8 leading-relaxed mb-3">{course.description || "No description provided."}</p>
                                                                                                        <span className="px-2 py-0.5 bg-gray-100 text-[10px] text-gray-600 rounded-md capitalize font-semibold">{course.video_type}</span>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div className="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                                                                                                    <button onClick={() => openEditCourseModal(course)} className="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md text-xs">
                                                                                                        <i className="fa fa-edit"></i>
                                                                                                    </button>
                                                                                                    <button onClick={() => openDeleteCourseModal(course)} className="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs">
                                                                                                        <i className="fa fa-trash"></i>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        ))}
                                                                                    </div>
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
                            placeholder="e.g. Gospel Essentials"
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

            {/* Create Course Modal */}
            <Modal isOpen={isCreateCourseModalOpen} onClose={() => setIsCreateCourseModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4 capitalize">Add Course to {selectedCategoryName}</h3>
                <form onSubmit={handleCreateCourseSubmit} className="space-y-4 max-h-[80vh] overflow-y-auto px-1">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Course Title</label>
                            <input
                                type="text"
                                required
                                value={newCourse.title}
                                onChange={(e) => setNewCourse({ ...newCourse, title: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                            <select
                                value={newCourse.video_type}
                                onChange={(e) => setNewCourse({ ...newCourse, video_type: e.target.value })}
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
                                value={newCourse.video_url}
                                onChange={(e) => setNewCourse({ ...newCourse, video_url: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500"
                                rows="2"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                value={newCourse.status}
                                onChange={(e) => setNewCourse({ ...newCourse, status: e.target.value })}
                                className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Related Courses</label>
                            <Select
                                isMulti
                                options={allCourses}
                                onChange={(opts) => setNewCourse({ ...newCourse, related_courses: opts ? opts.map(o => o.value) : [] })}
                                className="basic-multi-select"
                                classNamePrefix="select"
                                placeholder="Select related courses..."
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
                                value={newCourse.description}
                                onChange={(e) => setNewCourse({ ...newCourse, description: e.target.value })}
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
                        <button type="button" onClick={() => setIsCreateCourseModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
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
                                "Save Course"
                            )}
                        </button>
                    </div>
                </form>
            </Modal>

            {/* Edit Course Modal */}
            <Modal isOpen={isEditCourseModalOpen} onClose={() => setIsEditCourseModalOpen(false)}>
                <h3 className="text-lg font-bold text-gray-800 mb-4">Edit Course Details</h3>
                {editingCourse && (
                    <form onSubmit={handleEditCourseSubmit} className="space-y-4 max-h-[80vh] overflow-y-auto px-1">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div className="col-span-1 sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Course Title</label>
                                <input
                                    type="text"
                                    required
                                    value={editingCourse.title}
                                    onChange={(e) => setEditingCourse({ ...editingCourse, title: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                                <select
                                    value={editingCourse.video_type}
                                    onChange={(e) => setEditingCourse({ ...editingCourse, video_type: e.target.value })}
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
                                    value={editingCourse.video_url}
                                    onChange={(e) => setEditingCourse({ ...editingCourse, video_url: e.target.value })}
                                    className="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm outline-none"
                                    rows="2"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select
                                    value={editingCourse.status}
                                    onChange={(e) => setEditingCourse({ ...editingCourse, status: e.target.value })}
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
                                {descriptionImageFiles.length > 0 && (
                                    <div className="text-xs text-gray-500 mt-1">
                                        {descriptionImageFiles.length} file(s) selected
                                    </div>
                                )}
                                {editingCourse.image_urls && editingCourse.image_urls.length > 0 && (
                                    <div className="mt-2 flex gap-2 flex-wrap">
                                        {editingCourse.image_urls.map((url, idx) => (
                                            <div key={idx} className="w-16 h-12 border rounded overflow-hidden">
                                                <img src={url} className="w-full h-full object-cover" />
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                            <div className="col-span-1 sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Related Courses</label>
                                <Select
                                    isMulti
                                    options={allCourses}
                                    onChange={(opts) => setEditingCourse({ ...editingCourse, related_courses: opts ? opts.map(o => o.value) : [] })}
                                    value={allCourses.filter(opt => editingCourse.related_courses.includes(opt.value))}
                                    className="basic-multi-select"
                                    classNamePrefix="select"
                                    placeholder="Select related courses..."
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Audio Track (Optional)</label>
                                <input
                                    type="file"
                                    accept="audio/*"
                                    onChange={(e) => setEditAudioResourceFile(e.target.files[0] || null)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                                {editingCourse.audio_resource_url && (
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
                                {editingCourse.pdf_resource_url && (
                                    <div className="text-xs text-gray-500 mt-1">PDF already uploaded.</div>
                                )}
                            </div>
                            <div className="col-span-1 sm:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea
                                    value={editingCourse.description || ""}
                                    onChange={(e) => setEditingCourse({ ...editingCourse, description: e.target.value })}
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
                            <button type="button" onClick={() => setIsEditCourseModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
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
                                    "Update Course"
                                )}
                            </button>
                        </div>
                    </form>
                )}
            </Modal>

            {/* Delete Course Modal */}
            <Modal isOpen={isDeleteCourseModalOpen} onClose={() => setIsDeleteCourseModalOpen(false)}>
                <div className="text-center p-3">
                    <h3 className="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h3>
                    <p className="text-gray-500 text-sm mb-6">Are you sure you want to delete course <span className="font-semibold text-red-600">"{courseToDelete?.title}"</span>? This action is permanent.</p>
                    <div className="flex justify-center gap-3">
                        <button onClick={() => setIsDeleteCourseModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">Cancel</button>
                        <button
                            onClick={handleDeleteCourse}
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

export default ExtraCoursesAdmin;

if (document.getElementById("extra-courses-admin")) {
    const root = ReactDOM.createRoot(document.getElementById("extra-courses-admin"));
    root.render(
        <FlashMessageProvider>
            <ExtraCoursesAdmin />
        </FlashMessageProvider>
    );
}
