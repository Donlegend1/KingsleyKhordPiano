import ReactDOM from "react-dom/client";
import React from "react";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const FreeCall = () => {
    const { showMessage } = useFlashMessage();
    const authUser = window.authUser || {};
    const isPremium = authUser?.is_premium || authUser?.premium;

    const handleScheduleClick = () => {
        if (!isPremium) {
            showMessage("This feature is only available to premium users.", "error");
            return;
        }

        // Open Google Calendar scheduling page in a new tab
        window.open(
            "https://calendar.google.com/calendar/appointments/schedules/AcZssZ0VKbR_cb5DfipW_nRZiGtwsXkBlbwwG8q4kutzKRqaVO9-AdBCzb3ltzCS3BqotzPnKRCIGpoV?gv=true",
            // "_blank"
        );
    };

    return (
        <div className="flex flex-col items-center justify-center p-4 sm:p-6 bg-white border border-gray-300 rounded-lg w-full min-h-[200px]">
            <div className="text-center my-4 sm:my-6 mx-4 sm:mx-10">
                <p className="text-gray-800 mb-2 text-base sm:text-lg font-bold">
                    Get on a 10 mins Discovery Call with Kingsley Khord
                </p>
                <p className="text-gray-500 text-sm sm:text-base leading-relaxed">
                    This call will help you find clarity in your piano journey so you know where to commence your learning from.
                </p>
            </div>
            <button
                onClick={handleScheduleClick}
                className="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
            >
                Schedule Call <i className="fa fa-angle-right ml-2" aria-hidden="true"></i>
            </button>
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
        </React.StrictMode>
    );
}
