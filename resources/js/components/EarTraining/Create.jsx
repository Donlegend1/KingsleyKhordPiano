import React, { useState, useEffect } from "react";
import axios from "axios";
import ReactDOM from "react-dom/client";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const getOptionsByCategory = (category) => {
    const map = {
        "Relative Pitch": ["DOH", "REH", "MI", "FAH", "SOH", "LAH", "TI"],
        "Di-tone Pitch": ["DOH MI", "REH FAH", "MI SOH", "FAH LAH", "SOH TI", "LAH DOH", "TI REH"],
        "Diatonic Intervals": ["Major 2nd", "Major 3rd", "Perfect 4th", "Perfect 5th", "Major 6th", "Major 7th", "Octave"],
        "Non-diatonic Intervals": ["Minor 2nd", "Minor 3rd", "Tri tone", "Minor 6th", "Minor 7th"],
        "Intervals": ["Minor 2nd", "Major 2nd", "Minor 3rd", "Major 3rd", "Perfect 4th", "Tri tone", "Perfect 5th", "Minor 6th", "Major 6th", "Minor 7th", "Major 7th", "Octave"],
        "Basic Triad": ["Augmented", "Diminished", "Major", "Minor", "Sus"],
        "7th Degree Chords (Basic)": ["Diminished 7th", "Dominant 7th", "Minor 7b5", "Major 7th", "Minor 7th"],
        "7th Degree Chords (Secondary)": ["Dim (Maj7)", "Dom7#5", "Dom7b5", "Maj7#5", "Maj7b5", "minMaj7"],
        "7th Degree Chords (General)": ["Diminished 7th", "Dominant 7th", "Minor 7b5", "Major 7th", "Minor 7th", "Dim (Maj7)", "Dom7#5", "Dom7b5", "Maj7#5", "Maj7b5", "minMaj7"],
        "9th degree Chords (Basic)": ["Dim7 (9)", "Dom9", "Dom7 (b9)", "Maj 6/9", "min 6/9", "min9", "min9 (b5)"],
        "9th Degree Chords (Secondary)": ["DimMaj7 (9)", "Dom9 (b5)", "Dom9 (#5)", "Maj9 (b5)", "Maj9 (#5)", "min (Maj9)"],
        "9th Degree Chords (General)": ["DimMaj7 (9)", "Dom9 (b5)", "Dom9 (#5)", "Maj9 (b5)", "Maj9 (#5)", "min (Maj9)", "Dim7 (9)", "Dom9", "Dom7 (b9)", "Maj 6/9", "min 6/9", "min9", "min9 (b5)"],
        "11th Degree Chords": ["6/9 (#11)", "Dom9 (#11)", "Dom7 (b9#11)", "Maj9 (#11)", "min6/9 (11)", "min 9 (11)"],
        "13th Degree Chords": ["13sus4", "Dom 9 (13) #11", "Dom 13 (b9#11)", "Maj13 (#11)", "min13 (9,11)"],
        "Others": ["9sus4", "DimM9 (#5)", "Dom9 (#5b5)", "Maj9sus4", "Maj9 (b5#5)", "min9/11 (Maj7)"],
    };
    return map[category] ?? [];
};

const categories = [
    "Relative Pitch", "Di-tone Pitch", "Diatonic Intervals", "Non-diatonic Intervals",
    "Intervals", "Basic Triad", "7th Degree Chords (Basic)", "7th Degree Chords (Secondary)",
    "7th Degree Chords (General)", "9th degree Chords (Basic)", "9th Degree Chords (Secondary)",
    "9th Degree Chords (General)", "11th Degree Chords", "13th Degree Chords", "Others",
];

