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
import { formatLocalTime } from "../../utils/formatRelativeTime";

const PremiumVideoSection = () => {
    const [videos, setVideos] = useState([]);
    const [countdowns, setCountdowns] = useState({});
    const [notifySubscribed, setNotifySubscribed] = useState(false);
    const [activeTab, setActiveTab] = useState("upcoming");
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
            } catch (error) {
                console.error("Failed to fetch live shows:", error);
            }
        };

        const fetchSubscription = async () => {
            try {
                const res = await axios.get("/member/notifications/live-shows/status");
                setNotifySubscribed(Boolean(res.data?.subscribed));
            } catch (error) {
                // Not subscribed yet
            }
        };

        fetchVideos();
        fetchSubscription();
    }, []);

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
        }
    };

    const handleCreateLiveShowNotification = async () => {
        if (notifySubscribed) return;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
            await axios.post(
                "/member/notifications/subscribe-live-shows",
                {},
                {
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                }
            );
            setNotifySubscribed(true);
            showMessage("You'll be notified about upcoming live shows!", "success");
        } catch (error) {
            console.error("Failed to subscribe to live show notifications:", error);
            if (error.response?.status === 400) {
                setNotifySubscribed(true);
                return;
            }
            showMessage("Failed to subscribe for notifications. Please try again later.", "error");
        }
    };

    const now = dayjs();
    const isComingSoon = (v) => v.status === "coming_soon";
    const byStartTimeAsc = (a, b) => dayjs(a.start_time).valueOf() - dayjs(b.start_time).valueOf();

    const pastShows = videos.filter((v) => dayjs(v.start_time).isBefore(now) && v.recording_url);
    const openSessions = videos
        .filter((v) => v.category === "session" && !isComingSoon(v) && !dayjs(v.start_time).isBefore(now))
        .sort(byStartTimeAsc);
    const openEvents = videos
        .filter((v) => v.category === "event" && !isComingSoon(v) && !dayjs(v.start_time).isBefore(now))
        .sort(byStartTimeAsc);
    const comingSoonShows = videos
        .filter((v) => isComingSoon(v) && !dayjs(v.start_time).isBefore(now))
        .sort(byStartTimeAsc);

    const featuredSession = openSessions[0] || null;
    const featuredEvent = openEvents[0] || null;
    const hasFeatured = Boolean(featuredSession || featuredEvent);

    const scheduleList = [
        ...openSessions.slice(1),
        ...openEvents.slice(1),
        ...comingSoonShows,
    ].sort(byStartTimeAsc);

    const renderSessionCard = (video) => {
        const date = dayjs(video.start_time);
        const isRestricted = video.access_type === "premium" && !isPremium;
        const isToday = date.isSame(now, "day");
        const { localDate, localTime, tzLabel } = formatLocalTime(video.start_time);
        const countdown = countdowns[video.id] || { days: 0, hours: 0, minutes: 0, seconds: 0 };

        let button;
        if (isToday) {
            button = (
                <a
                    href={isRestricted ? "#" : video.zoom_link}
                    onClick={isRestricted ? handleVideoClick : undefined}
                    className="w-full inline-flex items-center justify-center gap-2 py-3 rounded-lg bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-semibold transition-colors"
                >
                    Join Live Coaching Call
                    <i className="fa-solid fa-chevron-right text-xs"></i>
                </a>
            );
        } else if (video.booked_by_user) {
            button = (
                <a
                    href={isRestricted ? "#" : video.zoom_link}
                    onClick={isRestricted ? handleVideoClick : undefined}
                    className="w-full inline-flex items-center justify-center gap-2 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors"
                >
                    Enter Live Session
                </a>
            );
        } else if (video.bookings_count >= video.max_slots) {
            button = (
                <button
                    disabled
                    className="w-full py-3 rounded-lg bg-gray-100 text-gray-400 text-sm font-semibold cursor-not-allowed"
                >
                    Slot Full
                </button>
            );
        } else {
            button = (
                <a
                    href={isRestricted ? "#" : `/member/live-session/${video.id}/confirm`}
                    onClick={isRestricted ? (e) => { e.preventDefault(); handleVideoClick(video); } : undefined}
                    className="w-full inline-flex items-center justify-center gap-2 py-3 rounded-lg bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-semibold uppercase tracking-wide transition-colors"
                >
                    Book a Slot
                </a>
            );
        }

        return (
            <div
                key={video.id}
                className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col"
            >
                <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <i className="fa-solid fa-user-group text-indigo-400"></i>
                        </div>
                        <span className="text-indigo-600 text-[11px] font-bold uppercase tracking-widest bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-full">Live Session</span>
                    </div>
                </div>
                <h3 className="font-bold text-gray-900 text-base mb-3">{video.title}</h3>
                <div className="flex items-center gap-4 text-gray-500 text-sm font-medium mb-6">
                    <div className="flex items-center gap-1.5">
                        <i className="fa-regular fa-calendar text-gray-400"></i>
                        {localDate}
                    </div>
                    <div className="flex items-center gap-1.5">
                        <i className="fa-regular fa-clock text-gray-400"></i>
                        {localTime} ({tzLabel})
                    </div>
                </div>

                <div className="grid grid-cols-4 gap-2 mb-6">
                    {[
                        { label: "Days", val: countdown.days },
                        { label: "Hours", val: countdown.hours },
                        { label: "Min", val: countdown.minutes },
                        { label: "Sec", val: countdown.seconds },
                    ].map((unit, i) => (
                        <div key={i} className="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-2.5 border border-gray-100">
                            <span className="text-lg font-bold text-gray-900 tabular-nums">{unit.val ?? "-"}</span>
                            <span className="text-[9px] font-bold text-gray-400 uppercase tracking-wide">{unit.label}</span>
                        </div>
                    ))}
                </div>

                <div className="mt-auto">{button}</div>
            </div>
        );
    };

    const renderEventCard = (video) => {
        const isRestricted = video.access_type === "premium" && !isPremium;
        const countdown = countdowns[video.id] || { days: 0, hours: 0, minutes: 0, seconds: 0 };
        const { localDate, localTime, tzLabel } = formatLocalTime(video.start_time);

        return (
            <div
                key={video.id}
                className="bg-white rounded-2xl border border-gray-100 shadow-sm p-7 flex flex-col transition-all duration-300 hover:shadow-md hover:-translate-y-0.5"
            >
                <div className="flex items-center justify-between mb-4">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-50">
                            <i className="fa-solid fa-tower-broadcast text-red-500 text-sm"></i>
                        </div>
                        <span className="text-amber-600 text-[11px] font-bold uppercase tracking-widest bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">Live Event</span>
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
                        {localDate}
                    </div>
                    <div className="flex items-center gap-1.5">
                        <i className="fa-regular fa-clock text-gray-400"></i>
                        {localTime} ({tzLabel})
                    </div>
                </div>

                <div className="grid grid-cols-4 gap-2 mb-6">
                    {[
                        { label: "Days", val: countdown.days },
                        { label: "Hours", val: countdown.hours },
                        { label: "Min", val: countdown.minutes },
                        { label: "Sec", val: countdown.seconds },
                    ].map((unit, i) => (
                        <div key={i} className="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-2.5 border border-gray-100">
                            <span className="text-lg font-bold text-gray-900 tabular-nums">{unit.val ?? "-"}</span>
                            <span className="text-[9px] font-bold text-gray-400 uppercase tracking-wide">{unit.label}</span>
                        </div>
                    ))}
                </div>

                <div className="flex flex-col gap-2.5 mt-auto">
                    <a
                        href={isRestricted ? "#" : video.zoom_link}
                        onClick={isRestricted ? handleVideoClick : undefined}
                        target={isRestricted ? undefined : "_blank"}
                        rel={isRestricted ? undefined : "noopener noreferrer"}
                        className="w-full py-3.5 rounded-xl bg-red-500 text-white text-sm font-bold text-center uppercase tracking-widest shadow-sm shadow-red-200 hover:bg-red-600 transition-all duration-200"
                    >
                        Register
                    </a>
                </div>
            </div>
        );
    };

    const renderPastCard = (video) => {
        const isRestricted = video.access_type === "premium" && !isPremium;
        const isSession = video.category === "session";
        const { localDate, localTime, tzLabel } = formatLocalTime(video.start_time);

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
                        <span className="text-sky-700 text-[11px] font-bold uppercase tracking-widest bg-sky-50 border border-sky-200 px-2.5 py-1 rounded-full">Past Show</span>
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
                        {localDate}
                    </div>
                    <div className="flex items-center gap-1.5">
                        <i className="fa-regular fa-clock text-gray-400"></i>
                        {localTime} ({tzLabel})
                    </div>
                </div>

                <div className="mt-auto">
                    {video.recording_url ? (
                        <a
                            href={isRestricted ? "#" : `/member/live-show/${video.id}/recording`}
                            onClick={isRestricted ? (e) => { e.preventDefault(); handleVideoClick(video); } : undefined}
                            className="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl transition-colors shadow-sm shadow-red-200"
                        >
                            <i className="fa-solid fa-play-circle"></i>
                            <span className="font-bold text-sm uppercase tracking-widest">Watch Recording</span>
                        </a>
                    ) : (
                        <div className="flex items-center justify-center gap-2 bg-gray-50 border border-gray-100 text-gray-400 py-3 rounded-xl">
                            <i className="fa-solid fa-circle-info"></i>
                            <span className="font-bold text-xs uppercase tracking-widest">Live Show Ended</span>
                        </div>
                    )}
                </div>
            </div>
        );
    };

    const hasUpcoming = hasFeatured || scheduleList.length > 0;

    return (
        <section className="max-w-7xl mx-auto px-6 pt-6 pb-16">

            {/* Tabs */}
            <div className="inline-flex bg-white border border-gray-200 rounded-lg p-1 mb-8 shadow-sm">
                <button
                    onClick={() => setActiveTab("upcoming")}
                    className={`px-8 py-2.5 rounded-md text-sm font-semibold transition-colors ${
                        activeTab === "upcoming" ? "bg-[#1447A6] text-white shadow-sm" : "text-gray-500 hover:text-gray-700"
                    }`}
                >
                    Upcoming Events
                </button>
                <button
                    onClick={() => setActiveTab("past")}
                    className={`px-8 py-2.5 rounded-md text-sm font-semibold transition-colors ${
                        activeTab === "past" ? "bg-[#1447A6] text-white shadow-sm" : "text-gray-500 hover:text-gray-700"
                    }`}
                >
                    Past Events
                </button>
            </div>

            {activeTab === "upcoming" ? (
                <>
                    {!hasUpcoming && (
                        <div className="flex flex-col items-center justify-center bg-blue-50/60 rounded-2xl py-20 px-8 text-center mb-12">
                            <div className="relative mb-6">
                                <div className="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center">
                                    <svg className="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div className="absolute -bottom-1 -right-1 w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center">
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
                                        : "bg-blue-600 hover:bg-blue-700 text-white shadow-blue-200 hover:scale-[1.02] active:scale-95"
                                }`}
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                {notifySubscribed ? "You'll be notified" : "Notify Me"}
                            </button>
                        </div>
                    )}

                    {hasFeatured && (
                        <div className="mb-14">
                            <span className="inline-block text-xs font-semibold text-gray-500 bg-white border border-gray-200 px-3 py-1 rounded-full mb-4">
                                Now Open
                            </span>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                {featuredEvent && renderEventCard(featuredEvent)}
                                {featuredSession && renderSessionCard(featuredSession)}
                            </div>
                        </div>
                    )}

                    {scheduleList.length > 0 && (
                        <div>
                            <h2 className="text-2xl font-extrabold text-gray-900 mb-1">Upcoming Schedule</h2>
                            <p className="text-sm text-gray-500 mb-4">Future sessions are visible for planning.</p>

                            <div className="flex flex-col gap-2">
                                {scheduleList.map((show) => {
                                    const d = dayjs(show.start_time);
                                    const comingSoon = isComingSoon(show);
                                    const isSession = show.category === "session";
                                    const isRestricted = show.access_type === "premium" && !isPremium;
                                    const Wrapper = comingSoon ? "div" : "a";
                                    const wrapperProps = comingSoon
                                        ? {}
                                        : isSession
                                        ? {
                                              href: isRestricted ? "#" : `/member/live-session/${show.id}/confirm`,
                                              onClick: isRestricted ? (e) => { e.preventDefault(); handleVideoClick(show); } : undefined,
                                          }
                                        : {
                                              href: isRestricted ? "#" : show.zoom_link,
                                              target: isRestricted ? undefined : "_blank",
                                              rel: isRestricted ? undefined : "noopener noreferrer",
                                              onClick: isRestricted ? handleVideoClick : undefined,
                                          };

                                    return (
                                        <Wrapper
                                            key={show.id}
                                            {...wrapperProps}
                                            className={`flex items-center justify-between bg-gray-50 rounded-xl px-5 py-4 ${
                                                comingSoon ? "" : "hover:bg-gray-100 transition-colors"
                                            }`}
                                        >
                                            <div>
                                                <div className="flex items-center gap-1.5 mb-1">
                                                    <span className={`text-[9px] font-semibold px-1.5 py-px rounded border ${
                                                        isSession
                                                            ? "text-indigo-600 bg-indigo-50 border-indigo-200"
                                                            : "text-amber-600 bg-amber-50 border-amber-200"
                                                    }`}>
                                                        {isSession ? "Live Session" : "Live Event"}
                                                    </span>
                                                    <p className="font-bold text-gray-900 text-sm">{show.title}</p>
                                                </div>
                                                <p className="text-xs text-gray-400">{d.format("ddd, MMM D YYYY")} &middot; {d.format("h:mm A")}</p>
                                            </div>
                                            <span className={`text-xs font-semibold px-3 py-1 rounded-full border flex-shrink-0 ${
                                                comingSoon
                                                    ? "text-gray-500 bg-white border-gray-200"
                                                    : "text-emerald-700 bg-emerald-50 border-emerald-200"
                                            }`}>
                                                {comingSoon ? "Coming Soon" : "Open"}
                                            </span>
                                        </Wrapper>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </>
            ) : (
                <>
                    {pastShows.length === 0 ? (
                        <div className="flex flex-col items-center justify-center bg-gray-50 rounded-2xl py-20 px-8 text-center">
                            <h3 className="text-xl font-bold text-gray-800 mb-2">No Past Shows Yet</h3>
                            <p className="text-gray-500 text-sm max-w-xs">Shows you've attended will appear here once they've ended.</p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {pastShows.map(renderPastCard)}
                        </div>
                    )}
                </>
            )}
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
