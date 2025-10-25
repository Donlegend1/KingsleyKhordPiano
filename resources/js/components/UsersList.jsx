import ReactDOM from "react-dom/client";
import React, { useEffect, useState } from "react";
import axios from "axios";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";
import CustomPagination from "./Pagination/CustomPagination";

const Modal = ({ isOpen, onClose, children }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
            <div className="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                {children}
            </div>
        </div>
    );
};

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
    });
    const [paymentDetails, setPaymentDetails] = useState({
        user_id: selectedUser?.id,
        starts_at: "",
        ends_at: "",
        amount: "",
        premium: 0,
    });
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
        try {
            await axios.put(`/api/admin/users/${selectedUser.id}`, editForm, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });
            setIsEditModalOpen(false);
            fetchUsers(currentPage);
            showMessage("User updated sucessfully", "success");
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

    const handleOpenPaymentModal = (user) => {
        setSelectedUser(user);
        setPaymentModalOpen(true);
        setPaymentDetails({
            user_id: user?.id,
        });
        // window.location.href = `/admin/subscriptions/${userId}`;
    };

    const handleClosePaymentModal = () => {
        setPaymentModalOpen(false);
        setPaymentDetails({
            duration: "",
            tier: "",
            starts_at: "",
            ends_at: "",
        });
    };

    const handleChangeSubscription = (e) => {
        const { name, value } = e.target;
        setPaymentDetails({ ...paymentDetails, [name]: value });
    };

    const handleUpdateSubscription = async () => {
        console.log(paymentDetails, "payment details");
        try {
            await axios.post(`/api/admin/payment/update`, paymentDetails, {
                headers: { "X-CSRF-TOKEN": csrfToken },
            });
            handleClosePaymentModal();
            showMessage("payment updated", "success");
            fetchUsers();
        } catch (error) {
            console.error("Error updating payment:", error);
            showMessage("Error updating payment", "error");
        } finally {
            setLoading(false);
        }
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

    return (
        <div className="flex flex-col h-full">
            {/* Header section */}
            <div className="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4 p-4">
                <div>
                    <h2 className="text-2xl font-bold text-gray-800">
                        Users Management
                    </h2>
                    <p className="text-gray-500 mt-1">
                        Manage and monitor user accounts
                    </p>
                </div>
                <div className="relative w-full md:w-72">
                    <input
                        type="search"
                        onChange={(e) => handleSearchUsers(e)}
                        placeholder="Search users..."
                        className="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-100 focus:border-blue-400 focus:ring focus:ring-blue-200 transition-all"
                    />
                    <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <i className="fa fa-search text-lg"></i>
                    </span>
                </div>
            </div>

            {/* Table section with contained scroll */}
            <div className="flex-1 overflow-hidden">
                <div className="overflow-x-auto">
                    <div className="inline-block min-w-full align-middle">
                        {/* Existing table */}
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr className="bg-gradient-to-r from-blue-500 to-blue-600">
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        S/N
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        User Details
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Subscription
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {userList.length > 0 ? (
                                    userList.map((user, index) => (
                                        <tr
                                            key={user.id}
                                            className="hover:bg-gray-50 transition-colors"
                                        >
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {(currentPage - 1) * perPage +
                                                    index +
                                                    1}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 rounded-full bg-gradient-to-r from-blue-200 to-blue-300 flex items-center justify-center text-blue-600 font-semibold">
                                                        {user.first_name[0]}
                                                        {user.last_name[0]}
                                                    </div>
                                                    <div className="ml-4">
                                                        <div className="text-sm font-medium text-gray-900">
                                                            {user.first_name}{" "}
                                                            {user.last_name}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {user.email}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    className={`inline-flex px-3 py-1 rounded-full text-xs font-semibold ${
                                                        user.payment_status ===
                                                        "successful"
                                                            ? "bg-green-100 text-green-800"
                                                            : "bg-yellow-100 text-yellow-800"
                                                    }`}
                                                >
                                                    {user.payment_status ||
                                                        "Pending"}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center gap-2">
                                                    <span className="text-sm text-gray-600">
                                                        {
                                                            user.metadata
                                                                ?.duration
                                                        }{" "}
                                                        ({user.metadata?.tier})
                                                    </span>
                                                    <button
                                                        onClick={() =>
                                                            handleOpenPaymentModal(
                                                                user
                                                            )
                                                        }
                                                        className="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-lg text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                                                    >
                                                        Update
                                                    </button>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div className="flex items-center gap-2">
                                                    <button
                                                        onClick={() =>
                                                            openEditModal(user)
                                                        }
                                                        className="inline-flex items-center p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all"
                                                    >
                                                        <i className="fas fa-edit"></i>
                                                    </button>
                                                    <button
                                                        onClick={() =>
                                                            openDeleteModal(
                                                                user
                                                            )
                                                        }
                                                        className="inline-flex items-center p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all"
                                                    >
                                                        <i className="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td
                                            colSpan="6"
                                            className="px-6 py-12 text-center"
                                        >
                                            <div className="flex flex-col items-center">
                                                <div className="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i className="fas fa-users text-gray-400 text-2xl"></i>
                                                </div>
                                                <p className="text-gray-500 text-sm">
                                                    No users found
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
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
                <div className="max-h-[80vh] overflow-y-auto">
                    <h2 className="text-lg font-bold mb-4">Edit User</h2>
                    <label className="block mb-2">
                        First Name:
                        <input
                            type="text"
                            defaultValue={editForm.first_name}
                            onChange={(e) =>
                                setEditForm({
                                    ...editForm,
                                    first_name: e.target.value,
                                })
                            }
                            className="w-full border rounded px-3 py-2 mt-1"
                        />
                    </label>
                    <label className="block mb-2">
                        Last Name:
                        <input
                            type="text"
                            defaultValue={editForm.last_name}
                            onChange={(e) =>
                                setEditForm({
                                    ...editForm,
                                    last_name: e.target.value,
                                })
                            }
                            className="w-full border rounded px-3 py-2 mt-1"
                        />
                    </label>
                    <label className="block mb-2">
                        Email:
                        <input
                            type="email"
                            defaultValue={editForm.email}
                            onChange={(e) =>
                                setEditForm({
                                    ...editForm,
                                    email: e.target.value,
                                })
                            }
                            className="w-full border rounded px-3 py-2 mt-1"
                        />
                    </label>
                    <label className="block mb-2">
                        Payment Status:
                        <select
                            defaultValue={editForm.payment_status}
                            name="payment_status"
                            id=""
                            className="w-full border rounded px-3 py-2 mt-1"
                        >
                            <option value="">--select--</option>
                            <option value="successful">Paid</option>
                            <option value="fail">Fail</option>
                        </select>
                    </label>
                    <label className="block mb-2">
                        Premium:
                        <select
                            name="premium"
                            defaultValue={editForm.premium}
                            id=""
                            className="w-full border rounded px-3 py-2 mt-1"
                        >
                            <option value="">--select--</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </label>
                    <div className="mt-4 flex justify-between space-x-3">
                        <button
                            onClick={() => closeEditModal()}
                            className="px-4 py-2 bg-gray-400 text-white rounded"
                        >
                            Cancel
                        </button>
                        <button
                            disabled={loading}
                            onClick={handleEditSubmit}
                            className="px-4 py-2 bg-red-600 text-white rounded"
                        >
                            {loading ? (
                                <>
                                    <svg
                                        className="animate-spin h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            className="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            strokeWidth="4"
                                        ></circle>
                                        <path
                                            className="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"
                                        ></path>
                                    </svg>
                                    <span>Updating...</span>
                                </>
                            ) : (
                                <span>Update</span>
                            )}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Payment Modal */}
            <Modal isOpen={paymentModalOpen} onClose={handleClosePaymentModal}>
                <div className="max-h-[80vh] overflow-y-auto">
                    <h2 className="text-lg font-bold mb-4">
                        Update Subscription for {selectedUser?.first_name}{" "}
                        {selectedUser?.last_name}
                    </h2>
                    <label className="block mb-2">
                        Start Date
                        <input
                            type="date"
                            name="starts_at"
                            defaultValue={paymentDetails.starts_at}
                            onChange={handleChangeSubscription}
                            className="w-full border rounded px-3 py-2 mt-1"
                        />
                    </label>
                    <label className="block mb-2">
                        End Date
                        <input
                            type="date"
                            name="ends_at"
                            defaultValue={paymentDetails.ends_at}
                            onChange={handleChangeSubscription}
                            className="w-full border rounded px-3 py-2 mt-1"
                        />
                    </label>
                    <label className="block mb-2">
                        Amount
                        <input
                            type="text"
                            name="amount"
                            defaultValue={paymentDetails.amount}
                            onChange={handleChangeSubscription}
                            className="w-full border rounded px-3 py-2 mt-1"
                        />
                    </label>
                    <label className="block mb-2">
                        Premium:
                        <select
                            name="premium"
                            defaultValue={paymentDetails.premium}
                            onChange={handleChangeSubscription}
                            className="w-full border rounded px-3 py-2 mt-1"
                        >
                            <option value="">--select--</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </label>

                    <div className="mt-4 flex justify-between space-x-3">
                        <button
                            onClick={() => handleClosePaymentModal()}
                            className="px-4 py-2 bg-gray-400 text-white rounded"
                        >
                            Cancel
                        </button>
                        <button
                            disabled={loading}
                            onClick={handleUpdateSubscription}
                            className="px-4 py-2 bg-red-600 text-white rounded"
                        >
                            {loading ? (
                                <>
                                    <svg
                                        className="animate-spin h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            className="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            strokeWidth="4"
                                        ></circle>
                                        <path
                                            className="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"
                                        ></path>
                                    </svg>
                                    <span>Updating...</span>
                                </>
                            ) : (
                                <span>Update</span>
                            )}
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
