import React, { useEffect, useState, useCallback } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import PostWithComments from "../PostWithComments.jsx";
import CreatePostBox from "../CreatePostBox.jsx";
import SkeletonPost from "../Skeleton/SkeletonPost.jsx";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";
import { FaRepublican } from "react-icons/fa6";

const SubCategory = () => {
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

    const lastSegment = window.location.pathname
        .split("/")
        .filter((segment) => segment !== "")
        .pop();

    useEffect(() => {
        let timer;

        if (loading) {
            setShowSkeleton(true);
            timer = setTimeout(() => {
                setShowSkeleton(false);
            }, 2000);
        }

        return () => clearTimeout(timer);
    }, [loading]);

    const handleSortChange = (event) => {
        const selectedSort = event.target.value;
        setSortBy(selectedSort);
        setPage(1);
        setPosts([]);
        setHasMore(true);
    };

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

    const fetchPostsByCategory = useCallback(async () => {
        if (!hasMore || loading) return;
        setLoading(true);

        try {
            const response = await axios.get(`/api/member/posts`, {
                params: {
                    page,
                    sort: sortBy,
                    subcategory: lastSegment,
                },
            });

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
        fetchPostsByCategory();
    }, [page, sortBy, lastSegment]);

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

    const handlePost = async (data) => {
        if (!handleValidation()) return;
        setPosting(true);

        try {
            await axios.post("/api/member/post", data, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            showMessage("Posted successfully.", "success");

            setPostDetails({
                body: "",
                category: "",
                subcategory: "",
            });
            setMediaFiles([]);
            setExpanded(false);
            setPosts([]);
            setPage(1);
            setHasMore(true);
            await fetchPostsByCategory();
        } catch (error) {
            showMessage("Error creating post.", "error");
            console.error("Error creating post:", error);
        } finally {
            setPosting(false);
        }
    };

    const handleDeletePost = async (id) => {
        try {
            await axios.delete(`/api/member/post/${id}`);

            setPosts([]);
            setPage(1);
            setHasMore(true);
            await fetchPostsByCategory();
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
            await fetchPostsByCategory();

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
            <div className="flex">
                <div className=" w-full md:w-2/3">
                    <div className="flex-1 space-y-6 mb-5">
                        <CreatePostBox
                            handlePost={handlePost}
                            postDetails={postDetails}
                            setPostDetails={setPostDetails}
                            posting={posting}
                            expanded={expanded}
                            setExpanded={setExpanded}
                            mediaFiles={mediaFiles}
                            setMediaFiles={setMediaFiles}
                            subcategory={lastSegment}
                        />
                    </div>

                    <div className="post-list">
                        <div className="flex items-center justify-between mb-4">
                            <div className="flex items-center w-4/5 gap-2">
                                <hr className="flex-grow border-t border-gray-300 dark:border-gray-600" />
                                <span className="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    Sort by:
                                </span>
                            </div>

                            <div className="flex items-center gap-2">
                                <select
                                    className="bg-gray-100 dark:bg-gray-700 dark:text-white text-sm px-3 py-1.5 w-28 rounded text-semi-bold"
                                    onChange={handleSortChange}
                                    value={sortBy}
                                >
                                    <option value="latest">Latest</option>
                                    <option value="old">Old</option>
                                    <option value="popular">Popular</option>
                                    <option value="likes">Likes</option>
                                </select>
                                <div className="text-gray-600 dark:text-gray-300">
                                    <i
                                        className="fa fa-list"
                                        aria-hidden="true"
                                    ></i>
                                </div>
                            </div>
                        </div>

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

                        {loading && showSkeleton && posts.length > 0 ? (
                            <div>
                                {[...Array(2)].map((_, i) => (
                                    <SkeletonPost key={i} />
                                ))}
                            </div>
                        ) : !loading && posts.length === 0 ? (
                            <p className="text-center text-sm text-gray-400 mb-4">
                                No posts yet.
                            </p>
                        ) : !loading && posts.length > 0 && !hasMore ? (
                            <p className="text-center text-sm text-gray-400 mb-4">
                                No more posts to load.
                            </p>
                        ) : null}
                    </div>
                </div>

                <div className="hidden md:block mx-4 md:mx-7 w-1/3">
                    {/* About Section */}
                    <div className="bg-white rounded-md w-full px-3 mb-5 py-5">
                        <p className="text-black font-semibold">About</p>
                        <p className="flex items-center gap-2">
                            <FaRepublican />
                            <span className="font-semibold">Public</span>
                        </p>
                        <p className="text-sm text-gray-500">
                            Any site member can see who's in the Space and what
                            they post.
                        </p>
                    </div>

                    {/* Recent Activities */}
                    <div className="bg-white rounded-md w-full px-3 mb-5 py-5">
                        <p className="font-semibold">Recent Space Activities</p>
                    </div>
                </div>
            </div>
        </>
    );
};

export default SubCategory;

if (document.getElementById("subcategory")) {
    const Index = ReactDOM.createRoot(document.getElementById("subcategory"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <SubCategory />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
