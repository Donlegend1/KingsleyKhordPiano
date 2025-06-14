import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";

const ShowEartraining = () => {
    const [quiz, setQuiz] = useState(null);
    const [currentQuestion, setCurrentQuestion] = useState(0);
    const [selectedOption, setSelectedOption] = useState(null);
    const [isSubmitted, setIsSubmitted] = useState(false);
    const [showResult, setShowResult] = useState(false);
    const [score, setScore] = useState(0);

    const STANDARD_OPTIONS = ["DOH", "REH", "MI", "FAH", "SOH", "LAH", "TI"];

    const lastSegment = window.location.pathname
        .split("/")
        .filter(Boolean)
        .pop();

    useEffect(() => {
        const fetchQuiz = async () => {
            try {
                const response = await axios.get(
                    `/admin/ear-training/${lastSegment}`
                );
                setQuiz(response.data);
            } catch (error) {
                console.error("Error fetching quiz:", error);
            }
        };
        fetchQuiz();
    }, [lastSegment]);

    if (!quiz || !quiz.questions?.length) {
        return (
            <div className="max-w-3xl mx-auto p-6 bg-white shadow rounded-lg mt-6">
                <div className="animate-pulse space-y-4">
                    <div className="h-6 bg-gray-300 rounded w-1/2"></div>
                    <div className="h-48 bg-gray-200 rounded"></div>
                    <div className="h-4 bg-gray-200 rounded w-1/3"></div>
                    <div className="space-y-2">
                        {STANDARD_OPTIONS.map((_, i) => (
                            <div
                                key={i}
                                className="h-10 bg-gray-100 rounded"
                            ></div>
                        ))}
                    </div>
                </div>
            </div>
        );
    }

    const question = quiz.questions[currentQuestion];

    const handleOptionSelect = (index) => {
        if (!isSubmitted) {
            setSelectedOption(index);
        }
    };

    const handleSubmit = () => {
        if (selectedOption === null) return;

        setIsSubmitted(true);
        if (selectedOption === question.correct_option) {
            setScore((prev) => prev + 1);
        }
    };

    const handleNext = () => {
        if (currentQuestion < quiz.questions.length - 1) {
            setCurrentQuestion((prev) => prev + 1);
            setSelectedOption(null);
            setIsSubmitted(false);
        } else {
            setShowResult(true);
        }
    };

    console.log(STANDARD_OPTIONS, "standatd");
    return (
        <div className="max-w-3xl mx-auto p-6 bg-white shadow rounded-lg mt-6">
            <h2 className="text-2xl font-bold mb-4">{quiz.title}</h2>

            {STANDARD_OPTIONS.map((idex) => (
                <p>hehehreh</p>
            ))}

            {/* Main video */}
            <div
                className="mb-6"
                dangerouslySetInnerHTML={{ __html: quiz.video_url }}
            />

            {/* Main audio */}
            {quiz.main_audio_path && (
                <div className="mb-6">
                    <p className="font-semibold mb-2">Main Audio:</p>
                    <audio
                        controls
                        src={`/storage/${quiz.main_audio_path}`}
                        className="w-full max-w-2xl"
                    />
                </div>
            )}

            {!showResult ? (
                <>
                    {/* Question audio */}
                    <div className="mb-4">
                        <p className="text-lg font-medium mb-2">
                            Question {currentQuestion + 1} of{" "}
                            {quiz.questions.length}
                        </p>
                        <audio
                            controls
                            src={`/storage/${question.audio_path}`}
                            className="mb-4"
                        />
                    </div>

                    {/* Options list */}
                    <div className="space-y-3">
                        {STANDARD_OPTIONS.map((opt, i) => (
                            <button
                                key={i}
                                onClick={() => handleOptionSelect(i)}
                                disabled={isSubmitted}
                                className={`w-full p-3 border rounded-lg text-left 
                ${
                    selectedOption === i
                        ? "bg-blue-100 border-blue-500"
                        : "bg-gray-50"
                }
                ${
                    isSubmitted && i === question.correct_option
                        ? "border-green-500 bg-green-100"
                        : ""
                }
                ${
                    isSubmitted &&
                    selectedOption === i &&
                    selectedOption !== question.correct_option
                        ? "border-red-500 bg-red-100"
                        : ""
                }
            `}
                            >
                                {opt}
                            </button>
                        ))}
                    </div>

                    {/* Correct Answer */}
                    {isSubmitted && (
                        <div className="mt-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                            Correct Answer:{" "}
                            <strong>
                                {STANDARD_OPTIONS[question.correct_option]}
                            </strong>
                        </div>
                    )}

                    {/* Submit / Next Button */}
                    <div className="mt-6 flex justify-end space-x-3">
                        {!isSubmitted ? (
                            <button
                                onClick={handleSubmit}
                                className="bg-black text-white px-6 py-2 rounded hover:bg-blue-600 hover:text-black transition"
                                disabled={selectedOption === null}
                            >
                                Submit Answer
                            </button>
                        ) : (
                            <button
                                onClick={handleNext}
                                className="bg-gray-800 text-white px-6 py-2 rounded hover:bg-green-500 hover:text-black transition"
                            >
                                {currentQuestion === quiz.questions.length - 1
                                    ? "Finish"
                                    : "Next Question"}
                            </button>
                        )}
                    </div>
                </>
            ) : (
                <div className="text-center">
                    <h3 className="text-xl font-semibold mb-2">
                        Quiz Complete!
                    </h3>
                    <p className="text-lg">
                        Your Score: {score} / {quiz.questions.length}
                    </p>
                </div>
            )}
        </div>
    );
};

if (document.getElementById("ear-training-quiz")) {
    const root = ReactDOM.createRoot(
        document.getElementById("ear-training-quiz")
    );
    root.render(<ShowEartraining />);
}
