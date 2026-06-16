import React, { useEffect, useState } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
import relativeTime from "dayjs/plugin/relativeTime";
import isoWeek from "dayjs/plugin/isoWeek";

import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import { calculateCountdown, formatLocalTime } from "@/utils/formatRelativeTime";

const LiveShowCard = () => {
    const [shows, setShows] = useState([]);
    const [countdowns, setCountdowns] = useState({});
    const { showMessage } = useFlashMessage();

    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;
    dayjs.extend(duration);
    dayjs.extend(relativeTime);
    dayjs.extend(isoWeek);

    useEffect(() => {
        const fetchShows = async () => {
            try {
                const res = await axios.get("/api/live-shows", {
                    params: { filter: "future" }, // or "previous"
                });

                setShows(res.data);

                const countdownsObj = {};
                res.data.forEach((show) => {
                    countdownsObj[show.id] = calculateCountdown(
                        show.start_time
                    );
                });

                setCountdowns(countdownsObj);
            } catch (error) {
                console.error("Error fetching live shows:", error);
            }
        };

        fetchShows();
    }, []);

    useEffect(() => {
        const interval = setInterval(() => {
            setCountdowns((prev) => {
                const updated = { ...prev };
                shows.forEach((show) => {
                    updated[show.id] = calculateCountdown(show.start_time);
                });
                return updated;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, [shows]);

    const handleRestrictedClick = (e) => {
        e.preventDefault();
        showMessage(
            "This content is available for premium users only.",
            "error"
        );
    };


    return (
        <section className="max-w-7xl mx-auto px-6 py-16">
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                {shows && shows.length > 0 ? (
                    shows.map((show) => {
                        const date = dayjs(show.start_time);
                        const isRestricted = show.access_type === "premium" && !isPremium;
                        const countdown = countdowns[show.id] || { days: 0, hours: 0, minutes: 0, seconds: 0 };
                        const { localDate, localTime, tzLabel } = formatLocalTime(show.start_time); 
                        return (
                            <div
                                key={show.id}
                                className="relative group h-[400px] rounded-3xl overflow-hidden shadow-2xl transition-all duration-500 hover:-translate-y-3 hover:shadow-[0_20px_50px_rgba(255,215,54,0.15)] bg-gray-900"
                            >
                                {/* Background Image with dynamic overlay */}
                                <div 
                                    className="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                                    style={{ backgroundImage: `url('/images/Background.jpg')` }}
                                ></div>
                                <div className="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent opacity-90 transition-opacity duration-500 group-hover:opacity-95"></div>

                                {/* Premium Badge */}
                                {show.access_type === "premium" && (
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
                                            <div className="p-2 bg-[#FFD736]/20 backdrop-blur-md rounded-lg border border-[#FFD736]/30">
                                                <img src="/icons/wave.png" alt="Icon" className="w-5 h-5 invert" />
                                            </div>
                                            <span className="text-yellow-400 text-xs font-bold uppercase tracking-widest bg-yellow-400/10 px-2 py-0.5 rounded">Live Event</span>
                                        </div>
                                        <h3 className="text-2xl font-black text-white leading-tight mb-2 tracking-tight group-hover:text-[#FFD736] transition-colors">
                                            {show.title}
                                        </h3>
                                        <div className="flex items-center gap-4 text-white/70 text-sm font-medium">
                                            <div className="flex items-center gap-1.5">
                                                <i className="fa-regular fa-calendar text-[#FFD736]"></i>
                                                {localDate}
                                            </div>
                                            <div className="flex items-center gap-1.5">
                                                <i className="fa-regular fa-clock text-[#FFD736]"></i>
                                                {localTime} {tzLabel}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Countdown Blocks */}
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

                                    {/* Action Buttons */}
                                    <div className="flex flex-col gap-3">
                                        <a
                                            href={isRestricted ? "#" : show.zoom_link}
                                            onClick={isRestricted ? handleRestrictedClick : undefined}
                                            className="w-full py-3.5 rounded-xl bg-[#FFD736] text-black text-sm font-black text-center uppercase tracking-widest shadow-xl shadow-yellow-400/20 hover:bg-white hover:scale-[1.02] active:scale-95 transition-all duration-300"
                                        >
                                            Enter Live Show
                                        </a>
                                        <a
                                            href={isRestricted ? "#" : `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(show.title)}&dates=${date.format("YYYYMMDDTHHmmss")}/${date.add(1, "hour").format("YYYYMMDDTHHmmss")}&details=${encodeURIComponent(show.zoom_link)}`}
                                            onClick={isRestricted ? handleRestrictedClick : undefined}
                                            className="w-full py-3 rounded-xl border border-white/20 bg-white/5 backdrop-blur-md text-white text-[11px] font-bold text-center uppercase tracking-widest hover:bg-white/10 hover:border-white/40 transition-all duration-300"
                                        >
                                            <i className="fa-regular fa-calendar-plus mr-2"></i>
                                            Add to Calendar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        );
                    })
                ) : (
                    <div className="col-span-full py-16 flex flex-col items-center justify-center bg-[#F3F0FF] rounded-2xl border border-[#E5DEFF] text-center">
                        {/* Calendar illustration */}
                        <div className="relative mb-6 flex items-end justify-center">
                            {/* Left cloud */}
                            <svg className="absolute left-0 bottom-4 w-14 h-10 text-[#DDD6FE] opacity-80" viewBox="0 0 80 50" fill="currentColor">
                                <ellipse cx="40" cy="35" rx="35" ry="15"/>
                                <ellipse cx="25" cy="28" rx="18" ry="14"/>
                                <ellipse cx="55" cy="25" rx="20" ry="16"/>
                            </svg>
                            {/* Right cloud */}
                            <svg className="absolute right-0 bottom-6 w-12 h-8 text-[#DDD6FE] opacity-60" viewBox="0 0 80 50" fill="currentColor">
                                <ellipse cx="40" cy="35" rx="30" ry="13"/>
                                <ellipse cx="28" cy="26" rx="16" ry="13"/>
                                <ellipse cx="52" cy="23" rx="18" ry="14"/>
                            </svg>
                            {/* Calendar icon */}
                            <div className="relative z-10">
                                <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    {/* Calendar body */}
                                    <rect x="8" y="18" width="68" height="60" rx="8" fill="white" stroke="#C4B5FD" strokeWidth="2"/>
                                    {/* Calendar header */}
                                    <rect x="8" y="18" width="68" height="20" rx="8" fill="#7C3AED"/>
                                    <rect x="8" y="30" width="68" height="8" fill="#7C3AED"/>
                                    {/* Hooks */}
                                    <rect x="25" y="10" width="6" height="16" rx="3" fill="#A78BFA"/>
                                    <rect x="59" y="10" width="6" height="16" rx="3" fill="#A78BFA"/>
                                    {/* Grid dots */}
                                    {[0,1,2,3,4,5,6,7,8].map((i) => (
                                        <rect key={i} x={20 + (i % 3) * 18} y={48 + Math.floor(i / 3) * 12} width="10" height="6" rx="2" fill="#DDD6FE"/>
                                    ))}
                                    {/* Live badge */}
                                    <circle cx="68" cy="65" r="14" fill="#7C3AED"/>
                                    <text x="68" y="70" textAnchor="middle" fontSize="10" fill="white" fontWeight="bold">((•))</text>
                                </svg>
                            </div>
                            {/* Sparkles */}
                            <span className="absolute top-0 left-8 text-[#C4B5FD] text-lg">✦</span>
                            <span className="absolute top-4 right-6 text-[#C4B5FD] text-sm">✦</span>
                        </div>

                        <h3 className="text-2xl font-bold text-[#1E1B4B] mb-2">No Live Shows Available</h3>
                        <p className="text-gray-500 max-w-xs text-sm leading-relaxed mb-6">
                            Check back later for upcoming live sessions<br/>and workshops.
                        </p>
                        <button className="flex items-center gap-2 px-6 py-2.5 rounded-full border-2 border-[#7C3AED] text-[#7C3AED] font-semibold text-sm hover:bg-[#7C3AED] hover:text-white transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            Notify Me
                        </button>
                        <p className="text-gray-400 text-xs mt-3">We'll notify you when new shows are scheduled.</p>
                    </div>
                )}
            </div>
        </section>
    );
};

export default LiveShowCard;

if (document.getElementById("live-show")) {
    const Index = ReactDOM.createRoot(document.getElementById("live-show"));
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <LiveShowCard />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
