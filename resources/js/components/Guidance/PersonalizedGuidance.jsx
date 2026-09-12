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

    const [youtubeLink, setYoutubeLink] = useState("");
    const [details, setDetails] = useState("");
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [errors, setErrors] = useState({});

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!isPremium) {
            showMessage("This feature is for Premium members only.", "error");
            return;
        }

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
        <div className="flex-1 bg-white border border-gray-200 rounded-2xl shadow-sm p-8 flex flex-col relative overflow-hidden">
            {/* Premium badge */}
            <div className="absolute top-4 right-4 flex items-center space-x-1 bg-amber-50 border border-amber-200 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" className="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span>Premium Choice</span>
            </div>

            {/* Icon */}
            <div className="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-5 mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" className="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.6">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>

            <h3 className="text-xl font-bold text-gray-900 mb-1">Personalized Guidance</h3>
            <p className="text-sm text-gray-500 mb-6">
                Share a video of yourself playing along with a bit about your goals, and Kingsley will design a roadmap tailored to you.
            </p>

            {!isPremium ? (
                <div className="mt-auto flex items-start space-x-3 bg-amber-50 border border-amber-100 rounded-xl p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p className="text-sm text-amber-700">This feature is available exclusively to Premium members.</p>
                </div>
            ) : submitted ? (
                <div className="mt-auto flex items-start space-x-3 bg-green-50 border border-green-100 rounded-xl p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p className="text-sm font-semibold text-green-800">Submitted for review</p>
                        <p className="text-xs text-green-700 mt-0.5">Kingsley will reach out once he's reviewed your video and put together your roadmap.</p>
                    </div>
                </div>
            ) : (
                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
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
                            className="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
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
                            className="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                        ></textarea>
                        {errors.details && (
                            <p className="text-xs text-red-500 mt-1">{errors.details[0]}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={submitting}
                        className="mt-auto w-full flex items-center justify-center space-x-2 bg-amber-500 hover:bg-amber-600 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-4 rounded-xl transition text-sm"
                    >
                        <span>{submitting ? "Submitting..." : "Submit for Review"}</span>
                        {!submitting && (
                            <svg xmlns="http://www.w3.org/2000/svg" className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        )}
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
