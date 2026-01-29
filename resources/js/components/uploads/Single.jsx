import React, { useEffect, useState } from "react";
import ReactDOM from "react-dom/client";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const SingleUpload = () => {
    const [upload, setUpload] = useState(null);

    useEffect(() => {
        if (window.uploadData) {
            setUpload(window.uploadData);
        }
    }, []);

    const renderVideoPlayer = () => {
        if (!upload?.video_type || !upload?.video_url) {
            return (
                <div className="mb-4 text-gray-500">
                    No video available.
                </div>
            );
        }

        switch (upload.video_type) {
            case "iframe":
                return (
                    <div
                        className="mb-4 w-full rounded overflow-hidden"
                        dangerouslySetInnerHTML={{
                            __html: upload.video_url,
                        }}
                    />
                );

            case "google":
                return (
                    <iframe
                        className="w-full h-[400px] rounded"
                        src={`https://drive.google.com/file/d/${upload.video_url}/preview`}
                        allow="autoplay"
                        allowFullScreen
                    />
                );

            case "youtube":
                return (
                    <iframe
                        className="w-full h-[400px] rounded"
                        src={`https://www.youtube.com/embed/${upload.video_url}`}
                        title="YouTube video player"
                        frameBorder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                    />
                );

            case "local":
                return (
                    <video
                        controls
                        className="w-full h-[400px] rounded bg-black"
                    >
                        <source
                            src={`/uploads/videos/${upload.video_url}`}
                            type="video/mp4"
                        />
                        Your browser does not support HTML5 video.
                    </video>
                );

            default:
                return (
                    <div className="mb-4 text-gray-500">
                        Unsupported video type.
                    </div>
                );
        }
    };

    if (!upload) {
        return (
            <div className="text-gray-400">
                Loading video…
            </div>
        );
    }

    return (
        <div className="w-full">
            {renderVideoPlayer()}
        </div>
    );
};

export default SingleUpload;

if (document.getElementById("uploads-single")) {
    const Index = ReactDOM.createRoot(document.getElementById("uploads-single"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <SingleUpload />
            </FlashMessageProvider>
        </React.StrictMode>,
    );
}
