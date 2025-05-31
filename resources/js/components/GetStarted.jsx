import React from "react";
import ReactDOM from "react-dom/client";
import { useState } from "react";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const questions = [
    {
        id: 1,
        text: "Can you play all 12 major scales?",
        answers: ["Yes.", "No."],
    },
    {
        id: 2,
        text: "Can you play all 12 minor scales?",
        answers: ["Yes.", "No."],
    },
    {
        id: 3,
        text: "Can you play all 12 major triads and their inversions?",
        answers: ["Yes.", "No."],
    },
    {
        id: 4,
        text: "Can you play all 12 minor triads and their inversions?",
        answers: ["Yes.", "No."],
    },
    {
        id: 5,
        text: "Can you play songs by ear?",
        answers: ["Not at all.", "I can but I am not confident.", "Yes I can."],
    },
    {
        id: 6,
        text: "How confident are you in harmonizing song melodies?",
        answers: [
            "I am not confident.",
            "I am little bit confident.",
            "I am good at it.",
        ],
    },
    {
        id: 7,
        text: "Can you use passing chords like, secondary dominant, diminished 7th, chord progressions, e.t.c.",
        answers: [
            "No I dont.",
            "I know a little bit, but not confident.",
            "I am confident in passing chords.",
        ],
    },
    {
        id: 8,
        text: "Can you play, in all 12 keys, drop 2 chords of major, minor and diminished?",
        answers: ["No I cannot .", "Yes I can."],
    },
    {
        id: 9,
        text: "How do you explore and refine your personal sound?",
        answers: [
            "I focus on learning new chords, scales, and patterns to add to my playing.",
            "I experiment with combining different techniques and ideas to refine my sound.",
            "I focus on the emotional impact of my playing and the sound I want to convey.",
        ],
    },
];

function Modal({ onClose, children }) {
    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-white p-5 rounded-lg shadow-lg w-full max-w-5xl">
                <div className="flex justify-between items-center">
                    <h2 className="text-[25px] font-bold text-gray-700">
                        Customize your learning experience
                    </h2>
                    <button
                        onClick={onClose}
                        className="text-gray-500 hover:text-black"
                    >
                        <i
                            className="fa fa-times text-2xl"
                            aria-hidden="true"
                        ></i>
                    </button>
                </div>
                {children}
            </div>
        </div>
    );
}

