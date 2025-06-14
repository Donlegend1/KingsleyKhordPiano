import ReactDOM from "react-dom/client";
import React, { useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";


const LiveShowForm = () => {
    const [liveShow, setLiveShow] = useState({
        title: "",
        recording_url: "",
        zoom_link: "",
        access_type: "all",
        start_time: "",
    });

    const { showMessage } = useFlashMessage();

    const handleChange = (e) => {
        const { name, value } = e.target;
        setLiveShow((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post(
                "/admin/live-shows",
                liveShow
            );
            showMessage("Live show created successfully!", "success");
            setLiveShow({
                title: "",
                recording_url: "",
                zoom_link: "",
                access_type: "all",
                start_time: "", 
            })
           
        } catch (error) {
            console.error("Error creating live show:", error);
            showMessage("Failed to create live show. Check console for details.", "error");
        }
    };

    return (
        <div className="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12">
            <div className="w-full max-w-2xl bg-white p-8 rounded-xl shadow-lg">
                <h2 className="text-2xl font-bold mb-6 text-gray-800 text-center">
                    Create New Live Show
                </h2>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Title
                            </label>
                            <input
                                name="title"
                                type="text"
                                placeholder="Enter live show title"
                                defaultValue={liveShow.title}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-md"
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Start Date & Time
                            </label>
                            <input
                                name="start_time"
                                type="datetime-local"
                                defaultValue={liveShow.start_time}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-md"
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Zoom Link
                            </label>
                            <input
                                name="zoom_link"
                                type="url"
                                placeholder="e.g. https://zoom.us/..."
                                defaultValue={liveShow.zoom_link}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-md"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Access Type
                            </label>
                            <select
                                name="access_type"
                                defaultValue={liveShow.access_type}
                                onChange={handleChange}
                                className="w-full p-3 border rounded-md"
                            >
                                <option value="all">All Members</option>
                                <option value="premium">Premium Only</option>
                            </select>
                        </div>
                    </div>

                    <div className="text-center">
                        <button
                            type="submit"
                            className="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition"
                        >
                            Save Live Show
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default LiveShowForm;

// Mount component
if (document.getElementById("liveShows-create")) {
    const root = ReactDOM.createRoot(
        document.getElementById("liveShows-create")
    );
    root.render(
        <React.StrictMode>
             <FlashMessageProvider>
            <LiveShowForm />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
