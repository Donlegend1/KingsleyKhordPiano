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

    const perPage = 10;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const { showMessage } = useFlashMessage();

    const fetchUsers = async (page = 1) => {
        setLoading(true);
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

    return (
        <div className="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
            <h2 className="text-lg font-bold mb-4">Users List</h2>

            {loading ? (
                <p>Loading...</p>
            ) : (
                <>
                    <table className="min-w-full bg-white mb-4">
                        <thead className="bg-gray-800 text-white">
                            <tr>
                                <th className="py-2 px-4 text-left">S/N</th>
                                <th className="py-2 px-4 text-left">Name</th>
                                <th className="py-2 px-4 text-left">Email</th>
                                <th className="py-2 px-4 text-left">
                                    Payment Status
                                </th>
                                <th className="py-2 px-4 text-left">
                                    Subscription Plan
                                </th>
                                <th className="py-2 px-4 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {userList.length > 0 ? (
                                userList.map((user, index) => (
                                    <tr key={user.id} className="border-b">
                                        <td className="py-2 px-4">
                                            {(currentPage - 1) * perPage +
                                                index +
                                                1}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.first_name +
                                                " " +
                                                user.last_name}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.email}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.payment_status}
                                        </td>
                                        <td className="py-2 px-4">
                                            {user.metadata?.duration} (
                                            {user.metadata?.tier})
                                            <button
                                                onClick={() =>
                                                    handleOpenPaymentModal(user)
                                                }
                                                className="ml-2 px-3 py-1 text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none"
                                            >
                                                Update
                                            </button>
                                        </td>
                                        <td className="py-2 px-4 flex justify-center text-center items-center">
                                            <div className="flex gap-2">
                                                <button
                                                    onClick={() =>
                                                        openEditModal(user)
                                                    }
                                                    className="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition"
                                                >
                                                    <span className="fa fa-edit"></span>
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        openDeleteModal(user)
                                                    }
                                                    className="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition"
                                                >
                                                    <span className="fa fa-trash"></span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td
                                        colSpan="6"
                                        className="py-2 px-4 text-center"
                                    >
                                        No users found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>

                    <div className="flex items-center justify-center gap-6 mt-6">
                        <CustomPagination
                            currentPage={currentPage}
                            totalPages={totalPages}
                            onPageChange={handlePageChange}
                        />
                    </div>
                </>
            )}

            {/* Edit Modal */}
            <Modal isOpen={isEditModalOpen} onClose={() => closeEditModal()}>
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
                            setEditForm({ ...editForm, email: e.target.value })
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
            </Modal>

            <Modal
                isOpen={paymentModalOpen}
                onClose={() => handleClosePaymentModal()}
            >
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
            </Modal>

            {/* Delete Modal */}
            <Modal
                isOpen={isDeleteModalOpen}
                onClose={() => setIsDeleteModalOpen(false)}
            >
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
            </Modal>
        </div>
    );
};

export default UsersList;

if (document.getElementById("UsersList")) {
    const Index = ReactDOM.createRoot(document.getElementById("UsersList"));
    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <UsersList />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
