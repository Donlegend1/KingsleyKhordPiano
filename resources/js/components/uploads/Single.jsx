import React, { useEffect, useState, useRef } from "react";
import ReactDOM from "react-dom/client";
import {
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const SingleUpload = () => {
    const [upload, setUpload] = useState(null);
    const iframeWrapperRef = useRef(null);

    useEffect(() => {
        if (window.uploadData) {
            setUpload(window.uploadData);
        }
    }, []);

    // After the iframe HTML is injected, force override its inline styles
    useEffect(() => {
        if (upload?.video_type === "iframe" && iframeWrapperRef.current) {
            const iframe = iframeWrapperRef.current.querySelector("iframe");
            if (iframe) {
                iframe.style.position = "absolute";
                iframe.style.top = "0";
                iframe.style.left = "0";
                iframe.style.width = "100%";
                iframe.style.height = "100%";
                iframe.style.border = "0";
            }
        }
    }, [upload]);

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
                    // Outer div establishes the 16:9 aspect ratio box
                    <div className="relative w-full aspect-video rounded overflow-hidden shadow-lg bg-black">
                        {/* Inner div fills the box; iframe gets injected here */}
                        <div
                            ref={iframeWrapperRef}
                            className="absolute inset-0"
                            dangerouslySetInnerHTML={{ __html: upload.video_url }}
                        />
                    </div>
                );

            case "vimeo":
                return (
                    <iframe
                        className="w-full aspect-video rounded shadow-lg"
                        src={`https://player.vimeo.com/video/${upload.video_url}?h=0&badge=0&autopause=0&player_id=0&app_id=58479`}
                        frameBorder="0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowFullScreen
                    />
                );

            case "google":
                return (
                    <iframe
                        className="w-full aspect-video rounded shadow-lg"
                        src={`https://drive.google.com/file/d/${upload.video_url}/preview`}
                        allow="autoplay"
                        allowFullScreen
                    />
                );

            case "youtube":
                return (
                    <iframe
                        className="w-full aspect-video rounded shadow-lg"
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
                        className="w-full aspect-video rounded bg-black shadow-lg"
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
                    <div className="p-10 border-2 border-dashed border-gray-200 rounded-xl text-center text-gray-500">
                        <i className="fa fa-exclamation-circle text-2xl mb-2 block"></i>
                        Unsupported video type: {upload.video_type}
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
