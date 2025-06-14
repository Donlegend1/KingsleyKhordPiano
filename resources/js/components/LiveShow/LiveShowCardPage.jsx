import React, { useEffect, useState } from "react";
import dayjs from "dayjs";
import axios from "axios";
import ReactDOM from 'react-dom/client';
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const PremiumVideoSection = () => {
  const [videos, setVideos] = useState([]);
  const [selectedVideo, setSelectedVideo] = useState(null);
  const [alert, setAlert] = useState(null);
  const { showMessage } = useFlashMessage();

  const authUser = window.authUser || {};
  const isPremium = authUser?.is_premium;

  useEffect(() => {
    axios.get('/api/live-shows').then(res => setVideos(res.data));
  }, []);

  const handleVideoClick = (video) => {
    if (video.access_type === 'premium' && !isPremium) {
      setAlert("This video is only available to premium users.");
      showMessage("This video is only available to premium users!", "error");
      setTimeout(() => setAlert(null), 4000);
      return;
    }
    setSelectedVideo(video);
  };

  return (
    <section className="max-w-6xl mx-auto px-4 py-12">
      <h2 className="text-2xl font-bold mb-6 text-center">Live Session</h2>

      {alert && (
        <div className="mb-6 p-4 bg-yellow-100 text-yellow-800 rounded-md text-center font-medium">
          {alert}
        </div>
      )}

      {selectedVideo && (
        <div className="mb-8">
          <video
            src={selectedVideo.video_url}
            controls
            className="w-full max-h-[500px] rounded-lg shadow-lg"
          />
          <h3 className="text-xl font-semibold mt-4">{selectedVideo.title}</h3>
          <p className="text-sm text-gray-500">{dayjs(selectedVideo.created_at).format("MMMM D, YYYY")}</p>
        </div>
      )}

      <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        {videos.map((video) => (
          <div
            key={video.id}
            onClick={() => handleVideoClick(video)}
            className="cursor-pointer bg-white p-4 rounded-lg shadow-md hover:shadow-xl transition"
          >
            <div className="aspect-video bg-gray-200 mb-3 rounded flex items-center justify-center text-gray-400 text-sm">
              {video.access_type === "premium" ? "Premium Content" : "Free Content"}
            </div>
            <h4 className="font-bold text-lg">{video.title}</h4>
            <p className="text-sm text-gray-500">{dayjs(video.created_at).format("MMM D, YYYY")}</p>
            <span
              className={`inline-block mt-2 text-xs font-medium px-2 py-1 rounded-full ${
                video.access_type === "premium"
                  ? "bg-yellow-200 text-yellow-800"
                  : "bg-green-100 text-green-800"
              }`}
            >
              {video.access_type === "premium" ? "Premium" : "Free"}
            </span>
          </div>
        ))}
      </div>
    </section>
  );
};

export default PremiumVideoSection;

if (document.getElementById("live-show-page")) {
  const Index = ReactDOM.createRoot(document.getElementById("live-show-page"));
  Index.render(
    <React.StrictMode>
      <FlashMessageProvider>
        <PremiumVideoSection />
        </FlashMessageProvider>
    </React.StrictMode>
  );
}
