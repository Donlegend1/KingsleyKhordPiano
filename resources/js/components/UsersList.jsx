import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";
import CustomPagination from "./Pagination/CustomPagination";
import AuthorNameWithVerification from "./User/AuthorNameWithVerification";
import Modal from "./Modal/Modal";

const UsersList = () => {
    const [userList, setUserList] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [loading, setLoading] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);
    const [paymentModalOpen, setPaymentModalOpen] = useState(false);
    const [editForm, setEditForm] = useState({
        first_name: "",
        last_name: "",
        email: "",
        premium: 0,
        payment_status: null,
        verified: 0,
    });
    const [paymentDetails, setPaymentDetails] = useState({
        user_id: null,
        starts_at: "",
        ends_at: "",
        paused: false,
    });
    const [subscriptionSaving, setSubscriptionSaving] = useState(false);
    const [error, setError] = useState(null);

    const perPage = 10;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const { showMessage } = useFlashMessage();

    const fetchUsers = async (page = 1) => {
        setLoading(true);
        setError(null);
        try {
            const response = await axios.get(
                `/api/admin/users?page=${page}&perPage=${perPage}`,
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );

            setUserList(response.data.data);
            setCurrentPage(response.data.current_page);
            setTotalPages(response.data.last_page);
        } catch (error) {
            console.error("Error fetching users:", error);
            setError(error.response?.data?.message || "Error loading users");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, []);

    const handlePageChange = (page) => fetchUsers(page);

    const openEditModal = (user) => {
        setSelectedUser(user);
        setEditForm({
            first_name: user.first_name,
            last_name: user.last_name,
            email: user.email,
            premium: user.premium,
            payment_status: user.payment_status,
            verified: user.verified,
        });
        setIsEditModalOpen(true);
    };

    const closeEditModal = () => {
        setIsEditModalOpen(false);
    };

    const openDeleteModal = (user) => {
        setSelectedUser(user);
        setIsDeleteModalOpen(true);
    };

    const handleEditSubmit = async () => {
        setLoading(true);

        const payload = {};

        if (editForm.first_name !== selectedUser.first_name) {
            payload.first_name = editForm.first_name;
        }

        if (editForm.last_name !== selectedUser.last_name) {
            payload.last_name = editForm.last_name;
        }

        if (editForm.email !== selectedUser.email) {
            payload.email = editForm.email;
        }

        if (editForm.premium !== selectedUser.premium) {
            payload.premium = editForm.premium;
        }

        if (editForm.verified !== selectedUser.verified) {
            payload.verified = editForm.verified;
        }

        if (editForm.payment_status !== selectedUser.payment_status) {
            payload.payment_status = editForm.payment_status;
        }

        // Nothing changed → exit early
        if (Object.keys(payload).length === 0) {
            showMessage("No changes detected", "info");
            setLoading(false);
            return;
        }

        try {
            await axios.put(`/api/admin/users/${selectedUser.id}`, payload, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });

            setIsEditModalOpen(false);
            fetchUsers(currentPage);
            showMessage("User updated successfully", "success");
        } catch (error) {
            console.error("Error updating user:", error);
            showMessage("Error updating user", "error");
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteSubmit = async () => {
        try {
            await axios.delete(`/api/admin/user/${selectedUser.id}`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });
            setIsDeleteModalOpen(false);
            fetchUsers(currentPage);
            showMessage("User deleted sucessfully", "success");
        } catch (error) {
            console.error("Error deleting user:", error);
            showMessage("Error deleting user", "error");
        }
    };

    const toDateInput = (value) => {
        if (!value) return "";
        const match = String(value).match(/^(\d{4}-\d{2}-\d{2})/);
        if (match) return match[1];
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return "";
        const pad = (part) => String(part).padStart(2, "0");
        return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    };

    const subscriptionPayload = (user, overrides = {}) => {
        const details = getSubscriptionDetails(user);
        const paused =
            String(details.status || "").toLowerCase() === "paused";

        return {
            starts_at: toDateInput(details.startedAt) || null,
            ends_at: toDateInput(details.endsAt) || null,
            paused,
            ...overrides,
        };
    };

    const handleOpenPaymentModal = (user) => {
        setSelectedUser(user);
        setPaymentModalOpen(true);
        setPaymentDetails({
            user_id: user?.id,
            ...subscriptionPayload(user),
        });
    };

    const handleClosePaymentModal = () => {
        setPaymentModalOpen(false);
        setPaymentDetails({
            user_id: null,
            starts_at: "",
            ends_at: "",
            paused: false,
        });
    };

    const handleChangeSubscription = (e) => {
        const { name, value, type, checked } = e.target;
        setPaymentDetails({
            ...paymentDetails,
            [name]: type === "checkbox" ? checked : value,
        });
    };

    const saveSubscription = async (user, payload, successMessage) => {
        setSubscriptionSaving(true);
        try {
            await axios.put(
                `/api/admin/users/${user.id}/subscription`,
                payload,
                { headers: { "X-CSRF-TOKEN": csrfToken } }
            );
            showMessage(successMessage, "success");
            fetchUsers(currentPage);
            return true;
        } catch (error) {
            console.error("Error updating subscription:", error);
            const message =
                error.response?.data?.message ||
                "Could not update the subscription";
            showMessage(message, "error");
            return false;
        } finally {
            setSubscriptionSaving(false);
        }
    };

    const handleUpdateSubscription = async () => {
        if (!selectedUser) return;
        if (!paymentDetails.starts_at || !paymentDetails.ends_at) {
            showMessage("Choose a start date and an expiry date", "error");
            return;
        }

        const saved = await saveSubscription(
            selectedUser,
            {
                starts_at: paymentDetails.starts_at,
                ends_at: paymentDetails.ends_at,
                paused: !!paymentDetails.paused,
            },
            paymentDetails.paused
                ? "Subscription paused"
                : "Subscription dates saved"
        );

        if (saved) {
            handleClosePaymentModal();
        }
    };

    const handleTogglePause = async (user) => {
        const details = getSubscriptionDetails(user);
        const paused = String(details.status || "").toLowerCase() === "paused";
        await saveSubscription(
            user,
            subscriptionPayload(user, { paused: !paused }),
            paused ? "Subscription resumed" : "Subscription paused"
        );
    };

    const handleSearchUsers = async (e) => {
        const query = e.target.value;
        setLoading(true);
        try {
            const response = await axios.get(
                `/api/admin/users?search=${query}&perPage=${perPage}`,
                {
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    withCredentials: true,
                }
            );
            setUserList(response.data.data);
            setCurrentPage(response.data.current_page);
            setTotalPages(response.data.last_page);
        } catch (error) {
            console.error("Error searching users:", error);
        } finally {
            setLoading(false);
        }
    };

    const formatDate = (value) => {
        if (!value) return null;
        const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);
        const date = match
            ? new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]))
            : new Date(value);
        if (Number.isNaN(date.getTime())) return null;
        return date.toLocaleDateString(undefined, {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    };

    const formatLabel = (value) => {
        if (!value) return null;
        return String(value)
            .replace(/_/g, " ")
            .replace(/\b\w/g, (char) => char.toUpperCase());
    };

    const getSubscriptionDetails = (user) => {
        const details = user.subscription_details || {};
        const latestSub = user.subscriptions?.[0];
        const metadata = user.metadata || {};

        return {
            type:
                details.type ||
                metadata.duration ||
                user.subscription_type ||
                null,
            tier: details.tier || metadata.tier || (user.premium ? "premium" : null),
            paymentMethod:
                details.payment_method ||
                user.payment_method ||
                latestSub?.payment_method ||
                null,
            status:
                details.status ||
                user.subscription_status ||
                latestSub?.stripe_status ||
                null,
            startedAt:
                details.started_at ||
                user.subscription_started_at ||
                latestSub?.created_at ||
                null,
            endsAt:
                details.ends_at ||
                user.subscription_expires_at ||
                latestSub?.ends_at ||
                null,
        };
    };

    const describeSubscription = (user) => {
        const subscription = getSubscriptionDetails(user);
        const typeLabel = formatLabel(subscription.type);
        const tierLabel = formatLabel(subscription.tier);
        const startsAtLabel = formatDate(subscription.startedAt);
        const endsAtLabel = formatDate(subscription.endsAt);
        const isPaused =
            String(subscription.status || "").toLowerCase() === "paused";

        let rangeLabel = "No expiry date";
        if (startsAtLabel && endsAtLabel) {
            rangeLabel = `${startsAtLabel} – ${endsAtLabel}`;
        } else if (endsAtLabel) {
            rangeLabel = `Expires ${endsAtLabel}`;
        } else if (startsAtLabel) {
            rangeLabel = `Starts ${startsAtLabel}`;
        }

        return {
            subscription,
            title:
                typeLabel || tierLabel
                    ? [tierLabel, typeLabel].filter(Boolean).join(" · ")
                    : "No subscription",
            providerLabel: formatLabel(subscription.paymentMethod),
            rangeLabel,
            isPaused,
        };
    };

    const providerBadgeClass = (method) => {
        const normalized = String(method || "").toLowerCase();
        if (normalized.includes("stripe")) {
            return "bg-indigo-100 text-indigo-800";
        }
        if (normalized.includes("paystack")) {
            return "bg-emerald-100 text-emerald-800";
        }
        if (normalized.includes("paypal")) {
            return "bg-sky-100 text-sky-800";
        }
        if (normalized.includes("manual")) {
            return "bg-gray-100 text-gray-700";
        }
        return "bg-slate-100 text-slate-700";
    };

    return (
        <div className="flex flex-col h-full">
            {/* Header section */}
            <div className="mb-6 rounded-2xl border border-gray-100 bg-white p-5">
                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    {/* Title */}
                    <div>
                        <h2 className="text-xl font-semibold text-gray-900">
                            Users
                        </h2>
                        <p className="mt-1 text-sm text-gray-500">
                            Manage and monitor user accounts
                        </p>
                    </div>

                    {/* Search */}
                    <div className="relative w-full md:w-80">
                        <span className="pointer-events-none mt-3 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i className="fa fa-search text-sm"></i>
                        </span>

                        <input
                            type="search"
                            onChange={handleSearchUsers}
                            placeholder="Search by name or email"
                            className="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-11 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition"
                        />
                    </div>
                </div>
            </div>

            <div className="flex-1">
                {userList.length === 0 ? (
                    <div className="px-6 py-12 text-center">
                        <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                            <i className="fas fa-users text-2xl text-gray-400"></i>
                        </div>
                        <p className="text-sm text-gray-500">No users found</p>
                    </div>
                ) : (
                    <>
                        <div className="space-y-3 md:hidden">
                            {userList.map((user, index) => {
                                const summary = describeSubscription(user);
                                return (
                                    <article
                                        key={user.id}
                                        className="rounded-xl border border-gray-200 bg-white p-4"
                                    >
                                        <div className="flex items-start gap-3">
                                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-blue-200 to-blue-300 font-semibold text-blue-600">
                                                {user.first_name?.[0]}
                                                {user.last_name?.[0]}
                                            </div>
                                            <div className="min-w-0 flex-1">
                                                <div className="flex items-start justify-between gap-2">
                                                    <AuthorNameWithVerification
                                                        author={user}
                                                    />
                                                    <span className="text-xs text-gray-400">
                                                        #
                                                        {(currentPage - 1) *
                                                            perPage +
                                                            index +
                                                            1}
                                                    </span>
                                                </div>
                                                <p className="mt-1 break-all text-sm text-gray-500">
                                                    {user.email}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="mt-3 flex flex-wrap items-center gap-2">
                                            <PaymentBadge user={user} />
                                            <SubscriptionBadge
                                                summary={summary}
                                                providerBadgeClass={
                                                    providerBadgeClass
                                                }
                                            />
                                        </div>
                                        <p className="mt-2 text-xs text-gray-500">
                                            {summary.rangeLabel}
                                        </p>
                                        <div className="mt-3 flex flex-wrap gap-2">
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    handleOpenPaymentModal(user)
                                                }
                                                className="rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-medium text-blue-700"
                                            >
                                                Set dates
                                            </button>
                                            <button
                                                type="button"
                                                disabled={subscriptionSaving}
                                                onClick={() =>
                                                    handleTogglePause(user)
                                                }
                                                className={`rounded-lg px-3 py-1.5 text-xs font-medium ${
                                                    summary.isPaused
                                                        ? "bg-emerald-100 text-emerald-800"
                                                        : "bg-amber-100 text-amber-800"
                                                }`}
                                            >
                                                {summary.isPaused
                                                    ? "Resume"
                                                    : "Pause"}
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    openEditModal(user)
                                                }
                                                className="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-600"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    openDeleteModal(user)
                                                }
                                                className="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>

                        <div className="hidden overflow-x-auto md:block">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr className="bg-gradient-to-r from-blue-500 to-blue-600">
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white lg:px-6">
                                            S/N
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white lg:px-6">
                                            User
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white lg:px-6">
                                            Email
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white lg:px-6">
                                            Status
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white lg:px-6">
                                            Subscription
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white lg:px-6">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 bg-white">
                                    {userList.map((user, index) => {
                                        const summary =
                                            describeSubscription(user);
                                        return (
                                            <tr
                                                key={user.id}
                                                className="align-top transition-colors hover:bg-gray-50"
                                            >
                                                <td className="px-4 py-4 text-sm text-gray-500 lg:px-6">
                                                    {(currentPage - 1) *
                                                        perPage +
                                                        index +
                                                        1}
                                                </td>
                                                <td className="px-4 py-4 lg:px-6">
                                                    <div className="flex items-center">
                                                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-blue-200 to-blue-300 font-semibold text-blue-600">
                                                            {user.first_name?.[0]}
                                                            {user.last_name?.[0]}
                                                        </div>
                                                        <div className="ml-3 min-w-0">
                                                            <AuthorNameWithVerification
                                                                author={user}
                                                            />
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="max-w-[220px] px-4 py-4 text-sm text-gray-500 lg:px-6">
                                                    <span className="break-all">
                                                        {user.email}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4 lg:px-6">
                                                    <PaymentBadge user={user} />
                                                </td>
                                                <td className="px-4 py-4 lg:px-6">
                                                    <div className="max-w-xs space-y-1.5">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {summary.title}
                                                        </div>
                                                        <SubscriptionBadge
                                                            summary={summary}
                                                            providerBadgeClass={
                                                                providerBadgeClass
                                                            }
                                                        />
                                                        <div className="text-xs text-gray-500">
                                                            {summary.rangeLabel}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4 text-sm font-medium lg:px-6">
                                                    <div className="flex flex-wrap items-center gap-2">
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                handleOpenPaymentModal(
                                                                    user
                                                                )
                                                            }
                                                            className="rounded-lg bg-blue-100 px-2.5 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-200"
                                                        >
                                                            Dates
                                                        </button>
                                                        <button
                                                            type="button"
                                                            disabled={
                                                                subscriptionSaving
                                                            }
                                                            onClick={() =>
                                                                handleTogglePause(
                                                                    user
                                                                )
                                                            }
                                                            className={`rounded-lg px-2.5 py-1.5 text-xs font-medium ${
                                                                summary.isPaused
                                                                    ? "bg-emerald-100 text-emerald-800 hover:bg-emerald-200"
                                                                    : "bg-amber-100 text-amber-800 hover:bg-amber-200"
                                                            }`}
                                                        >
                                                            {summary.isPaused
                                                                ? "Resume"
                                                                : "Pause"}
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                openEditModal(
                                                                    user
                                                                )
                                                            }
                                                            className="inline-flex items-center rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100"
                                                        >
                                                            <i className="fas fa-edit"></i>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                openDeleteModal(
                                                                    user
                                                                )
                                                            }
                                                            className="inline-flex items-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100"
                                                        >
                                                            <i className="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </>
                )}
            </div>

            {/* Pagination section */}
            <div className="mt-4 p-4 border-t">
                <CustomPagination
                    currentPage={currentPage}
                    totalPages={totalPages}
                    onPageChange={handlePageChange}
                />
            </div>

            {/* Error Display */}
            {error && (
                <div className="mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    {error}
                </div>
            )}

            {/* Edit Modal */}
            <Modal isOpen={isEditModalOpen} onClose={closeEditModal}>
                <div className="w-full max-w-xl p-6">
                    {/* Header */}
                    <div className="mb-6">
                        <h2 className="text-lg font-semibold text-gray-900">
                            Edit User
                        </h2>
                        <p className="text-sm text-gray-500">
                            Update user information and account status
                        </p>
                    </div>

                    {/* Form */}
                    <div className="space-y-5">
                        {/* Name */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="text-xs font-medium text-gray-500">
                                    First Name
                                </label>
                                <input
                                    type="text"
                                    value={editForm.first_name}
                                    onChange={(e) =>
                                        setEditForm({
                                            ...editForm,
                                            first_name: e.target.value,
                                        })
                                    }
                                    className="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label className="text-xs font-medium text-gray-500">
                                    Last Name
                                </label>
                                <input
                                    type="text"
                                    value={editForm.last_name}
                                    onChange={(e) =>
                                        setEditForm({
                                            ...editForm,
                                            last_name: e.target.value,
                                        })
                                    }
                                    className="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                        </div>

                        {/* Email */}
                        <div>
                            <label className="text-xs font-medium text-gray-500">
                                Email Address
                            </label>
                            <input
                                type="email"
                                value={editForm.email}
                                onChange={(e) =>
                                    setEditForm({
                                        ...editForm,
                                        email: e.target.value,
                                    })
                                }
                                className="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        {/* Divider */}
                        <div className="border-t pt-4" />

                        {/* Status Row */}
                        <div className="grid grid-cols-2 gap-4">
                            {/* Payment */}
                            <div>
                                <label className="text-xs font-medium text-gray-500">
                                    Payment Status
                                </label>
                                <select
                                    value={editForm.payment_status}
                                    onChange={(e) =>
                                        setEditForm({
                                            ...editForm,
                                            payment_status: e.target.value,
                                        })
                                    }
                                    className="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Select status</option>
                                    <option value="successful">Paid</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>

                            {/* Toggles */}
                            <div className="space-y-3">
                                <Toggle
                                    label="Premium Account"
                                    checked={!!editForm.premium}
                                    onChange={(value) =>
                                        setEditForm({
                                            ...editForm,
                                            premium: value,
                                        })
                                    }
                                />

                                <Toggle
                                    label="Verified User"
                                    checked={!!editForm.verified}
                                    onChange={(value) =>
                                        setEditForm({
                                            ...editForm,
                                            verified: value,
                                        })
                                    }
                                />
                            </div>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="mt-8 flex justify-end gap-3">
                        <button
                            onClick={closeEditModal}
                            className="rounded-md border px-4 py-2 text-sm text-gray-600 hover:bg-gray-50"
                        >
                            Cancel
                        </button>

                        <button
                            disabled={loading}
                            onClick={handleEditSubmit}
                            className="rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700 flex items-center gap-2"
                        >
                            {loading ? "Updating..." : "Save Changes"}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Subscription dates and pause */}
            <Modal isOpen={paymentModalOpen} onClose={handleClosePaymentModal}>
                <div className="w-full max-w-lg p-6">
                    <h2 className="text-lg font-semibold text-gray-900">
                        Subscription for {selectedUser?.first_name}{" "}
                        {selectedUser?.last_name}
                    </h2>
                    <p className="mt-1 text-sm text-gray-500">
                        Access stays on from the start date through the expiry
                        date. Pausing blocks access until you resume it.
                    </p>

                    <label className="mt-5 block text-sm text-gray-700">
                        Start date
                        <input
                            type="date"
                            name="starts_at"
                            value={paymentDetails.starts_at}
                            onChange={handleChangeSubscription}
                            className="mt-1 w-full rounded border px-3 py-2"
                        />
                    </label>
                    <label className="mt-3 block text-sm text-gray-700">
                        Expires on
                        <input
                            type="date"
                            name="ends_at"
                            value={paymentDetails.ends_at}
                            onChange={handleChangeSubscription}
                            className="mt-1 w-full rounded border px-3 py-2"
                        />
                    </label>
                    <label className="mt-4 flex items-center gap-2 text-sm text-gray-700">
                        <input
                            type="checkbox"
                            name="paused"
                            checked={!!paymentDetails.paused}
                            onChange={handleChangeSubscription}
                        />
                        Pause subscription
                    </label>

                    <div className="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            onClick={handleClosePaymentModal}
                            className="rounded border px-4 py-2 text-sm text-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            disabled={subscriptionSaving}
                            onClick={handleUpdateSubscription}
                            className="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-60"
                        >
                            {subscriptionSaving ? "Saving..." : "Save"}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Delete Modal */}
            <Modal
                isOpen={isDeleteModalOpen}
                onClose={() => setIsDeleteModalOpen(false)}
            >
                <div className="max-h-[80vh] overflow-y-auto">
                    <h2 className="text-lg font-bold mb-4">Confirm Delete</h2>
                    <p>
                        Are you sure you want to delete{" "}
                        <strong>{selectedUser?.name}</strong>?
                    </p>
                    <div className="mt-4 flex justify-between space-x-3">
                        <button
                            onClick={() => setIsDeleteModalOpen(false)}
                            className="px-4 py-2 bg-gray-400 text-white rounded"
                        >
                            Cancel
                        </button>
                        <button
                            onClick={handleDeleteSubmit}
                            className="px-4 py-2 bg-red-600 text-white rounded"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </Modal>
        </div>
    );
};

export default UsersList;

const PaymentBadge = ({ user }) => (
    <span
        className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ${
            user.payment_status === "successful"
                ? "bg-green-100 text-green-800"
                : "bg-yellow-100 text-yellow-800"
        }`}
    >
        {user.payment_status || "Pending"}
    </span>
);

const SubscriptionBadge = ({ summary, providerBadgeClass }) => (
    <span className="inline-flex flex-wrap items-center gap-1.5">
        {summary.isPaused ? (
            <span className="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                Paused
            </span>
        ) : null}
        {summary.providerLabel ? (
            <span
                className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${providerBadgeClass(
                    summary.subscription.paymentMethod
                )}`}
            >
                {summary.providerLabel}
            </span>
        ) : null}
    </span>
);

const Toggle = ({ label, checked, onChange }) => (
    <div className="flex items-center justify-between">
        <span className="text-sm text-gray-700">{label}</span>

        <button
            type="button"
            onClick={() => onChange(!checked)}
            className={`relative inline-flex h-5 w-9 items-center rounded-full transition ${
                checked ? "bg-indigo-600" : "bg-gray-300"
            }`}
        >
            <span
                className={`inline-block h-4 w-4 transform rounded-full bg-white transition ${
                    checked ? "translate-x-4" : "translate-x-1"
                }`}
            />
        </button>
    </div>
);

if (document.getElementById("UsersList")) {
    const root = ReactDOM.createRoot(document.getElementById("UsersList"));
    root.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <UsersList />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
