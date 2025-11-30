import React, { useState } from "react";
import ReactDOM from "react-dom/client";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const CallCard = () => {
    const { showMessage } = useFlashMessage();
    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;

    const handleClick = (e) => {
        if (!isPremium) {
            e.preventDefault();
            showMessage(
                "You must be a Premium member to access Piano Coaching.",
                "error"
            );
        }
    };

    return (
        <>
            <a
                href="/member/premium-booking"
                onClick={handleClick}
                className="block transition-shadow hover:shadow-md"
            >
                <div className="min-h-[120px] h-full flex justify-between items-center p-6 bg-[#F3F5F6] rounded-lg shadow-sm border">
                    <div className="flex items-center space-x-4">
                        <img
                            src="/images/banner.jpg"
                            className="w-12 h-12 object-contain"
                        />
                        <div>
                            <h4 className="font-semibold text-[#435065] text-[20px] font-sf">
                                Piano Coaching
                            </h4>
                            <p className="text-sm text-[#5E6779] my-5 font-sf">
                               One on One Live Session 
                            </p>
                        </div>
                    </div>

                    <i className="fa fa-angle-right text-gray-400 text-lg"></i>
                </div>
            </a>
        </>
    );
};

export default CallCard;

if (document.getElementById("premium-call-button")) {
    const root = ReactDOM.createRoot(
        document.getElementById("premium-call-button")
    );

    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <CallCard />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
