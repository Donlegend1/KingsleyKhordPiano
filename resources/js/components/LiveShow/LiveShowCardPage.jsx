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
            } catch (error) {
                console.error("Failed to fetch live shows:", error);
            }
        };

        fetchVideos();
    }, []);

    const handleVideoClick = (video) => {
        if (video.access_type === "premium" && !isPremium) {
            showMessage(
                "This video is only available to premium users!",
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
            <video
                src={selectedVideo.video_url}
                controls
                className="w-full max-h-[500px] rounded-xl shadow-lg"
            />
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
