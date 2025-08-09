import React, { useEffect, useState } from "react";
import { MoreHorizontal, MoreVertical, Search } from "lucide-react";
import ReactDOM from "react-dom/client";
import { FlashMessageProvider } from "../Alert/FlashMessageContext";

const members = [
    { id: 1, name: "John Doe", role: "Admin", avatar: "/avatar1.jpg" },
    { id: 2, name: "Jane Smith", role: "Member", avatar: "/avatar1.jpg" },
];

const MemberList = () => {
    const [openMenuId, setOpenMenuId] = useState(null);
    const [sortBy, setSortBy] = useState("latest");

    // Close menu on outside click
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
        // const selectedSort = event.target.value;
        // setSortBy(selectedSort);
        // setPage(1);
        // setPosts([]);
        // setHasMore(true);
    };

    return (
        <div className="space-y-3">
            {/* Tabs */}
            <div className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-10">
                <div className="flex space-x-6">
                    <button className="py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 focus:outline-none">
                        Active
                    </button>
                    <button className="py-3 text-sm font-medium text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-b-2 border-transparent focus:outline-none">
                        Blocked
                    </button>
                </div>
            </div>

            {/* Search */}
            <div className="px-10 py-6 bg-gray-50 dark:bg-gray-900">
    <div className="w-full relative">
        {/* Search Icon */}
        <Search
            className="absolute mt-2 left-3 top-1/2 -translate-y-1/2 text-gray-400"
            size={18}
        />

        {/* Search Input */}
        <input
            type="text"
            placeholder="Search members..."
            className="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    </div>
</div>


            {/* Sort Header */}
            <div className="flex items-center justify-between px-10">
                <div className="flex items-center w-4/5">
                    <hr className="flex-grow border-t border-gray-300 dark:border-gray-600" />
                    <span className="ml-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Sort by:
                    </span>
                </div>

                <div className="flex items-center">
                    <select
                        className="bg-gray-100 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5 w-28 rounded font-semibold"
                        onChange={handleSortChange}
                        value={sortBy}
                    >
                        <option value="latest">Latest</option>
                        <option value="old">Old</option>
                        <option value="popular">Popular</option>
                        <option value="likes">Likes</option>
                    </select>
                </div>
            </div>

            {/* Members List */}
            <div className=" space-y-3">
                {members.map((member) => (
                    <div
                        key={member.id}
                        className="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"
                    >
                        {/* Left: Avatar + Info */}
                        <div className="flex items-center gap-3">
                            <img
                                src={member.avatar || "/avatar1.png"}
                                alt={member.name}
                                className="w-10 h-10 rounded-full object-cover"
                            />
                            <div>
                                <p className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {member.name}
                                </p>
                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                    {member.role || "Member"}
                                </p>
                            </div>
                        </div>

                        {/* Right: Ellipsis Menu */}
                        <div className="relative menu-container">
                            <button
                                onClick={(e) => toggleMenu(e, member.id)}
                                className="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <MoreVertical
                                    size={18}
                                    className="text-gray-600 dark:text-gray-300"
                                />
                            </button>

                            {openMenuId === member.id && (
                                <div className="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-10">
                                    <button
                                        className="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                                        onClick={() =>
                                            console.log("Edit", member.id)
                                        }
                                    >
                                        Edit
                                    </button>
                                    <button
                                        className="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                                        onClick={() =>
                                            console.log("Remove", member.id)
                                        }
                                    >
                                        Remove
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                ))}
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
