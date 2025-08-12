import React, { useEffect, useState } from "react";
import {
    FaCheckCircle,
    FaFacebook,
    FaInstagram,
    FaYoutube,
    FaTwitter,
    FaSearch,
    FaEllipsisV,
} from "react-icons/fa";
import ReactDOM from "react-dom/client";
import {
    FlashMessageProvider,
    useFlashMessage,
} from "../Alert/FlashMessageContext";
import CustomPagination from "../Pagination/CustomPagination";
import axios from "axios";

const MemberList = () => {
    const [openMenuId, setOpenMenuId] = useState(null);
    const [sortBy, setSortBy] = useState(" ");
    const [searchTerm, setSearchTerm] = useState("");
    const [activeTab, setActiveTab] = useState("active");
    const [members, setMembers] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [hoveredMember, setHoveredMember] = useState(null);
    const { showMessage } = useFlashMessage();

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (!e.target.closest(".menu-container")) {
                setOpenMenuId(null);
            }
        };
        window.addEventListener("click", handleClickOutside);
        return () => window.removeEventListener("click", handleClickOutside);
    }, []);

    const toggleMenu = (e, id) => {
        e.stopPropagation();
        setOpenMenuId((prev) => (prev === id ? null : id));
    };

    const handleSortChange = (event) => {
        setSortBy(event.target.value);
    };

    const fetchUsers = async (page = 1) => {
        try {
            const response = await axios.get(
                `/api/admin/users?page=${page}&sort=${sortBy}`
            );
            setMembers(response?.data?.data || []);
            setTotalPages(response?.data?.last_page || 1);
        } catch (error) {
            console.error("Error fetching users:", error);
        }
    };

    useEffect(() => {
        fetchUsers(currentPage);
    }, [currentPage, sortBy]);

    const formatRelativeTime = (dateString) => {
        const now = Date.now();
        const posted = Date.parse(dateString);
        const diffInSeconds = Math.floor((now - posted) / 1000);

        if (isNaN(diffInSeconds)) return "Invalid date";

        if (diffInSeconds < 60) {
            return `${diffInSeconds} second${
                diffInSeconds !== 1 ? "s" : ""
            } ago`;
        } else if (diffInSeconds < 3600) {
            const minutes = Math.floor(diffInSeconds / 60);
            return `${minutes} minute${minutes !== 1 ? "s" : ""} ago`;
        } else if (diffInSeconds < 86400) {
            const hours = Math.floor(diffInSeconds / 3600);
            return `${hours} hour${hours !== 1 ? "s" : ""} ago`;
        } else if (diffInSeconds < 2592000) {
            const days = Math.floor(diffInSeconds / 86400);
            return `${days} day${days !== 1 ? "s" : ""} ago`;
        } else if (diffInSeconds < 31536000) {
            const months = Math.floor(diffInSeconds / 2592000);
            return `${months} month${months !== 1 ? "s" : ""} ago`;
        } else {
            const years = Math.floor(diffInSeconds / 31536000);
            return `${years} year${years !== 1 ? "s" : ""} ago`;
        }
    };

    const filteredMembers = members
        .filter((m) =>
            activeTab == "active"
                ? m.community?.status !== "blocked"
                : m.community?.status === "blocked"
        )
        .filter((m) =>
            m.first_name?.toLowerCase().includes(searchTerm.toLowerCase())
        );

    const updateUserStatus = async (userId, status) => {
        try {
            const res = await axios.post(
                `/api/member/community/${userId}/status`,
                {
                    status: status,
                }
            );
            if (res.ok) {
                showMessage("Status updated", "success");
                fetchUsers();
            } else {
                showMessage("Status updated", "success");
                console.error("Failed to update status");
            }
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
    };

    return (
        <div className="space-y-3">
            {/* Tabs */}
            <div className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-10">
                <div className="flex space-x-6">
                    {["active", "blocked"].map((tab) => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`py-3 text-sm font-medium ${
                                activeTab === tab
                                    ? "text-blue-600 border-b-2 border-blue-600"
                                    : "text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-b-2 border-transparent"
                            }`}
                        >
                            {tab.charAt(0).toUpperCase() + tab.slice(1)}
                        </button>
                    ))}
                </div>
            </div>

            {/* Search */}
            <div className="px-10 py-6 bg-gray-50 dark:bg-gray-900">
                <div className="w-full relative">
                    <FaSearch
                        className="absolute mt-2 left-3 top-1/2 -translate-y-1/2 text-gray-400"
                        size={18}
                    />
                    <input
                        type="text"
                        placeholder="Search members..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            {/* Sort */}
            <div className="flex items-center justify-between px-10">
                <div className="flex items-center w-4/5">
                    <hr className="flex-grow border-t border-gray-300 dark:border-gray-600" />
                    <span className="ml-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Sort by:
                    </span>
                </div>
                <select
                    className="bg-gray-100 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5 w-28 rounded font-semibold"
                    onChange={handleSortChange}
                    value={sortBy}
                >
                    <option value="">--select--</option>
                    <option value="display_name">Display Name</option>
                    <option value="latest_activity">Last Activity</option>
                    <option value="joining_date">Joining Date</option>
                </select>
            </div>

            {/* Member list */}
            <div className="space-y-3">
                {filteredMembers.length > 0 ? (
                    <>
                        {filteredMembers.map((member) => (
                            <div
                                key={member.id}
                                className="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 relative"
                            >
                                {/* Left Section */}
                                <div className="flex items-start gap-3">
                                    <img
                                        src={member.avatar || "/avatar1.jpg"}
                                        alt={member.name}
                                        className="w-10 h-10 rounded-full object-cover border border-gray-300 dark:border-gray-600"
                                    />
                                    <div className="flex flex-col relative">
                                        <div
                                            className="flex items-center gap-1 flex-wrap relative"
                                            onMouseEnter={() =>
                                                setHoveredMember(member.id)
                                            }
                                            onMouseLeave={() =>
                                                setHoveredMember(null)
                                            }
                                        >
                                            <p className="text-sm font-medium text-gray-900 dark:text-gray-100 cursor-pointer">
                                                <a
                                                    className="text-sm font-semibold text-gray-900 dark:text-gray-100 hover:underline"
                                                    href={`/member/community/u/${member?.community?.id}`}
                                                >
                                                    {member.first_name}{" "}
                                                    {member.last_name}
                                                </a>
                                            </p>
                                            {member.community
                                                ?.verified_status === 1 && (
                                                <FaCheckCircle className="text-blue-400 w-4 h-4" />
                                            )}
                                            <span className="text-xs text-gray-500">
                                                {member.community?.user_name}
                                            </span>

                                            {/* Hover Card */}
                                            {hoveredMember === member.id && (
                                                <div className="absolute top-6 left-0 z-20 w-[28rem] p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                                                    <div className="flex flex-col items-start gap-2">
                                                        <img
                                                            src={
                                                                member.avatar ||
                                                                "/avatar1.jpg"
                                                            }
                                                            alt={member.name}
                                                            className="w-12 h-12 rounded-full border border-gray-300 dark:border-gray-600"
                                                        />
                                                        <a
                                                            className="text-sm font-semibold text-gray-900 dark:text-gray-100"
                                                            href={`/member/community/u/${member?.community?.id}`}
                                                        >
                                                            {member.first_name}{" "}
                                                            {member.last_name}
                                                        </a>

                                                        <p className="text-xs text-gray-600 dark:text-gray-400">
                                                            {member.role ===
                                                            "admin"
                                                                ? "Piano Instructor"
                                                                : "Member"}
                                                        </p>
                                                        <p className="text-xs text-gray-500">
                                                            Joined{" "}
                                                            {formatRelativeTime(
                                                                member.created_at
                                                            )}
                                                        </p>
                                                    </div>
                                                    <div className="flex items-center gap-2 mt-1">
                                                        {member.community
                                                            ?.social
                                                            ?.facebook && (
                                                            <a
                                                                href={
                                                                    member
                                                                        .community
                                                                        .social
                                                                        .facebook
                                                                }
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                            >
                                                                <FaFacebook className="fa fa-instagram text-blue-600 hover:opacity-80" />{" "}
                                                            </a>
                                                        )}
                                                        {member.community
                                                            ?.social?.x && (
                                                            <a
                                                                href={
                                                                    member
                                                                        .community
                                                                        .social[
                                                                        "x"
                                                                    ]
                                                                }
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                            >
                                                                <FaTwitter className=" text-sky-500 hover:opacity-80" />
                                                            </a>
                                                        )}
                                                        {member.community
                                                            ?.social
                                                            ?.instagram && (
                                                            <a
                                                                href={
                                                                    member
                                                                        .community
                                                                        .social
                                                                        .instagram
                                                                }
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                            >
                                                                <FaInstagram className="fa fa-instagram text-pink-500 hover:opacity-80" />
                                                            </a>
                                                        )}
                                                        {member.community
                                                            ?.social
                                                            ?.youtube && (
                                                            <a
                                                                href={
                                                                    member
                                                                        .community
                                                                        .social
                                                                        .youtube
                                                                }
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                            >
                                                                <FaYoutube className="fa fa-instagram text-pink-500 hover:opacity-80" />
                                                            </a>
                                                        )}
                                                    </div>
                                                </div>
                                            )}
                                        </div>

                                        <span className="text-xs text-gray-500 dark:text-gray-400">
                                            Joined{" "}
                                            {formatRelativeTime(
                                                member.created_at
                                            )}{" "}
                                            • Last seen:{" "}
                                            {formatRelativeTime(
                                                member.last_login_at
                                            )}
                                        </span>

                                        <p className="text-xs text-gray-700 dark:text-gray-300 my-3">
                                            {member.role === "admin"
                                                ? "Piano Instructor"
                                                : "Member"}
                                        </p>

                                        {/* Socials */}
                                        <div className="flex items-center gap-2 mt-1">
                                            {member.community?.social
                                                ?.facebook && (
                                                <a
                                                    href={
                                                        member.community.social
                                                            .facebook
                                                    }
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <FaFacebook className="fa fa-instagram text-blue-600 hover:opacity-80" />
                                                </a>
                                            )}
                                            {member.community?.social?.x && (
                                                <a
                                                    href={
                                                        member.community.social[
                                                            "x"
                                                        ]
                                                    }
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <FaTwitter className=" text-sky-500 hover:opacity-80" />
                                                </a>
                                            )}
                                            {member.community?.social
                                                ?.instagram && (
                                                <a
                                                    href={
                                                        member.community.social
                                                            .instagram
                                                    }
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <FaInstagram className="fa fa-instagram text-pink-500 hover:opacity-80" />
                                                </a>
                                            )}
                                            {member.community?.social
                                                ?.youtube && (
                                                <a
                                                    href={
                                                        member.community.social
                                                            .youtube
                                                    }
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <FaYoutube className="fa fa-instagram text-red-500 hover:opacity-80" />
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Menu */}
                                <div className="relative menu-container">
                                    <button
                                        type="button"
                                        aria-label="Open menu"
                                        className="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                                        onClick={(e) =>
                                            toggleMenu(e, member.id)
                                        }
                                    >
                                        <FaEllipsisV className="text-gray-600 dark:text-gray-300" />
                                    </button>
                                    {openMenuId === member.id && (
                                        <div className="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-10">
                                            <button
                                                className="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                                                onClick={() =>
                                                    updateUserStatus(
                                                        member.id,
                                                        "active"
                                                    )
                                                }
                                            >
                                                Set Active
                                            </button>
                                            <button
                                                className="block w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                onClick={() =>
                                                    updateUserStatus(
                                                        member.id,
                                                        "pending"
                                                    )
                                                }
                                            >
                                                Set Inactive
                                            </button>
                                            <button
                                                className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                onClick={() =>
                                                    updateUserStatus(
                                                        member.id,
                                                        "blocked"
                                                    )
                                                }
                                            >
                                                Ban User
                                            </button>
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                    </>
                ) : (
                    <p className="px-10 py-4 text-gray-500 dark:text-gray-400 text-sm">
                        No members found in this category.
                    </p>
                )}
                <CustomPagination
                    currentPage={currentPage}
                    totalPages={totalPages}
                    onPageChange={setCurrentPage}
                />
            </div>
        </div>
    );
};

export default MemberList;

if (document.getElementById("members")) {
    const Index = ReactDOM.createRoot(document.getElementById("members"));
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <MemberList />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
