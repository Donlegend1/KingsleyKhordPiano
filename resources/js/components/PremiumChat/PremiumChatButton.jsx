import {
    formatRelativeTime,
    capitaliseAndRemoveHyphen,
} from "../../utils/formatRelativeTime";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import React from "react";
import ReactDOM from "react-dom/client";
const PremiumChatButton = () => {
    const {showMessage} = useFlashMessage();

    const handleClick = () => {

        if (window.authUser?.premium) {
            window.location.href = "/member/premium-chat";
        } else {
            showMessage(
             "You need to be a premium member to access Premium Chat.",
             "error"
            );
        }
    };

    console.log(window.authUser?.premium, 'auth user premium status');
    return (
     <a
            onClick={handleClick}
            className="fixed bottom-6 right-6 bg-blue-600 text-white w-14 h-14 
                rounded-full flex items-center justify-center shadow-lg 
                hover:bg-blue-700 transition-all duration-300 z-50"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                className="w-7 h-7"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M7.5 8.25h9m-9 3h6m-8.25 6.75L6 18a2.25 2.25 0 01-2.25-2.25V6A2.25 2.25 0 016 3.75h12A2.25 2.25 0 0120.25 6v9.75A2.25 2.25 0 0118 18h-6l-4.75 4.5z"
                />
            </svg>
        </a>
    );
};

if (document.getElementById("premium-chat-button")) {
    const Index = ReactDOM.createRoot(document.getElementById("premium-chat-button"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <PremiumChatButton />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
