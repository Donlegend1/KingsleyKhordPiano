import React, { useEffect, useState } from "react";
import dayjs from "dayjs";
import axios from "axios";
import ReactDOM from "react-dom/client";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const PremiumVideoSection = () => {
    const [videos, setVideos] = useState([]);
    const [fileId, setFileId] = useState(null);
    const [selectedVideo, setSelectedVideo] = useState(null);
    const { showMessage } = useFlashMessage();

    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;

    useEffect(() => {
        const fetchVideos = async () => {
            try {
                const res = await axios.get("/api/live-shows", {
                    params: { filter: "previous" },
                });
                setVideos(res.data);
                console.log("Fetched live shows:", res.data);
            } catch (error) {
                console.error("Failed to fetch live shows:", error);
            }
        };

        fetchVideos();
    }, []);

    const extractGoogleDriveFileId = (url) => {
        console.log("Extracting file ID from URL:", url);
        const regex = /(?:file\/d\/|open\?id=)([a-zA-Z0-9_-]+)/;
        const match = url.match(regex);
        return match ? match[1] : null;
    };

    useEffect(() => {
        if (selectedVideo && selectedVideo.recording_url) {
            const id = extractGoogleDriveFileId(selectedVideo?.recording_url);
            setFileId(id);
        } else {
            setFileId(null);
        }
    }, [selectedVideo]);

    const handleVideoClick = (video) => {
        if (!isPremium) {
            showMessage(
                "Please upgrade to premium to watch this video",
                "error"
            );
            return;
        }
        setSelectedVideo(video);
    };
    return (
        <section className="max-w-6xl mx-auto px-4 py-8">
            {/* Selected Video Player */}
            {selectedVideo && (
                <div className="mb-12">
                    {fileId ? (
                        <div className="aspect-video w-full rounded-xl overflow-hidden shadow-lg bg-black">
                            <iframe
                                src={`https://drive.google.com/file/d/${fileId}/preview`}
                                className="w-full h-full"
                                frameBorder="0"
                                allow="autoplay; encrypted-media; fullscreen"
                                allowFullScreen
                                title={selectedVideo.title}
                            />
                        </div>
                    ) : (
                        <p className="text-red-600">Invalid video URL</p>
                    )}
                    <h3 className="text-2xl font-semibold mt-6 text-gray-900">
                        {selectedVideo.title}
                    </h3>
                    <p className="text-sm text-gray-500 mt-1">
                        {dayjs(selectedVideo.created_at).format("MMMM D, YYYY")}
                    </p>
                </div>
            )}

            {/* Video Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                {videos.map((video) => (
                    <button
                        key={video.id}
                        onClick={() => handleVideoClick(video)}
                        className={`group text-left rounded-lg overflow-hidden border transition-all
                            ${
                                selectedVideo?.id === video.id
                                    ? "ring-2 ring-blue-500 shadow-lg"
                                    : "hover:shadow-md"
                            }`}
                    >
                        <div
                            key={video.id}
                            onClick={() => handleVideoClick(video)}
                            className="cursor-pointer bg-white p-4 rounded-xl shadow-md hover:shadow-xl transition duration-300"
                        >
                            {/* Thumbnail Placeholder */}
                            <div className="aspect-video bg-gray-100 rounded-lg mb-4 flex items-center justify-center text-gray-500 text-sm">
                                {video.access_type === "premium"
                                    ? "Premium Content"
                                    : "Free Content"}
                            </div>

                            {/* Video Info */}
                            <h4 className="font-bold text-lg text-gray-800 mb-1">
                                {video.title}
                            </h4>
                            <p className="text-sm text-gray-500">
                                {dayjs(video.created_at).format("MMM D, YYYY")}
                            </p>

                            {/* Access Type Badge (Unified Position) */}
                            {video.access_type === "premium" ? (
                                <span className="inline-flex items-center gap-2 mt-3 text-xs font-medium px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                    <img
                                        src="/icons/diamondred.png"
                                        alt="Premium Icon"
                                        className="w-4 h-4"
                                    />
                                    Premium
                                </span>
                            ) : (
                                <span className="inline-block mt-3 text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-green-700">
                                    Free
                                </span>
                            )}
                        </div>
                    </button>
                ))}
            </div>
        </section>
    );
};

export default PremiumVideoSection;

if (document.getElementById("live-show-page")) {
    const Index = ReactDOM.createRoot(
        document.getElementById("live-show-page")
    );
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <PremiumVideoSection />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
