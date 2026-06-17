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

            {/* Video Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                {videos.map((video) => {
                    const date = dayjs(video.start_time);
                    const isRestricted = video.access_type === "premium" && !isPremium;
                    const isPast = date.isBefore(dayjs());
                    const isToday = date.isSame(dayjs(), 'day');
                    const hasVideoLink = !!video.recording_url; // or zoom_link for live
                    const countdown = countdowns[video.id] || { days: 0, hours: 0, minutes: 0, seconds: 0 };

                    return (
                        <div
                            key={video.id}
                            className="relative group h-[420px] rounded-3xl overflow-hidden shadow-2xl transition-all duration-500 hover:-translate-y-3 hover:shadow-[0_20px_50px_rgba(255,215,54,0.15)] bg-gray-900"
                        >
                            {/* Background Image with dynamic overlay */}
                            <div 
                                className="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                                style={{ 
                                    backgroundImage: `url('${isPast && video.thumbnail ? video.thumbnail : "/images/Background.jpg"}')` 
                                }}
                            ></div>
                            <div className="absolute inset-0 bg-gradient-to-t from-black via-black/80 to-black/20 opacity-95 transition-opacity duration-500 group-hover:opacity-100"></div>

                            {/* Premium Badge */}
                            {video.access_type === "premium" && (
                                <div className="absolute top-5 right-5 z-20">
                                    <div className="flex items-center gap-2 bg-red-600/90 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-xl border border-white/20">
                                        <img src="/icons/diamondred.png" alt="Premium" className="w-3.5 h-3.5 animate-pulse" />
                                        Premium
                                    </div>
                                </div>
                            )}

                            {/* Content Container */}
                            <div className="relative z-10 h-full flex flex-col justify-end p-8">
                                 {/* Header Info */}
                                 <div className="mb-6 transform transition-transform duration-500 group-hover:-translate-y-2">
                                     <div className="flex items-center gap-3 mb-2">
                                         {video.category === "session" ? (
                                             <div className="p-2 bg-indigo-600/20 backdrop-blur-md rounded-lg border border-indigo-600/30">
                                                 <i className="fa-solid fa-users text-indigo-400 text-sm"></i>
                                             </div>
                                         ) : (
                                             <div className="p-2 bg-red-600/20 backdrop-blur-md rounded-lg border border-red-600/30">
                                                 <i className="fa-solid fa-tower-broadcast text-red-500 text-sm"></i>
                                             </div>
                                         )}
                                         {isPast ? (
                                             <span className="text-sky-400 text-xs font-black uppercase tracking-widest bg-sky-400/10 px-2.5 py-1 rounded-md border border-sky-400/20 shadow-[0_0_15px_rgba(56,189,248,0.1)]">Past Show</span>
                                         ) : video.category === "session" ? (
                                             <span className="text-indigo-400 text-xs font-bold uppercase tracking-widest bg-indigo-400/10 px-2.5 py-1 rounded-md border border-indigo-400/20 shadow-[0_0_15px_rgba(79,70,229,0.1)]">Live Session</span>
                                         ) : (
                                             <span className="text-yellow-400 text-xs font-bold uppercase tracking-widest bg-yellow-400/10 px-2.5 py-1 rounded-md border border-yellow-400/20 shadow-[0_0_15px_rgba(250,204,21,0.1)]">Live Event</span>
                                         )}
                                     </div>
                                     <h3 className="text-2xl font-black text-white leading-tight mb-2 tracking-tight group-hover:text-[#FFD736] transition-colors line-clamp-2">
                                         {video.title}
                                     </h3>
                                     <div className="flex items-center gap-4 text-white/70 text-sm font-medium">
                                         <div className="flex items-center gap-1.5">
                                             <i className="fa-regular fa-calendar text-[#FFD736]"></i>
                                             {date.format("MMM DD, YYYY")}
                                         </div>
                                         <div className="flex items-center gap-1.5">
                                             <i className="fa-regular fa-clock text-[#FFD736]"></i>
                                             {date.format("HH:mm")} (WAT)
                                         </div>
                                     </div>
                                 </div>

                                 {/* Custom Content Area (Past vs Future) */}
                                 {isPast ? (
                                     <div className="mb-8">
                                         {video.recording_url ? (
                                             <div 
                                                 onClick={() => handleVideoClick(video)}
                                                 className="flex items-center justify-center gap-3 bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl cursor-pointer transition-colors shadow-lg group/btn"
                                             >
                                                 <i className="fa-solid fa-play-circle text-xl animate-pulse group-hover/btn:scale-110 transition-transform"></i>
                                                 <span className="font-black text-sm uppercase tracking-widest">Watch Recording</span>
                                             </div>
                                         ) : (
                                             <div className="flex items-center justify-center gap-3 bg-white/5 backdrop-blur-md border border-white/10 text-white/50 py-3 rounded-xl">
                                                 <i className="fa-solid fa-circle-info mt-0.5"></i>
                                                 <span className="font-bold text-xs uppercase tracking-widest">Live Show Ended</span>
                                             </div>
                                         )}
                                     </div>
                                 ) : (
                                     <>
                                         {/* Session Seats Filled vs Event Countdown */}
                                         {video.category === "session" ? (
                                             <div className="flex flex-col gap-3 mb-8 transform transition-all duration-500 group-hover:-translate-y-1">
                                                 <div className="flex items-center justify-between bg-white/5 backdrop-blur-xl rounded-2xl p-3 border border-white/10 shadow-lg">
                                                     <div className="flex items-center gap-2">
                                                         <div className="w-7 h-7 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-300">
                                                             <i className="fa-solid fa-users text-xs"></i>
                                                         </div>
                                                         <div className="flex flex-col">
                                                             <span className="text-[9px] font-bold text-white/50 uppercase tracking-wider">Seats Filled</span>
                                                             <span className="text-xs font-black text-white">{video.bookings_count ?? 0} / {video.max_slots ?? 5}</span>
                                                         </div>
                                                     </div>
                                                     
                                                     {/* Overlapping avatars */}
                                                     <div className="flex -space-x-1.5 overflow-hidden">
                                                         {video.bookings && video.bookings.slice(0, 3).map((b) => {
                                                             const avatar = b.passport 
                                                                 ? (b.passport.startsWith('http') ? b.passport : '/' + b.passport) 
                                                                 : '/images/default-avatar.png';
                                                             return (
                                                                 <img 
                                                                     key={b.id} 
                                                                     src={avatar} 
                                                                     className="inline-block h-6 w-6 rounded-full ring-2 ring-gray-950 object-cover" 
                                                                     alt="Avatar"
                                                                     title={`${b.first_name} ${b.last_name}`}
                                                                     onError={(e) => { e.target.src = '/images/default-avatar.png'; }}
                                                                 />
                                                             );
                                                         })}
                                                         {video.bookings_count > 3 && (
                                                             <div className="inline-flex items-center justify-center h-6 w-6 rounded-full ring-2 ring-gray-950 bg-indigo-600 text-white text-[9px] font-black">
                                                                 +{video.bookings_count - 3}
                                                             </div>
                                                         )}
                                                     </div>
                                                 </div>
                                             </div>
                                         ) : (
                                             /* Countdown Blocks */
                                             <div className="grid grid-cols-4 gap-2 mb-8 transform transition-all duration-500 group-hover:-translate-y-1">
                                                 {[
                                                     { label: 'Days', val: countdown.days },
                                                     { label: 'Hours', val: countdown.hours },
                                                     { label: 'Min', val: countdown.minutes },
                                                     { label: 'Sec', val: countdown.seconds }
                                                 ].map((unit, i) => (
                                                     <div key={i} className="flex flex-col items-center justify-center bg-white/5 backdrop-blur-xl rounded-2xl p-2 border border-white/10 shadow-lg">
                                                         <span className="text-xl font-black text-white tabular-nums">{unit.val ?? '-'}</span>
                                                         <span className="text-[9px] font-bold text-white/50 uppercase tracking-tighter">{unit.label}</span>
                                                     </div>
                                                 ))}
                                             </div>
                                         )}

                                         {/* Action Buttons for future shows */}
                                         <div className="flex flex-col gap-3">
                                             {video.category === "session" ? (
                                                 isToday ? (
                                                     <a
                                                         href={isRestricted ? "#" : video.zoom_link}
                                                         onClick={isRestricted ? handleVideoClick : undefined}
                                                         className="w-full py-3.5 rounded-xl bg-indigo-600 text-white text-sm font-black text-center uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 hover:scale-[1.02] active:scale-95 transition-all duration-300"
                                                     >
                                                         Join the Live Show
                                                     </a>
                                                 ) : video.booked_by_user ? (
                                                     <a
                                                         href={isRestricted ? "#" : video.zoom_link}
                                                         onClick={isRestricted ? handleVideoClick : undefined}
                                                         className="w-full py-3.5 rounded-xl bg-green-600 text-white text-sm font-black text-center uppercase tracking-widest shadow-xl shadow-green-500/20 hover:bg-green-700 hover:scale-[1.02] active:scale-95 transition-all duration-300"
                                                     >
                                                         Enter Live Session
                                                     </a>
                                                 ) : video.bookings_count >= video.max_slots ? (
                                                     <button
                                                         disabled
                                                         className="w-full py-3.5 rounded-xl bg-gray-700 text-gray-400 text-sm font-black text-center uppercase tracking-widest cursor-not-allowed"
                                                     >
                                                         Slot Full
                                                     </button>
                                                 ) : (
                                                     <a
                                                         href={isRestricted ? "#" : `/member/live-session/${video.id}/confirm`}
                                                         onClick={isRestricted ? (e) => { e.preventDefault(); handleVideoClick(video); } : undefined}
                                                         className="w-full py-3.5 rounded-xl bg-indigo-600 text-white text-sm font-black text-center uppercase tracking-widest shadow-xl shadow-indigo-500/20 hover:bg-indigo-700 hover:scale-[1.02] active:scale-95 transition-all duration-300"
                                                     >
                                                         Join the Slot
                                                     </a>
                                                 )
                                             ) : (
                                                 <a
                                                     href={isRestricted ? "#" : video.zoom_link}
                                                     onClick={isRestricted ? handleVideoClick : undefined}
                                                     className="w-full py-3.5 rounded-xl bg-red-600 text-white text-sm font-black text-center uppercase tracking-widest shadow-xl shadow-red-500/20 hover:bg-red-700 hover:scale-[1.02] active:scale-95 transition-all duration-300"
                                                 >
                                                     {video.zoom_link ? "Join Live Show" : "Enter Live Show"}
                                                 </a>
                                             )}
                                             <a
                                                 href={isRestricted ? "#" : `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(video.title)}&dates=${date.format("YYYYMMDDTHHmmss")}/${date.add(1, "hour").format("YYYYMMDDTHHmmss")}&details=${encodeURIComponent(video.zoom_link || video.category)}`}
                                                 onClick={isRestricted ? handleVideoClick : undefined}
                                                 className="w-full py-3 rounded-xl border border-white/20 bg-white/5 backdrop-blur-md text-white text-[11px] font-bold text-center uppercase tracking-widest hover:bg-white/10 hover:border-white/40 transition-all duration-300"
                                             >
                                                 <i className="fa-regular fa-calendar-plus mr-2"></i>
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
