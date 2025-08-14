import React, { useState } from "react";
import { FaSearch } from "react-icons/fa";
import ReactDOM from "react-dom/client";
import { FlashMessageProvider } from "../Alert/FlashMessageContext";
import SpaceCard from "./SpaceCard";

const cardDetails = [
    { title: "Ask a question", isAdmin: true, members: "13" },
    { title: "Beginners Forum", isAdmin: true, members: "20" },
    { title: "Intermediates Forum", isAdmin: true, members: "19" },
    { title: "Lessons", isAdmin: true, members: "30" },
    { title: "Post Your progress", isAdmin: true, members: "18" },
    { title: "Professional Forum", isAdmin: true, members: "4" },
    { title: "Say Hello", isAdmin: true, members: "14" },
];

const MemberSpaces = ({ showSection }) => {
    const [sortBy, setSortBy] = useState("alphabetical");
    const [searchTerm, setSearchTerm] = useState("");
    const [activeTab, setActiveTab] = useState("All Space");
    const [isShowSection, setIsShowSection] = useState(showSection);

    const handleSortChange = (event) => {
        setSortBy(event.target.value);
    };

    return (
        <>
            {/* Page Header */}
            <div className="border border-gray-200 dark:border-gray-500 mb-4">
                {!isShowSection && (
                    <div className="flex justify-between items-center px-4 py-2 bg-white dark:bg-gray-800">
                        <h1 className="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                            Spaces
                        </h1>

                        <div className="flex gap-3">
                            {["All Space", "My Space"].map((tab) => (
                                <button
                                    key={tab}
                                    onClick={() => setActiveTab(tab)}
                                    className={`py-2 px-4 text-sm font-medium rounded-md transition ${
                                        activeTab === tab
                                            ? "bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white"
                                            : "text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                                    }`}
                                >
                                    {tab}
                                </button>
                            ))}
                            <button className="bg-black text-white rounded-md px-3 py-2 hover:bg-gray-900">
                                New Space
                            </button>
                        </div>
                    </div>
                )}
            </div>

            {/* Content */}
            <div className="space-y-4">
                {/* Search */}
                {!isShowSection && (
                    <div className="px-10 py-6 bg-gray-50 dark:bg-gray-900">
                        <div className="w-full relative">
                            <FaSearch
                                className="absolute left-3 top-2/3 -translate-y-1/2 text-gray-400"
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
                )}

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
                        <option value="alphabetical">Alphabetical</option>
                        <option value="latest_space">Last Space</option>
                        <option value="oldest_space">Oldest Space</option>
                    </select>
                </div>

                {/* Member Spaces List */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 px-10 pb-6">
                    {cardDetails.map((detail, index) => (
                        <SpaceCard
                            key={index}
                            isAdmin={detail.isAdmin}
                            members={detail.members}
                            title={detail.title}
                        />
                    ))}
                </div>
            </div>
        </>
    );
};


export default MemberSpaces;

if (document.getElementById("member-spaces")) {
    const Index = ReactDOM.createRoot(document.getElementById("member-spaces"));
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <MemberSpaces />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
