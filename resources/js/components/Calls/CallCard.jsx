import React, { useState } from "react";
import ReactDOM from "react-dom/client";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import { KeyboardMusic } from "lucide-react";

const CallCard = () => {
    const { showMessage } = useFlashMessage();
    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;

    const handleClick = (e) => {
        if (!isPremium) {
            e.preventDefault();
            showMessage(
                "You must be a Premium member to access Piano Coaching.",
                "error",
            );
        }
    };

    return (
        <>
            <a
                href="/member/live-coaching"
                onClick={handleClick}
                className="block transition-shadow hover:shadow-md"
            >
                <div className="min-h-[120px] h-full flex justify-between items-center gap-3 p-6 bg-[#2A2E35] rounded-lg shadow-sm border border-gray-700">
                    <div className="flex items-center space-x-4 min-w-0">
                        <div className="flex items-center justify-center w-12 h-12 rounded-md bg-[#353A42] border border-gray-600 shadow-sm flex-shrink-0">
                            <KeyboardMusic className="w-6 h-6 text-gray-200" />
                        </div>
                        <div className="min-w-0">
                            <div className="flex items-center gap-2">
                                <h4 className="font-semibold text-white text-[20px] font-sf truncate">
                                    Piano Coaching
                                </h4>

                                <img
                                    src="/icons/diamondred.png"
                                    alt="Premium Icon"
                                    className="w-4 h-4 flex-shrink-0"
                                />
                            </div>

                            <p className="text-sm text-gray-400 my-5 font-sf">
                                One on One Live Session
                            </p>
                        </div>
                    </div>

                    <i className="fa fa-angle-right text-gray-400 text-lg flex-shrink-0"></i>
                </div>
            </a>
        </>
    );
};

export default CallCard;

if (document.getElementById("premium-call-button")) {
    const root = ReactDOM.createRoot(
        document.getElementById("premium-call-button"),
    );

    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <CallCard />
            </FlashMessageProvider>
        </React.StrictMode>,
    );
}
