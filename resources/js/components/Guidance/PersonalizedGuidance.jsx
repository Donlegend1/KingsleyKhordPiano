import React from "react";
import ReactDOM from "react-dom/client";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const PersonalizedGuidance = () => {
    const { showMessage } = useFlashMessage();
    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;

    const handleBookCall = () => {
        if (!isPremium) {
            showMessage("This feature is for Premium members only.", "error");
        } else {
            const event = new CustomEvent("open-discovery-call-modal", { bubbles: true });
            window.dispatchEvent(event);
        }
    };

    return (
        <div className="flex-1 bg-white border border-gray-200 rounded-2xl shadow-sm p-8 flex flex-col items-center text-center relative overflow-hidden">
            {/* Premium badge */}
            <div className="absolute top-4 right-4 flex items-center space-x-1 bg-amber-50 border border-amber-200 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>Premium Choice</span>
            </div>

            {/* Icon */}
            <div className="w-20 h-20 bg-amber-100 rounded-2xl flex items-center justify-center mb-6 mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" className="w-10 h-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </div>

            <h3 className="text-xl font-bold text-gray-900 mb-6">Personalized Guidance</h3>

            {/* Features */}
            <ul className="divide-y divide-gray-100 mb-8 w-full text-left">
                <li className="flex items-center space-x-3 py-3">
                    <div className="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <span className="text-sm text-gray-700">1-on-1 consultation</span>
                </li>
                <li className="flex items-center space-x-3 py-3">
                    <div className="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span className="text-sm text-gray-700">10-minutes call</span>
                </li>
                <li className="flex items-center space-x-3 py-3">
                    <div className="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <span className="text-sm text-gray-700">Personalized Roadmap</span>
                </li>
            </ul>

            <button 
                onClick={handleBookCall}
                type="button"
                className="mt-auto w-full flex items-center justify-center space-x-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-4 rounded-xl transition text-sm"
            >
                <span>Schedule Discovery Call</span>
                <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    );
};

export default PersonalizedGuidance;

if (document.getElementById("personalized-guidance-card")) {
    const root = ReactDOM.createRoot(document.getElementById("personalized-guidance-card"));
    root.render(
        <FlashMessageProvider>
            <PersonalizedGuidance />
        </FlashMessageProvider>
    );
}
