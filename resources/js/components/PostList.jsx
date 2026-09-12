import React, { useEffect, useState, useCallback, useRef } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import PostWithComments from "./PostWithComments.jsx";
import CreatePostBox from "./CreatePostBox.jsx";
import SkeletonPost from "./Skeleton/SkeletonPost.jsx";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const TOPIC_INFO = {
    say_hello:
        "👋 Hey, we'd love to get to know you! Drop a post about who you are, where you're from, how far along you are on piano, and what brought you here. And hey, say hi to a fellow newbie while you're at it!",
    ask_question:
        "💬 Stuck on something? Just ask! Technique, practice, gear, whatever's on your mind — we're all here to help each other out.",
    announcement:
        "📢 This is where Kingsley drops the latest news and updates. Keep an eye out so you don't miss anything good!",
    suggestions:
        "💡 Got an idea to make this place even better? We're all ears — share it with us here!",
};

const PostList = ({ fixedSubcategory, hideComposer, parentPostId } = {}) => {
    const { showMessage } = useFlashMessage();
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [newComment, setNewComment] = useState("");
    const [commenting, setCommenting] = useState(false);

    const [sortBy, setSortBy] = useState("latest");
    const [subcategoryFilter, setSubcategoryFilter] = useState(
        () =>
            fixedSubcategory ||
            new URLSearchParams(window.location.search).get("subcategory") ||
            "",
    );
    const [posting, setPosting] = useState(false);
    const [expanded, setExpanded] = useState(false);
    const [selectedPost, setSelectedPost] = useState({});
    const [blocks, setBlocks] = useState([]);

    const [postDetails, setPostDetails] = useState({
        blocks: [],
        body: "",
        category: "",
        subcategory: "",
        media: [],
    });

    const [showSkeleton, setShowSkeleton] = useState(false);
    const [mediaFiles, setMediaFiles] = useState([]);

    // Member-profile URLs (e.g. /member/community/members/42) end in a numeric
    // user ID, which this component uses to switch to a "posts by member" feed.
    // A category feed (fixedSubcategory) is also sometimes mounted on a URL
    // that happens to end in a number (e.g. /member/post/23), so it must never
    // be treated as a member id in that case.
    const lastSegment = (() => {
        if (fixedSubcategory) return null;

        const segment = window.location.pathname
            .split("/")
            .filter(Boolean)
            .pop();

        const num = Number(segment);
        return isNaN(num) ? null : num;
    })();

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

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

    const handleSortChange = (event) => {
        const selectedSort = event.target.value;
        setSortBy(selectedSort);
        setPage(1);
        setPosts([]);
        setHasMore(true);
    };

    const handleValidation = () => {
        if (!postDetails.subcategory) {
            showMessage("Please select a category.", "error");
            return false;
        }
        return true;
    };

    const fetchPosts = async (pageOverride = null) => {
        // when pageOverride is provided (e.g. 1), ignore hasMore guard
        if (pageOverride === null && (!hasMore || loading)) return;
        setLoading(true);

        try {
            const reqPage = pageOverride ?? page;
            const response = await axios.get("/api/member/posts", {
                params: {
                    page: reqPage,
                    sort: sortBy,
                    subcategory: parentPostId ? undefined : subcategoryFilter || undefined,
                    parent_post_id: parentPostId || undefined,
                },
            });

            const newPosts = response.data.data;
            const currentPage = response.data.current_page;
            const lastPage = response.data.last_page;

            if (reqPage === 1) {
                // replace with fresh first page
                setPosts(newPosts);
            } else {
                setPosts((prev) => {
                    const existingIds = new Set(prev.map((p) => p.id));
                    const uniqueNewPosts = newPosts.filter(
                        (p) => !existingIds.has(p.id),
                    );
                    return [...prev, ...uniqueNewPosts];
                });
            }

            setHasMore(currentPage < lastPage);
        } catch (error) {
            console.error("Error fetching posts:", error);
        } finally {
            setLoading(false);
        }
    };

    const fetchPostsByMember = useCallback(
        async (pageOverride = null) => {
            if (pageOverride === null && (!hasMore || loading)) return;
            setLoading(true);

            try {
                const reqPage = pageOverride ?? page;
                const response = await axios.get(
                    `/api/member/posts/member/${lastSegment}`,
                    {
                        params: {
                            page: reqPage,
                            sort: sortBy,
                            subcategory: subcategoryFilter || undefined,
                        },
                    },
                );

                const newPosts = response.data.data;
                const currentPage = response.data.current_page;
                const lastPage = response.data.last_page;

                if (reqPage === 1) {
                    setPosts(newPosts);
                } else {
                    setPosts((prev) => {
                        const existingIds = new Set(prev.map((p) => p.id));
                        const uniqueNewPosts = newPosts.filter(
                            (p) => !existingIds.has(p.id),
                        );
                        return [...prev, ...uniqueNewPosts];
                    });
                }

                setHasMore(currentPage < lastPage);
            } catch (error) {
                console.error("Error fetching posts:", error);
            } finally {
                setLoading(false);
            }
        },
        [sortBy, subcategoryFilter, hasMore, loading, page, lastSegment],
    );

    useEffect(() => {
        setPosts([]);
        setPage(1);
        setHasMore(true);

        // Explicitly fetch page 1 here (rather than relying on the page-effect
        // below) because the `hasMore` guard in fetchPosts still holds its old
        // value in this same render pass — if the previous filter's list had
        // already reached its last page, the guard would otherwise silently
        // skip fetching the newly selected filter's posts.
        if (lastSegment) {
            fetchPostsByMember(1);
        } else {
            fetchPosts(1);
        }
    }, [sortBy, subcategoryFilter]);

    // A post made through a separately-mounted composer (e.g. the standalone
    // one on a forum category page) can't call fetchPosts directly since it's
    // a different React root — it broadcasts this event instead.
    useEffect(() => {
        if (!fixedSubcategory) return;

        const handlePostCreated = (event) => {
            const detail = event.detail || {};

            if (parentPostId) {
                // This feed shows submissions to one specific topic only.
                if (String(detail.parentPostId) !== String(parentPostId)) return;
            } else {
                // This feed shows a category's own topics — a submission to
                // one of them doesn't belong here.
                if (detail.subcategory !== fixedSubcategory || detail.parentPostId) return;
            }

            setPosts([]);
            setPage(1);
            setHasMore(true);
            fetchPosts(1);
        };

        window.addEventListener("community:post-created", handlePostCreated);
        return () => window.removeEventListener("community:post-created", handlePostCreated);
    }, [fixedSubcategory, parentPostId, sortBy]);

    useEffect(() => {
        if (page === 1) return;

        if (lastSegment) {
            fetchPostsByMember();
        } else {
            fetchPosts();
        }
    }, [page]);

    const handleFilterChange = (value) => {
        setSubcategoryFilter(value);
    };

    useEffect(() => {
        if (!hasMore || loading) return;

        const handleObserver = (entries) => {
            const target = entries[0];
            if (target.isIntersecting && hasMore && !loading) {
                setPage((prev) => prev + 1);
            }
        };

        const observer = new IntersectionObserver(handleObserver, {
            root: null,
            rootMargin: "200px",
            threshold: 0.1,
        });

        const sentinel = document.getElementById("scroll-sentinel");
        if (sentinel) observer.observe(sentinel);

        return () => {
            if (sentinel) observer.unobserve(sentinel);
            observer.disconnect();
        };
    }, [loading, hasMore]);

    const handlePost = async (data) => {
        if (!handleValidation()) return;
        setPosting(true);

        try {
            const res = await axios.post("/api/member/post", data, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
            showMessage("Posted successfully.", "success");

            // setPostDetails({ category: "", subcategory: "" });
            setBlocks([]);
            setMediaFiles([]);
            setExpanded(false);

            // reset pagination and fetch fresh first page
            setPosts([]);
            setPage(1);
            setHasMore(true);
            if (lastSegment) {
                await fetchPostsByMember(1);
            } else {
                await fetchPosts(1);
            }
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

            // remove locally
            setPosts((prev) => prev.filter((p) => p.id !== id));
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

        setCommenting(true);

        const comment = {
            body: newComment,
            post_id: selectedPost.id,
        };

        try {
            const res = await axios.post("/api/member/comment", comment, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });

            const created = res.data?.data || res.data;

            // append comment to the right post locally
            if (created && created.id) {
                setPosts((prev) =>
                    prev.map((p) =>
                        p.id === selectedPost.id
                            ? {
                                  ...p,
                                  comments: [...(p.comments || []), created],
                              }
                            : p,
                    ),
                );
            } else {
                // fallback: refetch
                setPosts([]);
                setPage(1);
                setHasMore(true);
                await fetchPosts();
            }

            showMessage("Comment posted.", "success");
            setNewComment("");
            setExpanded(false);
        } catch (error) {
            showMessage("Error adding comment.", "error");
            console.error("Error adding comment:", error);
        } finally {
            setCommenting(false);
        }
    };

    // allow child to update a post (likes/comments) optimistically
    const handleUpdatePost = (id, patch) => {
        setPosts((prev) =>
            prev.map((p) => (p.id === id ? { ...p, ...patch } : p)),
        );
    };

    const togglePinPost = async (id) => {
        try {
            await axios.post(`/api/member/posts/${id}/pin`, {
                headers: { "X-CSRF-TOKEN": csrfToken },
                withCredentials: true,
            });

            // Reset pagination and refetch to show pinned post at top
            setPosts([]);
            setPage(1);
            setHasMore(true);

            if (lastSegment) {
                await fetchPostsByMember();
            } else {
                await fetchPosts();
            }
            showMessage("Pinned to the top", "success");
        } catch (err) {
            console.error("Pin toggle failed:", err);
            showMessage(err.response?.data?.message, "error");
        }
    };

    return (
        <>
            {!hideComposer && (
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
                        blocks={blocks}
                        setBlocks={setBlocks}
                        fetchPosts={fetchPosts}
                        initialTopic={subcategoryFilter}
                    />
                </div>
            )}

            {!fixedSubcategory && (
                <div className="flex flex-nowrap sm:flex-wrap items-center gap-2 overflow-x-auto sm:overflow-visible -mx-4 px-4 sm:mx-0 sm:px-0 mb-5 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                    {[
                        { label: "All Posts", value: "" },
                        { label: "Say Hello", value: "say_hello" },
                        { label: "Discussions", value: "ask_question" },
                        { label: "Announcements", value: "announcement" },
                        { label: "Suggestions", value: "suggestions" },
                    ].map((pill) => (
                        <button
                            key={pill.value}
                            type="button"
                            onClick={() => handleFilterChange(pill.value)}
                            className={`flex-shrink-0 whitespace-nowrap text-sm font-medium px-4 py-2 rounded-lg border transition-colors ${
                                subcategoryFilter === pill.value
                                    ? "bg-black dark:bg-black border-black dark:border-black text-white shadow-sm"
                                    : "bg-gray-100 dark:bg-gray-700 border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600"
                            }`}
                        >
                            {pill.label}
                        </button>
                    ))}
                </div>
            )}

            {!fixedSubcategory && TOPIC_INFO[subcategoryFilter] && (
                <div className="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-xl p-4 mb-5">
                    <p className="text-sm text-indigo-900 dark:text-indigo-200 leading-relaxed">
                        {TOPIC_INFO[subcategoryFilter]}
                    </p>
                </div>
            )}

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
                            <i className="fa fa-list" aria-hidden="true"></i>
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
                        commenting={commenting}
                        onUpdatePost={handleUpdatePost}
                        // setCommenting={setCommenting}
                        togglePinPost={togglePinPost}
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
                ) : !loading && posts.length > 0 && hasMore ? (
                    <p
                        onClick={() => setPage((prev) => prev + 1)}
                        className="text-center text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 mb-4 cursor-pointer"
                    >
                        Load more
                    </p>
                ) : null}

                {/* Sentinel for IntersectionObserver */}
                <div id="scroll-sentinel" className="h-10"></div>
            </div>
        </>
    );
};

export default PostList;

if (document.getElementById("post-list")) {
    const Index = ReactDOM.createRoot(document.getElementById("post-list"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <PostList />
            </FlashMessageProvider>
        </React.StrictMode>,
    );
}

const categoryFeedMount = document.getElementById("category-post-feed");
if (categoryFeedMount) {
    const fixedSubcategory = categoryFeedMount.dataset.subcategory || "";
    const parentPostId = categoryFeedMount.dataset.parentPostId || null;

    ReactDOM.createRoot(categoryFeedMount).render(
        <React.StrictMode>
            <FlashMessageProvider>
                <PostList fixedSubcategory={fixedSubcategory} hideComposer parentPostId={parentPostId} />
            </FlashMessageProvider>
        </React.StrictMode>,
    );
}
