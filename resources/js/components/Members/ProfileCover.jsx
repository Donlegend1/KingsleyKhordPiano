import React, { useState } from "react";
import axios from "axios";
import { FaUpload } from "react-icons/fa6";
import { FaTruckLoading } from "react-icons/fa";
import {
    FlashMessageProvider,
    useFlashMessage,
} from "../Alert/FlashMessageContext";

const ProfileCover = ({ member }) => {
    const [coverImage, setCoverImage] = useState(
        member.user?.passport || "/avatar1.jpg"
    );
    const [selectedFile, setSelectedFile] = useState(null);
    const [loading, setLoading] = useState(false);
    const { showMessage } = useFlashMessage;

    const imageClick = () => {
        document.getElementById("passport").click();
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const imageUrl = URL.createObjectURL(file);
            setCoverImage(imageUrl);
            setSelectedFile(file);
        }
    };

    const handleSaveImage = async () => {
        if (!selectedFile) return;
        setLoading(true);

        const formData = new FormData();
        formData.append("passport", selectedFile);
        formData.append("user_id", member.user_id);

        try {
            const res = await axios.post("/api/member/passport", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            showMessage("Passport updated successfully", "success");
            setSelectedFile(null);
        } catch (error) {
            console.error(error);
            showMessage(error.response?.data?.message, "error");
        } finally {
            setLoading(false);
        }
    };

    return (
        <>
            {/* Cover Section */}
            <div className="relative bg-gray-300 h-48">
                <button
                    type="button"
                    className="absolute top-3 right-3 bg-white text-sm px-3 py-1 rounded shadow"
                    onClick={imageClick}
                >
                    Upload Cover
                </button>
                <input
                    type="file"
                    name="passport"
                    id="passport"
                    className="hidden"
                    onChange={handleImageChange}
                />
            </div>

            {/* Profile Info */}
            <div className="bg-white dark:bg-gray-800 p-6 relative">
                <div className="flex items-center space-x-4">
                    <img
                        src={coverImage}
                        alt="Profile"
                        className="z-30 w-24 h-24 rounded-full border-4 border-white dark:border-gray-800 -mt-12 object-cover"
                    />
                </div>

                {/* Save Button */}
                {selectedFile && (
                    <button
                        onClick={handleSaveImage}
                        disabled={loading}
                        className="mt-4 bg-black dark:bg-gray-600 text-white px-2 py-2 rounded shadow hover:bg-gray-900"
                    >
                        {loading ? <FaTruckLoading /> : <FaUpload />}
                    </button>
                )}
            </div>
        </>
    );
};

export default ProfileCover;

if (document.getElementById("profile-cover")) {
    const Index = ReactDOM.createRoot(document.getElementById("profile-cover"));
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <ProfileCover />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
