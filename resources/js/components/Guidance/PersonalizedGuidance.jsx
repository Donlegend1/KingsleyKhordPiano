import React from "react";
import ReactDOM from "react-dom/client";
import { FlashMessageProvider } from "../Alert/FlashMessageContext";

const PersonalizedGuidance = ({ guidanceUrl, paywallUrl }) => {
    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;

    const handleGetStarted = () => {
        if (!isPremium) {
            window.location.href = paywallUrl;
            return;
        }
        window.location.href = guidanceUrl;
    };

    return (
        <div className="flex-1 bg-white border-2 border-[#1447A6] rounded-xl shadow-sm p-5 flex flex-col relative overflow-hidden">
            {/* Icon */}
            <div className="w-9 h-9 bg-[#1447A6]/10 rounded-lg flex items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" className="w-4.5 h-4.5 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.6">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 3h12l4 6-10 12L2 9l4-6z"/>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M2 9h20M9 3l3 6 3-6M12 9v12"/>
                </svg>
            </div>

            <h3 className="text-base font-bold text-gray-900 mb-3">Personalized guidance</h3>

            <div className="border-t border-gray-100 mb-3"></div>

            <ul className="divide-y divide-gray-100 mb-5 flex-1">
                <li className="flex items-center gap-3 py-3">
                    <div className="w-8 h-8 bg-[#1447A6]/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span className="text-sm text-gray-700">One-on-one consultation</span>
                </li>
                <li className="flex items-center gap-3 py-3">
                    <div className="w-8 h-8 bg-[#1447A6]/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span className="text-sm text-gray-700">Accountability Plan</span>
                </li>
                <li className="flex items-center gap-3 py-3">
                    <div className="w-8 h-8 bg-[#1447A6]/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span className="text-sm text-gray-700">A Roadmap That Fits You</span>
                </li>
            </ul>

            <button
                type="button"
                onClick={handleGetStarted}
                className="w-full flex items-center justify-center bg-[#1447A6] hover:bg-[#0F3A8A] text-white font-semibold py-2.5 rounded-lg transition text-sm"
            >
                Get Customized Roadmap
            </button>
        </div>
    );
};

export default PersonalizedGuidance;

const mountEl = document.getElementById("personalized-guidance-card");
if (mountEl) {
    const root = ReactDOM.createRoot(mountEl);
    root.render(
        <FlashMessageProvider>
            <PersonalizedGuidance
                guidanceUrl={mountEl.dataset.guidanceUrl}
                paywallUrl={mountEl.dataset.paywallUrl}
            />
        </FlashMessageProvider>
    );
}
