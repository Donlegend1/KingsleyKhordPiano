import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import DraggableCategoryList from "./DraggableCategoryList";
import Modal from "../Modal/Modal";

const Courses = () => {
    // Add new state for collapsed sections
    const [collapsedSections, setCollapsedSections] = useState({
        beginner: false,
        intermediate: false,
        advanced: false,
    });
    const [courses, setCourses] = useState({
        beginner: { data: [], current_page: 1, last_page: 1 },
        intermediate: { data: [], current_page: 1, last_page: 1 },
        advanced: { data: [], current_page: 1, last_page: 1 },
    });
    const [loading, setLoading] = useState(false);
    const [newCategoryModalOpen, setNewCategoryModalOpen] = useState(false);
    const [newCategoryLevel, setNewCategoryLevel] = useState(null);
    const [newCategoryName, setNewCategoryName] = useState("");
    const { showMessage } = useFlashMessage();

    const [perPage, setPerPage] = useState("9");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const fetchCourses = async (level = null, page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(
                `/api/admin/courses?${
                    level ? `level=${level}&` : ""
                }page=${page}&per_page=${perPage}`,
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );
            if (level) {
                setCourses((prev) => ({
                    ...prev,
                    [level]: response.data[level],
                }));
            } else {
                setCourses(response.data);
            }
        } catch (error) {
            console.error("Error fetching courses:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCourses();
    }, [perPage]); // Add perPage dependency


    const handlePageChange = (level, page) => {
        fetchCourses(level, page);
    };

    // Add toggle function
    const toggleSection = (level) => {
        setCollapsedSections((prev) => ({
            ...prev,
            [level]: !prev[level],
        }));
    };

    const handleNewCategoryModal = (e, level) => {
        setNewCategoryModalOpen(!newCategoryModalOpen);
        setNewCategoryLevel(level);
    };

   const handleCreateCategory = async () => {
         try {
            const response = await axios.post(
                `/api/admin/courses/category/create`,
                {
                    category: newCategoryName,
                    level: newCategoryLevel,
                },
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            showMessage("Category Created", "success");
            handleNewCategoryModal();
            fetchCourses();
        } catch (error) {
            console.error("Error creating course category:", error);
            showMessage("Error creating course category", "error");
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="bg-white p-6 rounded-lg shadow-lg">
            <div className="flex justify-between items-center mb-6">
                <h2 className="text-2xl font-bold text-gray-800">
                    Course List
                </h2>
                <div className="flex items-center gap-4">
                    <select
                        value={perPage}
                        onChange={(e) => setPerPage(e.target.value)}
                        className="px-3 py-2 border rounded-lg text-sm"
                    >
                        <option value="9">Show 9</option>
                        <option value="18">Show 18</option>
                        <option value="27">Show 27</option>
                        <option value="all">Show All</option>
                    </select>
                    {/* <a
                        className="px-4 py-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors"
                        href="/admin/course/create"
                    >
                        <span className="fa fa-plus"></span>
                    </a> */}
                </div>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-64">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
                </div>
            ) : (
                <div className="space-y-8">
                    {["beginner", "intermediate", "advanced"].map((level) => (
                        <div key={level} className="mb-8">
                            {/* Level Header */}
                            <h3
                                onClick={() => toggleSection(level)}
                                className="text-xl font-semibold mb-4 px-4 py-2 bg-gray-50 rounded-lg capitalize flex justify-between items-center cursor-pointer hover:bg-gray-100"
                            >
                                <span>{level} Courses</span>
                                
                                <i
                                    className={`fa ${
                                        collapsedSections[level]
                                            ? "fa-chevron-down"
                                            : "fa-chevron-up"
                                    } text-sm`}
                                ></i>
                            </h3>

                            {/* Add Button */}
                            <div className="mb-4 flex justify-end">
                                <button className="rounded-md bg-black p-2 text-white" onClick={(e) => handleNewCategoryModal(e, level)}>
                                    New Category
                                </button>
                            </div>

                            {/* Level Content */}
                            <div
                                className={`transition-all duration-300 ${
                                    collapsedSections[level]
                                        ? "hidden"
                                        : "block"
                                }`}
                            >
                                {/* Draggable Category List */}
                                <DraggableCategoryList
                                    level={level}
                                    loading={loading}
                                    setLoading={setLoading}
                                    courses={courses}
                                    handlePageChange={handlePageChange}
                                    perPage={perPage}
                                    fetchCourses={fetchCourses}
                                />
                            </div>
                        </div>
                    ))}
                </div>
            )}

            <Modal
                isOpen={newCategoryModalOpen}
                onClose={handleNewCategoryModal}    
            >
                <h2 className="text-xl font-bold mb-4">Create New Category</h2>
                <p>Create a new category in {newCategoryLevel}</p>

                <div>
                    <label className="block mb-2 font-medium">Category Name:</label>
                    <input
                        type="text" 
                        value={newCategoryName}
                        onChange={(e) => setNewCategoryName(e.target.value)}
                        className="w-full px-3 py-2 border rounded-lg"
                    />
                </div>
                <div>
                    <button
                        onClick={handleCreateCategory}
                        className="mt-4 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800" 
                    >
                        Create Category
                    </button>
                </div>
            </Modal>

        </div>
    );
};

export default Courses;

if (document.getElementById("courses")) {
    const Index = ReactDOM.createRoot(document.getElementById("courses"));

    Index.render(
            <FlashMessageProvider>
                <Courses />
            </FlashMessageProvider>
    );
}
