import React, { useEffect, useState } from "react";
import {
    FaCheckCircle,
    FaUserEdit,
    FaCalendar,
    FaClock,
    FaFacebook,
    FaInstagram,
    FaYoutube,
} from "react-icons/fa";
import ReactDOM from "react-dom/client";
import { useParams } from "react-router-dom";
import {
    FlashMessageProvider,
    useFlashMessage,
} from "../Alert/FlashMessageContext";
import axios from "axios";
import { FaX } from "react-icons/fa6";
import PostList from "../PostList";
import { formatRelativeTime } from "../../utils/formatRelativeTime";

const ProfileSection = () => {
    const [activeTab, setActiveTab] = useState("about");
    const [member, setMember] = useState({});
    // const { id } = useParams();
    const { showMessage } = useFlashMessage();

    const lastSegment = window.location.pathname
        .split("/")
        .filter(Boolean)
        .pop();

    const tabs = [
        { id: "about", label: "About" },
        { id: "posts", label: "Posts" },
        { id: "spaces", label: "Spaces" },
        { id: "comments", label: "Comments" },
    ];
    
    const getMemberDetails = async () => {
        try {
            const res = await axios.get(`/api/member/user/${lastSegment}`);
            setMember(res.data);
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
    };

    useEffect(() => {
        getMemberDetails();
    }, []);

    return (
        <div className="bg-gray-100 dark:bg-gray-900 min-h-screen rounded-full">
            {/* Cover Image */}
            <div className="relative bg-gray-300 h-48">
                <button className="absolute top-3 right-3 bg-white text-sm px-3 py-1 rounded shadow">
                    Upload Cover
                </button>
            </div>

            {/* Profile Info */}
            <div className="bg-white dark:bg-gray-800 p-6 relative">
                <div className="flex items-center space-x-4">
                    <img
                        src="/avatar1.jpg"
                        alt="Profile"
                        className="z-30 w-24 h-24 rounded-full border-4 border-white dark:border-gray-800 -mt-12"
                    />
                    <div>
                        <div className="flex items-center space-x-1">
                            <h2 className="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                {member.user?.first_name} {member.user?.last_name}
                            </h2>
                            {member.community?.verified_status === 1 && (
                                <FaCheckCircle className="text-blue-500" />
                            )}
                        </div>
                        <p className="text-gray-500 dark:text-gray-400">
                            {member.community?.user_name}
                        </p>
                    </div>
                </div>

                {/* Tabs */}
                <div className="flex space-x-6 mt-6 border-b border-gray-200 dark:border-gray-700">
                    {tabs.map((tab) => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`pb-2 text-sm font-medium ${
                                activeTab === tab.id
                                    ? "border-b-2 border-blue-500 text-blue-500"
                                    : "text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>
            </div>

            <div className="flex justify-between gap-8 my-5">
                {/* Left Column */}
                <div className="flex-1">
                    {activeTab === "about" && (
                        <div className="p-6 bg-gray-50 dark:bg-gray-700 rounded-xl shadow-sm">
                            {/* Header */}
                            <div className="flex justify-between items-center mb-4">
                                <h3 className="text-lg font-semibold text-gray-800 dark:text-white">
                                    About
                                </h3>
                                <a
                                    href={`/member/community/u/${member?.id}/update`}
                                    className="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <FaUserEdit size={18} />
                                </a>
                            </div>

                            {/* Details */}
                            <div className="space-y-3 text-gray-700 dark:text-gray-300 text-sm">
                                <p className="font-medium">
                                    {" "}
                                    {member.community?.bio}
                                </p>

                                <p className="flex items-center gap-2">
                                    <FaCalendar className="text-gray-500 dark:text-gray-400" />
                                    {formatRelativeTime(member.user?.created_at)}
                                </p>

                                <p className="flex items-center gap-2">
                                    <FaClock className="text-gray-500 dark:text-gray-400" />
                                    {formatRelativeTime(member.user?.last_login_at)}
                                </p>

                                <div className="flex flex-col pt-2">
                                    <p className="font-medium my-3">
                                        Social Links
                                    </p>

                                    <div className="flex flex-col gap-4 text-sm">
                                        <a
                                            target="blank"
                                            href={
                                                member.social?.instagram
                                            }
                                            className="flex items-center gap-1 text-pink-500 hover:text-pink-600"
                                        >
                                            <FaInstagram /> {member.user_name}
                                        </a>

                                        <a
                                            target="blank"
                                            href={
                                                member.social?.facebook
                                            }
                                            className="flex items-center gap-1 text-blue-500 hover:text-blue-600"
                                        >
                                            <FaFacebook /> {member.user_name}
                                        </a>

                                        <a
                                            target="blank"
                                            href={
                                                member?.social?.youtube
                                            }
                                            className="flex items-center gap-1 text-red-500 hover:text-red-600"
                                        >
                                            <FaYoutube /> {member.user_name}
                                        </a>

                                        <a
                                            target="blank"
                                            href={member?.social?.x}
                                            className="flex items-center gap-1 text-gray-800 hover:text-gray-900 dark:text-gray-200 dark:hover:text-gray-100"
                                        >
                                            <FaX /> {member.user_name}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {activeTab === "posts" && (
                        <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <PostList />
                        </div>
                    )}
                    {activeTab === "spaces" && (
                        <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 className="font-semibold text-gray-800 dark:text-white mb-2">
                                Spaces Details
                            </h3>
                            <p className="text-sm text-gray-600 dark:text-gray-300">
                                Spaces details go here...
                            </p>
                        </div>
                    )}
                    {activeTab === "comments" && (
                        <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h3 className="font-semibold text-gray-800 dark:text-white mb-2">
                                Comment Details
                            </h3>
                            <p className="text-sm text-gray-600 dark:text-gray-300">
                                Comments go here...
                            </p>
                        </div>
                    )}
                </div>

                {/* Right Column (Recent Activities) */}
                <div className="w-1/3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 className="font-semibold text-gray-800 dark:text-white mb-2">
                        Recent Activities
                    </h3>
                    <ul className="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <li>📝 Posted an update</li>
                        <li>💬 Commented on a post</li>
                        <li>📌 Joined a new space</li>
                    </ul>
                </div>
            </div>
        </div>
    );
};

export default ProfileSection;

if (document.getElementById("community-profile")) {
    const Index = ReactDOM.createRoot(
        document.getElementById("community-profile")
    );
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <ProfileSection />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
