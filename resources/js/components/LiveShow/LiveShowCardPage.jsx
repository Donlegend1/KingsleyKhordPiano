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
    const [notifySubscribed, setNotifySubscribed] = useState(false);
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

    const handleCreateLiveShowNotification = async () => {
        if (notifySubscribed) return;

        try {
            await axios.post("/api/notifications/subscribe-live-shows");
            setNotifySubscribed(true);
            showMessage("You'll be notified about upcoming live shows!", "success");
        } catch (error) {
            console.error("Failed to subscribe to live show notifications:", error);
            showMessage("Failed to subscribe for notifications. Please try again later.", "error");
        }
    };
    return (
        <section className="max-w-7xl mx-auto px-6 py-16">
            {/* Video Modal Overlay */}
            {selectedVideo && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 md:p-10">
                    <div 
                        className="absolute inset-0 bg-black/90 backdrop-blur-xl animate-in fade-in duration-300"
                        onClick={() => setSelectedVideo(null)}
                    ></div>
                    
                    <div className="relative w-full max-w-5xl bg-gray-900 rounded-3xl overflow-hidden shadow-[0_0_100px_rgba(0,0,0,0.5)] border border-white/10 animate-in zoom-in-95 duration-300">
                        {/* Modal Header */}
                        <div className="absolute top-0 left-0 right-0 z-20 p-6 flex justify-between items-center bg-gradient-to-b from-black/80 to-transparent">
                            <h3 className="text-xl font-bold text-white drop-shadow-lg truncate pr-10">
                                {selectedVideo.title}
                            </h3>
                            <button 
                                onClick={() => setSelectedVideo(null)}
                                className="p-2 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full text-white transition-all hover:rotate-90"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {/* Video Player Container */}
                        <div className="aspect-video w-full bg-black">
                            {fileId ? (
                                <iframe
                                    src={`https://drive.google.com/file/d/${fileId}/preview`}
                                    className="w-full h-full"
                                    frameBorder="0"
                                    allow="autoplay; encrypted-media; fullscreen"
                                    allowFullScreen
                                    title={selectedVideo.title}
                                />
                            ) : (
                                <div className="h-full flex items-center justify-center text-red-500 font-bold">
                                    Invalid video URL or restricted access
                                </div>
                            )}
                        </div>
                        
                        {/* Modal Footer Info */}
                        <div className="p-6 bg-gray-900 border-t border-white/5 flex items-center justify-between">
                            <span className="text-white/50 text-sm font-medium">
                                Recorded on {dayjs(selectedVideo.start_time).format("MMMM D, YYYY")}
                            </span>
                            <div className="flex items-center gap-2 text-[#FFD736] text-xs font-black uppercase tracking-widest">
                                <img src="/icons/wave.png" alt="Icon" className="w-4 h-4 invert opacity-50" />
                                Premium Session
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Empty State */}
            {videos.length === 0 && (
                <div className="flex flex-col items-center justify-center bg-indigo-50/60 rounded-2xl py-20 px-8 text-center">
                    <div className="relative mb-6">
                        <div className="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center">
                            <svg className="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div className="absolute -bottom-1 -right-1 w-7 h-7 bg-indigo-600 rounded-full flex items-center justify-center">
                            <svg className="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M15.536 8.464a5 5 0 010 7.072M12 18v2m0-18v2m6.364 1.636l-1.414 1.414M7.05 7.05L5.636 5.636M21 12h-2M5 12H3"/>
                            </svg>
                        </div>
                    </div>
                    <h3 className="text-xl font-bold text-gray-800 mb-2">No Live Shows Available</h3>
                    <p className="text-gray-500 text-sm max-w-xs mb-7">Check back later for upcoming live sessions and workshops.</p>
                    <button
                        onClick={() => handleCreateLiveShowNotification()}
                        disabled={notifySubscribed}
                        className={`flex items-center gap-2 text-sm font-semibold px-6 py-3 rounded-xl shadow-md transition-all duration-150 ${
                            notifySubscribed
                                ? "bg-gray-200 text-gray-500 shadow-none cursor-not-allowed"
                                : "bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-200 hover:scale-[1.02] active:scale-95"
                        }`}
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        {notifySubscribed ? "You'll be notified" : "Notify Me"}
                    </button>
                </div>
            )}

            {/* Video Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {videos.map((video) => {
                    const date = dayjs(video.start_time);
                    const isRestricted = video.access_type === "premium" && !isPremium;
                    const isPast = date.isBefore(dayjs());
                    const isToday = date.isSame(dayjs(), 'day');
                    const countdown = countdowns[video.id] || { days: 0, hours: 0, minutes: 0, seconds: 0 };
                    const isSession = video.category === "session";

                    return (
                        <div
                            key={video.id}
                            className="bg-white rounded-2xl border border-gray-100 shadow-sm p-7 flex flex-col transition-all duration-300 hover:shadow-md hover:-translate-y-0.5"
                        >
                            <div className="flex items-center justify-between mb-4">
                                <div className="flex items-center gap-3">
                                    <div className={`w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 ${isSession ? "bg-indigo-50" : "bg-red-50"}`}>
                                        <i className={`fa-solid ${isSession ? "fa-users text-indigo-600" : "fa-tower-broadcast text-red-500"} text-sm`}></i>
                                    </div>
                                    {isPast ? (
                                        <span className="text-sky-700 text-[11px] font-bold uppercase tracking-widest bg-sky-50 border border-sky-200 px-2.5 py-1 rounded-full">Past Show</span>
                                    ) : isSession ? (
                                        <span className="text-indigo-600 text-[11px] font-bold uppercase tracking-widest bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-full">Live Session</span>
                                    ) : (
                                        <span className="text-amber-600 text-[11px] font-bold uppercase tracking-widest bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">Live Event</span>
                                    )}
                                </div>
                                {video.access_type === "premium" && (
                                    <div className="flex items-center gap-1 bg-red-50 border border-red-200 text-red-600 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full">
                                        <img src="/icons/diamondred.png" alt="Premium" className="w-3 h-3" />
                                        Premium
                                    </div>
                                )}
                            </div>

                            <h3 className="text-lg font-bold text-gray-900 leading-tight mb-3 line-clamp-2">
                                {video.title}
                            </h3>

                            <div className="flex items-center gap-4 text-gray-500 text-sm font-medium mb-6">
                                <div className="flex items-center gap-1.5">
                                    <i className="fa-regular fa-calendar text-gray-400"></i>
                                    {date.format("MMM DD, YYYY")}
                                </div>
                                <div className="flex items-center gap-1.5">
                                    <i className="fa-regular fa-clock text-gray-400"></i>
                                    {date.format("HH:mm")} (WAT)
                                </div>
                            </div>

                            {/* Custom Content Area (Past vs Future) */}
                            {isPast ? (
                                <div className="mt-auto">
                                    {video.recording_url ? (
                                        <button
                                            onClick={() => handleVideoClick(video)}
                                            className="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl transition-colors shadow-sm shadow-red-200"
                                        >
                                            <i className="fa-solid fa-play-circle"></i>
                                            <span className="font-bold text-sm uppercase tracking-widest">Watch Recording</span>
                                        </button>
                                    ) : (
                                        <div className="flex items-center justify-center gap-2 bg-gray-50 border border-gray-100 text-gray-400 py-3 rounded-xl">
                                            <i className="fa-solid fa-circle-info"></i>
                                            <span className="font-bold text-xs uppercase tracking-widest">Live Show Ended</span>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <>
                                    {/* Session Seats Filled vs Event Countdown */}
                                    {isSession ? (
                                        <div className="flex items-center justify-between mb-6 bg-gray-50 border border-gray-100 rounded-xl px-4 py-3.5">
                                            <div className="flex items-center gap-2">
                                                <i className="fa-solid fa-users text-indigo-500 text-sm"></i>
                                                <div>
                                                    <p className="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Seats Filled</p>
                                                    <p className="text-base font-bold text-gray-900 leading-none">
                                                        {video.bookings_count ?? 0} / {video.max_slots ?? 5}
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Overlapping avatars */}
                                            <div className="flex -space-x-2">
                                                {video.bookings && video.bookings.slice(0, 3).map((b) => {
                                                    const avatar = b.passport
                                                        ? (b.passport.startsWith('http') ? b.passport : '/' + b.passport)
                                                        : null;
                                                    return avatar ? (
                                                        <img
                                                            key={b.id}
                                                            src={avatar}
                                                            className="h-7 w-7 rounded-full ring-2 ring-white object-cover"
                                                            alt="Avatar"
                                                            title={`${b.first_name} ${b.last_name}`}
                                                            onError={(e) => { e.target.style.display = 'none'; }}
                                                        />
                                                    ) : (
                                                        <div key={b.id} className="h-7 w-7 rounded-full ring-2 ring-white bg-indigo-100 flex items-center justify-center text-[10px] font-bold text-indigo-600">
                                                            {(b.first_name || "?").charAt(0).toUpperCase()}
                                                        </div>
                                                    );
                                                })}
                                                {video.bookings_count > 3 && (
                                                    <div className="h-7 w-7 rounded-full ring-2 ring-white bg-indigo-500 flex items-center justify-center text-[10px] font-bold text-white">
                                                        +{video.bookings_count - 3}
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    ) : (
                                        /* Countdown Blocks */
                                        <div className="grid grid-cols-4 gap-2 mb-6">
                                            {[
                                                { label: 'Days', val: countdown.days },
                                                { label: 'Hours', val: countdown.hours },
                                                { label: 'Min', val: countdown.minutes },
                                                { label: 'Sec', val: countdown.seconds }
                                            ].map((unit, i) => (
                                                <div key={i} className="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-2.5 border border-gray-100">
                                                    <span className="text-lg font-bold text-gray-900 tabular-nums">{unit.val ?? '-'}</span>
                                                    <span className="text-[9px] font-bold text-gray-400 uppercase tracking-wide">{unit.label}</span>
                                                </div>
                                            ))}
                                        </div>
                                    )}

                                    {/* Action Buttons for future shows */}
                                    <div className="flex flex-col gap-2.5 mt-auto">
                                        {isSession ? (
                                            isToday ? (
                                                <a
                                                    href={isRestricted ? "#" : video.zoom_link}
                                                    onClick={isRestricted ? handleVideoClick : undefined}
                                                    className="w-full py-3.5 rounded-xl bg-indigo-600 text-white text-sm font-bold text-center uppercase tracking-widest shadow-sm shadow-indigo-200 hover:bg-indigo-700 transition-all duration-200"
                                                >
                                                    Join the Live Show
                                                </a>
                                            ) : video.booked_by_user ? (
                                                <a
                                                    href={isRestricted ? "#" : video.zoom_link}
                                                    onClick={isRestricted ? handleVideoClick : undefined}
                                                    className="w-full py-3.5 rounded-xl bg-green-600 text-white text-sm font-bold text-center uppercase tracking-widest shadow-sm shadow-green-200 hover:bg-green-700 transition-all duration-200"
                                                >
                                                    Enter Live Session
                                                </a>
                                            ) : video.bookings_count >= video.max_slots ? (
                                                <button
                                                    disabled
                                                    className="w-full py-3.5 rounded-xl bg-gray-100 text-gray-400 text-sm font-bold text-center uppercase tracking-widest cursor-not-allowed"
                                                >
                                                    Slot Full
                                                </button>
                                            ) : (
                                                <a
                                                    href={isRestricted ? "#" : `/member/live-session/${video.id}/confirm`}
                                                    onClick={isRestricted ? (e) => { e.preventDefault(); handleVideoClick(video); } : undefined}
                                                    className="w-full py-3.5 rounded-xl bg-indigo-600 text-white text-sm font-bold text-center uppercase tracking-widest shadow-sm shadow-indigo-200 hover:bg-indigo-700 transition-all duration-200"
                                                >
                                                    Join the Slot
                                                </a>
                                            )
                                        ) : (
                                            <a
                                                href={isRestricted ? "#" : video.zoom_link}
                                                onClick={isRestricted ? handleVideoClick : undefined}
                                                className="w-full py-3.5 rounded-xl bg-red-500 text-white text-sm font-bold text-center uppercase tracking-widest shadow-sm shadow-red-200 hover:bg-red-600 transition-all duration-200"
                                            >
                                                {video.zoom_link ? "Join Live Show" : "Enter Live Show"}
                                            </a>
                                        )}
                                        <a
                                            href={isRestricted ? "#" : `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(video.title)}&dates=${date.format("YYYYMMDDTHHmmss")}/${date.add(1, "hour").format("YYYYMMDDTHHmmss")}&details=${encodeURIComponent(video.zoom_link || video.category)}`}
                                            onClick={isRestricted ? handleVideoClick : undefined}
                                            className="w-full py-3 rounded-xl border border-gray-200 text-gray-600 text-xs font-bold text-center uppercase tracking-widest hover:bg-gray-50 transition-all duration-200"
                                        >
                                            <i className="fa-regular fa-calendar-plus mr-2"></i>
                                            Add to Calendar
                                        </a>
                                    </div>
                                </>
                            )}
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
