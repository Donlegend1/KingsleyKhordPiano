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
        <section className="max-w-7xl mx-auto px-4 py-10">
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                {shows && shows.length > 0 ? (
                    shows.map((show) => {
                        const date = dayjs(show.start_time);
                        const isRestricted =
                            show.access_type === "premium" && !isPremium;
                        return (
                            <div
                                key={show.id}
                                className="relative h-56 rounded-lg overflow-hidden shadow-lg group"
                                style={{
                                    backgroundImage: `url('/images/Background.jpg')`,
                                    backgroundSize: "cover",
                                    backgroundPosition: "center",
                                }}
                            >
                                {/* Premium badge */}
                                {show.access_type === "premium" && (
                                    <div className="absolute top-2 right-2 bg-black text-white text-xs font-semibold px-2 py-1 rounded shadow-md z-20">
                                        <div className="flex gap-2">

                                            <img
                                                src="/icons/diamondred.png"
                                                alt="Premium Icon"
                                                class="w-4 h-4"
                                            ></img>
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
                                            <span>{show.title}</span>
                                        </h3>
                                        <p className="text-sm mt-1">
                                            {date.format("DD-MM-YYYY")} |{" "}
                                            {date.format("HH:mm")}
                                        </p>
                                    </div>

                                    {/* Date Breakdown */}
                                    {/* Countdown Timer */}
                                    <div className="flex items-center justify-between font-semibold mt-4 divide-x divide-gray-300 text-center text-sm">
                                        <div className="px-2">
                                            <p>
                                                {countdowns[show.id]?.days ??
                                                    "-"}
                                            </p>
                                            <p className="text-gray-600">
                                                Days
                                            </p>
                                        </div>
                                        <div className="px-2">
                                            <p>
                                                {countdowns[show.id]?.hours ??
                                                    "-"}
                                            </p>
                                            <p className="text-gray-600">
                                                Hours
                                            </p>
                                        </div>
                                        <div className="px-2">
                                            <p>
                                                {countdowns[show.id]?.minutes ??
                                                    "-"}
                                            </p>
                                            <p className="text-gray-600">
                                                Minutes
                                            </p>
                                        </div>
                                        <div className="px-2">
                                            <p>
                                                {countdowns[show.id]?.seconds ??
                                                    "-"}
                                            </p>
                                            <p className="text-gray-600">
                                                Seconds
                                            </p>
                                        </div>
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="mt-3 flex space-x-3">
                                        <a
                                            href={
                                                isRestricted
                                                    ? "#"
                                                    : show.zoom_link
                                            }
                                            onClick={
                                                isRestricted
                                                    ? handleRestrictedClick
                                                    : undefined
                                            }
                                            className="px-4 py-1 text-sm rounded-full border border-[#404348] bg-[#404348] text-white transition"
                                        >
                                            Enter Live Show
                                        </a>
                                        <a
                                            href={
                                                isRestricted
                                                    ? "#"
                                                    : `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(
                                                          show.title
                                                      )}&dates=${date.format(
                                                          "YYYYMMDDTHHmmss"
                                                      )}/${date
                                                          .add(1, "hour")
                                                          .format(
                                                              "YYYYMMDDTHHmmss"
                                                          )}&details=${encodeURIComponent(
                                                          show.zoom_link
                                                      )}`
                                            }
                                            onClick={
                                                isRestricted
                                                    ? handleRestrictedClick
                                                    : undefined
                                            }
                                            className="px-4 py-1 text-sm rounded-full border border-[#404348] text-[#404348] hover:bg-[#404348] hover:text-white transition"
                                        >
                                            Add to Calendar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        );
                    })
                ) : (
                    <div className="col-span-3 text-center text-black border border-gray-500 rounded-sm bg-slate-400 my-auto p-4">
                        No live shows available at the moment.
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
