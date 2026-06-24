import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import { Clock, Video, Tag, Check, Calendar, Pencil, CreditCard, Wallet, Lock } from "lucide-react";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const DOW_LABELS = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

const dateKey = (year, month, day) =>
    `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

const monthIndex = (year, month) => year * 12 + month;

const ZoomMeetingBooking = () => {
    const [selectedDate, setSelectedDate] = useState(null);
    const [pendingTime, setPendingTime] = useState(null);
    const [selectedSlot, setSelectedSlot] = useState(null);
    const [meetingDetails, setMeetingDetails] = useState({
        name: "",
        email: "",
        focus: "",
        skillLevel: "",
    });
    const [availableSlots, setAvailableSlots] = useState({});
    const [loading, setLoading] = useState(true);
    const [currentMonth, setCurrentMonth] = useState(new Date().getMonth());
    const [currentYear, setCurrentYear] = useState(new Date().getFullYear());

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
            console.log(response, "bookingslot")
            const grouped = response.data.reduce((acc, slot) => {
                if (!acc[slot.date]) acc[slot.date] = [];
                acc[slot.date].push(slot);
                return acc;
            }, {});
            setAvailableSlots(grouped);

            // Auto-select first date if available
            const dates = Object.keys(grouped).sort();
            if (dates.length > 0) {
                setSelectedDate(dates[0]);
                const [y, m] = dates[0].split('-').map(Number);
                setCurrentYear(y);
                setCurrentMonth(m - 1);
            }
        } catch (error) {
            console.error("Failed to fetch slots:", error);
        } finally {
            setLoading(false);
        }
    };

    const availableDateKeys = Object.keys(availableSlots);
    const availableMonthIndexes = availableDateKeys
        .map((d) => {
            const [y, m] = d.split('-').map(Number);
            return monthIndex(y, m - 1);
        })
        .sort((a, b) => a - b);
    const minMonthIndex = availableMonthIndexes[0];
    const maxMonthIndex = availableMonthIndexes[availableMonthIndexes.length - 1];
    const thisMonthIndex = monthIndex(currentYear, currentMonth);

    const canGoPrev = minMonthIndex !== undefined && thisMonthIndex > minMonthIndex;
    const canGoNext = maxMonthIndex !== undefined && thisMonthIndex < maxMonthIndex;

    const goPrevMonth = () => {
        if (!canGoPrev) return;
        if (currentMonth === 0) { setCurrentMonth(11); setCurrentYear((y) => y - 1); }
        else { setCurrentMonth((m) => m - 1); }
        setSelectedDate(null);
        setPendingTime(null);
    };

    const goNextMonth = () => {
        if (!canGoNext) return;
        if (currentMonth === 11) { setCurrentMonth(0); setCurrentYear((y) => y + 1); }
        else { setCurrentMonth((m) => m + 1); }
        setSelectedDate(null);
        setPendingTime(null);
    };

    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const firstDayOfMonth = (() => {
        const d = new Date(currentYear, currentMonth, 1).getDay();
        return d === 0 ? 6 : d - 1;
    })();

    const selectDay = (day) => {
        const key = dateKey(currentYear, currentMonth, day);
        if (!availableSlots[key]) return;
        setSelectedDate(key);
        setPendingTime(null);
    };

    const selectedDateLabel = selectedDate
        ? new Date(selectedDate + 'T00:00:00').toLocaleDateString('en-US', {
              weekday: 'short', month: 'long', day: 'numeric', year: 'numeric',
          })
        : '';

    // Slot date/time are stored as West Africa Time (UTC+1, no DST).
    // Convert to the actual UTC instant, then let the browser render it
    // in the user's own local timezone automatically.
    const slotToDate = (date, time) => {
        const d = new Date(`${date}T${time}Z`);
        d.setUTCHours(d.getUTCHours() - 1);
        return d;
    };

    const formatSlotTime = (date, time) =>
        slotToDate(date, time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    const formatSlotRange = (date, time) => {
        const start = slotToDate(date, time);
        const end = new Date(start.getTime() + 60 * 60 * 1000);
        const fmt = (d) => d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        return `${fmt(start)} – ${fmt(end)}`;
    };

    const userTzLabel = (() => {
        try {
            const offset = -new Date().getTimezoneOffset();
            const h = Math.floor(Math.abs(offset) / 60);
            const m = Math.abs(offset) % 60;
            return 'GMT' + (offset >= 0 ? '+' : '-') + h + (m ? ':' + String(m).padStart(2, '0') : '');
        } catch (e) {
            return 'local time';
        }
    })();

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
                skillLevel: meetingDetails.skillLevel,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC'
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
                    One-on-One with Kingsley
                </h2>
                <p className="mt-4 text-xl text-gray-600 max-w-2xl mx-auto">
                    Book a private, one-hour session with Kingsley for focused, personalized piano coaching tailored to your skill level and goals.
                </p>
            </div>

            <div className="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">
                <div className="grid grid-cols-1 lg:grid-cols-5">
                    {/* Left Side: Info */}
                    <div className="lg:col-span-2 p-8 lg:p-12 bg-gray-50 border-r border-gray-100">
                        <div className="space-y-10">
                            <div>
                                <h3 className="text-xs font-semibold text-blue-600 uppercase tracking-widest">Hosted by</h3>
                                <p className="mt-2 text-3xl font-bold text-gray-900 tracking-tight">Kingsley Khord</p>
                            </div>

                            <div className="divide-y divide-gray-100">
                                <div className="flex items-center gap-3.5 py-3.5 text-gray-700">
                                    <Clock className="w-[18px] h-[18px] text-gray-400" strokeWidth={1.75} />
                                    <span className="text-[15px]">1 Hour Session</span>
                                </div>
                                <div className="flex items-center gap-3.5 py-3.5 text-gray-700">
                                    <Video className="w-[18px] h-[18px] text-gray-400" strokeWidth={1.75} />
                                    <span className="text-[15px]">Zoom (Link provided after booking)</span>
                                </div>
                                <div className="flex items-center gap-3.5 py-3.5 text-gray-900">
                                    <Tag className="w-[18px] h-[18px] text-gray-400" strokeWidth={1.75} />
                                    <span className="text-[15px] font-semibold">$60</span>
                                </div>
                            </div>

                            <div className="text-gray-600">
                                <p className="text-[15px] leading-relaxed">
                                    Prepare for a productive and focused session with Kingsley.
                                </p>
                                <ul className="mt-4 space-y-3">
                                    <li className="flex items-start gap-2.5 text-[15px] leading-relaxed">
                                        <Check className="w-4 h-4 mt-0.5 text-blue-600 flex-shrink-0" strokeWidth={2} />
                                        <span>Set clear goals for what you'd like to accomplish.</span>
                                    </li>
                                    <li className="flex items-start gap-2.5 text-[15px] leading-relaxed">
                                        <Check className="w-4 h-4 mt-0.5 text-blue-600 flex-shrink-0" strokeWidth={2} />
                                        <span>Choose topics that align with your current skill level.</span>
                                    </li>
                                    <li className="flex items-start gap-2.5 text-[15px] leading-relaxed">
                                        <Check className="w-4 h-4 mt-0.5 text-blue-600 flex-shrink-0" strokeWidth={2} />
                                        <span>Prioritize key areas to ensure the best use of our time together.</span>
                                    </li>
                                </ul>
                            </div>

                            {selectedSlot && (
                                <div className="bg-white rounded-xl border border-gray-200 p-5">
                                    <div className="flex items-center justify-between mb-3">
                                        <p className="text-sm font-bold text-gray-900">Your Selection</p>
                                        <button
                                            onClick={() => setSelectedSlot(null)}
                                            className="flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800"
                                        >
                                            <Pencil className="w-3.5 h-3.5" strokeWidth={2} />
                                            Edit
                                        </button>
                                    </div>
                                    <div className="flex items-center gap-2.5 text-gray-700 mb-2">
                                        <Calendar className="w-4 h-4 text-gray-400 flex-shrink-0" strokeWidth={1.75} />
                                        <span className="text-sm">
                                            {new Date(selectedSlot.date + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2.5 text-gray-700">
                                        <Clock className="w-4 h-4 text-gray-400 flex-shrink-0" strokeWidth={1.75} />
                                        <span className="text-sm">{formatSlotRange(selectedSlot.date, selectedSlot.time)}</span>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Right Side: Slots & Form */}
                    <div className="lg:col-span-3 p-8 lg:p-12">
                        {loading ? (
                            <div className="flex flex-col items-center justify-center h-64 text-gray-500">
                                <i className="fa fa-spinner fa-spin text-4xl mb-4 text-blue-600"></i>
                                <p>Loading available slots...</p>
                            </div>
                        ) : Object.keys(availableSlots).length > 0 ? (
                            <div className="space-y-8">
                                {!selectedSlot ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                        {/* Calendar */}
                                        <div>
                                            <h3 className="text-base font-bold text-gray-900 mb-6">1. Select a Date</h3>
                                            <div className="flex items-center justify-between mb-6">
                                                <button
                                                    onClick={goPrevMonth}
                                                    className={`p-1.5 rounded-lg transition ${canGoPrev ? "text-gray-500 hover:bg-gray-100" : "text-gray-200 cursor-not-allowed"}`}
                                                >
                                                    <i className="fa fa-chevron-left text-sm"></i>
                                                </button>
                                                <span className="text-sm font-semibold text-gray-800">
                                                    {MONTH_NAMES[currentMonth]} {currentYear}
                                                </span>
                                                <button
                                                    onClick={goNextMonth}
                                                    className={`p-1.5 rounded-lg transition ${canGoNext ? "text-gray-500 hover:bg-gray-100" : "text-gray-200 cursor-not-allowed"}`}
                                                >
                                                    <i className="fa fa-chevron-right text-sm"></i>
                                                </button>
                                            </div>
                                            <div className="grid grid-cols-7 mb-2">
                                                {DOW_LABELS.map((d) => (
                                                    <div key={d} className="text-center text-xs font-semibold py-1 text-gray-400">
                                                        {d}
                                                    </div>
                                                ))}
                                            </div>
                                            <div className="grid grid-cols-7 gap-y-1">
                                                {Array.from({ length: firstDayOfMonth }).map((_, i) => (
                                                    <div key={`e${i}`}></div>
                                                ))}
                                                {Array.from({ length: daysInMonth }).map((_, i) => {
                                                    const day = i + 1;
                                                    const key = dateKey(currentYear, currentMonth, day);
                                                    const hasSlots = !!availableSlots[key];
                                                    const isSelected = selectedDate === key;
                                                    return (
                                                        <button
                                                            key={day}
                                                            disabled={!hasSlots}
                                                            onClick={() => selectDay(day)}
                                                            className={`aspect-square flex items-center justify-center text-sm rounded-lg transition font-medium mx-auto w-9 h-9 ${
                                                                isSelected
                                                                    ? "bg-blue-600 text-white font-bold shadow"
                                                                    : hasSlots
                                                                    ? "text-gray-700 hover:bg-gray-100"
                                                                    : "text-gray-300 cursor-not-allowed"
                                                            }`}
                                                        >
                                                            {day}
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>

                                        {/* Time Slots */}
                                        <div className="flex flex-col">
                                            <h3 className="text-base font-bold text-gray-900 mb-1">2. Select a Time</h3>
                                            <div className="flex items-center justify-between mb-6">
                                                <p className="text-sm text-gray-400">
                                                    {selectedDate ? `Available slots for ${selectedDateLabel}` : "Select a date first"}
                                                </p>
                                                <span className="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full uppercase flex-shrink-0">
                                                    {userTzLabel}
                                                </span>
                                            </div>

                                            {selectedDate && (availableSlots[selectedDate] || []).length > 0 ? (
                                                <div className="flex flex-col gap-3 flex-1">
                                                    {availableSlots[selectedDate].map((slot) => (
                                                        <label
                                                            key={slot.id}
                                                            className={`flex items-center gap-4 px-5 py-4 rounded-xl border transition-all duration-150 ${
                                                                slot.is_booked
                                                                    ? "border-gray-100 bg-gray-50 cursor-not-allowed opacity-60"
                                                                    : (pendingTime === slot.id
                                                                        ? "border-blue-500 bg-blue-50 ring-1 ring-blue-400 cursor-pointer"
                                                                        : "border-gray-200 hover:border-gray-300 hover:bg-gray-50 cursor-pointer")
                                                            }`}
                                                        >
                                                            <input
                                                                type="radio"
                                                                name="time"
                                                                className="hidden"
                                                                disabled={!!slot.is_booked}
                                                                checked={pendingTime === slot.id}
                                                                onChange={() => setPendingTime(slot.id)}
                                                            />
                                                            <div
                                                                className={`w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition ${
                                                                    !slot.is_booked && pendingTime === slot.id ? "border-blue-500" : "border-gray-300"
                                                                }`}
                                                            >
                                                                {!slot.is_booked && pendingTime === slot.id && (
                                                                    <div className="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                                                )}
                                                            </div>
                                                            <span className={`text-sm font-semibold flex-1 ${slot.is_booked ? "text-gray-400" : "text-gray-700"}`}>
                                                                {formatSlotTime(slot.date, slot.time)}
                                                            </span>
                                                            {slot.is_booked ? (
                                                                <span className="text-[10px] font-bold text-gray-400 uppercase tracking-wide bg-gray-100 px-2 py-1 rounded-full">
                                                                    Booked
                                                                </span>
                                                            ) : null}
                                                        </label>
                                                    ))}
                                                </div>
                                            ) : (
                                                <div className="flex-1 flex items-center justify-center text-sm text-gray-400 italic py-10">
                                                    {selectedDate ? "No available slots for this day." : "Pick an available date to see time slots."}
                                                </div>
                                            )}

                                            <button
                                                onClick={() => {
                                                    if (!selectedDate || pendingTime === null) return;
                                                    const slot = availableSlots[selectedDate].find((s) => s.id === pendingTime);
                                                    setSelectedSlot(slot);
                                                }}
                                                disabled={!selectedDate || pendingTime === null}
                                                className={`mt-6 w-full py-4 rounded-xl text-sm font-bold uppercase tracking-widest transition-all duration-200 ${
                                                    selectedDate && pendingTime !== null
                                                        ? "bg-gray-900 text-white hover:bg-gray-700 shadow-md"
                                                        : "bg-gray-100 text-gray-400 cursor-not-allowed"
                                                }`}
                                            >
                                                Continue to Confirm
                                            </button>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="space-y-8 animate-in fade-in slide-in-from-right-4 duration-300">
                                        <div>
                                            <h3 className="text-xl font-bold text-gray-900">Confirm Your Booking</h3>
                                            <p className="mt-1 text-sm text-gray-500">Please provide your details below to complete your booking.</p>
                                        </div>

                                        <div className="space-y-5">
                                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                <div>
                                                    <label className="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                                                    <input
                                                        type="text"
                                                        placeholder="Enter your full name"
                                                        value={meetingDetails.name}
                                                        onChange={(e) => setMeetingDetails({ ...meetingDetails, name: e.target.value })}
                                                        className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    />
                                                </div>
                                                <div>
                                                    <label className="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                                                    <input
                                                        type="email"
                                                        placeholder="Enter your email address"
                                                        value={meetingDetails.email}
                                                        onChange={(e) => setMeetingDetails({ ...meetingDetails, email: e.target.value })}
                                                        className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    />
                                                </div>
                                            </div>

                                            <div>
                                                <label className="block text-sm font-semibold text-gray-700 mb-1.5">
                                                    What do you want to work on during this lesson?
                                                </label>
                                                <textarea
                                                    placeholder="Share your goals, topics, or specific areas you'd like to focus on..."
                                                    rows="4"
                                                    maxLength={500}
                                                    value={meetingDetails.focus}
                                                    onChange={(e) => setMeetingDetails({ ...meetingDetails, focus: e.target.value })}
                                                    className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                                />
                                                <p className="mt-1 text-right text-xs text-gray-400">{meetingDetails.focus.length}/500</p>
                                            </div>

                                            <div>
                                                <label className="block text-sm font-semibold text-gray-700 mb-2">
                                                    What is your perceived skill level? <span className="text-gray-400 font-normal">(Optional)</span>
                                                </label>
                                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    {["Beginner", "Intermediate", "Advanced"].map((level) => (
                                                        <button
                                                            key={level}
                                                            type="button"
                                                            onClick={() => setMeetingDetails({ ...meetingDetails, skillLevel: level })}
                                                            className={`flex items-center gap-3 px-4 py-3.5 rounded-xl border text-left transition ${
                                                                meetingDetails.skillLevel === level
                                                                    ? "border-blue-500 bg-blue-50 ring-1 ring-blue-400"
                                                                    : "border-gray-200 hover:border-gray-300 hover:bg-gray-50"
                                                            }`}
                                                        >
                                                            <div
                                                                className={`w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition ${
                                                                    meetingDetails.skillLevel === level ? "border-blue-500" : "border-gray-300"
                                                                }`}
                                                            >
                                                                {meetingDetails.skillLevel === level && (
                                                                    <div className="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                                                )}
                                                            </div>
                                                            <span className="text-sm font-semibold text-gray-800">{level}</span>
                                                        </button>
                                                    ))}
                                                </div>
                                            </div>

                                            <div>
                                                <div className="flex items-center justify-between mb-1">
                                                    <label className="block text-sm font-semibold text-gray-700">Choose a Payment Method</label>
                                                </div>
                                                <p className="flex items-center gap-1.5 text-xs text-gray-400 mb-3">
                                                    <Lock className="w-3.5 h-3.5" strokeWidth={1.75} />
                                                    Your payment is secure and encrypted.
                                                </p>
                                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <button
                                                        onClick={() => handlePayment("stripe")}
                                                        className="w-full flex items-center justify-center gap-2.5 py-4 px-6 bg-[#635bff] hover:bg-[#5147e0] text-white rounded-xl shadow-sm transition-colors"
                                                    >
                                                        <CreditCard className="w-5 h-5" strokeWidth={2} />
                                                        <span className="text-[15px]">Pay with <span className="font-extrabold italic">Stripe</span></span>
                                                    </button>
                                                    <button
                                                        onClick={() => handlePayment("paystack")}
                                                        className="w-full flex items-center justify-center gap-2.5 py-4 px-6 bg-white hover:bg-gray-50 text-gray-800 border border-gray-200 rounded-xl shadow-sm transition-colors"
                                                    >
                                                        <Wallet className="w-5 h-5 text-[#00C3F7]" strokeWidth={2} />
                                                        <span className="text-[15px]">Pay with <span className="font-extrabold text-[#00C3F7]">Paystack</span></span>
                                                    </button>
                                                </div>
                                            </div>

                                            <p className="text-xs text-gray-500">
                                                By proceeding, you agree to our{' '}
                                                <a href="/terms-of-service" target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">Terms of Service</a>
                                                {' '}and{' '}
                                                <a href="/privacy-policy" target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:underline">Privacy Policy</a>.
                                            </p>
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