export default function QuizCardWithModal() {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [answers, setAnswers] = useState({});
    const { showMessage } = useFlashMessage();

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const nextQuestion = () => {
        // Include the current answer in the check (answers[currentQuestionIndex])
        const updatedAnswers = { ...answers };

        // If the current question is not answered yet, do nothing or alert
        if (!updatedAnswers[currentQuestionIndex]) {
            showMessage('Please select an answer before proceeding.',"error");
            return;
        }

        // Run redirect check BEFORE advancing
        // If redirect occurs, do NOT advance question
        const redirected = checkProgressAndRedirect(updatedAnswers);
        if (redirected) {
            return; // stop here, redirect happened
        }

        // Otherwise, advance the question normally
        setCurrentQuestionIndex((prev) =>
            prev < questions.length - 1 ? prev + 1 : prev
        );
    };

    const prevQuestion = () => {
        setCurrentQuestionIndex((prev) => (prev > 0 ? prev - 1 : prev));
    };

    const checkProgressAndRedirect = (newAnswers) => {
        // 1. If any of the first 4 answers is "No." => redirect immediately
        for (let i = 0; i < 4; i++) {
            if (newAnswers[i] === "No.") {
                showMessage('You are in beginner level',"success");
                window.location = "/member/course/beginner";
                return true; // redirect happened
            }
        }

        // 2. Only continue if first 4 questions are answered
        const firstFourAnswered = [0, 1, 2, 3].every((i) => newAnswers[i]);

        // 3. Only check intermediate/advanced if first 4 answered
        if (!firstFourAnswered) return false;

        // 4. Check if questions 5-9 have all been answered
        const nextFourAnswered = [4, 5, 6, 7, 8].every((i) => newAnswers[i]);

        // 5. If any of questions 5-8 NOT answered yet, don't redirect yet
        if (!nextFourAnswered) return false;

        // 6. Now check answers for intermediate/advanced

        // Expected answers to be considered "advanced"
        const advancedAnswers = {
            4: "Yes I can.",
            5: "I am good at it.",
            6: "I am confident in passing chords.",
            7: "Yes I can.",
            9: "I focus on the emotional impact of my playing and the sound I want to convey.",
        };

        // If any of questions 5-9 is NOT the advanced answer, redirect to intermediate
        for (let i = 4; i <= 8; i++) {
            if (newAnswers[i] !== advancedAnswers[i]) {
                showMessage('You are in intermediate level',"success");
                window.location = "/member/course/intermediate";
                return true;
            }
        }

        // All 5-9 questions answered with advanced answers -> redirect advanced
        window.location = "/member/course/advanced";
        showMessage('You are in advanced level ',"success");
        return true;
    };

    return (
        <div className="flex flex-col items-center justify-center p-4 bg-white border border-gray-300 rounded-lg w-full min-h-[200px]">
            <div className="text-center my-5 mx-10">
                <p className="text-gray-800 font-semibold mb-2">
                    Take a Quick Quiz
                </p>
                <p className="text-gray-500 text-sm">
                    This quick quiz will help us customize your learning
                    experience and determine your skill level.
                </p>
            </div>
            <button
                onClick={openModal}
                className="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
            >
                View Details{" "}
                <i className="fa fa-angle-right ml-2" aria-hidden="true"></i>
            </button>

            {isModalOpen && (
                <Modal onClose={closeModal}>
                    <div className=" mb-4">
                        <div className="flex justify-between items-center my-4 gap-7">
                            <div className="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div
                                    className="bg-blue-600 h-2.5 rounded-full"
                                    style={{
                                        width: `${
                                            ((currentQuestionIndex + 1) /
                                                questions.length) *
                                            100
                                        }%`,
                                    }}
                                ></div>
                            </div>
                            <div className="text-gray-500 text-sm text-center whitespace-nowrap">
                                {currentQuestionIndex + 1} / {questions.length}
                            </div>
                        </div>

                        <div className="flex flex-col items-center justify-center my-12 max-w-lg mx-auto text-center">
                            <p className="mb-4 text-2xl font-bold">
                                {questions[currentQuestionIndex].text}
                            </p>
                            <p className="text-gray-500 ">
                                Knowing your scale is fundamental for piano
                                learning. A major scale is a set of 7 notes
                                following a specific pattern (e.g.,
                                C-D-E-F-G-A-B-C)
                            </p>
                        </div>

                        <div className="grid grid-cols-2 gap-4 max-w-3xl mx-auto">
                            {questions[currentQuestionIndex].answers.map(
                                (answer, idx) => (
                                    <label
                                        key={idx}
                                        className={`flex items-center gap-2 cursor-pointer p-3 rounded-lg border ${
                                            answers[currentQuestionIndex] ===
                                            answer
                                                ? "bg-blue-600 "
                                                : "bg-gray-100 border-gray-300"
                                        } hover:bg-blue-400 transition`}
                                    >
                                        <div
                                            className={`w-5 h-5 flex items-center justify-center rounded-full ${
                                                answers[
                                                    currentQuestionIndex
                                                ] === answer
                                                    ? "bg-blue-600 "
                                                    : "bg-white border border-gray-300"
                                            }`}
                                        >
                                            {answers[currentQuestionIndex] ===
                                                answer && (
                                                <i
                                                    className="fa fa-check-circle text-gray-100"
                                                    aria-hidden="true"
                                                ></i>
                                            )}
                                        </div>

                                        <input
                                            type="checkbox"
                                            checked={
                                                answers[
                                                    currentQuestionIndex
                                                ] === answer
                                            }
                                            onChange={() =>
                                                setAnswers({
                                                    ...answers,
                                                    [currentQuestionIndex]:
                                                        answer,
                                                })
                                            }
                                            className="hidden"
                                        />

                                        <span className="text-sm">
                                            {answer}
                                        </span>
                                    </label>
                                )
                            )}
                        </div>
                    </div>
                    <div className="flex justify-end items-center gap-6 mt-6">
                        <button
                            onClick={prevQuestion}
                            disabled={currentQuestionIndex === 0}
                            className={`px-6 py-2 rounded-full flex items-center gap-2 transition ${
                                currentQuestionIndex === 0
                                    ? "bg-gray-300 text-gray-500 cursor-not-allowed"
                                    : "bg-gray-200 hover:bg-gray-300 text-gray-600"
                            }`}
                        >
                            <i className="fa fa-angle-left"></i>
                            Previous
                        </button>

                        <button
                            onClick={nextQuestion}
                            // disabled={
                            //     currentQuestionIndex === questions.length - 1
                            // }
                            className={`px-6 py-2 rounded-full flex items-center gap-2 transition ${
                                currentQuestionIndex === questions.length - 1
                                    ? "bg-gray-300 text-gray-500 cursor-not-allowed"
                                    : "bg-black text-white hover:bg-gray-800"
                            }`}
                        >
                            Next
                            <i className="fa fa-angle-right"></i>
                        </button>
                    </div>
                </Modal>
            )}
        </div>
    );
}

if (document.getElementById("getStartedQuiz")) {
    const Index = ReactDOM.createRoot(
        document.getElementById("getStartedQuiz")
    );

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <QuizCardWithModal />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
