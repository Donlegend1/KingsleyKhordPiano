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

    const RELATIVE_OPTIONS = ["DOH", "REH", "MI", "FAH", "SOH", "LAH", "TI"];
    const DITONE_OPTIONS = [
        "DOH MI",
        "REH FAH",
        "MI SOH",
        "FAH LAH",
        "SOH TI",
        "LAH DOH",
        "TI REH",
    ];
    const DIATOMIC_OPTIONS = [
        "Major 2nd",
        "Major 3rd",
        "Perfect 4th",
        "Perfect 5th",
        "Major 6th",
        "Major 7th",
        "Octave",
    ];
    const NONDIATOMIC_OPTIONS = [
        "Minor 2nd",
        "Minor 3rd",
        "tri tone",
        "Minor 6th",
        "Minor 7th",
    ];
    const INTERVALS = [
        "Minor 2nd",
        "Major 2nd",
        "Minor 3rd",
        "Major 3rd",
        "Perfect 4th",
        "Tri tone",
        "Perfect 5th",
        "Minor 6th",
        "Major 6th",
        "Minor 7th",
        "Major 7th",
        "Octave",
    ];
    const BASICTRIADS = ["Augmented", "Diminished", "Major", "Minor", "Sus"];
    const DEGREECHORD = [
        "Diminished 7th",
        "Dominant 7th",
        "Half Diminished",
        "Major 7th",
        "Minor 7th",
    ];
    const DEGREECHORDs = [
        "Diminished 7 (b5)",
        "Minor Major 7",
        "Major 7 (b5)",
        "Dominant 7 (b13)",
        "Diminished Major 7th",
        "Major 7 (b13)",
    ];
    const DEGREECHORDg = [
        "Dominant 7 (b5)",
        "Minor Major 7",
        "Major 7 (b5)",
        "Dominant 7 (b13)",
        "Diminished Major 7th",
        "Major 7 (b13)",
    ];

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
            <div className="text-center p-6">
                <p className="text-lg font-semibold">Loading quiz...</p>
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
        setScore((prevScore) =>
            parseInt(selectedOption) === parseInt(question.correct_option)
                ? prevScore + 1
                : prevScore
        );
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

    const handlePrevious = () => {
        if (currentQuestion > 0) {
            setCurrentQuestion((prev) => prev - 1);
            setSelectedOption(null);
            setIsSubmitted(false);
        }
    };

    const percentageCompleted = Math.round(
        ((currentQuestion + (isSubmitted ? 1 : 0)) / quiz.questions.length) *
            100
    );

    const getOptionsByCategory = (category) => {
        switch (category) {
            case "Relative Pitch":
                return RELATIVE_OPTIONS;
            case "Di-tone Pitch":
                return DITONE_OPTIONS;
            case "Diatomic":
                return DIATOMIC_OPTIONS;
            case "Non-Diatomic":
                return NONDIATOMIC_OPTIONS;
            case "Intervals":
                return INTERVALS;
            case "Basic Triads":
                return BASICTRIADS;
            case "Degree Chords":
                return DEGREECHORD;
            case "Degree Chords S":
                return DEGREECHORDs;
            case "Degree Chords G":
                return DEGREECHORDg;
            default:
                return [];
        }
    };

    return (
        <div className="max-w-3xl mx-auto p-6 bg-white shadow rounded-lg mt-6">
            <h2 className="text-2xl font-bold mb-4">{quiz.title}</h2>

            {/* Progress Bar */}
            <div className="mb-6">
                <div className="w-full bg-gray-200 rounded-full h-3">
                    <div
                        className="bg-blue-600 h-3 rounded-full transition-all duration-300"
                        style={{ width: `${percentageCompleted}%` }}
                    ></div>
                </div>
                <p className="text-right text-sm mt-1 text-gray-600">
                    {percentageCompleted}% completed
                </p>
            </div>

            {/* Main Video */}
            <div
                className="mb-6"
                dangerouslySetInnerHTML={{ __html: quiz.video_url }}
            />

            {/* Main Audio */}
            {quiz.main_audio_path && (
                <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg shadow-sm">
                    <p className="font-semibold text-red-700 mb-3 text-lg">
                        Main Audio
                    </p>
                    <audio
                        controls
                        src={`/storage/${quiz.main_audio_path}`}
                        className="w-full max-w-2xl rounded-lg"
                    />
                </div>
            )}

            {!showResult ? (
                <>
                    {/* Question Audio */}
                    <div className="mb-6 text-center">
                        <p className="text-lg font-semibold mb-4">
                            Question {currentQuestion + 1} of{" "}
                            {quiz.questions.length}
                        </p>
                        <audio
                            controls
                            autoPlay
                            src={`/storage/${question.audio_path}`}
                            className="w-full max-w-md mx-auto rounded"
                        />
                    </div>

                    {/* Options */}
                    <div className="space-y-3 mb-6">
                        {getOptionsByCategory(quiz.category).map((opt, i) => (
                            <button
                                key={i}
                                onClick={() => handleOptionSelect(i)}
                                disabled={isSubmitted}
                                className={`w-full p-3 border rounded-lg text-left transition ${
                                    isSubmitted &&
                                    i === parseInt(question.correct_option)
                                        ? "bg-green-100 border-green-500"
                                        : isSubmitted && selectedOption === i
                                        ? "bg-red-100 border-red-500"
                                        : selectedOption === i
                                        ? "bg-blue-100 border-blue-500"
                                        : "bg-gray-50"
                                }`}
                            >
                                {opt}
                            </button>
                        ))}
                    </div>

                    {/* Correct Answer */}
                    {isSubmitted && (
                        <div className="mt-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                            Correct Answer:{" "}
                            <strong>
                                {
                                    getOptionsByCategory(quiz.category)[
                                        question.correct_option
                                    ]
                                }
                            </strong>
                        </div>
                    )}

                    {/* Navigation Buttons */}
                    <div className="mt-6 flex flex-wrap gap-4 justify-between">
                        {currentQuestion > 0 && (
                            <button
                                onClick={handlePrevious}
                                className="bg-gray-300 text-black px-6 py-2 rounded hover:bg-gray-400 transition"
                            >
                                Previous
                            </button>
                        )}

                        {!isSubmitted ? (
                            <button
                                onClick={handleSubmit}
                                disabled={selectedOption === null}
                                className={`px-6 py-2 rounded transition ${
                                    selectedOption === null
                                        ? "bg-gray-300 text-black cursor-not-allowed"
                                        : "bg-black text-white hover:bg-blue-600 hover:text-black"
                                }`}
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

// Mount the component
if (document.getElementById("ear-training-quiz-show")) {
    const root = ReactDOM.createRoot(
        document.getElementById("ear-training-quiz-show")
    );
    root.render(<ShowEartraining />);
}
