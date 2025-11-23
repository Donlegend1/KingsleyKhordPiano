const Modal = ({ isOpen, onClose, children }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50 p-4">
            <div className="relative bg-white rounded-lg shadow-lg w-full max-w-3xl max-h-[90vh] overflow-hidden">
                
                {/* Close Button */}
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl font-bold"
                    aria-label="Close"
                >
                    &times;
                </button>

                {/* Modal Content (Scrollable) */}
                <div className="p-6 overflow-y-auto max-h-[95vh]">
                    {children}
                </div>
            </div>
        </div>
    );
};

export default Modal;
