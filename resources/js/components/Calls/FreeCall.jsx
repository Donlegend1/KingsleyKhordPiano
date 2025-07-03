import ReactDOM from "react-dom/client";
import React, { useState, useEffect, useRef } from "react";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const FreeCall = () => {
    const [showModal, setShowModal] = useState(false);
    const { showMessage } = useFlashMessage();
    const calendlyRef = useRef(null);
    const authUser = window.authUser || {};
    const isPremium = authUser?.is_premium || authUser?.premium;

    const handleOpenModal = () => {
        if (!isPremium) {
            showMessage("This feature is only available to premium users.", "error");
            return;
        }
        setShowModal(true);
    };

    useEffect(() => {
        if (showModal && window.Calendly && calendlyRef.current) {
            window.Calendly.initInlineWidget({
                url: "https://calendly.com/shedrackogwuche5/30min",
                parentElement: calendlyRef.current,
                prefill: {},
                utm: {}
            });
        }
    }, [showModal]);

    return (
        <>
            {/* Call to Action Card */}
            <div className="flex flex-col items-center justify-center p-4 sm:p-6 bg-white border border-gray-300 rounded-lg w-full min-h-[200px]">
                <div className="text-center my-4 sm:my-6 mx-4 sm:mx-10">
                    <p className="text-gray-800  mb-2 text-base sm:text-lg font-bold">
                        Get on a 10 mins Discovery Call with Kingsley Khod
                    </p>
                    <p className="text-gray-500 text-sm sm:text-base leading-relaxed">
                        This call will help you find clarity in your piano journey so you know where to commence your learning from.
                    </p>
                </div>
                <button
                    onClick={handleOpenModal}
                    className="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
                >
                    Schedule Call <i className="fa fa-angle-right ml-2" aria-hidden="true"></i>
                </button>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center px-2">
                    <div className="bg-white rounded-lg w-full max-w-3xl p-4 sm:p-6 relative shadow-lg">
                        <button
                            className="absolute top-3 right-3 text-gray-600 hover:text-red-500"
                            onClick={() => setShowModal(false)}
                        >
                            <i className="fa fa-times text-xl" aria-hidden="true"></i>
                        </button>

                        {/* Calendly Widget Container */}
                        <div ref={calendlyRef} style={{ minWidth: "100%", height: "500px" }}></div>
                    </div>
                </div>
            )}
        </>
    );
};

export default FreeCall;

// Load Calendly script once
if (!document.querySelector("script[src*='calendly.com']")) {
    const script = document.createElement("script");
    script.src = "https://assets.calendly.com/assets/external/widget.js";
    script.async = true;
    document.head.appendChild(script);
}

// Mount to DOM
if (document.getElementById("free-call")) {
    const Index = ReactDOM.createRoot(document.getElementById("free-call"));
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <FreeCall />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
