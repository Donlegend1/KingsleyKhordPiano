import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import Calendar from "react-calendar";
import "react-calendar/dist/Calendar.css";
import axios from "axios";

const ZoomMeetingBooking = () => {
    const [loading, setLoading] = useState(false);
    const [paymentSuccess, setPaymentSuccess] = useState(false);
    const [selectedDate, setSelectedDate] = useState(null);
    const [selectedTime, setSelectedTime] = useState("");
    const [showForm, setShowForm] = useState(false);
    const [zoomLink, setZoomLink] = useState(null);
    const [meetingDetails, setMeetingDetails] = useState({
        name: "",
        email: "",
        focus: "",
        skillLevel: "Beginner",
    });

    const handleDateChange = (date) => {
        setSelectedDate(date);
        setShowForm(true);
    };

    useEffect(() => {
        const savedDetails = JSON.parse(localStorage.getItem("meetingDetails"));
        if (savedDetails) {
            setMeetingDetails(savedDetails);
        }
    }, []);

    useEffect(() => {
        localStorage.setItem("meetingDetails", JSON.stringify(meetingDetails));
    }, [meetingDetails]);

    // const handlePayment = async (method) => {
    //     setLoading(true);
    //     try {
    //         // Simulate payment processing (Replace with actual Paystack/Stripe integration)
    //         await new Promise((resolve) => setTimeout(resolve, 2000));
    //         setPaymentSuccess(true);

    //         // If payment is successful, create Zoom meeting
    //         await createZoomMeeting();
    //     } catch (error) {
    //         console.error("Payment error:", error);
    //     } finally {
    //         setLoading(false);
    //     }
    // };

    const createZoomMeeting = async () => {
        try {
            const response = await axios.post("/zoom-meeting-booking", {
                topic: "Personal Piano Lesson with Kingsley Khord",
                duration: 60,
                price: 60,
                ...meetingDetails,
                date: selectedDate,
                time: selectedTime,
            });
            console.log("Zoom Meeting Created:", response.data);
        } catch (error) {
            console.error("Error creating Zoom meeting:", error);
        }
    };

    const handleUndoDate = () => {
        setSelectedDate(null);
        setSelectedTime("");
        setShowForm(false);
        setMeetingDetails({
            name: "",
            email: "",
            focus: "",
            skillLevel: "Beginner",
        });
    };

    return (
        <section className="max-w-7xl mx-auto px-4 py-12 space-y-8">
            <div className="mb-12">
                <h2 className="text-3xl font-bold text-gray-800">
                    Schedule a One-on-One Call
                </h2>
                <p className="mt-2 text-gray-600">
                    Book a personalized consultation session with{" "}
                    <strong>Kingsley Khord</strong> to accelerate your learning
                    journey.
                </p>
            </div>

            <div className="border rounded-lg shadow-md p-6">
                <div className="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x gap-8 items-start">
                    <div className="space-y-6 pr-0 md:pr-6 text-gray-800">
                        <div>
                            <p className="text-lg">Kingsley Khord</p>
                            <p className="text-2xl font-bold">
                                Personal Piano Lesson
                            </p>
                        </div>

                        <div className="space-y-2">
                            <p className="font-semibold flex items-center space-x-2">
                                <i
                                    className="fa fa-video-camera"
                                    aria-hidden="true"
                                ></i>
                                <span>60 Minutes</span>
                            </p>
                            <p className="font-semibold flex items-center space-x-2">
                                <i
                                    className="fa fa-clock-o"
                                    aria-hidden="true"
                                ></i>
                                <span>Zoom Video</span>
                            </p>
                            <p className="font-semibold flex items-center space-x-2">
                                <i
                                    className="fa fa-money"
                                    aria-hidden="true"
                                ></i>
                                <span>$60</span>
                            </p>
                        </div>

                        <div className="space-y-4 text-sm leading-relaxed">
                            <p>
                                During this session, Kingsley will guide you
                                with any piano-related help you may need. In
                                order to make the most of your session, set a
                                specific and achievable goal that aligns with
                                your skill level.
                            </p>
                            <p className="font-semibold text-red-600">
                                📣 IMPORTANT: This is a one-time purchase, not a
                                subscription.
                            </p>
                            <p>
                                <strong>Communication:</strong> We send polite
                                notifications through email/text about our
                                upcoming session.
                            </p>
                            <p>
                                <strong>Arrive in advance:</strong> Log in to
                                Zoom 5 minutes earlier to prepare your video,
                                audio, and keyboard.
                            </p>
                            {zoomLink && (
                                <div className="mt-4 p-4 bg-green-100 rounded">
                                    <p className="text-green-800">
                                        Meeting created! Join via:{" "}
                                        <a
                                            href={zoomLink}
                                            className="underline text-blue-600"
                                            target="_blank"
                                        >
                                            Zoom Link
                                        </a>
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="w-full rounded-lg overflow-hidden shadow-sm border mt-8 md:mt-0 md:pl-6">
                        {selectedDate ? (
                            <div className="space-y-4">
                                <div className="flex justify-between items-center mb-4">
                                    <div>
                                        <h3 className="text-xl font-semibold">
                                            Selected Date:{" "}
                                            {selectedDate.toLocaleDateString()}
                                        </h3>

                                        <p className="text-gray-600">
                                            Time:{" "}
                                            {selectedTime || "Not selected"}
                                        </p>
                                    </div>
                                    <button
                                        className="bg-black rounded-full text-white p-2"
                                        onClick={handleUndoDate}
                                    >
                                        Change
                                    </button>
                                </div>
                                <input
                                    type="time"
                                    value={selectedTime}
                                    onChange={(e) =>
                                        setSelectedTime(e.target.value)
                                    }
                                    className="border p-2 w-full rounded mb-2"
                                />
                                <input
                                    type="text"
                                    placeholder="Your Name"
                                    value={meetingDetails.name}
                                    onChange={(e) =>
                                        setMeetingDetails({
                                            ...meetingDetails,
                                            name: e.target.value,
                                        })
                                    }
                                    className="border p-2 w-full rounded mb-2"
                                />
                                <input
                                    type="email"
                                    placeholder="Your Email"
                                    value={meetingDetails.email}
                                    onChange={(e) =>
                                        setMeetingDetails({
                                            ...meetingDetails,
                                            email: e.target.value,
                                        })
                                    }
                                    className="border p-2 w-full rounded mb-2"
                                />
                                <textarea
                                    placeholder="What would you like to focus on?"
                                    value={meetingDetails.focus}
                                    onChange={(e) =>
                                        setMeetingDetails({
                                            ...meetingDetails,
                                            focus: e.target.value,
                                        })
                                    }
                                    className="border p-2 w-full rounded mb-2"
                                />
                                <select
                                    value={meetingDetails.skillLevel}
                                    onChange={(e) =>
                                        setMeetingDetails({
                                            ...meetingDetails,
                                            skillLevel: e.target.value,
                                        })
                                    }
                                    className="border p-2 w-full rounded mb-2"
                                >
                                    <option>Beginner</option>
                                    <option>Intermediate</option>
                                    <option>Advanced</option>
                                </select>

                                <div className="space-x-4">
                                    <button
                                        onClick={
                                            () => createZoomMeeting()
                                            // handlePayment("paystack")
                                        }
                                        className="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded"
                                    >
                                        Pay with Paystack
                                    </button>
                                    <button
                                        onClick={() => handlePayment("stripe")}
                                        className="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded"
                                    >
                                        Pay with Stripe
                                    </button>
                                </div>
                            </div>
                        ) : (
                            <Calendar
                                onChange={handleDateChange}
                                value={selectedDate}
                            />
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
            <ZoomMeetingBooking />
        </React.StrictMode>
    );
}
