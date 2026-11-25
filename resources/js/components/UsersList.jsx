import ReactDOM from "react-dom/client";
import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import CustomPagination from "./Pagination/CustomPagination";
import AuthorNameWithVerification from "./User/AuthorNameWithVerification";

const PLANS = {
    "standard-monthly": "Standard Monthly",
    "standard-quarterly": "Standard Quarterly",
    "standard-yearly": "Standard Yearly",
    "premium-monthly": "Premium Monthly",
    "premium-quarterly": "Premium Quarterly",
    "premium-yearly": "Premium Yearly",
    manual: "Manual access",
    none: "No subscription",
};

const UsersList = () => {
    const [userList, setUserList] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [loading, setLoading] = useState(false);
    const [total, setTotal] = useState(0);
    const [search, setSearch] = useState("");
    const [planFilter, setPlanFilter] = useState("");
    const [statusFilter, setStatusFilter] = useState("");
    const requestId = useRef(0);
    const [error, setError] = useState(null);

    const perPage = 10;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const fetchUsers = async (page = 1) => {
        const id = ++requestId.current;
        setLoading(true);
        setError(null);
        try {
            const response = await axios.get("/api/admin/users", {
                params: {
                    page,
                    per_page: perPage,
                    search: search.trim() || undefined,
                    plan: planFilter || undefined,
                    status: statusFilter || undefined,
                },
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });

            if (id !== requestId.current) return;
            setUserList(response.data.data);
            setCurrentPage(response.data.current_page);
            setTotalPages(response.data.last_page);
            setTotal(response.data.total);
        } catch (error) {
            if (id !== requestId.current) return;
            console.error("Error fetching users:", error);
            setError(error.response?.data?.message || "Error loading users");
        } finally {
            if (id === requestId.current) setLoading(false);
        }
    };

    useEffect(() => {
        const timer = setTimeout(() => fetchUsers(), 180);
        return () => {
            clearTimeout(timer);
            requestId.current++;
        };
    }, [search, planFilter, statusFilter]);

    const handlePageChange = (page) => fetchUsers(page);

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

    const planLabel = (user) => {
        if (user.display_plan === "none" && user.subscription_details?.display_status === "Active") {
            return "Plan unavailable";
        }
        return PLANS[user.display_plan] || "No subscription";
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
                        <p className="mt-1 text-sm text-gray-500" aria-live="polite">
                            {loading
                                ? "Loading users…"
                                : `${total.toLocaleString()} ${total === 1 ? "user" : "users"}${search || planFilter || statusFilter ? " match these filters" : ""}`}
                        </p>
                    </div>

                    {/* Search */}
                    <div className="relative w-full md:w-80">
                        <span className="pointer-events-none mt-3 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i className="fa fa-search text-sm"></i>
                        </span>

                        <input
                            type="search"
                            value={search}
                            onChange={(event) => setSearch(event.target.value)}
                            placeholder="Search by name or email"
                            className="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-11 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 transition"
                        />
                    </div>
                </div>
                <div className="mt-4 flex flex-wrap items-end gap-3 border-t border-gray-100 pt-4">
                    <label className="text-xs font-semibold text-gray-600">Plan
                        <select value={planFilter} onChange={(event) => setPlanFilter(event.target.value)} className="mt-1 block min-w-[10rem] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                            <option value="">All plans</option>
                            {Object.entries(PLANS).map(([value, label]) => <option key={value} value={value}>{label}</option>)}
                        </select>
                    </label>
                    <label className="text-xs font-semibold text-gray-600">Status
                        <select value={statusFilter} onChange={(event) => setStatusFilter(event.target.value)} className="mt-1 block min-w-[8rem] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                            <option value="">All statuses</option>
                            {["Active", "Inactive", "Canceled"].map((status) => <option key={status}>{status}</option>)}
                        </select>
                    </label>
                    {(search || planFilter || statusFilter) && (
                        <button type="button" onClick={() => { setSearch(""); setPlanFilter(""); setStatusFilter(""); }} className="rounded-lg px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">Clear filters</button>
                    )}
                </div>
            </div>

            <div className="flex-1">
                {userList.length === 0 && !loading ? (
                    <div className="px-6 py-12 text-center">
                        <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                            <i className="fas fa-users text-2xl text-gray-400"></i>
                        </div>
                        <p className="text-sm text-gray-500">No users found</p>
                    </div>
                ) : (
                    <>
                        <div className="space-y-3 md:hidden">
                            {userList.map((user) => (
                                <article key={user.id} className="rounded-xl border border-gray-200 bg-white p-4">
                                    <div className="flex items-start justify-between gap-3">
                                        <UserIdentity user={user} />
                                        <ManageUserButton user={user} />
                                    </div>
                                    <dl className="mt-4 grid grid-cols-2 gap-3 text-sm">
                                        <div><dt className="text-xs text-gray-500">Plan</dt><dd className="mt-0.5 font-medium">{planLabel(user)}</dd></div>
                                        <div><dt className="text-xs text-gray-500">Status</dt><dd className="mt-0.5"><SubscriptionStatus user={user} /></dd></div>
                                        <div><dt className="text-xs text-gray-500">Joined</dt><dd className="mt-0.5">{formatDate(user.created_at) || "—"}</dd></div>
                                        <div><dt className="text-xs text-gray-500">Country</dt><dd className="mt-0.5">{user.country_name || "—"}</dd></div>
                                    </dl>
                                </article>
                            ))}
                        </div>

                        <div className="hidden overflow-x-auto rounded-xl border border-gray-200 md:block">
                            <table className="w-full divide-y divide-gray-200 text-left text-sm">
                                <thead className="bg-gray-50 text-xs font-semibold text-gray-600">
                                    <tr>
                                        {["User", "Plan", "Status", "Joined", "Country"].map((label) => (
                                            <th key={label} scope="col" className="px-4 py-3">{label}</th>
                                        ))}
                                        <th scope="col" className="px-4 py-3 text-right">Manage</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 bg-white">
                                    {userList.map((user) => (
                                        <tr key={user.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3"><UserIdentity user={user} /></td>
                                            <td className="px-4 py-3 font-medium text-gray-900">{planLabel(user)}</td>
                                            <td className="px-4 py-3"><SubscriptionStatus user={user} /></td>
                                            <td className="whitespace-nowrap px-4 py-3 text-gray-600">{formatDate(user.created_at) || "—"}</td>
                                            <td className="px-4 py-3 text-gray-600">{user.country_name || "—"}</td>
                                            <td className="px-4 py-3 text-right"><ManageUserButton user={user} /></td>
                                        </tr>
                                    ))}
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


        </div>
    );
};

export default UsersList;

const UserIdentity = ({ user }) => (
    <div className="flex min-w-0 items-center gap-3">
        <span aria-hidden="true" className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700">
            {user.first_name?.[0]}{user.last_name?.[0]}
        </span>
        <div className="min-w-0">
            <AuthorNameWithVerification author={user} />
            <p className="mt-0.5 break-all text-xs text-gray-500">{user.email}</p>
        </div>
    </div>
);

const SubscriptionStatus = ({ user }) => {
    const status = user.subscription_details?.display_status || "Inactive";
    const colors = {
        Active: "bg-emerald-100 text-emerald-800",
        Inactive: "bg-gray-100 text-gray-700",
        Canceled: "bg-amber-100 text-amber-800",
    };
    return (
        <span className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${colors[status]}`}>
            {status}
        </span>
    );
};

const ManageUserButton = ({ user }) => (
    <a
        href={`/admin/users/${user.id}`}
        aria-label={`Manage ${user.first_name || ""} ${user.last_name || ""}`.trim()}
        title="Manage user"
        className="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100"
    >
        <i className="fas fa-edit" aria-hidden="true"></i>
    </a>
);

if (document.getElementById("UsersList")) {
    const root = ReactDOM.createRoot(document.getElementById("UsersList"));
    root.render(
        <React.StrictMode>
            <UsersList />
        </React.StrictMode>
    );
}
