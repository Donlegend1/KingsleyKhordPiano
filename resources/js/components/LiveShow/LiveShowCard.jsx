import React, { useEffect, useState } from "react";
import ReactDOM from 'react-dom/client';
import axios from "axios";
import dayjs from "dayjs";

const LiveShowCard = () => {
  const [shows, setShows] = useState([]);
  const [alert, setAlert] = useState(null);

  const authUser = window.authUser || {};
  const isPremium = authUser?.is_premium;

  useEffect(() => {
    axios.get('/api/live-shows').then((res) => setShows(res.data));
  }, []);

  const handleRestrictedClick = (e) => {
    e.preventDefault();
    setAlert("This content is available for premium users only.");
    setTimeout(() => setAlert(null), 4000);
  };

  return (
    <section className="max-w-7xl mx-auto px-4 py-10">
      {alert && (
        <div className="mb-6 p-4 bg-yellow-100 text-yellow-800 rounded-md text-center font-medium">
          {alert}
        </div>
      )}
      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        {shows.map((show) => {
          const date = dayjs(show.start_time);
          const isRestricted = show.access_type === "premium" && !isPremium;

          return (
            <div key={show.id} className="relative h-56 rounded-lg overflow-hidden shadow-lg group" style={{
              backgroundImage: `url('/images/Background.jpg')`,
              backgroundSize: 'cover',
              backgroundPosition: 'center'
            }}>
              <div className="absolute inset-0 bg-white bg-opacity-50 group-hover:bg-opacity-60 transition duration-300"></div>
              <div className="relative z-10 h-full flex flex-col justify-between p-4 text-black">
                <div>
                  <h3 className="text-md font-bold flex items-center space-x-2">
                    <img src="/icons/wave.png" alt="Music Icon" className="w-5 h-5" />
                    <span>{show.title}</span>
                  </h3>
                  <p className="text-sm mt-1">
                    {date.format("DD-MM-YYYY")} | {date.format("HH:mm")}
                  </p>
                </div>

                <div className="flex items-center justify-between font-semibold mt-4 divide-x divide-gray-300">
                  <div className="text-center px-3"><p>{date.format("DD")}</p><p className="text-gray-600">Day</p></div>
                  <div className="text-center px-3"><p>{date.format("WW")}</p><p className="text-gray-600">Week</p></div>
                  <div className="text-center px-3"><p>{date.format("MM")}</p><p className="text-gray-600">Month</p></div>
                  <div className="text-center px-3"><p>{date.format("YYYY")}</p><p className="text-gray-600">Year</p></div>
                </div>

                <div className="mt-3 flex space-x-3">
                  <a
                    href={isRestricted ? "#" : show.zoom_link}
                    onClick={isRestricted ? handleRestrictedClick : undefined}
                    className="px-4 py-1 text-sm rounded-full border border-[#404348] bg-[#404348] text-white transition"
                  >
                    Enter Live show
                  </a>
                  <a
                    href={isRestricted
                      ? "#"
                      : `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(show.title)}&dates=${date.format('YYYYMMDDTHHmmss')}/${date.add(1, 'hour').format('YYYYMMDDTHHmmss')}&details=${encodeURIComponent(show.zoom_link)}`
                    }
                    onClick={isRestricted ? handleRestrictedClick : undefined}
                    className="px-4 py-1 text-sm rounded-full border border-[#404348] text-[#404348] hover:bg-[#404348] hover:text-white transition"
                  >
                    Add to Calendar
                  </a>
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </section>
  );
};

export default LiveShowCard;

if (document.getElementById("live-show")) {
  const Index = ReactDOM.createRoot(document.getElementById("live-show"));
  Index.render(
    <React.StrictMode>
      <LiveShowCard />
    </React.StrictMode>
  );
}
