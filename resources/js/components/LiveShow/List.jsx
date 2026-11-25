import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import CustomPagination from "../Pagination/CustomPagination";
import Modal from "../Modal/Modal";
import { watInputToUtcIso, utcIsoToWatInput } from "../../utils/formatRelativeTime";

const LiveShow = () => {
    const [courses, setCourses] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [loading, setLoading] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
     const { showMessage } = useFlashMessage();
    // const [liveShow, setliveShow] = useState(null);
    const perPage = 8;
    const [search, setSearch] = useState("");
    const [timeFilter, setTimeFilter] = useState("all");
    const [categoryFilter, setCategoryFilter] = useState("all");
    const [liveShow, setLiveShow] = useState({
        title: "",
        recording_url: "",
        zoom_link: "",
        access_type: "all",
        start_time: "",
    });
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const fetchCourses = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(`/api/live-shows`, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                withCredentials: true,
            });
            const rows = Array.isArray(response.data)
                ? response.data
                : response.data?.data || [];
            setCourses(rows);
        } catch (error) {
            console.error("Error fetching users:", error);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteCourse = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.delete(
                `/api/admin/live-show/${liveShow.id}/delete`,
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            closeDeleteModal();
            showMessage("Live show deleted successfully!", "success");
            fetchCourses();
        } catch (error) {
            console.error("Error deleting liveshow:", error);
            showMessage("Error deleting!", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setLiveShow({ ...liveShow, [name]: value });
    };

    useEffect(() => {
        fetchCourses();
    }, []);

    const handlePageChange = (page) => {
        setCurrentPage(page);
    };

    const now = Date.now();
    const filtered = (courses || [])
        .filter((show) => {
            const startsAt = new Date(show.start_time).getTime();
            if (timeFilter === "upcoming" && !(startsAt >= now)) return false;
            if (timeFilter === "past" && !(startsAt < now)) return false;
            if (
                categoryFilter !== "all" &&
                (show.category || "event") !== categoryFilter
            )
                return false;
            if (
                search.trim() &&
                !String(show.title || "")
                    .toLowerCase()
                    .includes(search.trim().toLowerCase())
            )
                return false;
            return true;
        })
        .sort((a, b) => new Date(b.start_time) - new Date(a.start_time));
    const totalPagesCalc = Math.max(1, Math.ceil(filtered.length / perPage));
    const page = Math.min(currentPage, totalPagesCalc);
    const pageRows = filtered.slice((page - 1) * perPage, page * perPage);

    const formatWhen = (iso) => {
        const d = new Date(iso);
        if (Number.isNaN(d.getTime())) return { date: "—", time: "" };
        return {
            date: d.toLocaleDateString("en-GB", {
                day: "numeric",
                month: "short",
                year: "numeric",
                timeZone: "Africa/Lagos",
            }),
            time:
                d.toLocaleTimeString("en-GB", {
                    hour: "2-digit",
                    minute: "2-digit",
                    timeZone: "Africa/Lagos",
                }) + " WAT",
        };
    };

    const openEditModal = (course) => {
        setLiveShow({ ...course, start_time: utcIsoToWatInput(course.start_time) });
        setIsEditModalOpen(true);
    };

    const openDeleteModal = (course) => {
        setLiveShow(course);
        setIsDeleteModalOpen(true);
    };

    const closeEditModal = () => {
        setIsEditModalOpen(false);
        setLiveShow(null);
    };

    const closeDeleteModal = () => {
        setIsDeleteModalOpen(false);
        setLiveShow(null);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            const response = await axios.patch(
                `/api/admin/live-shows/${liveShow.id}`,
                { ...liveShow, start_time: watInputToUtcIso(liveShow.start_time) },
                {
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    withCredentials: true,
                }
            );
            showMessage("Live show updated successfully!", "success");
            closeEditModal();
            fetchCourses();
        } catch (error) {
            console.error("Error creating course:", error);
            showMessage("Error updating live show!", "error");
        } finally {
            setLoading(false);
        }
    };

    const filterSelect = (label, value, setter, options) => (
        <select
            aria-label={label}
            value={value}
            onChange={(e) => {
                setter(e.target.value);
                setCurrentPage(1);
            }}
            className="rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
        >
            {options.map(([v, l]) => (
                <option key={v} value={v}>
                    {l}
                </option>
            ))}
        </select>
    );

    return (
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div className="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
                <div>
                    <h2 className="text-base font-bold text-gray-900">
                        Live Shows
                    </h2>
                    <p className="text-xs text-gray-500">
                        {filtered.length} of {courses.length}{" "}
                        {courses.length === 1 ? "show" : "shows"}
                    </p>
                </div>
                <a
                    className="inline-flex items-center gap-2 rounded-lg bg-black px-3.5 py-2 text-xs font-semibold text-white hover:bg-gray-800"
                    href="/admin/live-show/create"
                >
                    <span className="fa fa-plus"></span> New live show
                </a>
            </div>

            <div className="flex flex-wrap items-center gap-3 px-4 py-3">
                <div className="relative w-full sm:w-64">
                    <span className="fa fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></span>
                    <input
                        type="search"
                        value={search}
                        onChange={(e) => {
                            setSearch(e.target.value);
                            setCurrentPage(1);
                        }}
                        placeholder="Search by title"
                        className="w-full rounded-lg border border-gray-300 py-2 pl-8 pr-3 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    />
                </div>
                {filterSelect("When", timeFilter, setTimeFilter, [
                    ["all", "All dates"],
                    ["upcoming", "Upcoming"],
                    ["past", "Past"],
                ])}
                {filterSelect("Type", categoryFilter, setCategoryFilter, [
                    ["all", "All types"],
                    ["event", "Events"],
                    ["session", "Sessions"],
                ])}
            </div>

            {loading ? (
                <p className="px-4 pb-6 text-sm text-gray-500">Loading...</p>
            ) : (
                <>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead className="border-y border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th className="px-4 py-2.5 font-semibold">Title</th>
                                    <th className="px-4 py-2.5 font-semibold">Date</th>
                                    <th className="px-4 py-2.5 font-semibold">Type</th>
                                    <th className="px-4 py-2.5 font-semibold">Access</th>
                                    <th className="px-4 py-2.5 font-semibold">Status</th>
                                    <th className="px-4 py-2.5 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100">
                                {pageRows.length > 0 ? (
                                    pageRows.map((show) => {
                                        const when = formatWhen(show.start_time);
                                        const isSession = show.category === "session";
                                        const upcoming =
                                            new Date(show.start_time).getTime() >= now;
                                        return (
                                            <tr key={show.id} className="hover:bg-gray-50">
                                                <td className="max-w-[16rem] px-4 py-2.5">
                                                    <p className="truncate font-semibold text-gray-900" title={show.title}>
                                                        {show.title}
                                                    </p>
                                                </td>
                                                <td className="whitespace-nowrap px-4 py-2.5">
                                                    <p className="text-gray-900">{when.date}</p>
                                                    <p className="text-xs text-gray-500">{when.time}</p>
                                                </td>
                                                <td className="px-4 py-2.5">
                                                    <span className={`rounded-full px-2 py-0.5 text-[11px] font-bold ${isSession ? "bg-violet-50 text-violet-700" : "bg-blue-50 text-blue-700"}`}>
                                                        {isSession ? "Session" : "Event"}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-2.5">
                                                    <span className={`rounded-full px-2 py-0.5 text-[11px] font-bold capitalize ${show.access_type === "premium" ? "bg-amber-50 text-amber-700" : "bg-gray-100 text-gray-700"}`}>
                                                        {show.access_type === "premium" ? "Premium" : "All users"}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-2.5 text-xs font-semibold">
                                                    {show.status === "coming_soon" ? (
                                                        <span className="text-amber-600">Coming soon</span>
                                                    ) : (
                                                        <span className="text-emerald-600">Open</span>
                                                    )}
                                                    <span className="ml-1.5 font-normal text-gray-400">
                                                        {upcoming ? "· upcoming" : "· past"}
                                                    </span>
                                                </td>
                                                <td className="whitespace-nowrap px-4 py-2.5 text-right">
                                                    <button
                                                        onClick={() => openEditModal(show)}
                                                        aria-label={`Edit ${show.title}`}
                                                        className="rounded-md bg-blue-50 px-2.5 py-1.5 text-blue-600 hover:bg-blue-100"
                                                    >
                                                        <span className="fa fa-edit"></span>
                                                    </button>
                                                    <button
                                                        onClick={() => openDeleteModal(show)}
                                                        aria-label={`Delete ${show.title}`}
                                                        className="ml-1.5 rounded-md bg-red-50 px-2.5 py-1.5 text-red-600 hover:bg-red-100"
                                                    >
                                                        <span className="fa fa-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        );
                                    })
                                ) : (
                                    <tr>
                                        <td colSpan="6" className="px-4 py-10 text-center text-sm text-gray-500">
                                            No live shows match.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {totalPagesCalc > 1 && (
                        <div className="flex items-center justify-center gap-6 border-t border-gray-100 py-4">
                            <CustomPagination
                                currentPage={page}
                                totalPages={totalPagesCalc}
                                onPageChange={handlePageChange}
                            />
                        </div>
                    )}
                </>
            )}

            <Modal
                isOpen={isEditModalOpen}
                onClose={() => setIsEditModalOpen(false)}
            >
                <h2 className="text-lg font-bold mb-4">Edit Live Show</h2>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input
                            type="text"
                            name="title"
                            placeholder="Title"
                            value={liveShow?.title || ""}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            type="text"
                            name="zoom_link"
                            placeholder="Zoom Link"
                            value={liveShow?.zoom_link || ""}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <input
                            type="text"
                            name="recording_url"
                            placeholder="Recording URL"
                            value={liveShow?.recording_url || ""}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <label className="text-xs text-gray-500 -mb-2">Start Date & Time (WAT)</label>
                        <input
                            type="datetime-local"
                            name="start_time"
                            value={liveShow?.start_time || ""}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        />
                        <select
                            name="access_type"
                            value={liveShow?.access_type || "all"}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="all">All Users</option>
                            <option value="premium">Premium Only</option>
                        </select>
                        <select
                            name="category"
                            value={liveShow?.category || "event"}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="event">Live Event</option>
                            <option value="session">Live Session</option>
                        </select>
                        {liveShow?.category === "session" && (
                            <input
                                type="number"
                                name="max_slots"
                                min="1"
                                placeholder="Max Slots"
                                value={liveShow?.max_slots || 5}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-lg"
                                required
                            />
                        )}
                        <select
                            name="status"
                            value={liveShow?.status || "open"}
                            onChange={handleChange}
                            className="w-full p-3 border rounded-lg"
                        >
                            <option value="open">Open</option>
                            <option value="coming_soon">Coming Soon</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        className="w-full px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300"
                    >
                        Save Changes
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
                            {liveShow?.title}
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
        </div>
    );
};

export default LiveShow;

if (document.getElementById("live")) {
    const Index = ReactDOM.createRoot(document.getElementById("live"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <LiveShow />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
