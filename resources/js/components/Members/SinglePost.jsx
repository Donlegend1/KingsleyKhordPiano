import React, { useEffect, useState, useCallback } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import PostWithComments from "../PostWithComments.jsx";
import SkeletonPost from "../Skeleton/SkeletonPost.jsx";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const SinglePost = () => {
    const { showMessage } = useFlashMessage();
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [newComment, setNewComment] = useState("");

    const [sortBy, setSortBy] = useState("latest");
    const [posting, setPosting] = useState(false);
    const [expanded, setExpanded] = useState(false);
    const [selectedPost, setSelectedPost] = useState({});

    const [postDetails, setPostDetails] = useState({
        body: "",
        category: "",
        subcategory: "",
        media: [],
    });

    const [showSkeleton, setShowSkeleton] = useState(false);
    const [mediaFiles, setMediaFiles] = useState([]);

    const lastSegment = (() => {
        const segment = window.location.pathname
            .split("/")
            .filter(Boolean)
            .pop();

        const num = Number(segment);
        return isNaN(num) ? null : num;
    })();

    useEffect(() => {
        let timer;

        if (loading) {
            setShowSkeleton(true);
            timer = setTimeout(() => {
                setShowSkeleton(false);
            }, 1000);
        }

        return () => clearTimeout(timer);
    }, [loading]);

    const handleValidation = () => {
        if (!postDetails.body.trim()) {
            showMessage("Post content cannot be empty.", "error");
            return false;
        }
        if (!postDetails.subcategory) {
            showMessage("Please select a category.", "error");
            return false;
        }
        return true;
    };

    const fetchPostsByMember = useCallback(async () => {
        if (!hasMore || loading) return;
        setLoading(true);

        try {
            const response = await axios.get(
                `/api/member/posts`,
                {
                    params: {
                        page,
                        sort: sortBy,
                        post_id: lastSegment,
                    },
                }
            );

            const newPosts = response.data.data;
            const currentPage = response.data.current_page;
            const lastPage = response.data.last_page;

            setPosts((prev) => {
                const existingIds = new Set(prev.map((p) => p.id));
                const uniqueNewPosts = newPosts.filter(
                    (p) => !existingIds.has(p.id)
                );
                return [...prev, ...uniqueNewPosts];
            });

            setHasMore(currentPage < lastPage);
        } catch (error) {
            console.error("Error fetching posts:", error);
        } finally {
            setLoading(false);
        }
    }, [sortBy, hasMore, loading, page]);

    useEffect(() => {
        setPosts([]);
        setPage(1);
        setHasMore(true);
    }, [sortBy]);

    useEffect(() => {
        fetchPostsByMember();
    }, [page, sortBy]);

    useEffect(() => {
        const handleScroll = () => {
            if (
                window.innerHeight + document.documentElement.scrollTop + 100 >=
                document.documentElement.offsetHeight
            ) {
                if (!loading && hasMore) {
                    setPage((prev) => prev + 1);
                }
            }
        };

        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, [loading, hasMore]);

    const handleDeletePost = async (id) => {
        try {
            await axios.delete(`/api/member/post/${id}`);

            setPosts([]);
            setPage(1);
            setHasMore(true);
            window.location = "/member/community";
            showMessage("Post deleted.", "success");
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
            console.error("Error adding comment:", error);
        } finally {
            setPosting(false);
        }
    };

    const handleCommentSubmit = async (e) => {
        e.preventDefault();

        if (!newComment.trim()) {
            showMessage("Please add a comment", "error");
            return;
        }

        if (!selectedPost?.id) {
            showMessage("No post selected for comment", "error");
            return;
        }

        setPosting(true);

        const comment = {
            body: newComment,
            post_id: selectedPost.id,
        };

        try {
            await axios.post("/api/member/comment", comment, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            await fetchPostsByMember();

            showMessage("Comment posted.", "success");
            setNewComment("");
            setExpanded(false);
        } catch (error) {
            showMessage("Error adding comment.", "error");
            console.error("Error adding comment:", error);
        } finally {
            setPosting(false);
        }
    };

    return (
        <>
            <div className="post-list">
                {posts.map((post) => (
                    <PostWithComments
                        key={post.id}
                        setSelectedPost={setSelectedPost}
                        post={post}
                        newComment={newComment}
                        setNewComment={setNewComment}
                        handleCommentSubmit={handleCommentSubmit}
                        handleDeletePost={handleDeletePost}
                    />
                ))}

            </div>
        </>
    );
};

export default SinglePost;

if (document.getElementById("single-post")) {
    const Index = ReactDOM.createRoot(document.getElementById("single-post"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <SinglePost />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
