import ReactDOM from "react-dom/client";
import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import Select from "react-select";
import CustomPagination from "../Pagination/CustomPagination";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import Modal from "../Modal/Modal";

const MusicalApplicationList = () => {
    const [uploads, setUploads] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [loading, setLoading] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
    const perPage = 10;
    const [thumbnailFile, setThumbnailFile] = useState(null);
    const { showMessage } = useFlashMessage();

    const [tagOptions, setTagOptions] = useState([]);
    const [selectedTags, setSelectedTags] = useState([]);

    const skillLevels = ["Beginner", "Intermediate", "Advanced"];
    const [saving, setSaving] = useState(false);
    
    const [selectedUpload, setSelectedUpload] = useState({
        title: "",
        description: "",
        video_url: "",
        video_type: "vimeo",
        skill_level: "Beginner",
        series: "",
        status: "active",
        tags: [],
    });

    const [upload, setUpload] = useState({
        title: "",
        description: "",
        video_url: "",
        video_type: "vimeo",
        skill_level: "Beginner",
        series: "",
        status: "active",
        tags: [],
    });

    const [preview, setPreview] = useState(null);
    const fileInputRef = useRef(null);

    const handleTagsChange = (selectedOptions) => {
        setSelectedTags(selectedOptions || []);
    };

    const handleChangeCreate = (e) => {
        const { name, value } = e.target;
        setUpload({ ...upload, [name]: value });
    };

    const handleImageClick = () => {
        fileInputRef.current.click();
    };

    const handleFileChange = (e) => {
        setThumbnailFile(e.target.files[0]);
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setThumbnailFile(file);
            setPreview(URL.createObjectURL(file));
        }
    };

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const fetchTagOptions = async () => {
        try {
            const response = await axios.get('/admin/musical-application-all-courses');
            const formatted = response.data.map(item => ({
                value: item.id,
                label: item.title
            }));
            setTagOptions(formatted);
        } catch (error) {
            console.error("Error fetching course options:", error);
        }
    };

    const fetchUploads = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(
                `/admin/musical-application-list?page=${page}&perPage=${perPage}`,
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );

            setUploads(response.data.data);
            setCurrentPage(response.data.current_page);
            setTotalPages(response.data.last_page);
        } catch (error) {
            console.error("Error fetching musical applications:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteUpload = async () => {
        setLoading(true);
        try {
            await axios.delete(`/admin/musical-application/${selectedUpload.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });
            setIsDeleteModalOpen(false);
            showMessage("Deleted successfully.", "success");
            fetchUploads();
        } catch (error) {
            console.error("Error deleting:", error);
            showMessage("Error deleting.", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setSelectedUpload({ ...selectedUpload, [name]: value });
    };

    useEffect(() => {
        fetchUploads();
        fetchTagOptions();
    }, []);

    const handlePageChange = (page) => {
        fetchUploads(page);
    };

    const openEditModal = (item) => {
        setSelectedUpload(item);
        setPreview(item.thumbnail_url);
        
        // Convert array of IDs to Select objects
        if (item.tags && item.tags.length > 0) {
            const selected = tagOptions.filter(opt => item.tags.includes(opt.value));
            setSelectedTags(selected);
        } else {
            setSelectedTags([]);
        }
        
        setIsEditModalOpen(true);
    };

    const openDeleteModal = (item) => {
        setSelectedUpload(item);
        setIsDeleteModalOpen(true);
    };

    const handleSubmitUpdate = async (e) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData();
        Object.entries(selectedUpload).forEach(([key, value]) => {
            if (key === 'tags') return; // Handled separately
            if (value !== null) formData.append(key, value);
        });
        
        // Append selected tag IDs
        selectedTags.forEach((tag, index) => {
            formData.append(`tags[${index}]`, tag.value);
        });

        formData.append("_method", "PUT");

        if (thumbnailFile instanceof File) {
            formData.append("thumbnail", thumbnailFile);
        }

        try {
            await axios.post(
                `/admin/musical-application/${selectedUpload.id}`,
                formData,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "multipart/form-data",
                    },
                    withCredentials: true,
                }
            );
            setIsEditModalOpen(false);
            showMessage("Updated successfully.", "success");
            fetchUploads();
        } catch (error) {
            console.error("Error updating:", error);
            showMessage("Error updating.", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleCreateUpload = async (e) => {
        e.preventDefault();
        setSaving(true);

        const formData = new FormData();
        if (thumbnailFile) {
            formData.append("thumbnail", thumbnailFile);
        }

        Object.entries(upload).forEach(([key, value]) => {
            if (key === 'tags') return;
            formData.append(key, value);
        });
        
        // Append selected tag IDs
        selectedTags.forEach((tag, index) => {
            formData.append(`tags[${index}]`, tag.value);
        });

        try {
            await axios.post("/admin/musical-application", formData, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "multipart/form-data",
                },
            });

            showMessage("Saved successfully.", "success");
            setUpload({
                title: "",
                description: "",
                video_url: "",
                video_type: "vimeo",
                skill_level: "Beginner",
                series: "",
                status: "active",
                tags: [],
            });
            setSelectedTags([]);
            setThumbnailFile(null);
            setPreview(null);
            fetchUploads();
            setIsCreateModalOpen(false);
        } catch (error) {
            showMessage("Error creating.", "error");
            console.error("Error creating:", error);
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 className="text-lg font-bold mb-4">
                Musical Application List
            </h2>
            <div className="flex justify-end items-center mb-4 ">
                <button
                    className="px-4 py-2 bg-[#0FA9A0] text-white rounded-full hover:bg-[#0d928a] transition-colors"
                    onClick={() => setIsCreateModalOpen(true)}
                >
                    <span className="fa fa-plus"></span> Add New
                </button>
            </div>
            {loading ? (
                <p>Loading...</p>
            ) : (
                <>
                    <table className="min-w-full bg-white mb-4">
                        <thead className="bg-gray-800 text-white">
                            <tr>
                                <th className="py-2 px-4 text-left">S/N</th>
                                <th className="py-2 px-4 text-left">Title</th>
                                <th className="py-2 px-4 text-left">Thumbnail</th>
                                <th className="py-2 px-4 text-left">Series</th>
                                <th className="py-2 px-4 text-left">Level</th>
                                <th className="py-2 px-4 text-left">Status</th>
                                <th className="py-2 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {uploads && uploads.length > 0 ? (
                                uploads.map((item, index) => (
                                    <tr key={item.id} className="border-b">
                                        <td className="py-2 px-4">{index + 1}</td>
                                        <td className="py-2 px-4">{item.title}</td>
                                        <td className="py-2 px-4">
                                            <div className="w-16 h-10 bg-gray-100 rounded overflow-hidden">
                                                <img src={item.thumbnail_url} alt="" className="w-full h-full object-cover" />
                                            </div>
                                        </td>
                                        <td className="py-2 px-4">{item.series}</td>
                                        <td className="py-2 px-4">{item.skill_level}</td>
                                        <td className="py-2 px-4">
                                            <span className={`px-2 py-1 rounded-full text-[10px] font-bold uppercase ${item.status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500'}`}>
                                                {item.status}
                                            </span>
                                        </td>
                                        <td className="py-2 px-4 flex justify-center gap-2">
                                            <button onClick={() => openEditModal(item)} className="bg-blue-500 text-white p-1.5 rounded hover:bg-blue-600">
                                                <span className="fa fa-edit"></span>
                                            </button>
                                            <button onClick={() => openDeleteModal(item)} className="bg-red-500 text-white p-1.5 rounded hover:bg-red-600">
                                                <span className="fa fa-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="py-10 text-center text-gray-400">
                                        No entries found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>

                    <div className="flex items-center justify-center mt-6">
                        <CustomPagination
                            currentPage={currentPage}
                            totalPages={totalPages}
                            onPageChange={handlePageChange}
                        />
                    </div>
                </>
            )}

            {/* Create Modal */}
            <Modal isOpen={isCreateModalOpen} onClose={() => setIsCreateModalOpen(false)}>
                <h2 className="text-xl font-bold mb-6">Add Musical Application</h2>
                <form onSubmit={handleCreateUpload} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input name="title" value={upload.title} onChange={handleChangeCreate} className="w-full p-2 border rounded-lg" required />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Skill Level</label>
                            <select name="skill_level" value={upload.skill_level} onChange={handleChangeCreate} className="w-full p-2 border rounded-lg">
                                {skillLevels.map(lvl => <option key={lvl} value={lvl}>{lvl}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Series</label>
                            <input name="series" value={upload.series} onChange={handleChangeCreate} className="w-full p-2 border rounded-lg" placeholder="e.g. Drop 2" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                            <select name="video_type" value={upload.video_type} onChange={handleChangeCreate} className="w-full p-2 border rounded-lg">
                                <option value="vimeo">Vimeo</option>
                                <option value="youtube">YouTube</option>
                                <option value="google">Google Drive</option>
                                <option value="iframe">Iframe</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video URL/ID</label>
                            <input name="video_url" value={upload.video_url} onChange={handleChangeCreate} className="w-full p-2 border rounded-lg" required />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" value={upload.status} onChange={handleChangeCreate} className="w-full p-2 border rounded-lg">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                            <input type="file" onChange={handleImageChange} className="w-full text-sm" accept="image/*" />
                        </div>
                        <div className="col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Related Courses</label>
                            <Select
                                isMulti
                                options={tagOptions}
                                value={selectedTags}
                                onChange={handleTagsChange}
                                className="basic-multi-select"
                                classNamePrefix="select"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" value={upload.description} onChange={handleChangeCreate} className="w-full p-2 border rounded-lg" rows="3"></textarea>
                    </div>
                    <div className="flex justify-end gap-3 mt-6">
                        <button type="button" onClick={() => setIsCreateModalOpen(false)} className="px-4 py-2 text-gray-500 hover:text-gray-700">Cancel</button>
                        <button type="submit" disabled={saving} className="px-6 py-2 bg-[#0FA9A0] text-white rounded-lg hover:bg-[#0d928a] disabled:opacity-50">
                            {saving ? 'Saving...' : 'Save Entry'}
                        </button>
                    </div>
                </form>
            </Modal>

            {/* Edit Modal */}
            <Modal isOpen={isEditModalOpen} onClose={() => setIsEditModalOpen(false)}>
                <h2 className="text-xl font-bold mb-6">Edit Musical Application</h2>
                <form onSubmit={handleSubmitUpdate} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="col-span-2 text-center mb-4">
                            <div className="w-40 h-24 bg-gray-100 rounded-lg mx-auto overflow-hidden border cursor-pointer" onClick={handleImageClick}>
                                {preview ? <img src={preview} className="w-full h-full object-cover" /> : <div className="flex items-center justify-center h-full text-gray-400">No Image</div>}
                                <input type="file" ref={fileInputRef} onChange={handleImageChange} className="hidden" accept="image/*" />
                            </div>
                            <p className="text-[10px] text-gray-400 mt-1">Click image to change thumbnail</p>
                        </div>
                        <div className="col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                            <input name="title" value={selectedUpload.title} onChange={handleChange} className="w-full p-2 border rounded-lg" required />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Skill Level</label>
                            <select name="skill_level" value={selectedUpload.skill_level} onChange={handleChange} className="w-full p-2 border rounded-lg">
                                {skillLevels.map(lvl => <option key={lvl} value={lvl}>{lvl}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Series</label>
                            <input name="series" value={selectedUpload.series || ""} onChange={handleChange} className="w-full p-2 border rounded-lg" />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video Type</label>
                            <select name="video_type" value={selectedUpload.video_type} onChange={handleChange} className="w-full p-2 border rounded-lg">
                                <option value="vimeo">Vimeo</option>
                                <option value="youtube">YouTube</option>
                                <option value="google">Google Drive</option>
                                <option value="iframe">Iframe</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Video URL/ID</label>
                            <input name="video_url" value={selectedUpload.video_url} onChange={handleChange} className="w-full p-2 border rounded-lg" required />
                        </div>
                        <div className="col-span-2">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Related Courses</label>
                            <Select
                                isMulti
                                options={tagOptions}
                                value={selectedTags}
                                onChange={handleTagsChange}
                                className="basic-multi-select"
                                classNamePrefix="select"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" value={selectedUpload.description || ""} onChange={handleChange} className="w-full p-2 border rounded-lg" rows="3"></textarea>
                    </div>
                    <div className="flex justify-end gap-3 mt-6">
                        <button type="button" onClick={() => setIsEditModalOpen(false)} className="px-4 py-2 text-gray-500 hover:text-gray-700">Cancel</button>
                        <button type="submit" disabled={loading} className="px-6 py-2 bg-[#0FA9A0] text-white rounded-lg hover:bg-[#0d928a] disabled:opacity-50">
                            {loading ? 'Updating...' : 'Update Entry'}
                        </button>
                    </div>
                </form>
            </Modal>

            {/* Delete Modal */}
            <Modal isOpen={isDeleteModalOpen} onClose={() => setIsDeleteModalOpen(false)}>
                <div className="text-center p-4">
                    <i className="fa fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                    <h2 className="text-xl font-bold mb-2">Delete Confirmation</h2>
                    <p className="text-gray-500 mb-6">Are you sure you want to delete <strong>{selectedUpload?.title}</strong>? This action cannot be undone.</p>
                    <div className="flex justify-center gap-3">
                        <button onClick={() => setIsDeleteModalOpen(false)} className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button onClick={handleDeleteUpload} className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Yes, Delete</button>
                    </div>
                </div>
            </Modal>
        </div>
    );
};

export default MusicalApplicationList;

if (document.getElementById("musical-applications")) {
    const root = ReactDOM.createRoot(document.getElementById("musical-applications"));
    root.render(
        <FlashMessageProvider>
            <MusicalApplicationList />
        </FlashMessageProvider>
    );
}
