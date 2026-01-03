import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import ReactQuill from "react-quill";
import { Send, Eye, ArrowLeft, Loader2 } from "lucide-react";

const CreateEmailCampaign = () => {
    const [subject, setSubject] = useState("");
    const [body, setBody] = useState("");
    const [targets, setTargets] = useState([]);
    const [loading, setLoading] = useState(false);
    const { showMessage } = useFlashMessage();

    const toggleTarget = (value) => {
        setTargets((prev) =>
            prev.includes(value)
                ? prev.filter((t) => t !== value)
                : [...prev, value]
        );
    };

    const handleSubmit = async (status = "sent") => {
        if (!targets.length) {
            showMessage("Please select at least one audience", "error");
            return;
        }

        const payload = { subject, body, targets, status };

        try {
            setLoading(true);

            const response = await axios.post(
                "/api/admin/email-campaign",
                payload
            );

            if (response.status === 200 || response.status === 201) {
                showMessage(
                    status === "sent"
                        ? "Email Campaign sent successfully!"
                        : "Email Campaign saved as draft!",
                    "success"
                );
                window.location = "/admin/email-campaign";
            }
        } catch (error) {
            if (error.response?.status === 422) {
                const errors = error.response.data.errors;
                Object.values(errors)
                    .flat()
                    .forEach((msg) => showMessage(msg, "error"));
            } else {
                console.error(error);
                showMessage("Failed to send Email Campaign", "error");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="p-6 max-w-5xl mx-auto space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-3">
                    <a
                        href="/admin/email-campaign"
                        className="p-2 rounded-lg hover:bg-gray-100"
                    >
                        <ArrowLeft className="w-5 h-5" />
                    </a>

                    <div>
                        <h1 className="text-2xl font-semibold text-gray-800">
                            Create Email Campaign
                        </h1>
                        <p className="text-sm text-gray-500">
                            Send targeted emails to users or roadmap subscribers
                        </p>
                    </div>
                </div>
            </div>

            {/* Form */}
            <div className="bg-white rounded-xl shadow p-6 space-y-6">
                {/* Audience */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        Send To
                    </label>

                    <div className="flex flex-wrap gap-4">
                        <AudienceOption
                            label="Premium Users"
                            value="premium"
                            checked={targets.includes("premium")}
                            onChange={toggleTarget}
                        />
                        <AudienceOption
                            label="Standard Users"
                            value="standard"
                            checked={targets.includes("standard")}
                            onChange={toggleTarget}
                        />
                        <AudienceOption
                            label="Roadmap Subscribers"
                            value="visitor"
                            checked={targets.includes("visitor")}
                            onChange={toggleTarget}
                        />
                    </div>
                </div>

                {/* Subject */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        Email Subject
                    </label>
                    <input
                        type="text"
                        value={subject}
                        onChange={(e) => setSubject(e.target.value)}
                        placeholder="Enter email subject"
                        className="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>

                {/* Editor */}
                <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                        Email Content
                    </label>

                    <div className="border rounded-lg overflow-hidden">
                        <ReactQuill
                            theme="snow"
                            value={body}
                            onChange={setBody}
                            placeholder="Write your email here..."
                            className="bg-white"
                        />
                    </div>
                </div>

                {/* Actions */}
                <div className="flex justify-end gap-3 pt-4 border-t">
                    <button
                        onClick={() => handleSubmit("draft")}
                        disabled={loading}
                        className="flex items-center gap-2 px-4 py-2 rounded-lg border hover:bg-gray-50"
                    >
                        {loading ? (
                            <Loader2 className="w-4 h-4 animate-spin" />
                        ) : (
                            <>
                                <Eye className="w-4 h-4" />
                                Save as Draft
                            </>
                        )}
                    </button>

                    <button
                        onClick={() => handleSubmit("sent")}
                        disabled={loading}
                        className="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg shadow"
                    >
                        {loading ? (
                            <Loader2 className="w-4 h-4 animate-spin" />
                        ) : (
                            <>
                                <Send className="w-4 h-4" />
                                Send Email
                            </>
                        )}
                    </button>
                </div>
            </div>
        </div>
    );
};

const AudienceOption = ({ label, value, checked, onChange }) => (
    <label
        className={`flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer ${
            checked
                ? "border-indigo-500 bg-indigo-50 text-indigo-700"
                : "border-gray-300"
        }`}
    >
        <input
            type="checkbox"
            checked={checked}
            onChange={() => onChange(value)}
            className="hidden"
        />
        {label}
    </label>
);

export default CreateEmailCampaign;

if (document.getElementById("email-campaign-create")) {
    const root = ReactDOM.createRoot(
        document.getElementById("email-campaign-create")
    );
    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <CreateEmailCampaign />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
