import React, { useEffect, useState } from "react";
import {
    FaCheckCircle,
    FaUserEdit,
    FaCalendar,
    FaClock,
    FaFacebook,
    FaInstagram,
    FaYoutube,
    FaEdit,
    FaTwitter,
} from "react-icons/fa";
import ReactDOM from "react-dom/client";
import { useParams } from "react-router-dom";
import {
    FlashMessageProvider,
    useFlashMessage,
} from "../Alert/FlashMessageContext";
import axios from "axios";
import { FaWebAwesome, FaX } from "react-icons/fa6";
import PostList from "../PostList";
import { formatRelativeTime } from "../../utils/formatRelativeTime";
import MemberSpaces from "./MemberSpaces";
import ProfileCover from "./ProfileCover";
import Comments from "./Comments";

const ProfileSection = () => {
    const [activeTab, setActiveTab] = useState("about");
    const [member, setMember] = useState({});
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

            <ProfileCover member={member} />

            {/* Profile Info */}
            <div className="bg-white dark:bg-gray-800 p-6 relative">
                <div className="flex items-center space-x-4">
                    <div>
                        <div className="flex items-center space-x-1">
                            <h2 className="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                {member.user?.first_name}{" "}
                                {member.user?.last_name}
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
                                    {formatRelativeTime(
                                        member.user?.created_at
                                    )}
                                </p>

                                <p className="flex items-center gap-2">
                                    <FaClock className="text-gray-500 dark:text-gray-400" />
                                    {formatRelativeTime(
                                        member.user?.last_login_at
                                    )}
                                </p>

                                <div className="flex flex-col pt-2">
                                    <p className="font-medium my-3">
                                        Social Links
                                    </p>

                                    <div className="flex flex-col gap-4 text-sm">
                                        <a
                                            target="blank"
                                            href={member.social?.instagram}
                                            className="flex items-center gap-1 text-pink-500 hover:text-pink-600"
                                        >
                                            <FaInstagram /> {member.user_name}
                                        </a>

                                        <a
                                            target="blank"
                                            href={member.social?.facebook}
                                            className="flex items-center gap-1 text-blue-500 hover:text-blue-600"
                                        >
                                            <FaFacebook /> {member.user_name}
                                        </a>

                                        <a
                                            target="blank"
                                            href={member?.social?.youtube}
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
                            <MemberSpaces showSection={lastSegment !== null} />
                        </div>
                    )}
                    {activeTab === "comments" && (
                        <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <Comments member={member} />
                        </div>
                    )}
                </div>

                {/* Right Column (Recent Activities) */}
                {activeTab === "about" && (
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
                )}
                {activeTab !== "about" && activeTab !== "spaces" && (
                    <div className="w-1/3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg max-h-fit">
                        <div className="flex justify-between">
                            <h3 className="font-semibold text-gray-800 dark:text-white mb-2">
                                About{" "}
                            </h3>
                            <a
                                className="hover:bg-gray-100"
                                href={`/member/community/u/${member.id}/update`}
                            >
                                <FaEdit />
                            </a>
                        </div>

                        <ul className="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <p>{member.bio}</p>
                        </ul>
                        <div className="border-b border-gray-300 px-3"></div>

                        <div className="text-xs text-gray-500 my-5">
                            <p>
                                Joined{" "}
                                {formatRelativeTime(member.user?.created_at)}
                            </p>
                            <p>
                                Last seen{" "}
                                {formatRelativeTime(member.user?.last_login_at)}
                            </p>
                        </div>

                        <div className="flex justify-between gap-2 mx-12 my-5">
                            {member.social?.web_url && (
                                <a
                                    href={member.social?.web_url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <FaWebAwesome />
                                </a>
                            )}
                            {member.social?.instagram && (
                                <a
                                    href={member.social?.instagram}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <FaInstagram />
                                </a>
                            )}
                            {member.social?.facebook && (
                                <a
                                    href={member.social?.facebook}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <FaFacebook />
                                </a>
                            )}
                            {member.social?.x && (
                                <a
                                    href={member.social?.x}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <FaTwitter />
                                </a>
                            )}
                            {member.social?.youtube && (
                                <a
                                    href={member.social?.youtube}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <FaYoutube />
                                </a>
                            )}
                        </div>
                    </div>
                )}
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
