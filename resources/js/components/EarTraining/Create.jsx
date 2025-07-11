import React, { useState } from "react";
import axios from "axios";
import ReactDOM from "react-dom/client";

const CreateEarTrainingQuiz = () => {
    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [videoUrl, setVideoUrl] = useState("");
    const [thumbnail, setThumbnail] = useState(null);
    const [mainAudio, setMainAudio] = useState(null);
    const [questions, setQuestions] = useState([
        { audio: null, correct_option: "" },
    ]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");

    const STANDARD_OPTIONS = ["DOH", "REH", "MI", "FAH", "SOH", "LAH", "TI"];

    const handleQuestionChange = (index, field, value) => {
        const updated = [...questions];
        updated[index][field] = value;
        setQuestions(updated);
    };

    const addQuestion = () => {
        setQuestions([...questions, { audio: null, correct_option: "" }]);
    };

    const removeQuestion = (index) => {
        const updated = questions.filter((_, i) => i !== index);
        setQuestions(updated);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setSuccessMessage("");

        const formData = new FormData();
        formData.append("title", title);
        formData.append("description", description);
        formData.append("video_url", videoUrl);
        formData.append("thumbnail", thumbnail);
        formData.append("main_audio", mainAudio);

        questions.forEach((q, index) => {
            formData.append(`questions[${index}][audio]`, q.audio);
            formData.append(`questions[${index}][correct_option]`, q.correct_option);
        });

        try {
            const response = await axios.post("/admin/ear-training", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            setSuccessMessage("Quiz created successfully!");
            setTitle("");
            setDescription("");
            setVideoUrl("");
            setThumbnail(null);
            setMainAudio(null);
            setQuestions([{ audio: null, correct_option: "" }]);
        } catch (error) {
            console.error("Error uploading quiz:", error);
            alert("Failed to create quiz.");
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="max-w-4xl mx-auto p-8 bg-white rounded-lg shadow mt-8">
            <h2 className="text-2xl font-bold mb-6">Create New Ear Training Quiz</h2>

            {successMessage && (
                <div className="mb-4 p-4 bg-green-100 border border-green-500 rounded">
                    {successMessage}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <label className="block mb-1 font-medium">Title</label>
                    <input
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        required
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-1 font-medium">Description</label>
                    <textarea
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-1 font-medium">Video Embed Code (iframe)</label>
                    <textarea
                        value={videoUrl}
                        onChange={(e) => setVideoUrl(e.target.value)}
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-1 font-medium">Thumbnail Image</label>
                    <input
                        type="file"
                        accept="image/*"
                        onChange={(e) => setThumbnail(e.target.files[0])}
                        required
                        className="w-full"
                    />
                </div>

                <div>
                    <label className="block mb-1 font-medium">Main Audio</label>
                    <input
                        type="file"
                        accept="audio/*"
                        onChange={(e) => setMainAudio(e.target.files[0])}
                        required
                        className="w-full"
                    />
                </div>

                <div>
                    <h3 className="text-lg font-semibold mb-2">Questions</h3>
                    {questions.map((q, index) => (
                        <div key={index} className="border p-4 rounded mb-4">
                            <div className="mb-2">
                                <label className="block mb-1 font-medium">Question Audio</label>
                                <input
                                    type="file"
                                    accept="audio/*"
                                    onChange={(e) => handleQuestionChange(index, "audio", e.target.files[0])}
                                    required
                                    className="w-full"
                                />
                            </div>

                            <div className="mb-2">
                                <label className="block mb-1 font-medium">Correct Option</label>
                                <select
                                    value={q.correct_option}
                                    onChange={(e) => handleQuestionChange(index, "correct_option", e.target.value)}
                                    required
                                    className="w-full p-2 border rounded"
                                >
                                    <option value="">-- Select Correct Option --</option>
                                    {STANDARD_OPTIONS.map((opt, i) => (
                                        <option key={i} value={i}>
                                            {opt}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {questions.length > 1 && (
                                <button
                                    type="button"
                                    onClick={() => removeQuestion(index)}
                                    className="text-red-600 mt-2"
                                >
                                    Remove Question
                                </button>
                            )}
                        </div>
                    ))}

                    <button
                        type="button"
                        onClick={addQuestion}
                        className="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        Add Another Question
                    </button>
                </div>

                <div className="pt-4">
                    <button
                        type="submit"
                        disabled={isSubmitting}
                        className="bg-black text-white px-6 py-3 rounded hover:bg-green-600"
                    >
                        {isSubmitting ? "Submitting..." : "Create Quiz"}
                    </button>
                </div>
            </form>
        </div>
    );
};

export default CreateEarTrainingQuiz;

if (document.getElementById("ear-training-quiz")) {
    const root = ReactDOM.createRoot(document.getElementById("ear-training-quiz"));
    root.render(<CreateEarTrainingQuiz />);
}
