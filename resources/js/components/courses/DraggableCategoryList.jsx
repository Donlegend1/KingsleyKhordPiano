import React, { useState, useEffect } from "react";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import Select from "react-select";
import CustomPagination from "../Pagination/CustomPagination";
import Modal from "../Modal/Modal";
import RichTextEditor from "../common/RichTextEditor";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const DraggableCategoryList = ({
    level,
    courses,
    handlePageChange,
    perPage,
    loading,
    setLoading,
    fetchCourses,
}) => {
    const [collapsedCategories, setCollapsedCategories] = useState({});
    const [orderedCategories, setOrderedCategories] = useState(
        Object.entries(courses[level].data)
    );
    const { showMessage } = useFlashMessage();
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [isNewCourseModalOpen, setIsNewCourseModalOpen] = useState(false);
    const [isNewCheckpointModalOpen, setIsNewCheckpointModalOpen] = useState(false);
    const [newCheckpoint, setNewCheckpoint] = useState({
        course_category_id: null,
        title: "",
        video_url: "",
        overview: "",
        redirect_url: "",
        downloads: [],
    });

    // Edit Checkpoint states
    const [isEditCheckpointModalOpen, setIsEditCheckpointModalOpen] = useState(false);
    const [editingCheckpoint, setEditingCheckpoint] = useState(null);
    const [newDownloadTitle, setNewDownloadTitle] = useState("");
    const [newDownloadFile, setNewDownloadFile] = useState(null);
    const [uploadingDownload, setUploadingDownload] = useState(false);

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
                `/api/admin/course/category/${originalCategoryName}/update`,
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
            showMessage("Course Category Updated", "success");
        } catch (error) {
            showMessage(error.response?.data?.message || "Error updating category", "error");
        } finally {
            setLoading(false);
        }
    };
    const [selectedCourse, setSelectedCourse] = useState({
        title: "",
        category: "",
        description: "",
        video_url: "",
        level: "beginner",
        status: "active",
        published_at: "",
        thumbnail: null,
        created_at: "",
    });
    const [course, setCourse] = useState({
        title: "",
        category: "",
        description: "",
        video_url: "",
        video_type: "youtube",
        level: "beginner",
        status: "active",
        related_courses: [],
        related_lessons: [],
    });

    const [allCourses, setAllCourses] = useState([]);
    const [descriptionImageFiles, setDescriptionImageFiles] = useState([]);

    const handleChangeNewCourse = (e) => {
        const { name, value } = e.target;
        setCourse({ ...course, [name]: value });
    };

    const handleRelatedCoursesChange = (selectedOptions, isEdit = false) => {
        const ids = selectedOptions ? selectedOptions.map((opt) => opt.value) : [];
        if (isEdit) {
            setSelectedCourse({ ...selectedCourse, related_courses: ids });
        } else {
            setCourse({ ...course, related_courses: ids });
        }
    };

    const [selectedCourseLevel, setSelectedCourseLevel] = useState();
    const [selectedCourseCategory, setSelectedCourseCategory] = useState("");
    useEffect(() => {
        setOrderedCategories(Object.entries(courses[level].data));
    }, [courses[level].data]);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    useEffect(() => {
        const fetchAllCourses = async () => {
            try {
                const response = await axios.get("/api/admin/all-courses");
                setAllCourses(
                    response.data.map((c) => ({
                        value: c.id,
                        label: `${c.title} (${c.level})`,
                    }))
                );
            } catch (error) {
                console.error("Error fetching courses:", error);
            }
        };
        fetchAllCourses();
    }, []);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setSelectedCourse({ ...selectedCourse, [name]: value });
    };

    const toggleCategory = (key) => {
        setCollapsedCategories((prev) => ({
            ...prev,
            [key]: !prev[key],
        }));
    };

    const openEditModal = (course) => {
        setSelectedCourse(course);
        setDescriptionImageFiles([]);
        setIsEditModalOpen(true);
    };

    const openDeleteModal = (course) => {
        setSelectedCourse(course);
        setIsDeleteModalOpen(true);
    };

    const closeEditModal = () => {
        setIsEditModalOpen(false);
        setSelectedCourse(null);
    };

    const closeDeleteModal = () => {
        setIsDeleteModalOpen(false);
        setSelectedCourse(null);
    };

    const closeNewCourseModal = () => {
        setIsNewCourseModalOpen(false);
        setSelectedCourseLevel(null);
        setSelectedCourseCategory("");
    };

    const openNewCourseModal = (e, level, category) => {
        setSelectedCourseLevel(level);
        setSelectedCourseCategory(category);
        setDescriptionImageFiles([]);
        setIsNewCourseModalOpen(true);
    };

    const openNewCheckpointModal = (level, category) => {
        const categoryId = courses[level]?.category_ids?.[category];
        setNewCheckpoint({
            course_category_id: categoryId,
            title: "",
            video_url: "",
            overview: "",
            redirect_url: "",
            downloads: [],
        });
        setIsNewCheckpointModalOpen(true);
    };

    const closeNewCheckpointModal = () => {
        setIsNewCheckpointModalOpen(false);
    };

    const handleCreateCheckpoint = async () => {
        if (!newCheckpoint.course_category_id || !newCheckpoint.title.trim()) return;
        setLoading(true);
        try {
            const formData = new FormData();
            formData.append("course_category_id", newCheckpoint.course_category_id);
            formData.append("title", newCheckpoint.title || "");
            formData.append("video_url", newCheckpoint.video_url || "");
            formData.append("overview", newCheckpoint.overview || "");
            formData.append("redirect_url", newCheckpoint.redirect_url || "");
            newCheckpoint.downloads.forEach((download, index) => {
                if (download.title && download.file) {
                    formData.append(`downloads[${index}][title]`, download.title);
                    formData.append(`downloads[${index}][file]`, download.file);
                }
            });

            await axios.post("/api/admin/checkpoints/store", formData, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "multipart/form-data",
                },
                withCredentials: true,
            });
            showMessage("Checkpoint Added", "success");
            closeNewCheckpointModal();
            fetchCourses();
        } catch (error) {
            showMessage(
                error.response?.data?.message || "Error adding checkpoint",
                "error"
            );
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCheckpoint = async (checkpoint) => {
        if (!confirm(`Remove checkpoint "${checkpoint.title}"?`)) return;
        setLoading(true);
        try {
            await axios.delete(`/api/admin/checkpoints/${checkpoint.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });
            showMessage("Checkpoint Deleted", "success");
            fetchCourses();
        } catch (error) {
            showMessage("Error deleting checkpoint", "error");
        } finally {
            setLoading(false);
        }
    };

    const openEditCheckpointModal = (checkpoint) => {
        setEditingCheckpoint({
            ...checkpoint,
            downloads: checkpoint.downloads || [],
        });
        setNewDownloadTitle("");
        setNewDownloadFile(null);
        setIsEditCheckpointModalOpen(true);
    };

    const closeEditCheckpointModal = () => {
        setIsEditCheckpointModalOpen(false);
        setEditingCheckpoint(null);
    };

    const handleUpdateCheckpoint = async () => {
        if (!editingCheckpoint) return;
        setLoading(true);
        try {
            const response = await axios.post(
                `/api/admin/checkpoints/${editingCheckpoint.id}/update`,
                {
                    title: editingCheckpoint.title || "",
                    description: editingCheckpoint.description || "",
                    video_url: editingCheckpoint.video_url || "",
                    overview: editingCheckpoint.overview || "",
                },
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );
            showMessage("Checkpoint Updated", "success");
            setEditingCheckpoint(response.data);
            fetchCourses();
        } catch (error) {
            showMessage("Error updating checkpoint", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleUploadCheckpointDownload = async () => {
        if (!editingCheckpoint || !newDownloadTitle.trim() || !newDownloadFile) return;
        setUploadingDownload(true);
        try {
            const formData = new FormData();
            formData.append("title", newDownloadTitle);
            formData.append("file", newDownloadFile);

            const response = await axios.post(
                `/api/admin/checkpoints/${editingCheckpoint.id}/downloads`,
                formData,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "multipart/form-data",
                    },
                    withCredentials: true,
                }
            );
            setEditingCheckpoint({
                ...editingCheckpoint,
                downloads: [...editingCheckpoint.downloads, response.data],
            });
            setNewDownloadTitle("");
            setNewDownloadFile(null);
            showMessage("File Uploaded", "success");
        } catch (error) {
            showMessage("Error uploading file", "error");
        } finally {
            setUploadingDownload(false);
        }
    };

    const handleDeleteCheckpointDownload = async (download) => {
        if (!confirm(`Remove "${download.title}"?`)) return;
        try {
            await axios.delete(`/api/admin/checkpoint-downloads/${download.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });
            setEditingCheckpoint({
                ...editingCheckpoint,
                downloads: editingCheckpoint.downloads.filter((d) => d.id !== download.id),
            });
            showMessage("File Removed", "success");
        } catch (error) {
            showMessage("Error removing file", "error");
        }
    };

    const handleDeleteCourse = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.delete(
                `/api/admin/courses/${selectedCourse.id}`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            closeDeleteModal();
            showMessage("course deleted", "success");
            fetchCourses();
        } catch (error) {
            showMessage("Error deleting course", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            const formData = new FormData();
            formData.append("_method", "PATCH");
            formData.append("title", selectedCourse.title || "");
            formData.append("category", selectedCourse.category || "");
            formData.append("description", selectedCourse.description || "");
            formData.append("video_url", selectedCourse.video_url || "");
            formData.append("video_type", selectedCourse.video_type || "");
            formData.append("status", selectedCourse.status || "");
            formData.append("level", selectedCourse.level || "");
            if (selectedCourse.related_courses) {
                selectedCourse.related_courses.forEach((id) => {
                    formData.append("related_courses[]", id);
                });
            }
            if (selectedCourse.related_lessons) {
                selectedCourse.related_lessons.forEach((rl, idx) => {
                    formData.append(`related_lessons[${idx}][title]`, rl.title);
                    formData.append(`related_lessons[${idx}][url]`, rl.url);
                });
            }
            if (selectedCourse.thumbnail_file) {
                formData.append("thumbnail", selectedCourse.thumbnail_file);
            }
            if (selectedCourse.pdf_resource_file) {
                formData.append("pdf_resource", selectedCourse.pdf_resource_file);
            }
            descriptionImageFiles.forEach((file, idx) => {
                formData.append(`images[${idx}]`, file);
            });

            const response = await axios.post(
                `/api/admin/courses/${selectedCourse.id}`,
                formData,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "multipart/form-data",
                    },
                    withCredentials: true,
                }
            );
            showMessage("course updated", "success");
            closeEditModal();
            fetchCourses();
        } catch (error) {
            console.error("Error updating course:", error);
            showMessage("Error updating course", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleOnDragEnd = async (result) => {
        if (!result.destination) return;

        if (result.source.droppableId === `droppable-${level}`) {
            const items = Array.from(orderedCategories);
            const [reorderedItem] = items.splice(result.source.index, 1);
            items.splice(result.destination.index, 0, reorderedItem);

            setOrderedCategories(items);

            try {
                await axios.post("/api/admin/reorder/courses", {
                    level,
                    categories: items.map(([category]) => category),
                });
            } catch (error) {
                console.error("Failed to persist category order:", error);
            }
        } else if (result.source.droppableId.startsWith("courses-")) {
            const categoryName = result.source.droppableId.replace("courses-", "");
            
            const updatedCategories = orderedCategories.map(([categoryNameKey, categoryCourses]) => {
                if (categoryNameKey === categoryName) {
                    const coursesList = Array.from(categoryCourses);
                    const [reorderedItem] = coursesList.splice(result.source.index, 1);
                    coursesList.splice(result.destination.index, 0, reorderedItem);
                    return [categoryNameKey, coursesList];
                }
                return [categoryNameKey, categoryCourses];
            });

            setOrderedCategories(updatedCategories);

            const targetCategory = updatedCategories.find(([cat]) => cat === categoryName);
            if (targetCategory) {
                const items = targetCategory[1].map((c) => ({
                    type: c.item_type === "checkpoint" ? "checkpoint" : "course",
                    id: c.id,
                }));
                try {
                    await axios.post("/api/admin/reorder/courses/items", {
                        items,
                    });
                } catch (error) {
                    console.error("Failed to persist course order:", error);
                }
            }
        }
    };

    const handleCreateNewCourse = async () => {
        setLoading(true);
        try {
            const formData = new FormData();
            formData.append("title", course.title || "");
            formData.append("video_type", course.video_type || "youtube");
            formData.append("video_url", course.video_url || "");
            formData.append("status", course.status || "active");
            formData.append("description", course.description || "");
            formData.append("category", selectedCourseCategory || "");
            formData.append("level", selectedCourseLevel || "");
            if (course.related_courses) {
                course.related_courses.forEach((id) => {
                    formData.append("related_courses[]", id);
                });
            }
            if (course.related_lessons) {
                course.related_lessons.forEach((rl, idx) => {
                    formData.append(`related_lessons[${idx}][title]`, rl.title);
                    formData.append(`related_lessons[${idx}][url]`, rl.url);
                });
            }
            if (course.thumbnail_file) {
                formData.append("thumbnail", course.thumbnail_file);
            }
            if (course.pdf_resource_file) {
                formData.append("pdf_resource", course.pdf_resource_file);
            }
            descriptionImageFiles.forEach((file, idx) => {
                formData.append(`images[${idx}]`, file);
            });

            const response = await axios.post(
                `/api/admin/course/store`,
                formData,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "multipart/form-data",
                    },
                    withCredentials: true,
                }
            );
            showMessage("Course Created", "success");
            closeNewCourseModal();
            setCourse({
                title: "",
                category: "",
                description: "",
                video_url: "",
                video_type: "youtube",
                level: "beginner",
                status: "active",
                related_courses: [],
                related_lessons: [],
                thumbnail_file: null,
                pdf_resource_file: null,
            });
            setDescriptionImageFiles([]);
            fetchCourses();
        } catch (error) {
            console.error("Error creating new course:", error);
            showMessage("Error creating course", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCategory = async (category) => {
        if (!confirm(`Are you sure you want to delete category "${category}"?`)) return;
        setLoading(true);
        try {
            const response = await axios.delete(
                `/api/admin/course/category/${category}/delete`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            fetchCourses();
            showMessage("Course Category Deleted", "success");
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        } finally {
            setLoading(false);
        }
    };
    return (
        <div className="mt-6">
            <DragDropContext onDragEnd={handleOnDragEnd}>
                <Droppable droppableId={`droppable-${level}`}>
                    {(provided) => (
                        <div
                            ref={provided.innerRef}
                            {...provided.droppableProps}
                        >
                            {orderedCategories.map(
                                ([category, categoryCourses], index) => {
                                    const isCollapsed =
                                        collapsedCategories[
                                            `${level}-${category}`
                                        ];
                                    return (
                                        <Draggable
                                            key={category}
                                            draggableId={`${level}-${String(
                                                category
                                            )}`}
                                            index={index}
                                        >
                                            {(provided, snapshot) => (
                                                <div
                                                    ref={provided.innerRef}
                                                    {...provided.draggableProps}
                                                    className={`mb-6 p-3 rounded-md border transition-all duration-300 ${
                                                        snapshot.isDragging
                                                            ? "bg-blue-50 border-blue-300 shadow-lg scale-[1.02]"
                                                            : "bg-gray-50 dark:bg-gray-800 border-gray-200"
                                                    }`}
                                                >
                                                    {/* Drag handle only on header */}
                                                    <h4
                                                        {...provided.dragHandleProps}
                                                        onClick={() =>
                                                            toggleCategory(
                                                                `${level}-${category}`
                                                            )
                                                        }
                                                        className="text-lg font-medium mb-3 px-2 py-1 bg-blue-50 dark:bg-gray-700 rounded cursor-pointer flex justify-between items-center hover:bg-blue-100 dark:hover:bg-gray-600 select-none"
                                                    >
                                                        <span>{category}</span>

                                                        <div className="flex items-center gap-4">
                                                            {/* Edit Icon */}
                                                            <i
                                                                className="fa fa-pencil text-blue-600 hover:text-blue-800 text-sm cursor-pointer"
                                                                onClick={(
                                                                    e
                                                                ) => {
                                                                    e.stopPropagation(); // prevent toggle
                                                                    openEditCategoryModal(
                                                                        category
                                                                    );
                                                                }}
                                                            ></i>

                                                            {/* Delete Icon */}
                                                            <i
                                                                className="fa fa-trash text-red-600 hover:text-red-800 text-sm cursor-pointer"
                                                                onClick={(
                                                                    e
                                                                ) => {
                                                                    e.stopPropagation(); // prevent toggle
                                                                    handleDeleteCategory(
                                                                        category
                                                                    );
                                                                }}
                                                            ></i>

                                                            {/* Chevron */}
                                                            <i
                                                                className={`fa ${
                                                                    isCollapsed
                                                                        ? "fa-chevron-down"
                                                                        : "fa-chevron-up"
                                                                } text-sm`}
                                                            ></i>
                                                        </div>
                                                    </h4>

                                                    {/* Category Content (not hidden, just collapsed visually) */}
                                                    <div
                                                        className={`overflow-hidden transition-all duration-500 ease-in-out ${
                                                            isCollapsed
                                                                ? "max-h-0 opacity-0 scale-y-0"
                                                                : "max-h-[2000px] opacity-100 scale-y-100"
                                                        }`}
                                                    >
                                                        <div className="mb-4 flex justify-end gap-2">
                                                            <button
                                                                className="rounded-md bg-indigo-600 p-2 text-white"
                                                                onClick={() =>
                                                                    openNewCheckpointModal(
                                                                        level,
                                                                        category
                                                                    )
                                                                }
                                                            >
                                                                Add Checkpoint
                                                            </button>
                                                            <button
                                                                className="rounded-md bg-black p-2 text-white"
                                                                onClick={(e) =>
                                                                    openNewCourseModal(
                                                                        e,
                                                                        level,
                                                                        category
                                                                    )
                                                                }
                                                            >
                                                                Add Course
                                                            </button>
                                                        </div>
                                                        <Droppable droppableId={`courses-${category}`} type="course">
                                                            {(provided) => (
                                                                <div
                                                                    ref={provided.innerRef}
                                                                    {...provided.droppableProps}
                                                                    className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                                                                >
                                                                    {categoryCourses.map(
                                                                        (course, courseIndex) => (
                                                                            <Draggable
                                                                                key={`${course.item_type || "course"}-${course.id}`}
                                                                                draggableId={`${course.item_type || "course"}-${course.id}`}
                                                                                index={courseIndex}
                                                                            >
                                                                                {(provided, snapshot) =>
                                                                                course.item_type === "checkpoint" ? (
                                                                                    <div
                                                                                        ref={provided.innerRef}
                                                                                        {...provided.draggableProps}
                                                                                        {...provided.dragHandleProps}
                                                                                        className={`bg-indigo-50 dark:bg-gray-800 border border-indigo-100 dark:border-gray-700 p-4 rounded-lg shadow hover:shadow-md transition-all flex flex-col ${
                                                                                            snapshot.isDragging
                                                                                                ? "ring-2 ring-indigo-400 scale-[1.01]"
                                                                                                : ""
                                                                                        }`}
                                                                                    >
                                                                                        <div className="flex items-start justify-between mb-3">
                                                                                            <span className="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex-shrink-0">
                                                                                                <i className="fa fa-bullseye text-indigo-600"></i>
                                                                                            </span>
                                                                                            <span className="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                                                                                Checkpoint
                                                                                            </span>
                                                                                        </div>
                                                                                        <h3 className="font-semibold text-lg mb-1 truncate">
                                                                                            {course.title}
                                                                                        </h3>
                                                                                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-3 truncate">
                                                                                            {course.description}
                                                                                        </p>
                                                                                        {course.linked_course && (
                                                                                            <p className="text-xs text-gray-500 dark:text-gray-500 mb-3 truncate">
                                                                                                Links to: {course.linked_course.title}
                                                                                            </p>
                                                                                        )}
                                                                                        <div className="flex items-center justify-end gap-2 mt-auto">
                                                                                            <button
                                                                                                onClick={() =>
                                                                                                    openEditCheckpointModal(course)
                                                                                                }
                                                                                                className="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100"
                                                                                            >
                                                                                                <i className="fa fa-edit"></i>
                                                                                            </button>
                                                                                            <button
                                                                                                onClick={() =>
                                                                                                    handleDeleteCheckpoint(course)
                                                                                                }
                                                                                                className="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                                                                            >
                                                                                                <i className="fa fa-trash"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                ) : (
                                                                                    <div
                                                                                        ref={provided.innerRef}
                                                                                        {...provided.draggableProps}
                                                                                        {...provided.dragHandleProps}
                                                                                        className={`bg-white dark:bg-gray-900 p-4 rounded-lg shadow hover:shadow-md transition-all ${
                                                                                            snapshot.isDragging
                                                                                                ? "ring-2 ring-blue-400 scale-[1.01]"
                                                                                                : ""
                                                                                        }`}
                                                                                    >
                                                                                        <div className="relative h-48 rounded-t-lg overflow-hidden">
                                                                                            {course.thumbnail ? (
                                                                                                <img
                                                                                                    src={
                                                                                                        course.thumbnail
                                                                                                    }
                                                                                                    alt={
                                                                                                        course.title
                                                                                                    }
                                                                                                    className="w-full h-full object-cover"
                                                                                                />
                                                                                            ) : (
                                                                                                <div className="w-full h-full bg-gray-200 flex items-center justify-center">
                                                                                                    <i className="fa fa-image text-4xl text-gray-400"></i>
                                                                                                </div>
                                                                                            )}
                                                                                            <div className="absolute top-2 right-2">
                                                                                                <span
                                                                                                    className={`px-2 py-1 text-xs rounded-full ${
                                                                                                        course.status ===
                                                                                                        "active"
                                                                                                            ? "bg-green-100 text-green-800"
                                                                                                            : "bg-yellow-100 text-yellow-800"
                                                                                                    }`}
                                                                                                >
                                                                                                    {
                                                                                                        course.status
                                                                                                    }
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div className="p-4">
                                                                                            <h3 className="font-semibold text-lg mb-2 truncate">
                                                                                                {
                                                                                                    course.title
                                                                                                }
                                                                                            </h3>
                                                                                            <div className="flex items-center gap-2 text-sm text-gray-600 mb-3">
                                                                                                <span className="px-2 py-1 bg-blue-50 rounded-md">
                                                                                                    {
                                                                                                        course.category
                                                                                                    }
                                                                                                </span>
                                                                                                <span className="px-2 py-1 bg-purple-50 rounded-md">
                                                                                                    {
                                                                                                        course.level
                                                                                                    }
                                                                                                </span>
                                                                                            </div>
                                                                                            <div className="flex items-center justify-end gap-2 mt-4">
                                                                                                <div className="flex gap-2">
                                                                                                    <button
                                                                                                        onClick={() =>
                                                                                                            openEditModal(
                                                                                                                course
                                                                                                            )
                                                                                                        }
                                                                                                        className="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                                                                                    >
                                                                                                        <i className="fa fa-edit"></i>
                                                                                                    </button>
                                                                                                    <button
                                                                                                        onClick={() =>
                                                                                                            openDeleteModal(
                                                                                                                course
                                                                                                            )
                                                                                                        }
                                                                                                        className="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                                                                                    >
                                                                                                        <i className="fa fa-trash"></i>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                )
                                                                                }
                                                                            </Draggable>
                                                                        )
                                                                    )}
                                                                    {provided.placeholder}
                                                                </div>
                                                            )}
                                                        </Droppable>

                                                        {/* Pagination */}
                                                        {perPage !== "all" && (
                                                            <div className="mt-4">
                                                                <CustomPagination
                                                                    currentPage={
                                                                        courses[
                                                                            level
                                                                        ]
                                                                            .current_page
                                                                    }
                                                                    totalPages={
                                                                        courses[
                                                                            level
                                                                        ]
                                                                            .last_page
                                                                    }
                                                                    onPageChange={(
                                                                        page
                                                                    ) =>
                                                                        handlePageChange(
                                                                            level,
                                                                            page
                                                                        )
                                                                    }
                                                                />
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            )}
                                        </Draggable>
                                    );
                                }
                            )}
                            {provided.placeholder}
                        </div>
                    )}
                </Droppable>
            </DragDropContext>
            <Modal
                isOpen={isEditModalOpen}
                onClose={() => setIsEditModalOpen(false)}
            >
                <div className="space-y-6">
                    <h2 className="text-lg font-bold mb-2">Edit Course</h2>
                    <p>Editing Course: {selectedCourse?.title}</p>
                </div>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input
                            name="title"
                            placeholder="Title"
                            defaultValue={selectedCourse?.title}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            name="category"
                            placeholder="Category"
                            disabled
                            defaultValue={selectedCourse?.category}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <div>
                            <label
                                htmlFor="video_type"
                                className="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Video Type
                            </label>

                            <select
                                type="text"
                                name="video_type"
                                value={selectedCourse?.video_type}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                            >
                                <option value="">Select Video Type</option>
                                <option value="youtube">YouTube</option>
                                <option value="google">Google</option>
                                <option value="local">Local</option>
                                <option value="iframe">Iframe</option>
                            </select>
                        </div>
                        <textarea
                            name="video_url"
                            placeholder="Video URL"
                            defaultValue={selectedCourse?.video_url}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                            rows="2"
                        />

                        <select
                            name="status"
                            defaultValue={selectedCourse?.status}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>

                        <select
                            name="level"
                            defaultValue={selectedCourse?.level}
                            onChange={handleChange}
                            disabled
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>

                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Related Courses
                            </label>
                            <Select
                                isMulti
                                options={allCourses}
                                className="basic-multi-select"
                                classNamePrefix="select"
                                onChange={(opts) => handleRelatedCoursesChange(opts, true)}
                                value={allCourses.filter(opt => selectedCourse?.related_courses?.includes(opt.value))}
                                placeholder="Select related courses..."
                            />
                        </div>

                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Related Lessons (Optional)</label>
                            <div className="space-y-2">
                                {(selectedCourse?.related_lessons || []).map((rl, idx) => (
                                    <div key={idx} className="flex items-center gap-2">
                                        <input
                                            type="text"
                                            placeholder="Lesson name"
                                            value={rl.title}
                                            onChange={(e) => {
                                                const rows = [...(selectedCourse.related_lessons || [])];
                                                rows[idx] = { ...rows[idx], title: e.target.value };
                                                setSelectedCourse({ ...selectedCourse, related_lessons: rows });
                                            }}
                                            className="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                        />
                                        <input
                                            type="text"
                                            placeholder="Link"
                                            value={rl.url}
                                            onChange={(e) => {
                                                const rows = [...(selectedCourse.related_lessons || [])];
                                                rows[idx] = { ...rows[idx], url: e.target.value };
                                                setSelectedCourse({ ...selectedCourse, related_lessons: rows });
                                            }}
                                            className="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => {
                                                const rows = (selectedCourse.related_lessons || []).filter((_, i) => i !== idx);
                                                setSelectedCourse({ ...selectedCourse, related_lessons: rows });
                                            }}
                                            className="flex-shrink-0 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition"
                                        >
                                            <i className="fa fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                ))}
                                <button
                                    type="button"
                                    onClick={() => setSelectedCourse({ ...selectedCourse, related_lessons: [...(selectedCourse.related_lessons || []), { title: "", url: "" }] })}
                                    className="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5"
                                >
                                    <i className="fa fa-plus"></i> Add related lesson
                                </button>
                            </div>
                        </div>

                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Thumbnail Image (Optional)
                            </label>
                            <input
                                type="file"
                                accept="image/*"
                                onChange={(e) => {
                                    setSelectedCourse({
                                        ...selectedCourse,
                                        thumbnail_file: e.target.files[0]
                                    });
                                }}
                                className="w-full p-2 border rounded-lg"
                            />
                        </div>

                        <div className="col-span-1 sm:col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                PDF Download (Optional)
                            </label>
                            <input
                                type="file"
                                accept="application/pdf"
                                onChange={(e) => {
                                    setSelectedCourse({
                                        ...selectedCourse,
                                        pdf_resource_file: e.target.files[0]
                                    });
                                }}
                                className="w-full p-2 border rounded-lg"
                            />
                            {selectedCourse?.pdf_resource_url && !selectedCourse?.pdf_resource_file && (
                                <p className="text-xs text-gray-500 mt-1">
                                    Current file: <a href={selectedCourse.pdf_resource_url} target="_blank" rel="noreferrer" className="text-blue-600 underline">View PDF</a>
                                </p>
                            )}
                        </div>
                    </div>

                    <textarea
                        name="description"
                        placeholder="Description"
                        defaultValue={selectedCourse?.description}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                        rows="4"
                    ></textarea>

                    <div className="mt-4">
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Description Images (Optional)
                        </label>
                        <input
                            type="file"
                            multiple
                            accept="image/*"
                            onChange={(e) => setDescriptionImageFiles(Array.from(e.target.files))}
                            className="w-full p-2 border rounded-lg outline-none text-sm"
                        />
                        {descriptionImageFiles.length > 0 && (
                            <div className="text-xs text-gray-500 mt-1">
                                {descriptionImageFiles.length} file(s) selected
                            </div>
                        )}
                        {selectedCourse?.image_urls && selectedCourse.image_urls.length > 0 && (
                            <div className="mt-2 flex gap-2 flex-wrap">
                                {selectedCourse.image_urls.map((url, idx) => (
                                    <div key={idx} className="w-16 h-12 border rounded overflow-hidden">
                                        <img src={url} className="w-full h-full object-cover" />
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={loading}
                        className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {loading ? (
                            <span className="fa fa-spinner fa-spin"></span>
                        ) : (
                            "Update Course"
                        )}
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
                            {selectedCourse?.title}
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

            {/* Edit Category Modal */}
            <Modal
                isOpen={editCategoryModalOpen}
                onClose={() => setEditCategoryModalOpen(false)}
            >
                <div className="p-6">
                    <h2 className="text-xl font-bold mb-4 text-gray-800">
                        Edit Category Name
                    </h2>
                    <div className="mb-4">
                        <label className="block mb-2 font-medium text-gray-700">Category Name:</label>
                        <input
                            type="text"
                            value={editingCategoryName}
                            onChange={(e) => setEditingCategoryName(e.target.value)}
                            className="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    <div className="flex justify-end space-x-4">
                        <button
                            onClick={() => setEditCategoryModalOpen(false)}
                            className="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded"
                        >
                            Cancel
                        </button>
                        <button
                            onClick={handleUpdateCategory}
                            disabled={loading}
                            className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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

            <Modal
                isOpen={isNewCourseModalOpen}
                onClose={() => setIsNewCourseModalOpen(false)}
            >
                <div className="text-center p-6">
                    <h2 className="text-2xl font-bold text-gray-800 mb-4">
                        Add New Course
                    </h2>
                    <p className="text-gray-600 mb-6">
                        Create a new course in{" "}
                        <span className="font-semibold text-blue-600">
                            {selectedCourseLevel} - {selectedCourseCategory}
                        </span>
                        .
                    </p>
                </div>

                <div>
                    <label className="block mb-2 font-medium">
                        Course Title:
                    </label>
                    <input
                        type="text"
                        name="title"
                        value={course?.title}
                        onChange={handleChangeNewCourse}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>

                <div className="my-3">
                    <label
                        htmlFor="video_type"
                        className="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Video Type
                    </label>

                    <select
                        type="text"
                        name="video_type"
                        value={course?.video_type}
                        onChange={handleChangeNewCourse}
                        className="w-full px-3 py-2 border rounded-lg"
                    >
                        <option value="">Select Video Type</option>
                        <option value="youtube">YouTube</option>
                        <option value="google">Google</option>
                        <option value="local">Local</option>
                        <option value="iframe">Iframe</option>
                    </select>
                </div>

                <div>
                    <label
                        htmlFor="video_url"
                        className="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Video URL
                    </label>
                    <textarea
                        id="video_url"
                        name="video_url"
                        value={course.video_url}
                        onChange={handleChangeNewCourse}
                        className="w-full p-3 border rounded-lg"
                        rows="2"
                    />
                </div>
                <div>
                    <label
                        htmlFor="status"
                        className="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        value={course.status}
                        onChange={handleChangeNewCourse}
                        className="w-full p-3 border rounded-lg"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Thumbnail Image (Optional)
                    </label>
                    <input
                        type="file"
                        accept="image/*"
                        onChange={(e) => {
                            setCourse({
                                ...course,
                                thumbnail_file: e.target.files[0]
                            });
                        }}
                        className="w-full p-2 border rounded-lg"
                    />
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        PDF Download (Optional)
                    </label>
                    <input
                        type="file"
                        accept="application/pdf"
                        onChange={(e) => {
                            setCourse({
                                ...course,
                                pdf_resource_file: e.target.files[0]
                            });
                        }}
                        className="w-full p-2 border rounded-lg"
                    />
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Related Courses
                    </label>
                    <Select
                        isMulti
                        options={allCourses}
                        className="basic-multi-select"
                        classNamePrefix="select"
                        onChange={(opts) => handleRelatedCoursesChange(opts, false)}
                        value={allCourses.filter(opt => course.related_courses?.includes(opt.value))}
                        placeholder="Select related courses..."
                    />
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">Related Lessons (Optional)</label>
                    <div className="space-y-2">
                        {(course.related_lessons || []).map((rl, idx) => (
                            <div key={idx} className="flex items-center gap-2">
                                <input
                                    type="text"
                                    placeholder="Lesson name"
                                    value={rl.title}
                                    onChange={(e) => {
                                        const rows = [...(course.related_lessons || [])];
                                        rows[idx] = { ...rows[idx], title: e.target.value };
                                        setCourse({ ...course, related_lessons: rows });
                                    }}
                                    className="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                                <input
                                    type="text"
                                    placeholder="Link"
                                    value={rl.url}
                                    onChange={(e) => {
                                        const rows = [...(course.related_lessons || [])];
                                        rows[idx] = { ...rows[idx], url: e.target.value };
                                        setCourse({ ...course, related_lessons: rows });
                                    }}
                                    className="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none"
                                />
                                <button
                                    type="button"
                                    onClick={() => {
                                        const rows = (course.related_lessons || []).filter((_, i) => i !== idx);
                                        setCourse({ ...course, related_lessons: rows });
                                    }}
                                    className="flex-shrink-0 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition"
                                >
                                    <i className="fa fa-trash text-xs"></i>
                                </button>
                            </div>
                        ))}
                        <button
                            type="button"
                            onClick={() => setCourse({ ...course, related_lessons: [...(course.related_lessons || []), { title: "", url: "" }] })}
                            className="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5"
                        >
                            <i className="fa fa-plus"></i> Add related lesson
                        </button>
                    </div>
                </div>

                <div>
                    <label
                        htmlFor="description"
                        className="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        value={course.description}
                        onChange={handleChangeNewCourse}
                        className="w-full p-3 border rounded-lg"
                        rows="4"
                    ></textarea>
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Description Images (Optional)
                    </label>
                    <input
                        type="file"
                        multiple
                        accept="image/*"
                        onChange={(e) => setDescriptionImageFiles(Array.from(e.target.files))}
                        className="w-full p-2 border rounded-lg outline-none text-sm"
                    />
                    {descriptionImageFiles.length > 0 && (
                        <div className="text-xs text-gray-500 mt-1">
                            {descriptionImageFiles.length} file(s) selected
                        </div>
                    )}
                </div>

                <div className="mt-4 flex justify-end">
                    <button
                        type="button"
                        disabled={loading}
                        onClick={handleCreateNewCourse}
                        className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {loading ? (
                            <span className="fa fa-spinner fa-spin"></span>
                        ) : (
                            "Save Course"
                        )}
                    </button>
                </div>
            </Modal>

            <Modal
                isOpen={isNewCheckpointModalOpen}
                onClose={closeNewCheckpointModal}
            >
                <div className="text-center p-6">
                    <h2 className="text-2xl font-bold text-gray-800 mb-4">
                        Add Practice Checkpoint
                    </h2>
                    <p className="text-gray-600 mb-6">
                        Insert a checkpoint into this category's lesson list.
                    </p>
                </div>

                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Page Title
                    </label>
                    <input
                        type="text"
                        value={newCheckpoint.title}
                        onChange={(e) =>
                            setNewCheckpoint({ ...newCheckpoint, title: e.target.value })
                        }
                        placeholder="e.g. Major Scale"
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Embedded Video (Optional)
                    </label>
                    <input
                        type="text"
                        value={newCheckpoint.video_url}
                        onChange={(e) =>
                            setNewCheckpoint({ ...newCheckpoint, video_url: e.target.value })
                        }
                        placeholder="e.g. https://www.youtube.com/watch?v=..."
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                    <p className="text-xs text-gray-500 mt-1">
                        Shown at the top of the checkpoint page. Supports YouTube, Vimeo, and Google Drive links.
                    </p>
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Course Overview (Optional)
                    </label>
                    <RichTextEditor
                        id="new-checkpoint-overview"
                        value={newCheckpoint.overview}
                        onChange={(html) =>
                            setNewCheckpoint({ ...newCheckpoint, overview: html })
                        }
                        placeholder="Explain what this checkpoint covers..."
                    />
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Downloads for this Course (Optional)
                    </label>
                    <div className="space-y-2">
                        {newCheckpoint.downloads.map((download, idx) => (
                            <div key={idx} className="flex items-center gap-2">
                                <input
                                    type="text"
                                    placeholder="File title"
                                    value={download.title}
                                    onChange={(e) => {
                                        const rows = [...newCheckpoint.downloads];
                                        rows[idx] = { ...rows[idx], title: e.target.value };
                                        setNewCheckpoint({ ...newCheckpoint, downloads: rows });
                                    }}
                                    className="flex-1 px-3 py-2 border rounded-lg text-sm"
                                />
                                <input
                                    type="file"
                                    accept="application/pdf"
                                    onChange={(e) => {
                                        const file = e.target.files[0];
                                        const rows = [...newCheckpoint.downloads];
                                        rows[idx] = {
                                            ...rows[idx],
                                            file,
                                            title: rows[idx].title || (file ? file.name.replace(/\.pdf$/i, "") : ""),
                                        };
                                        setNewCheckpoint({ ...newCheckpoint, downloads: rows });
                                    }}
                                    className="flex-1 text-sm"
                                />
                                <button
                                    type="button"
                                    onClick={() => {
                                        const rows = newCheckpoint.downloads.filter((_, i) => i !== idx);
                                        setNewCheckpoint({ ...newCheckpoint, downloads: rows });
                                    }}
                                    className="flex-shrink-0 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition"
                                >
                                    <i className="fa fa-trash text-xs"></i>
                                </button>
                            </div>
                        ))}
                        <button
                            type="button"
                            onClick={() =>
                                setNewCheckpoint({
                                    ...newCheckpoint,
                                    downloads: [...newCheckpoint.downloads, { title: "", file: null }],
                                })
                            }
                            className="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5"
                        >
                            <i className="fa fa-plus"></i> Add a PDF file
                        </button>
                    </div>
                    <p className="text-xs text-gray-500 mt-1">PDF files only, up to 20MB each.</p>
                </div>

                <div className="mt-3">
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        Custom Redirect Link (Optional)
                    </label>
                    <input
                        type="text"
                        value={newCheckpoint.redirect_url}
                        onChange={(e) =>
                            setNewCheckpoint({
                                ...newCheckpoint,
                                redirect_url: e.target.value,
                            })
                        }
                        placeholder="e.g. /member/course/beginner or https://..."
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                    <p className="text-xs text-gray-500 mt-1">
                        Shows a "Watch Lesson" button on the checkpoint page linking here.
                    </p>
                </div>

                <div className="mt-4 flex justify-end">
                    <button
                        type="button"
                        disabled={loading}
                        onClick={handleCreateCheckpoint}
                        className="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {loading ? (
                            <span className="fa fa-spinner fa-spin"></span>
                        ) : (
                            "Add Checkpoint"
                        )}
                    </button>
                </div>
            </Modal>

            {/* Edit Checkpoint Modal */}
            <Modal
                isOpen={isEditCheckpointModalOpen}
                onClose={closeEditCheckpointModal}
            >
                <div className="p-2">
                    <h2 className="text-2xl font-bold text-gray-800 mb-4">
                        Edit Practice Checkpoint
                    </h2>

                    {editingCheckpoint && (
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input
                                    type="text"
                                    value={editingCheckpoint.title || ""}
                                    onChange={(e) =>
                                        setEditingCheckpoint({ ...editingCheckpoint, title: e.target.value })
                                    }
                                    className="w-full px-3 py-2 border rounded-lg"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea
                                    value={editingCheckpoint.description || ""}
                                    onChange={(e) =>
                                        setEditingCheckpoint({ ...editingCheckpoint, description: e.target.value })
                                    }
                                    rows="2"
                                    className="w-full px-3 py-2 border rounded-lg resize-none"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Video URL (Optional)</label>
                                <input
                                    type="text"
                                    value={editingCheckpoint.video_url || ""}
                                    onChange={(e) =>
                                        setEditingCheckpoint({ ...editingCheckpoint, video_url: e.target.value })
                                    }
                                    placeholder="e.g. https://www.youtube.com/watch?v=..."
                                    className="w-full px-3 py-2 border rounded-lg"
                                />
                                <p className="text-xs text-gray-500 mt-1">
                                    Shown as an embedded video above the checkpoint content. Supports YouTube, Vimeo, and Google Drive links.
                                </p>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Course Overview (Optional)</label>
                                <RichTextEditor
                                    id="edit-checkpoint-overview"
                                    value={editingCheckpoint.overview || ""}
                                    onChange={(html) =>
                                        setEditingCheckpoint({ ...editingCheckpoint, overview: html })
                                    }
                                    placeholder="Explain what this checkpoint covers..."
                                />
                            </div>

                            <div className="flex justify-end">
                                <button
                                    type="button"
                                    disabled={loading}
                                    onClick={handleUpdateCheckpoint}
                                    className="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
                                >
                                    {loading ? <span className="fa fa-spinner fa-spin"></span> : "Save Changes"}
                                </button>
                            </div>

                            <div className="pt-4 border-t">
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Downloads for this Course
                                </label>

                                <div className="space-y-2 mb-3">
                                    {editingCheckpoint.downloads.map((download) => (
                                        <div key={download.id} className="flex items-center gap-2 bg-gray-50 border rounded-lg px-3 py-2">
                                            <i className="fa fa-file-pdf text-red-500"></i>
                                            <span className="flex-1 min-w-0 text-sm truncate">{download.title}</span>
                                            <a href={download.file_url} target="_blank" rel="noopener noreferrer" className="text-xs text-indigo-600 hover:underline">
                                                View
                                            </a>
                                            <button
                                                type="button"
                                                onClick={() => handleDeleteCheckpointDownload(download)}
                                                className="flex-shrink-0 w-7 h-7 flex items-center justify-center text-gray-400 hover:text-red-600 rounded hover:bg-red-50 transition"
                                            >
                                                <i className="fa fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    ))}
                                    {editingCheckpoint.downloads.length === 0 && (
                                        <p className="text-xs text-gray-400 italic">No files uploaded yet.</p>
                                    )}
                                </div>

                                <div className="flex items-center gap-2">
                                    <input
                                        type="text"
                                        value={newDownloadTitle}
                                        onChange={(e) => setNewDownloadTitle(e.target.value)}
                                        placeholder="File title"
                                        className="flex-1 px-3 py-2 border rounded-lg text-sm"
                                    />
                                    <input
                                        type="file"
                                        accept="application/pdf"
                                        onChange={(e) => {
                                            const file = e.target.files[0];
                                            setNewDownloadFile(file);
                                            if (file && !newDownloadTitle.trim()) {
                                                setNewDownloadTitle(file.name.replace(/\.pdf$/i, ""));
                                            }
                                        }}
                                        className="flex-1 text-sm"
                                    />
                                    <button
                                        type="button"
                                        disabled={uploadingDownload || !newDownloadTitle.trim() || !newDownloadFile}
                                        onClick={handleUploadCheckpointDownload}
                                        className="flex-shrink-0 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-black transition disabled:opacity-50"
                                    >
                                        {uploadingDownload ? <span className="fa fa-spinner fa-spin"></span> : "Upload"}
                                    </button>
                                </div>
                                <p className="text-xs text-gray-500 mt-1">PDF files only, up to 20MB.</p>
                            </div>
                        </div>
                    )}
                </div>
            </Modal>
        </div>
    );
};

export default DraggableCategoryList;
