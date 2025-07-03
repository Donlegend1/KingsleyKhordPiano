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
        axios.get("/api/live-shows").then((res) => setVideos(res.data));
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
        <section className="max-w-6xl mx-auto px-4 py-5">

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

            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                {videos.map((video) => (
                    <div
                        key={video.id}
                        onClick={() => handleVideoClick(video)}
                        className="cursor-pointer bg-white p-5 rounded-xl shadow-md hover:shadow-xl transition-all duration-200"
                    >
                        <div className="aspect-video bg-gray-200 mb-4 rounded flex items-center justify-center text-gray-400 text-sm">
                            {video.access_type === "premium"
                                ? "Premium Content"
                                : "Free Content"}
                        </div>
                        <h4 className="font-bold text-lg text-gray-800 mb-1">
                            {video.title}
                        </h4>
                        <p className="text-sm text-gray-500">
                            {dayjs(video.created_at).format("MMM D, YYYY")}
                        </p>
                        <span
                            className={`inline-block mt-3 text-xs font-medium px-3 py-1 rounded-full ${
                                video.access_type === "premium"
                                    ? "bg-yellow-100 text-yellow-800"
                                    : "bg-green-100 text-green-700"
                            }`}
                        >
                            {video.access_type === "premium"
                                ? "Premium"
                                : "Free"}
                        </span>
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
