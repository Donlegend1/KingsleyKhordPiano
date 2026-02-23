import ReactDOM from "react-dom/client";
import React, { useState } from "react";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const FreeCall = () => {
    const { showMessage } = useFlashMessage();
    const authUser = window.authUser || {};
    const isPremium = authUser?.is_premium || authUser?.premium;

    const [showModal, setShowModal] = useState(false);

    const handleScheduleClick = () => {
        if (!isPremium) {
            showMessage(
                "This feature is only available to premium users.",
                "error",
            );
            return;
        }

        // Open modal with iframe
        setShowModal(true);
    };

    return (
        <div
            className="relative w-full bg-[#FBF6E4] border-2 border-[#D6B44A]
                rounded-2xl shadow-sm p-8 flex flex-col items-center text-center min-h-[260px]"
        >
            {/* Premium Badge */}
            <span
                className="absolute -top-3 px-4 py-1 text-xs font-semibold
                     bg-[#D6B44A] text-black rounded-full"
            >
                Premium Choice
            </span>

            {/* Icons */}
            <div className="flex items-center gap-4 mb-5 text-gray-800">
                <i className="fa fa-headphones text-xl"></i>
                <i className="fa fa-keyboard text-xl"></i>
            </div>

            {/* Title */}
            <h3 className="text-xl font-semibold text-gray-900 mb-3">
                Personalized Guidance
            </h3>

            {/* Description */}
            <p className="text-gray-700 text-sm leading-relaxed max-w-xs mb-2">
                Get on a{" "}
                <span className="font-semibold">10-minute Discovery Call</span>
                with Kingsley Khord
            </p>

            <p className="text-gray-600 text-sm max-w-xs mb-8">
                Talk 1-on-1 to map out your unique musical journey.
            </p>

            {/* CTA */}
            <button
                onClick={handleScheduleClick}
                className="mt-auto px-6 py-2.5 bg-[#D6B44A] text-black text-sm
                   rounded-full hover:bg-[#C2A13F] transition
                   inline-flex items-center gap-2"
            >
                Schedule Call
                <i className="fa fa-angle-right"></i>
            </button>

            {/* MODAL (unchanged) */}
            {showModal && (
                <div
                    className="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
                    onClick={() => setShowModal(false)}
                >
                    <div
                        className="bg-white rounded-xl shadow-xl max-w-3xl w-full relative"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <button
                            onClick={() => setShowModal(false)}
                            className="absolute right-4 top-3 text-gray-600 text-xl"
                        >
                            &times;
                        </button>

                        <iframe
                            src="https://calendar.google.com/calendar/appointments/schedules/AcZssZ0VKbR_cb5DfipW_nRZiGtwsXkBlbwwG8q4kutzKRqaVO9-AdBCzb3ltzCS3BqotzPnKRCIGpoV?gv=true"
                            className="w-full h-[520px] rounded-b-xl"
                            allow="fullscreen"
                        />
                    </div>
                </div>
            )}
        </div>
    );
};

export default FreeCall;

// Mount to DOM
if (document.getElementById("free-call")) {
    const Index = ReactDOM.createRoot(document.getElementById("free-call"));
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <FreeCall />
            </FlashMessageProvider>
        </React.StrictMode>,
    );
}
