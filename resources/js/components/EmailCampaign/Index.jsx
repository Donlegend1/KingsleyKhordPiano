import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import {
    Mail,
    Plus,
    Eye,
    RotateCcw,
    CheckCheckIcon,
    LoaderPinwheel,
} from "lucide-react";
import Modal from "../Modal/Modal";

const IndexEmailCampaign = () => {
    const [campaigns, setcampaigns] = useState([]);
    const [loading, setLoading] = useState(false);
    const [counts, setCounts] = useState({});
    const [selectedCampaign, setSelectedCampaign] = useState({});
    const [previewModal, setPreviewModal] = useState(false);

    const fetchEmailCampaign = async (page = 1) => {
        setLoading(true);
        try {
            const response = await axios.get(
                `/api/admin/email-campaigns?page=${page}`
            );
            setcampaigns(response.data?.data);
            setCounts(response.data?.counts);
        } catch (error) {
            console.error("Error fetching courses:", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchEmailCampaign();
    }, []);

    const openPreviewModal = (campaign) => {
        setSelectedCampaign(campaign);
        setPreviewModal(true);
    };

    const closePreviewModal = () => {
        setSelectedCampaign(null);
        setPreviewModal(false);
    };

    const handleResend = async (campaign) => {
        try {
            setLoading(true);

            const response = await axios.post(
                `/api/admin/email-campaign-resend/${campaign?.id}`
            );
            closePreviewModal();
            fetchEmailCampaign()
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
        <div className="p-6 space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-800">
                        Email Campaigns
                    </h1>
                    <p className="text-sm text-gray-500">
                        Send emails to premium, normal users or roadmap
                        subscribers
                    </p>
                </div>

                <a
                    href="/admin/email-campaign/create"
                    className="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow"
                >
                    <Plus className="w-4 h-4" />
                    Send New Email
                </a>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <StatCard title="Total Campaigns" value={campaigns.length} />
                <StatCard title="Sent to Premium" value={counts?.premium} />
                <StatCard title="Sent to Standard" value={counts?.standard} />
                <StatCard title="Roadmap Emails" value={counts?.visitor} />
            </div>

            {/* Table */}
            <div className="bg-white rounded-xl shadow overflow-hidden">
                <table className="w-full text-sm">
                    <thead className="bg-gray-50 text-gray-600">
                        <tr>
                            <th className="px-6 py-3 text-left">S/N</th>
                            <th className="px-6 py-3 text-left">Subject</th>
                            <th className="px-6 py-3 text-left">Audience</th>
                            <th className="px-6 py-3 text-left">Status</th>
                            <th className="px-6 py-3 text-left">Sent At</th>
                            <th className="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody className="divide-y">
                        {campaigns.map((campaign, index) => (
                            <tr key={campaign.id} className="hover:bg-gray-50">
                                <td className="px-6 py-4 font-medium text-gray-800">
                                    {index + 1}
                                </td>
                                <td className="px-6 py-4 font-medium text-gray-800">
                                    {campaign.subject}
                                </td>

                                <td className="px-6 py-4">
                                    <div className="flex flex-wrap gap-2">
                                        {campaign.targets.map((target) => (
                                            <span
                                                key={target}
                                                className="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700"
                                            >
                                                {target}
                                            </span>
                                        ))}
                                    </div>
                                </td>

                                <td className="px-6 py-4">
                                    <span
                                        className={`px-3 py-1 text-xs rounded-full ${
                                            campaign.status === "Sent"
                                                ? "bg-green-100 text-green-700"
                                                : "bg-yellow-100 text-yellow-700"
                                        }`}
                                    >
                                        {campaign.status}
                                    </span>
                                </td>

                                <td className="px-6 py-4 text-gray-500">
                                    {campaign.sent_at
                                        ? new Date(
                                              campaign.sent_at
                                          ).toLocaleString()
                                        : "—"}
                                </td>

                                <td className="px-6 py-4 text-right">
                                    <div className="flex justify-end gap-2">
                                        <button
                                            className="p-2 rounded-lg hover:bg-gray-100"
                                            onClick={(e) =>
                                                openPreviewModal(campaign)
                                            }
                                        >
                                            <Eye className="w-4 h-4 text-gray-600" />
                                        </button>

                                        {campaign.status === "sent" ? (
                                            <button className="p-2 rounded-lg hover:bg-gray-100">
                                                <CheckCheckIcon className="w-4 h-4 text-gray-600" />
                                            </button>
                                        ) : (
                                            <button
                                                className="p-2 rounded-lg hover:bg-gray-100"
                                                disabled={loading}
                                                onClick={(e) =>
                                                    handleResend(campaign)
                                                }
                                            >
                                                {loading ? (
                                                    <LoaderPinwheel />
                                                ) : (
                                                    <RotateCcw className="w-4 h-4 text-gray-600" />
                                                )}
                                            </button>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        ))}

                        {campaigns.length === 0 && (
                            <tr>
                                <td
                                    colSpan="5"
                                    className="px-6 py-10 text-center text-gray-500"
                                >
                                    No email campaigns yet
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
            <Modal isOpen={previewModal} onClose={closePreviewModal}>
                <div className="bg-white rounded-xl shadow-lg max-w-3xl w-full mx-auto p-6 space-y-6">
                    {/* Header */}
                    <div className="flex justify-between items-center border-b pb-3">
                        <h2 className="text-xl font-semibold text-gray-800">
                            {selectedCampaign?.subject || "No Subject"}
                        </h2>
                    </div>

                    {/* Target Info */}
                    <div className="flex gap-2 text-sm text-gray-500">
                        <span className="font-medium">Targets:</span>
                        {selectedCampaign?.targets?.length ? (
                            selectedCampaign.targets.map((target) => (
                                <span
                                    key={target}
                                    className="px-2 py-1 bg-gray-100 rounded-full text-gray-700"
                                >
                                    {target.charAt(0).toUpperCase() +
                                        target.slice(1)}
                                </span>
                            ))
                        ) : (
                            <span>—</span>
                        )}
                    </div>

                    {/* Sent Info */}
                    <div className="text-sm text-gray-400">
                        {selectedCampaign?.sent_at ? (
                            `Sent at: ${new Date(
                                selectedCampaign.sent_at
                            ).toLocaleString()}`
                        ) : (
                            <div className="flex justify-between">
                                <p>"Not sent yet"</p>
                                <button
                                    className="p-2 rounded-lg hover:bg-gray-100"
                                    onClick={(e) =>
                                        handleResend(selectedCampaign)
                                    }
                                >
                                    <RotateCcw className="w-4 h-4 text-gray-600" />
                                </button>
                            </div>
                        )}
                    </div>

                    {/* Body */}
                    <div className="prose max-w-none border p-4 rounded-lg bg-gray-50">
                        {selectedCampaign?.body ? (
                            <div
                                dangerouslySetInnerHTML={{
                                    __html: selectedCampaign.body,
                                }}
                            />
                        ) : (
                            <p className="text-gray-400 italic">
                                No content available
                            </p>
                        )}
                    </div>

                    {/* Footer Actions */}
                    <div className="flex justify-end gap-3 pt-4 border-t">
                        <button
                            onClick={closePreviewModal}
                            className="px-4 py-2 rounded-lg border hover:bg-gray-50"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    );
};

const StatCard = ({ title, value }) => (
    <div className="bg-white rounded-xl shadow p-5 flex items-center gap-4">
        <div className="p-3 rounded-lg bg-indigo-100">
            <Mail className="w-5 h-5 text-indigo-600" />
        </div>
        <div>
            <p className="text-sm text-gray-500">{title}</p>
            <p className="text-xl font-semibold text-gray-800">{value}</p>
        </div>
    </div>
);

export default IndexEmailCampaign;

if (document.getElementById("email-campaign")) {
    const root = ReactDOM.createRoot(document.getElementById("email-campaign"));
    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <IndexEmailCampaign />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
