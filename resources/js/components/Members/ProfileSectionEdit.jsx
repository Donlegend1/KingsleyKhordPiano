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
import {
    FlashMessageProvider,
    useFlashMessage,
} from "../Alert/FlashMessageContext";
import axios from "axios";
import { FaX } from "react-icons/fa6";
import PostList from "../PostList";
import { formatRelativeTime } from "../../utils/formatRelativeTime";

const ProfileSectionEdit = () => {
    const [activeTab, setActiveTab] = useState("about");
    const [member, setMember] = useState({});
    const [formData, setFormData] = useState({
        first_name: "",
        last_name: "",
        email: "",
        website: "",
        bio: "",
        instagram: "",
        facebook: "",
        youtube: "",
        x: "",
        status: "",
        nickname: "",
    });
    const segments = window.location.pathname.split("/").filter(Boolean);

    const secondToLast = segments[segments.length - 2] || null;

    const tabs = [
        { id: "about", label: "About" },
        { id: "posts", label: "Posts" },
        { id: "spaces", label: "Spaces" },
        { id: "comments", label: "Comments" },
    ];

    const getMemberDetails = async () => {
        try {
            const res = await axios.get(`/api/member/user/${secondToLast}`);
            setMember(res.data);
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
        }
    };

    useEffect(() => {
        getMemberDetails();
    }, []);

    useEffect(() => {
        if (member && Object.keys(member).length > 0) {
            setFormData({
                first_name: member.user?.first_name || "",
                last_name: member.user?.last_name || "",
                email: member.user?.email || "",
                website: member?.website || "",
                bio: member?.bio || "",
                instagram: member?.social?.instagram || "",
                facebook: member?.social?.facebook || "",
                youtube: member?.social?.youtube || "",
                x: member?.social?.x || "",
                status: member.status || "",
                nickname: member.user_name || "",
            });
        }
    }, [member]);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        console.log("Form submitted:", formData);
        // TODO: send data to backend
    };

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
                    <form
                        onSubmit={handleSubmit}
                        className="p-6 bg-gray-50 dark:bg-gray-700 rounded-xl shadow-sm space-y-4"
                    >
                        {/* Header */}
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-lg font-semibold text-gray-800 dark:text-white">
                                Edit Member
                            </h3>
                            <FaUserEdit size={18} className="text-blue-500" />
                        </div>

                        {/* Name */}
                        <div className="grid grid-cols-2 gap-4">
                            <input
                                type="text"
                                name="first_name"
                                placeholder="First Name"
                                value={formData.first_name}
                                onChange={handleChange}
                                className="px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                            />
                            <input
                                type="text"
                                name="last_name"
                                placeholder="Last Name"
                                value={formData.last_name}
                                onChange={handleChange}
                                className="px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                            />
                        </div>

                        {/* Email & Nickname */}
                        <div className="grid grid-cols-2 gap-4">
                            <input
                                type="email"
                                name="email"
                                placeholder="Email"
                                value={formData.email}
                                onChange={handleChange}
                                className="px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                            />
                            <input
                                type="text"
                                name="nickname"
                                placeholder="Nickname"
                                value={formData.nickname}
                                onChange={handleChange}
                                className="px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                            />
                        </div>

                        {/* Website */}
                        <input
                            type="url"
                            name="web_url"
                            placeholder="Website URL"
                            value={formData.web_url}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                        />

                        {/* Short Bio */}
                        <textarea
                            name="short_bio"
                            placeholder="Short Bio"
                            value={formData.short_bio}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                            rows="3"
                        ></textarea>

                        {/* Social Links */}
                        <div className="grid grid-cols-2 gap-4">
                            <div className="flex items-center gap-2">
                                <FaInstagram className="text-pink-500" />
                                <input
                                    type="url"
                                    name="instagram"
                                    placeholder="Instagram URL"
                                    value={formData.instagram}
                                    onChange={handleChange}
                                    className="flex-1 px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                                />
                            </div>
                            <div className="flex items-center gap-2">
                                <FaFacebook className="text-blue-500" />
                                <input
                                    type="url"
                                    name="facebook"
                                    placeholder="Facebook URL"
                                    value={formData.facebook}
                                    onChange={handleChange}
                                    className="flex-1 px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                                />
                            </div>
                            <div className="flex items-center gap-2">
                                <FaYoutube className="text-red-500" />
                                <input
                                    type="url"
                                    name="youtube"
                                    placeholder="YouTube URL"
                                    value={formData.youtube}
                                    onChange={handleChange}
                                    className="flex-1 px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                                />
                            </div>
                            <div className="flex items-center gap-2">
                                <FaX className="text-gray-800 dark:text-gray-200" />
                                <input
                                    type="url"
                                    name="x"
                                    placeholder="X (Twitter) URL"
                                    value={formData.x}
                                    onChange={handleChange}
                                    className="flex-1 px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                                />
                            </div>
                        </div>

                        {/* Status */}
                        <select
                            name="status"
                            value={formData.status}
                            onChange={handleChange}
                            className="w-full px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-600"
                        >
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="blocked">Blocked</option>
                        </select>

                        {/* Submit */}
                        <button
                            type="submit"
                            className="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
                        >
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default ProfileSectionEdit;

if (document.getElementById("community-profile-edit")) {
    const Index = ReactDOM.createRoot(
        document.getElementById("community-profile-edit")
    );
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <ProfileSectionEdit />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
