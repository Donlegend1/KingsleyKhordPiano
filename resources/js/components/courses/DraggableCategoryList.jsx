import React, { useState, useEffect } from "react";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import CustomPagination from "../Pagination/CustomPagination";
import Modal from "../Modal/Modal";
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
        // image_path: '',
        level: "beginner",
        status: "active",
    });

    const handleChangeNewCourse = (e) => {
        const { name, value } = e.target;
        setCourse({ ...course, [name]: value });
    };

    const [selectedCourseLevel, setSelectedCourseLevel] = useState();
    const [selectedCourseCategory, setSelectedCourseCategory] = useState("");
    useEffect(() => {
        setOrderedCategories(Object.entries(courses[level].data));
    }, [courses[level].data]);
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

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
        setIsNewCourseModalOpen(true);
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
            const response = await axios.patch(
                `/api/admin/courses/${selectedCourse.id}`,
                selectedCourse,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            showMessage("course updated", "success");
            closeEditModal();
            fetchCourses();
        } catch (error) {
            console.error("Error updating course:", error);
            showMessage("Error creating course", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleOnDragEnd = async (result) => {
        if (!result.destination) return;

        const items = Array.from(orderedCategories);
        const [reorderedItem] = items.splice(result.source.index, 1);
        items.splice(result.destination.index, 0, reorderedItem);

        setOrderedCategories(items);

        try {
            const response = await axios.post("/api/admin/reorder/courses", {
                level,
                categories: items.map(([category]) => category),
            });
        } catch (error) {
            console.error("Failed to persist category order:", error);
        }
    };

    const handleCreateNewCourse = () => {
        setLoading(true);
        try {
            const response = axios.post(
                `/api/admin/course/store`,
                {
                    ...course,
                    category: selectedCourseCategory,
                    level: selectedCourseLevel,
                },
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            showMessage("Course Created", "success");
            closeNewCourseModal();
            fetchCourses();
        } catch (error) {
            console.error("Error creating new course:", error);
            showMessage("Error creating course", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCategory = async (category) => {
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
                                                        <div className="mb-4 flex justify-end">
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
                                                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                                            {categoryCourses.map(
                                                                (course) => (
                                                                    <div
                                                                        key={
                                                                            course.id
                                                                        }
                                                                        className="bg-white dark:bg-gray-900 p-4 rounded-lg shadow hover:shadow-md transition-all"
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
                                                            )}
                                                        </div>

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
                <h2 className="text-lg font-bold mb-2">Edit Course</h2>
                <p>Editing Course: {selectedCourse?.title}</p>
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
                        <input
                            name="video_url"
                            placeholder="Video URL"
                            defaultValue={selectedCourse?.video_url}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
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
                    </div>

                    <textarea
                        name="description"
                        placeholder="Description"
                        defaultValue={selectedCourse?.description}
                        onChange={handleChange}
                        className="w-full p-3 border rounded-lg"
                        rows="4"
                    ></textarea>

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
                    <input
                        id="video_url"
                        name="video_url"
                        value={course.video_url}
                        onChange={handleChangeNewCourse}
                        className="w-full p-3 border rounded-lg"
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
        </div>
    );
};

export default DraggableCategoryList;
