import React, { useState } from "react";
import axios from "axios";
import ReactDOM from "react-dom/client";

const CreateEarTrainingQuiz = () => {
    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [category, setCategory] = useState("");
    const [videoUrl, setVideoUrl] = useState("");
    const [thumbnail, setThumbnail] = useState(null);
    const [mainAudio, setMainAudio] = useState(null);
    const [questions, setQuestions] = useState([
        { audio: null, correct_option: "" },
    ]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");
    const categories = [
        "Relative Pitch",
        "Di-tone Pitch",
        "Diatonic Intervals",
        "Non-diatonic Intervals",
        "Intervals",
        "Basic Triad",
        "7th Degree Chords (Basic)",
        "7th Degree Chords (Secondary)",
        "7th Degree Chords (General)",
        "9th degree Chords (Basic)",
        "9th Degree Chords (Secondary)",
        "9th Degree Chords (General)",
        "11th Degree Chords",
        "13th Degree Chords",
        "Others"
    ];

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
    const DIATOMIC_INTERVALS = [
        "Major 2nd",
        "Major 3rd",
        "Perfect 4th",
        "Perfect 5th",
        "Major 6th",
        "Major 7th",
        "Octave",
    ];
    const NONDIATOMIC_INTERVALS = [
        "Minor 2nd",
        "Minor 3rd",
        "Tri tone",
        "Minor 6th",
        "Minor 7th",
    ];
    const BASICTRIADS = ["Augmented", "Diminished", "Major", "Minor", "Sus"];

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

    const SEVENDEGREECHORD = [
        "Diminished 7th",
        "Dominant 7th",
        "Minor 7b5",
        "Major 7th",
        "Minor 7th",
    ];

    const SEVENDEGREECHORDSECONDARY = [
        "Dim (Maj7)",
        "Dom7#5",
        "Dom7b5",
        "Maj7#5",
        "Maj7b5",
        "minMaj7",
    ];
    const SEVENDEGREECHORDEGENERAL = [
        "Diminished 7th",
        "Dominant 7th",
        "Minor 7b5",
        "Major 7th",
        "Minor 7th",
        "Dim (Maj7)",
        "Dom7#5",
        "Dom7b5",
        "Maj7#5",
        "Maj7b5",
        "minMaj7",
    ];

    const NINEDEGREECHORD = [
        "Dim7 (9)",
        "Dom9",
        "Dom7 (b9)",
        "Maj 6/9",
        "min 6/9",
        "min9",
        "min9 (b5)",
    ];

    const NINEDEGREECHORDSECONDARY = [
        "DimMaj7 (9)",
        "Dom9 (b5)",
        "Dom9 (#5)",
        "Maj9 (b5)",
        "Maj9 (#5)",
        "min (Maj9)",
    ];

    const NINEDEGREECHORDGENERAL = [
        "DimMaj7 (9)",
        "Dom9 (b5)",
        "Dom9 (#5)",
        "Maj9 (b5)",
        "Maj9 (#5)",
        "min (Maj9)",
        "Dim7 (9)",
        "Dom9",
        "Dom7 (b9)",
        "Maj 6/9",
        "min 6/9",
        "min9",
        "min9 (b5)",
    ];

    const ELEVENDEGREE = [
        "6/9 (#11)",
        "Dom9 (#11)",
        "Dom7 (b9#11)",
        "Maj9 (#11)",
        "min6/9 (11)",
        "min 9 (11)",
    ];

    const THIRTEENDEGREE = [
        "13sus4",
        "Dom 9 (13) #11",
        "Dom 13 (b9#11)",
        "Maj13 (#11)",
        "min13 (9,11)",
    ];

    const OTHERS = [
        "9sus4",
        "DimM9 (#5)",
        "Dom9 (#5b5)",
        "Maj9sus4",
        "Maj9 (b5#5)",
        "min9/11 (Maj7)",
    ];

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
        formData.append("category", category);
        formData.append("video_url", videoUrl);
        formData.append("thumbnail", thumbnail);
        formData.append("main_audio", mainAudio);

        questions.forEach((q, index) => {
            formData.append(`questions[${index}][audio]`, q.audio);
            formData.append(
                `questions[${index}][correct_option]`,
                q.correct_option
            );
        });

        try {
            const response = await axios.post("/admin/ear-training", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            setSuccessMessage("Quiz created successfully!");
            setTitle("");
            setDescription("");
            setVideoUrl("");
            setCategory("");
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
            <h2 className="text-2xl font-bold mb-6">
                Create New Ear Training Quiz
            </h2>

            {successMessage && (
                <div className="mb-4 p-4 bg-green-100 border border-green-500 rounded">
                    {successMessage}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <label className="block mb-1 font-medium">Categories</label>
                    <select
                        name=""
                        id=""
                        className="w-full p-2 border rounded"
                        onChange={(e) => setCategory(e.target.value)}
                    >
                        <option value="">--select-</option>
                        {categories.map((catgory, index) => (
                            <option value={catgory} key={index}>
                                {catgory}
                            </option>
                        ))}
                    </select>
                </div>
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
                    <label className="block mb-1 font-medium">
                        Description
                    </label>
                    <textarea
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-1 font-medium">
                        Google Video Link
                    </label>
                    <textarea
                        value={videoUrl}
                        onChange={(e) => setVideoUrl(e.target.value)}
                        className="w-full p-2 border rounded"
                    />
                </div>

                <div>
                    <label className="block mb-1 font-medium">
                        Thumbnail Image
                    </label>
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
                                <label className="block mb-1 font-medium">
                                    Question Audio
                                </label>
                                <input
                                    type="file"
                                    accept="audio/*"
                                    onChange={(e) =>
                                        handleQuestionChange(
                                            index,
                                            "audio",
                                            e.target.files[0]
                                        )
                                    }
                                    required
                                    className="w-full"
                                />
                            </div>

                            <div className="mb-2">
                                <label className="block mb-1 font-medium">
                                    Correct Option
                                </label>
                                <select
                                    value={q.correct_option}
                                    onChange={(e) =>
                                        handleQuestionChange(
                                            index,
                                            "correct_option",
                                            e.target.value
                                        )
                                    }
                                    required
                                    className="w-full p-2 border rounded"
                                >
                                    <option value="">
                                        -- Select Correct Option --
                                    </option>

                                    {category === "Relative Pitch" &&
                                        RELATIVE_OPTIONS.map((opt, i) => (
                                            <option key={i} value={i}>
                                                {opt}
                                            </option>
                                        ))}
                                    {category === "Di-tone Pitch" &&
                                        DITONE_OPTIONS.map((opt, i) => (
                                            <option key={i} value={i}>
                                                {opt}
                                            </option>
                                        ))}
                                    {category === "Diatonic Intervals" &&
                                        DIATOMIC_INTERVALS.map((opt, i) => (
                                            <option key={i} value={i}>
                                                {opt}
                                            </option>
                                        ))}
                                    {category === "Non-diatonic Intervals" &&
                                        NONDIATOMIC_INTERVALS.map((opt, i) => (
                                            <option key={i} value={i}>
                                                {opt}
                                            </option>
                                        ))}
                                    {category === "Intervals" &&
                                        INTERVALS.map((opt, i) => (
                                            <option key={i} value={i}>
                                                {opt}
                                            </option>
                                        ))}
                                    {category === "Basic Triad" &&
                                        BASICTRIADS.map((opt, i) => (
                                            <option key={i} value={i}>
                                                {opt}
                                            </option>
                                        ))}
                                    {category === "7th Degree Chords (Basic)" &&
                                        SEVENDEGREECHORD.map((opt, i) => (
                                            <option key={i} value={i}>
                                                {opt}
                                            </option>
                                        ))}
                                    
                                    {category ===
                                        "7th Degree Chords (Secondary)" &&
                                        SEVENDEGREECHORDSECONDARY.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
                                    {category === "7th Degree Chords (General)" &&
                                        SEVENDEGREECHORDEGENERAL.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
                                    {category === "9th degree Chords (Basic)" &&
                                        NINEDEGREECHORD.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
                                    {category === "9th Degree Chords (Secondary)" &&
                                        NINEDEGREECHORDSECONDARY.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
                                    {category === "9th Degree Chords (General)" &&
                                        NINEDEGREECHORDGENERAL.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
                                    {category === "11th Degree Chords" &&
                                        ELEVENDEGREE.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
                                    
                                    {category === "13th Degree Chords" &&
                                        THIRTEENDEGREE.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
                                    {category === "Others" &&
                                        OTHERS.map(
                                            (opt, i) => (
                                                <option key={i} value={i}>
                                                    {opt}
                                                </option>
                                            )
                                        )}
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
    const root = ReactDOM.createRoot(
        document.getElementById("ear-training-quiz")
    );
    root.render(<CreateEarTrainingQuiz />);
}
