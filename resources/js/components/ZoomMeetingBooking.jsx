import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const ZoomMeetingBooking = () => {
    const [selectedDate, setSelectedDate] = useState(null);
    const [selectedSlot, setSelectedSlot] = useState(null);
    const [meetingDetails, setMeetingDetails] = useState({
        name: "",
        email: "",
        focus: "",
        skillLevel: "Beginner",
    });
    const [availableSlots, setAvailableSlots] = useState({});
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const savedDetails = JSON.parse(localStorage.getItem("meetingDetails"));
        if (savedDetails) {
            setMeetingDetails(savedDetails);
        }
        fetchAllSlots();
    }, []);

    useEffect(() => {
        localStorage.setItem("meetingDetails", JSON.stringify(meetingDetails));
    }, [meetingDetails]);

    const fetchAllSlots = async () => {
        setLoading(true);
        try {
            const response = await axios.get('/guest-booking/all-slots');
            // Group slots by date
            const grouped = response.data.reduce((acc, slot) => {
                if (!acc[slot.date]) acc[slot.date] = [];
                acc[slot.date].push(slot);
                return acc;
            }, {});
            setAvailableSlots(grouped);
            
            // Auto-select first date if available
            const dates = Object.keys(grouped);
            if (dates.length > 0) {
                setSelectedDate(dates[0]);
            }
        } catch (error) {
            console.error("Failed to fetch slots:", error);
        } finally {
            setLoading(false);
        }
    };

    const handlePayment = async (method) => {
        if (!selectedSlot || !meetingDetails.name || !meetingDetails.email) {
            alert("Please fill in all details and select a time slot.");
            return;
        }

        try {
            const response = await axios.post("/guest-booking/pay", {
                ...meetingDetails,
                date: selectedSlot.date,
                time: selectedSlot.time,
                paymentMethod: method,
                skillLevel: meetingDetails.skillLevel
            });

            if (response.data.url) {
                window.location.href = response.data.url;
            } else {
                alert("Something went wrong. Please try again.");
            }
        } catch (error) {
            console.error("Payment initiation failed:", error);
            alert(error.response?.data?.error || "Failed to initiate payment. Please check your details.");
        }
    };

    return (
        <section className="max-w-7xl mx-auto px-4 py-12 space-y-8">
            <div className="mb-12 text-center">
                <h2 className="text-4xl font-extrabold text-gray-900 tracking-tight">
                    Premium One-on-One Coaching
                </h2>
                <p className="mt-4 text-xl text-gray-600 max-w-2xl mx-auto">
                    Accelerate your piano journey with a personalized 60-minute session tailored to your goals.
                </p>
            </div>

            <div className="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">
                <div className="grid grid-cols-1 lg:grid-cols-2">
                    {/* Left Side: Info */}
                    <div className="p-8 lg:p-12 bg-gray-50 border-r border-gray-200">
                        <div className="space-y-8">
                            <div>
                                <h3 className="text-sm font-semibold text-blue-600 uppercase tracking-wide">Hosted by</h3>
                                <p className="mt-1 text-3xl font-bold text-gray-900">Kingsley Khord</p>
                            </div>

                            <div className="space-y-4">
                                <div className="flex items-center space-x-4 text-gray-700">
                                    <div className="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i className="fa fa-clock-o text-blue-600"></i>
                                    </div>
                                    <span className="text-lg font-medium">60 Minutes Session</span>
                                </div>
                                <div className="flex items-center space-x-4 text-gray-700">
                                    <div className="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i className="fa fa-video-camera text-green-600"></i>
                                    </div>
                                    <span className="text-lg font-medium">Video Call (Link provided after booking)</span>
                                </div>
                                <div className="flex items-center space-x-4 text-gray-700">
                                    <div className="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i className="fa fa-money text-purple-600"></i>
                                    </div>
                                    <span className="text-lg font-bold text-gray-900">$10 / ₦15,000</span>
                                </div>
                            </div>

                            <div className="prose prose-blue text-gray-600">
                                <p>
                                    Get direct feedback, personalized exercises, and expert guidance on any piano-related topic you're struggling with.
                                </p>
                                <ul className="list-disc pl-5 space-y-2 mt-4">
                                    <li>Technical skill assessment</li>
                                    <li>Custom practice roadmap</li>
                                    <li>Musical application techniques</li>
                                    <li>Q&A session</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {/* Right Side: Slots & Form */}
                    <div className="p-8 lg:p-12">
                        {loading ? (
                            <div className="flex flex-col items-center justify-center h-64 text-gray-500">
                                <i className="fa fa-spinner fa-spin text-4xl mb-4 text-blue-600"></i>
                                <p>Loading available slots...</p>
                            </div>
                        ) : Object.keys(availableSlots).length > 0 ? (
                            <div className="space-y-8">
                                {!selectedSlot ? (
                                    <div className="space-y-6">
                                        <h3 className="text-xl font-bold text-gray-900 border-b pb-2">Select a Date & Time</h3>
                                        
                                        {/* Date Picker (Tabs) */}
                                        <div className="flex overflow-x-auto pb-2 gap-2 custom-scrollbar">
                                            {Object.keys(availableSlots).map((date) => (
                                                <button
                                                    key={date}
                                                    onClick={() => setSelectedDate(date)}
                                                    className={`flex-shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all border ${
                                                        selectedDate === date
                                                            ? "bg-blue-600 text-white border-blue-600"
                                                            : "bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200"
                                                    }`}
                                                >
                                                    {new Date(date).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })}
                                                </button>
                                            ))}
                                        </div>

                                        {/* Time Slots for Selected Date */}
                                        {selectedDate && (
                                            <div className="animate-in fade-in slide-in-from-top-2 duration-300">
                                                <div className="flex justify-between items-center mb-3">
                                                    <h4 className="text-xs font-bold text-gray-500 uppercase tracking-widest">
                                                        Available times for {new Date(selectedDate).toLocaleDateString('en-US', { month: 'long', day: 'numeric' })}
                                                    </h4>
                                                    <span className="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full uppercase">
                                                        All times in WAT
                                                    </span>
                                                </div>
                                                <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                    {availableSlots[selectedDate].map((slot) => (
                                                        <button
                                                            key={slot.id}
                                                            onClick={() => setSelectedSlot(slot)}
                                                            className="py-3 px-3 text-sm font-bold rounded-xl border border-gray-200 bg-white text-gray-900 hover:border-blue-500 hover:bg-blue-50 transition-all text-center shadow-sm"
                                                        >
                                                            {new Date(`1970-01-01T${slot.time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} {"(WAT)"}
                                                        </button>
                                                    ))}
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <div className="space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
                                        <div className="flex justify-between items-center bg-blue-50 p-4 rounded-xl border border-blue-100">
                                            <div>
                                                <p className="text-sm text-blue-600 font-semibold uppercase tracking-wide">Selected Slot</p>
                                                <p className="text-lg font-bold text-gray-900">
                                                    {new Date(selectedSlot.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}
                                                </p>
                                                <p className="text-blue-800 font-medium">
                                                    {new Date(`1970-01-01T${selectedSlot.time}`).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} (West Africa Time)
                                                </p>
                                            </div>
                                            <button
                                                className="text-sm font-bold text-blue-600 hover:text-blue-800 underline"
                                                onClick={() => setSelectedSlot(null)}
                                            >
                                                Change
                                            </button>
                                        </div>

                                        <div className="space-y-4">
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                                    <input
                                                        type="text"
                                                        placeholder="John Doe"
                                                        value={meetingDetails.name}
                                                        onChange={(e) => setMeetingDetails({ ...meetingDetails, name: e.target.value })}
                                                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    />
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-medium text-gray-700 mb-1">Skill Level</label>
                                                    <select
                                                        value={meetingDetails.skillLevel}
                                                        onChange={(e) => setMeetingDetails({ ...meetingDetails, skillLevel: e.target.value })}
                                                        className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    >
                                                        <option>Beginner</option>
                                                        <option>Intermediate</option>
                                                        <option>Advanced</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                                <input
                                                    type="email"
                                                    placeholder="john@example.com"
                                                    value={meetingDetails.email}
                                                    onChange={(e) => setMeetingDetails({ ...meetingDetails, email: e.target.value })}
                                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                />
                                            </div>

                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">Your Focus/Goals</label>
                                                <textarea
                                                    placeholder="What would you like to achieve in this session?"
                                                    rows="3"
                                                    value={meetingDetails.focus}
                                                    onChange={(e) => setMeetingDetails({ ...meetingDetails, focus: e.target.value })}
                                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                />
                                            </div>

                                            <div className="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <button
                                                    onClick={() => handlePayment("paystack")}
                                                    className="w-full py-4 px-6 bg-[#09a5db] hover:bg-[#0894c5] text-white font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-1"
                                                >
                                                    Pay ₦15,000 with Paystack
                                                </button>
                                                <button
                                                    onClick={() => handlePayment("stripe")}
                                                    className="w-full py-4 px-6 bg-[#6772e5] hover:bg-[#5469d4] text-white font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-1"
                                                >
                                                    Pay $10 with Stripe
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <div className="flex flex-col items-center justify-center h-64 text-gray-500 text-center">
                                <i className="fa fa-calendar-times-o text-4xl mb-4"></i>
                                <h3 className="text-lg font-bold">No Available Slots</h3>
                                <p className="text-sm">Please check back later for new available coaching sessions.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
};

export default ZoomMeetingBooking;

if (document.getElementById("zoomMeetingBooking")) {
    const Index = ReactDOM.createRoot(
        document.getElementById("zoomMeetingBooking")
    );

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <ZoomMeetingBooking />
                </FlashMessageProvider>
        </React.StrictMode>
    );
}
