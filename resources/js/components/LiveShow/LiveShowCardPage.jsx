import React, { useEffect, useState } from "react";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import relativeTime from "dayjs/plugin/relativeTime";
import isoWeek from "dayjs/plugin/isoWeek";

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
    const [countdowns, setCountdowns] = useState({});
    const { showMessage } = useFlashMessage();

    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;
    dayjs.extend(duration);
    dayjs.extend(relativeTime);
    dayjs.extend(isoWeek);

    useEffect(() => {
        const fetchVideos = async () => {
            try {
                const res = await axios.get("/api/live-shows");
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

    useEffect(() => {
        const interval = setInterval(() => {
            setCountdowns((prev) => {
                const updated = { ...prev };
                videos.forEach((show) => {
                    updated[show.id] = calculateCountdown(show.start_time);
                });
                return updated;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, [videos]);

    const calculateCountdown = (startTime) => {
        const now = dayjs();
        const eventTime = dayjs(startTime);
        const diff = eventTime.diff(now);

        if (diff <= 0) {
            return { days: 0, hours: 0, minutes: 0, seconds: 0 };
        }

        const dur = dayjs.duration(diff);
        return {
            days: Math.floor(dur.asDays()),
            hours: dur.hours(),
            minutes: dur.minutes(),
            seconds: dur.seconds(),
        };
    };

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
                {videos.map((video) => {
                    const date = dayjs(video.start_time);
                    const isRestricted =
                        video.access_type === "premium" && !isPremium;
                    const isPast = date.isBefore(dayjs()); // Has start time passed?
                    const hasVideoLink = !!video.zoom_link; // Video link exists?

                    return (
                        <div
                            key={video.id}
                            className="relative h-56 rounded-lg overflow-hidden shadow-lg group"
                            style={{
                                backgroundImage: `url('${
                                    isPast && video.thumbnail
                                        ? video.thumbnail
                                        : "/images/Background.jpg"
                                }')`,
                                backgroundSize: "cover",
                                backgroundPosition: "center",
                            }}
                        >
                            {/* Premium badge */}
                            {video.access_type === "premium" && (
                                <div className="absolute top-2 right-2 bg-black text-white text-xs font-semibold px-2 py-1 rounded shadow-md z-20">
                                    <div className="flex gap-2">
                                        <img
                                            src="/icons/diamondred.png"
                                            alt="Premium Icon"
                                            className="w-4 h-4"
                                        />
                                    </div>
                                </div>
                            )}

                            <div className="absolute inset-0 bg-white bg-opacity-50 group-hover:bg-opacity-60 transition duration-300"></div>

                            <div className="relative z-10 h-full flex flex-col justify-between p-4 text-black">
                                {/* Title and Time */}
                                <div>
                                    <h3 className="text-md font-bold flex items-center space-x-2">
                                        <img
                                            src="/icons/wave.png"
                                            alt="Music Icon"
                                            className="w-5 h-5"
                                        />
                                        <span>{video.title}</span>
                                    </h3>
                                    <p className="text-sm mt-1">
                                        {date.format("DD-MM-YYYY")} |{" "}
                                        {date.format("HH:mm")}
                                    </p>
                                </div>

                                {/* ✅ Logic for past/future/live shows */}
                                {isPast ? (
                                    hasVideoLink ? (
                                        // ✅ Show "Watch Now" if video link exists after start time
                                        <div
                                            className="flex items-center justify-center mt-4 cursor-pointer"
                                            onClick={(e) =>
                                                handleVideoClick(video)
                                            }
                                        >
                                            <span className="text-red-600 font-bold text-sm flex items-center gap-2">
                                                <i className="fa fa-play-circle animate-pulse"></i>
                                                Watch Now
                                            </span>
                                        </div>
                                    ) : (
                                        // ✅ Show "Live Show Ended" if past but no video link
                                        <div className="flex items-center justify-center mt-4">
                                            <span className="text-gray-600 font-semibold text-sm flex items-center gap-2">
                                                <i className="fa fa-clock"></i>
                                                Live Show Ended
                                            </span>
                                        </div>
                                    )
                                ) : (
                                    // ✅ Future show — show countdown and join buttons
                                    <>
                                        <div className="flex items-center justify-between font-semibold mt-4 divide-x divide-gray-300 text-center text-sm">
                                            <div className="px-2">
                                                <p>
                                                    {countdowns[video.id]
                                                        ?.days ?? "-"}
                                                </p>
                                                <p className="text-gray-600">
                                                    Days
                                                </p>
                                            </div>
                                            <div className="px-2">
                                                <p>
                                                    {countdowns[video.id]
                                                        ?.hours ?? "-"}
                                                </p>
                                                <p className="text-gray-600">
                                                    Hours
                                                </p>
                                            </div>
                                            <div className="px-2">
                                                <p>
                                                    {countdowns[video.id]
                                                        ?.minutes ?? "-"}
                                                </p>
                                                <p className="text-gray-600">
                                                    Minutes
                                                </p>
                                            </div>
                                            <div className="px-2">
                                                <p>
                                                    {countdowns[video.id]
                                                        ?.seconds ?? "-"}
                                                </p>
                                                <p className="text-gray-600">
                                                    Seconds
                                                </p>
                                            </div>
                                        </div>

                                        <div className="mt-3 flex space-x-3">
                                            <a
                                                href={
                                                    isRestricted
                                                        ? "#"
                                                        : video.zoom_link
                                                }
                                                onClick={
                                                    isRestricted
                                                        ? handleVideoClick
                                                        : undefined
                                                }
                                                className="px-4 py-1 text-sm rounded-full border border-[#404348] bg-[#404348] text-white transition"
                                            >
                                                {video.zoom_link
                                                    ? "Join Live Show"
                                                    : "Enter Live Show"}
                                            </a>
                                            <a
                                                href={
                                                    isRestricted
                                                        ? "#"
                                                        : `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(
                                                              video.title
                                                          )}&dates=${date.format(
                                                              "YYYYMMDDTHHmmss"
                                                          )}/${date
                                                              .add(1, "hour")
                                                              .format(
                                                                  "YYYYMMDDTHHmmss"
                                                              )}&details=${encodeURIComponent(
                                                              video.zoom_link
                                                          )}`
                                                }
                                                onClick={
                                                    isRestricted
                                                        ? handleVideoClick
                                                        : undefined
                                                }
                                                className="px-4 py-1 text-sm rounded-full border border-[#404348] text-[#404348] hover:bg-[#404348] hover:text-white transition"
                                            >
                                                Add to Calendar
                                            </a>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                    );
                })}
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
