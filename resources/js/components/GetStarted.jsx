import React from 'react';
import ReactDOM from 'react-dom/client';
import { useState } from 'react';

const questions = [
  { id: 1, text: 'Can you play all 12 major scales?', answers: ['Yes.', 'No.'] },
  { id: 2, text: 'Can you play all 12 minor scales?', answers: ['Yes.', 'No.'] },
  { id: 3, text: 'Can you play all 12 major triads and their inversions?', answers: ['Yes.', 'No.'] },
  { id: 4, text: 'Can you play all 12 minor triads and their inversions?', answers: ['Yes.', 'No.'] },
  { id: 5, text: 'Can you play songs by ear?', answers: ['Not at all.', 'I can but I am not confident.', 'Yes I can.'] },
  { id: 6, text: 'How confident are you in harmonizing song melodies?', answers: ['I am not confident.', 'I am little bit confident.', 'I am good at it.'] },
  { id: 7, text: 'Can you use passing chords like, secondary dominant, diminished 7th, chord progressions, e.t.c.', answers: ['No I dont.', 'I know a little bit, but not confident.', 'I am confident in passing chords.'] },
  { id: 8, text: 'Can you play, in all 12 keys, drop 2 chords of major, minor and diminished?', answers: ['No I cannot .', 'Yes I can.'] },
  { id: 9, text: 'How do you explore and refine your personal sound?', answers: ['I focus on learning new chords, scales, and patterns to add to my playing.', 'I experiment with combining different techniques and ideas to refine my sound.', 'I focus on the emotional impact of my playing and the sound I want to convey.'] }
];

function Modal({ onClose, children }) {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white p-5 rounded-lg shadow-lg w-full max-w-5xl">
     <div className="flex justify-between items-center">
     <h2 className="text-[25px] font-bold text-gray-700">Customize your learning experience</h2>
          <button onClick={onClose} className="text-gray-500 hover:text-black">
          <i class="fa fa-times text-2xl" aria-hidden="true"></i>
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

  const openModal = () => setIsModalOpen(true);
  const closeModal = () => setIsModalOpen(false);

  const nextQuestion = () => {
    setCurrentQuestionIndex((prev) => (prev < questions.length - 1 ? prev + 1 : prev));
  };

  const prevQuestion = () => {
    setCurrentQuestionIndex((prev) => (prev > 0 ? prev - 1 : prev));
  };

  return (
    <div className="flex flex-col items-center justify-center p-4 bg-white border border-gray-300 rounded-lg w-full min-h-[200px]">
      <div className="text-center my-5 mx-10">
        <p className="text-gray-800 font-semibold mb-2">Take a Quick Quiz</p>
        <p className="text-gray-500 text-sm">
          This quick quiz will help us customize your learning experience and determine your skill level.
        </p>
      </div>
      <button 
        onClick={openModal}
        className="px-5 py-2 bg-[#404348] text-white text-sm rounded-full hover:bg-yellow-400 hover:text-black transition inline-flex items-center"
      >
        View Details <i className="fa fa-angle-right ml-2" aria-hidden="true"></i>
      </button>

      {isModalOpen && (
        <Modal onClose={closeModal}>
      <div className=" mb-4">
      <div className="flex justify-between items-center my-4 gap-7">
        <div className="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
          <div 
            className="bg-blue-600 h-2.5 rounded-full" 
            style={{ width: `${((currentQuestionIndex + 1) / questions.length) * 100}%` }}
          ></div>
        </div>
          <div className="text-gray-500 text-sm text-center whitespace-nowrap">
           {currentQuestionIndex + 1} / {questions.length}
         </div>
        </div>

        <div className="flex flex-col items-center justify-center my-12 max-w-lg mx-auto text-center">
         <p className="mb-4 text-2xl font-bold">{questions[currentQuestionIndex].text}</p>
         <p className="text-gray-500 ">
           Knowing your scale is fundamental for piano learning. A major scale is a set of 7 notes following a specific pattern (e.g., C-D-E-F-G-A-B-C)
         </p>
       </div>

       <div className="grid grid-cols-2 gap-4 max-w-3xl mx-auto">
  {questions[currentQuestionIndex].answers.map((answer, idx) => (
    <label 
      key={idx} 
      className={`flex items-center gap-2 cursor-pointer p-3 rounded-lg border ${
        answers[currentQuestionIndex] === answer 
          ? 'bg-blue-600 ' 
          : 'bg-gray-100 border-gray-300'
      } hover:bg-blue-400 transition`}
    >
      <div 
        className={`w-5 h-5 flex items-center justify-center rounded-full ${
          answers[currentQuestionIndex] === answer 
            ? 'bg-blue-600 ' 
            : 'bg-white border border-gray-300'
        }`}
      >
        {answers[currentQuestionIndex] === answer && (
          <i class="fa fa-check-circle text-gray-100" aria-hidden="true"></i>
        )}
      </div>

      <input 
        type="checkbox" 
        checked={answers[currentQuestionIndex] === answer} 
        onChange={() => setAnswers({ ...answers, [currentQuestionIndex]: answer })} 
        className="hidden"
      />
      
      <span className="text-sm">
        {answer}
      </span>
    </label>
  ))}
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
    disabled={currentQuestionIndex === questions.length - 1}
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

if (document.getElementById('getStartedQuiz')) {
    const Index = ReactDOM.createRoot(document.getElementById("getStartedQuiz"));

    Index.render(
        <React.StrictMode>
            <QuizCardWithModal />
        </React.StrictMode>
    )
}