const CreateEarTrainingQuiz = () => {
    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [category, setCategory] = useState("");
    const [videoUrl, setVideoUrl] = useState("");
    const [thumbnail, setThumbnail] = useState(null);
    const [mainAudio, setMainAudio] = useState(null);
    const [questions, setQuestions] = useState([{ audio: null, correct_option: "" }]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isCopied, setIsCopied] = useState(false); // flag to show copied banner
    const { showMessage } = useFlashMessage();

    // ✅ Read from sessionStorage on mount (set by the list page copy button)
    useEffect(() => {
        const raw = sessionStorage.getItem("copy_quiz");
        if (!raw) return;

        try {
            const data = JSON.parse(raw);
            setTitle(data.title ? `${data.title} (Copy)` : "");
            setDescription(data.description ?? "");
            setVideoUrl(data.video_url ?? "");
            setCategory(data.category ?? "");
            setIsCopied(true);

            // Pre-fill questions with correct_option — audio must be re-uploaded
            if (data.questions?.length > 0) {
                setQuestions(
                    data.questions.map((q) => ({
                        audio: null,
                        correct_option: q.correct_option ?? "",
                        _refAudioPath: q.audio_path ?? null, // reference only
                    }))
                );
            }
        } catch (e) {
            console.error("Failed to parse copied quiz:", e);
        } finally {
            sessionStorage.removeItem("copy_quiz"); // clean up immediately
        }
    }, []);

    const handleQuestionChange = (index, field, value) => {
    const updated = [...questions];
    updated[index][field] = value;

    // Generate preview URL when audio file is selected
    if (field === "audio" && value instanceof File) {
        updated[index]._audioPreview = URL.createObjectURL(value);
        updated[index]._audioName = value.name;
    }

    setQuestions(updated);
};


    const addQuestion = () => {
        setQuestions([...questions, { audio: null, correct_option: "" }]);
    };

    const removeQuestion = (index) => {
        setQuestions(questions.filter((_, i) => i !== index));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);

        const formData = new FormData();
        formData.append("title", title);
        formData.append("description", description);
        formData.append("category", category);
        formData.append("video_url", videoUrl);
        if (thumbnail) formData.append("thumbnail", thumbnail);
        if (mainAudio) formData.append("main_audio", mainAudio);

        questions.forEach((q, index) => {
            if (q.audio) formData.append(`questions[${index}][audio]`, q.audio);
            formData.append(`questions[${index}][correct_option]`, q.correct_option);
        });

        try {
            await axios.post("/admin/ear-training", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            showMessage("Quiz Saved", "success");
            setTitle("");
            setDescription("");
            setVideoUrl("");
            setCategory("");
            setThumbnail(null);
            setMainAudio(null);
            setIsCopied(false);
            setQuestions([{ audio: null, correct_option: "" }]);
        } catch (error) {
            console.error("Error uploading quiz:", error);
            showMessage("Failed to create quiz", "error");
        } finally {
            setIsSubmitting(false);
        }
    };

    const options = getOptionsByCategory(category);

    const duplicateQuestion = (index) => {
        const questionToCopy = { ...questions[index] }; // carries audio, _audioPreview, _audioName
        const updated = [...questions];
        updated.splice(index + 1, 0, questionToCopy);
        setQuestions(updated);
    };

    return (
        <div className="max-w-4xl mx-auto p-8 bg-white rounded-lg shadow mt-8">

            {/* ── Copied Banner ── */}
            {isCopied && (
                <div className="flex items-start gap-3 bg-yellow-50 border border-yellow-300 rounded-xl px-4 py-3 mb-6">
                    <span className="text-yellow-500 text-lg mt-0.5">📋</span>
                    <div className="flex-1">
                        <p className="text-sm font-semibold text-yellow-800">Copied from existing quiz</p>
                        <p className="text-xs text-yellow-600 mt-0.5">
                            Title, description, category, and question options have been pre-filled.
                            Audio files cannot be copied — please upload new audio for each question.
                        </p>
                    </div>
                    <button
                        onClick={() => setIsCopied(false)}
                        className="text-yellow-400 hover:text-yellow-600 text-lg leading-none"
                    >
                        &times;
                    </button>
                </div>
            )}

            <h2 className="text-2xl font-bold mb-6">
                {isCopied ? "Create Quiz (from Copy)" : "Create New Ear Training Quiz"}
            </h2>

            <form onSubmit={handleSubmit} className="space-y-6">

                {/* Category */}
                <div>
                    <label className="block mb-1 font-medium text-sm text-gray-700">Category</label>
                    <select
                        value={category}
                        onChange={(e) => setCategory(e.target.value)}
                        className="w-full p-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-black/10"
                        required
                    >
                        <option value="">-- Select Category --</option>
                        {categories.map((cat, i) => (
                            <option key={i} value={cat}>{cat}</option>
                        ))}
                    </select>
                </div>

                {/* Title */}
                <div>
                    <label className="block mb-1 font-medium text-sm text-gray-700">Title</label>
                    <input
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        required
                        placeholder="Quiz title"
                        className="w-full p-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-black/10"
                    />
                </div>

                {/* Description */}
                <div>
                    <label className="block mb-1 font-medium text-sm text-gray-700">Description</label>
                    <textarea
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        rows={3}
                        placeholder="Quiz description"
                        className="w-full p-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-black/10"
                    />
                </div>

                {/* Video URL */}
                <div>
                    <label className="block mb-1 font-medium text-sm text-gray-700">Google Video Link</label>
                    <textarea
                        value={videoUrl}
                        onChange={(e) => setVideoUrl(e.target.value)}
                        rows={2}
                        placeholder="Paste embed URL"
                        className="w-full p-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-black/10"
                    />
                </div>

                {/* Thumbnail */}
                <div>
                    <label className="block mb-1 font-medium text-sm text-gray-700">Thumbnail Image</label>
                    <input
                        type="file"
                        accept="image/*"
                        onChange={(e) => setThumbnail(e.target.files[0])}
                        required
                        className="w-full text-sm"
                    />
                </div>

                {/* Main Audio */}
                <div>
                    <label className="block mb-1 font-medium text-sm text-gray-700">Main Audio</label>
                    <input
                        type="file"
                        accept="audio/*"
                        onChange={(e) => setMainAudio(e.target.files[0])}
                        className="w-full text-sm"
                    />
                </div>

                {/* ── Questions ── */}
                <div>
                    <div className="flex items-center justify-between mb-3">
                        <h3 className="text-lg font-semibold">Questions</h3>
                        <span className="text-xs bg-gray-100 text-gray-500 font-medium px-2.5 py-1 rounded-full">
                            {questions.length} question{questions.length !== 1 ? "s" : ""}
                        </span>
                    </div>

                    <div className="space-y-4">
                        {questions.map((q, index) => (
                            <div
                                key={index}
                                className={`border rounded-xl p-4 relative
                                    ${q._refAudioPath
                                        ? "border-yellow-300 bg-yellow-50/40"
                                        : "border-gray-200 bg-gray-50/40"
                                    }`}
                            >
                                {/* Question header */}
                                <div className="flex items-center justify-between mb-3">
                                    <div className="flex items-center gap-2">
                                        <span className="text-xs font-bold text-gray-400 uppercase tracking-wide">
                                            Question {index + 1}
                                        </span>
                                        {q._refAudioPath && (
                                            <span className="text-[10px] bg-yellow-200 text-yellow-800 font-semibold px-2 py-0.5 rounded-full">
                                                Copied — upload new audio
                                            </span>
                                        )}
                                    </div>

                                    <div className="flex items-center gap-2">
                                        <button
                                            type="button"
                                            onClick={() => duplicateQuestion(index)}
                                            className="flex items-center gap-1 text-xs text-blue-500 hover:text-blue-700 border border-blue-200 hover:border-blue-400 px-2 py-1 rounded-lg transition"
                                        >
                                            <span className="fa fa-copy text-[10px]"></span> Copy
                                        </button>
                                        {questions.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeQuestion(index)}
                                                className="flex items-center gap-1 text-xs text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 px-2 py-1 rounded-lg transition"
                                            >
                                                <span className="fa fa-trash text-[10px]"></span> Remove
                                            </button>
                                        )}
                                    </div>
                                </div>

                                {/* Reference audio (copied questions only) */}
                                {q._refAudioPath && (
                                    <div className="mb-3 p-3 bg-yellow-100/60 rounded-lg border border-yellow-200">
                                        <p className="text-xs text-yellow-700 font-medium mb-1.5">
                                            🎵 Original audio (reference only):
                                        </p>
                                        <audio controls src={q._refAudioPath} className="w-full h-8" />
                                    </div>
                                )}

                                {/* Audio upload */}
                                {/* <div className="mb-3">
                                    <label className="block mb-1 text-sm font-medium text-gray-600">
                                        {q._refAudioPath ? "New Audio File *" : "Question Audio *"}
                                    </label>
                                    <input
                                        type="file"
                                        accept="audio/*"
                                        onChange={(e) => handleQuestionChange(index, "audio", e.target.files[0])}
                                        required
                                        className="w-full text-sm"
                                    />
                                </div> */}
                                {/* Audio upload */}
                                <div className="mb-3">
                                    <label className="block mb-1 text-sm font-medium text-gray-600">
                                        {q._refAudioPath ? "New Audio File *" : "Question Audio *"}
                                    </label>
                                    <input
                                        type="file"
                                        accept="audio/*"
                                        onChange={(e) => handleQuestionChange(index, "audio", e.target.files[0])}
                                    
                                        className="w-full text-sm"
                                    />

                                    {/* Show preview if audio is already selected (e.g. from duplication) */}
                                    {q._audioPreview && (
                                        <div className="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                            <p className="text-xs text-green-700 font-medium mb-1">
                                                🎵 {q._audioName}
                                            </p>
                                            <audio controls src={q._audioPreview} className="w-full h-8" />
                                        </div>
                                    )}
                                </div>

                                {/* Correct option */}
                                <div>
                                    <label className="block mb-1 text-sm font-medium text-gray-600">Correct Option *</label>
                                    <select
                                        value={q.correct_option}
                                        onChange={(e) => handleQuestionChange(index, "correct_option", e.target.value)}
                                        required
                                        className="w-full p-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-black/10"
                                    >
                                        <option value="">-- Select Correct Option --</option>
                                        {options.map((opt, i) => (
                                            <option key={i} value={i}>{opt}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        ))}
                    </div>

                    <button
                        type="button"
                        onClick={addQuestion}
                        className="mt-4 flex items-center gap-2 px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition"
                    >
                        <span className="fa fa-plus text-xs"></span> Add Another Question
                    </button>
                </div>

                {/* Submit */}
                <div className="pt-4 border-t flex items-center gap-4">
                    <button
                        type="submit"
                        disabled={isSubmitting}
                        className="flex items-center gap-2 bg-black text-white px-6 py-3 rounded-lg hover:bg-gray-800 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {isSubmitting ? (
                            <><span className="fa fa-spinner fa-spin"></span> Submitting...</>
                        ) : (
                            <><span className="fa fa-check"></span> Create Quiz</>
                        )}
                    </button>
                    
                      <a  href="/admin/ear-training"
                        className="text-sm text-gray-500 hover:text-gray-700 underline transition"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    );
};

export default CreateEarTrainingQuiz;

if (document.getElementById("ear-training-quiz")) {
    const root = ReactDOM.createRoot(document.getElementById("ear-training-quiz"));
    root.render(
        <FlashMessageProvider>
            <CreateEarTrainingQuiz />
        </FlashMessageProvider>
    );
}