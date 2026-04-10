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
                                                {date.format("MMM DD, YYYY")}
                                            </div>
                                            <div className="flex items-center gap-1.5">
                                                <i className="fa-regular fa-clock text-[#FFD736]"></i>
                                                {date.format("HH:mm")}
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
                    <div className="col-span-full py-20 flex flex-col items-center justify-center bg-gray-900/50 backdrop-blur-md rounded-3xl border border-white/5 text-center">
                        <div className="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-6 border border-white/10">
                            <i className="fa-solid fa-microphone-slash text-3xl text-gray-600"></i>
                        </div>
                        <h3 className="text-xl font-bold text-white mb-2">No Live Shows Available</h3>
                        <p className="text-gray-500 max-w-xs">Check back later for upcoming live sessions and workshops.</p>
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
