import { FaEllipsisV } from "react-icons/fa";

const Comments = ({ comments }) => {
    return (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            {comments &&
                comments.map((comment) => (
                    <div
                        key={comment.id}
                        className="flex items-start gap-3 py-3 border-b border-gray-200 dark:border-gray-700 last:border-0"
                    >
                        {/* Avatar */}
                        <img
                            src={comment.user?.avatar || "/avatar1.jpg"}
                            alt={comment.user?.name}
                            className="w-10 h-10 rounded-full object-cover"
                        />

                        {/* Content */}
                        <div className="flex-1">
                            <div className="flex items-center justify-between">
                                <div>
                                    <h4 className="font-medium text-gray-800 dark:text-gray-100">
                                        {comment.user?.name}
                                    </h4>
                                    <span className="text-xs text-gray-500 dark:text-gray-400">
                                        {comment.created_at}
                                    </span>
                                </div>

                                {/* Actions */}
                                <button className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <FaEllipsisV />
                                </button>
                            </div>

                            <p className="text-gray-700 dark:text-gray-300 mt-1">
                                {comment.text}
                            </p>
                        </div>
                    </div>
                ))}
        </div>
    );
};

export default Comments;
