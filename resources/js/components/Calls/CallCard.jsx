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
                href="/member/premium-booking"
                onClick={handleClick}
                className="block transition-shadow hover:shadow-md"
            >
                <div className="min-h-[120px] h-full flex justify-between items-center p-6 bg-[#F3F5F6] rounded-lg shadow-sm border">
                    <div className="flex items-center space-x-4">
                        <div className="flex items-center justify-center w-12 h-12 rounded-md bg-[#F3F5F6] border shadow-sm">
                            <KeyboardMusic className="w-6 h-6 text-[#435065]" />
                        </div>
                        <div>
                            <div className="flex items-center justify-center gap-2">
                                <h4 className="font-semibold text-[#435065] text-[20px] font-sf">
                                    Piano Coaching
                                </h4>

                                <img
                                    src="/icons/diamondred.png"
                                    alt="Premium Icon"
                                    className="w-4 h-4"
                                />
                            </div>

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
