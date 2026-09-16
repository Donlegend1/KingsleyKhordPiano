import React, { useState } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const PersonalizedGuidance = () => {
    const { showMessage } = useFlashMessage();
    const authUser = window.authUser || {};
    const isPremium = authUser?.premium;

    const [showForm, setShowForm] = useState(false);
    const [youtubeLink, setYoutubeLink] = useState("");
    const [details, setDetails] = useState("");
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [errors, setErrors] = useState({});

    const handleGetStarted = () => {
        if (!isPremium) {
            showMessage("This feature is for Premium members only.", "error");
            return;
        }
        setShowForm(true);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        setSubmitting(true);
        setErrors({});

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            await axios.post(
                "/member/personalized-guidance/submit",
                { youtube_link: youtubeLink, details },
                { headers: { "X-CSRF-TOKEN": csrfToken } }
            );
            setSubmitted(true);
            showMessage("Thanks! Your video and notes have been sent for review.", "success");
        } catch (err) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors || {});
            } else {
                showMessage(err.response?.data?.message || "Something went wrong. Please try again.", "error");
            }
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="flex-1 bg-white border-2 border-indigo-600 rounded-xl shadow-sm p-5 flex flex-col relative overflow-hidden">
            {/* Icon */}
            <div className="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" className="w-4.5 h-4.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.6">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 3h12l4 6-10 12L2 9l4-6z"/>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M2 9h20M9 3l3 6 3-6M12 9v12"/>
                </svg>
            </div>

            <h3 className="text-base font-bold text-gray-900 mb-3">Personalized guidance</h3>

            <div className="border-t border-gray-100 mb-3"></div>

            {submitted ? (
                <div className="flex-1 flex items-start space-x-3 bg-green-50 border border-green-100 rounded-lg p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p className="text-sm font-semibold text-green-800">Submitted for review</p>
                        <p className="text-xs text-green-700 mt-0.5">Kingsley will reach out once he's reviewed your video and put together your roadmap.</p>
                    </div>
                </div>
            ) : !showForm ? (
                <>
                    <ul className="divide-y divide-gray-100 mb-5 flex-1">
                        <li className="flex items-center gap-3 py-3">
                            <div className="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span className="text-sm text-gray-700">One-on-one consultation</span>
                        </li>
                        <li className="flex items-center gap-3 py-3">
                            <div className="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <span className="text-sm text-gray-700">Accountability Plan</span>
                        </li>
                        <li className="flex items-center gap-3 py-3">
                            <div className="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span className="text-sm text-gray-700">A Roadmap That Fits You</span>
                        </li>
                    </ul>

                    <button
                        type="button"
                        onClick={handleGetStarted}
                        className="w-full flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg transition text-sm"
                    >
                        Get Customized Roadmap
                    </button>
                </>
            ) : (
                <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                    <p className="text-sm text-gray-500 -mt-1 mb-1">
                        Share a video of yourself playing along with a bit about your goals, and Kingsley will design a roadmap tailored to you.
                    </p>

                    <div>
                        <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                            YouTube video link
                        </label>
                        <input
                            type="url"
                            required
                            value={youtubeLink}
                            onChange={(e) => setYoutubeLink(e.target.value)}
                            placeholder="https://youtube.com/watch?v=..."
                            className="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                        />
                        {errors.youtube_link && (
                            <p className="text-xs text-red-500 mt-1">{errors.youtube_link[0]}</p>
                        )}
                        <p className="text-xs text-gray-400 mt-1">Record yourself playing and paste the link here — unlisted links work too.</p>
                    </div>

                    <div>
                        <label className="block text-xs font-semibold text-gray-700 mb-1.5">
                            Tell us about your skill level &amp; goals
                        </label>
                        <textarea
                            rows={4}
                            value={details}
                            onChange={(e) => setDetails(e.target.value)}
                            placeholder="e.g. I've been playing for 6 months, comfortable with basic chords, and I want to be able to play by ear during worship sessions..."
                            className="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                        ></textarea>
                        {errors.details && (
                            <p className="text-xs text-red-500 mt-1">{errors.details[0]}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={submitting}
                        className="mt-auto w-full flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-2.5 rounded-lg transition text-sm"
                    >
                        {submitting ? "Submitting..." : "Submit for Review"}
                    </button>
                </form>
            )}
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
