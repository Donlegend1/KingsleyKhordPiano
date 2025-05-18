import ReactDOM from 'react-dom/client';
import React, { useEffect, useState } from "react";
import axios from "axios";

const Modal = ({ isOpen, onClose, children }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
      <div className="bg-white p-6 rounded-lg shadow-lg">
        {children}
        <button className="mt-4 px-4 py-2 bg-blue-500 text-white rounded" onClick={onClose}>Close</button>
      </div>
    </div>
  );
};

const UsersList = () => {
  const [courses, setCourses] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(false);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const perPage = 10;
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

  const fetchUsers = async (page = 1) => {
    setLoading(true);
    try {
      const response = await axios.get(`/api/admin/courses?page=${page}&perPage=${perPage}`, {
        headers: {
          "X-CSRF-TOKEN": csrfToken,
        },
        withCredentials: true,
      });
      setCourses(response.data.data);
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

  const handlePageChange = (page) => {
    fetchUsers(page);
  };

  const openEditModal = (user) => {
    setSelectedUser(user);
    setIsEditModalOpen(true);
  };

  const openDeleteModal = (user) => {
    setSelectedUser(user);
    setIsDeleteModalOpen(true);
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
                <th className="py-2 px-4 text-left">Payment Status</th>
                <th className="py-2 px-4 text-left">Subscription Plan</th>
                <th className="py-2 px-4 text-left">Actions</th>
              </tr>
            </thead>
            <tbody>
              {courses.length > 0 ? (
                courses.map((user, index) => (
                  <tr key={user.id} className="border-b">
                    <td className="py-2 px-4">{index + 1}</td>
                    <td className="py-2 px-4">{user.name}</td>
                    <td className="py-2 px-4">{user.email}</td>
                    <td className="py-2 px-4">{user.payment_status}</td>
                    <td className="py-2 px-4">{user.plan.name}</td>
                    <td className="py-2 px-4">
                      <button onClick={() => openEditModal(user)} className="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                      <button onClick={() => openDeleteModal(user)} className="bg-red-500 text-white px-2 py-1 rounded ml-2">Delete</button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="6" className="py-2 px-4 text-center">No courses found.</td>
                </tr>
              )}
            </tbody>
          </table>

          <div className="flex justify-between items-center">
            <button
              disabled={currentPage === 1}
              onClick={() => handlePageChange(currentPage - 1)}
              className="px-4 py-2 bg-gray-300 rounded disabled:bg-gray-200"
            >
              Previous
            </button>
            <span>
              Page {currentPage} of {totalPages}
            </span>
            <button
              disabled={currentPage === totalPages}
              onClick={() => handlePageChange(currentPage + 1)}
              className="px-4 py-2 bg-gray-300 rounded disabled:bg-gray-200"
            >
              Next
            </button>
          </div>
        </>
      )}

      <Modal isOpen={isEditModalOpen} onClose={() => setIsEditModalOpen(false)}>
        <h2 className="text-lg font-bold mb-2">Edit User</h2>
        <p>Editing user: {selectedUser?.name}</p>
      </Modal>

      <Modal isOpen={isDeleteModalOpen} onClose={() => setIsDeleteModalOpen(false)}>
        <h2 className="text-lg font-bold mb-2">Confirm Delete</h2>
        <p>Are you sure you want to delete {selectedUser?.name}?</p>
      </Modal>
    </div>
  );
};

export default UsersList;


if (document.getElementById('UsersList')) {
    const Index = ReactDOM.createRoot(document.getElementById("UsersList"));

    Index.render(
        <React.StrictMode>
            <UsersList/>
        </React.StrictMode>
    )
}