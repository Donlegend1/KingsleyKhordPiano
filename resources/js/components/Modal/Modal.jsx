const Modal = ({ isOpen, onClose, children }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
            <div className="relative bg-white rounded-lg shadow-lg max-w-3xl w-full mx-4 p-6">
                {/* Close Button (X) */}
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold"
                    aria-label="Close"
                >
                    &times;
                </button>

                {/* Modal Content */}
                <div className="mt-2">{children}</div>
            </div>
        </div>
    );
};

export default Modal;