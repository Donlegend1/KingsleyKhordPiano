import { useState, useCallback, useEffect } from "react";
import { FaTrashAlt } from "react-icons/fa";
import axios from "axios";
import {
    formatRelativeTime,
    capitaliseAndRemoveHyphen,
} from "../../utils/formatRelativeTime";

import { useFlashMessage } from "../Alert/FlashMessageContext";

const Comments = ({ member }) => {
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [sortBy, setSortBy] = useState("latest");
    const { showMessage } = useFlashMessage();

    // Fetch member's posts
    const fetchPostsByMember = useCallback(async () => {
        if (!hasMore || loading) return;
        setLoading(true);

        try {
            const response = await axios.get(
                `/api/member/posts/member/${member.id}`,
                { params: { page, sort: sortBy } }
            );

            const { data: newPosts, current_page, last_page } = response.data;

            setPosts((prev) => {
                const existingIds = new Set(prev.map((p) => p.id));
                const uniquePosts = newPosts.filter(
                    (p) => !existingIds.has(p.id)
                );
                return [...prev, ...uniquePosts];
            });

            setHasMore(current_page < last_page);
        } catch (error) {
            console.error("Error fetching posts:", error);
        } finally {
            setLoading(false);
        }
    }, [member.id, page, sortBy, hasMore, loading]);

    useEffect(() => {
        fetchPostsByMember();
    }, [page, sortBy]);

    // Flatten all comments with post subcategory
    const allComments = posts.flatMap((post) =>
        (post.comments || []).map((comment) => ({
            ...comment,
            subcategory: post.subcategory,
        }))
    );

    const handleDeleteComment = async (e, id) => {
        try {
            const res = await axios.delete(`/api/member/postComment/${id}`);

            setPosts([]);
            setPage(1);
            setHasMore(true);
            await fetchPostsByMember();
            showMessage("Comment deleted.", "success");
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
            console.error("Error adding comment:", error);
        }
    };

    return (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            {allComments.length > 0 ? (
                allComments.map((comment) => (
                    <div
                        key={comment.id}
                        className="flex items-start gap-3 py-3 border-b border-gray-200 dark:border-gray-700 last:border-0"
                    >
                        {/* Avatar */}
                        <img
                            src={comment.user?.avatar || "/avatar1.jpg"}
                            alt={comment.user?.name || "User"}
                            className="w-10 h-10 rounded-full object-cover"
                        />

                        {/* Comment content */}
                        <div className="flex-1">
                            {/* Time & Delete */}
                            <div className="flex items-center justify-between mb-1">
                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                    {formatRelativeTime(comment.created_at)}
                                </span>
                                <button
                                    onClick={(e) => {
                                        handleDeleteComment(e, comment.id);
                                    }}
                                    className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    title="Delete comment"
                                >
                                    <FaTrashAlt />
                                </button>
                            </div>

                            {/* Category */}
                            <h4 className="font-medium text-gray-800 dark:text-gray-100">
                                {capitaliseAndRemoveHyphen(comment.subcategory)}
                            </h4>

                            {/* Comment text */}
                            <p className="text-gray-700 dark:text-gray-300 mt-1 bg-gray-200 dark:bg-gray-700 rounded-md p-2">
                                {comment.body}
                            </p>
                        </div>
                    </div>
                ))
            ) : (
                <p className="text-gray-500 text-sm">No comments found.</p>
            )}
        </div>
    );
};

export default Comments;
