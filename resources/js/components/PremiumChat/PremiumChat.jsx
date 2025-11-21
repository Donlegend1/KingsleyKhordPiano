import React, { useEffect, useState, useCallback } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import ChatWithComments from "./ChatWithComments.jsx";
import CreatePostBox from "./CreatePostBox.jsx";
import SkeletonPost from "../Skeleton/SkeletonPost.jsx";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "../Alert/FlashMessageContext";

const PremiumChat = () => {
    const { showMessage } = useFlashMessage();
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [newComment, setNewComment] = useState("");
    const [commenting, setCommenting] = useState(false);

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
        return true;
    };

    const fetchPosts = useCallback(async () => {
        if (!hasMore || loading) return;
        setLoading(true);

        try {
            const response = await axios.get(
                "/api/member/premium/rooms/1/messages",
                {
                    params: {
                        page,
                        sort: sortBy,
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
    }, [hasMore, loading, page]);

    useEffect(() => {
        setPosts([]);
        setPage(1);
        setHasMore(true);
    }, [sortBy]);

    useEffect(() => {
        fetchPosts();
    }, [page]);

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
            const res = await axios.post(
                "/api/member/premium/rooms/1/messages",
                data,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );

            // try to get created post from response
            const created = res.data?.data || res.data;

            // prepend to local posts so UI updates immediately
            if (created && created.id) {
                setPosts((prev) => [created, ...prev]);
            } else {
                // fallback: refetch first page
                setPosts([]);
                setPage(1);
                setHasMore(true);
                await fetchPosts();
            }

            showMessage("Posted successfully.", "success");

            setPostDetails({
                body: "",
            });
            setMediaFiles([]);
            setExpanded(false);
        } catch (error) {
            showMessage("Error creating post.", "error");
            console.error("Error creating post:", error);
        } finally {
            setPosting(false);
        }
    };

    const handleDeletePost = async (id) => {
        try {
            await axios.delete(`/api/member/premium/messages/${id}`);

            // remove locally so UI updates immediately
            setPosts((prev) => prev.filter((p) => p.id !== id));
            // clear selection if it was the deleted post
            setSelectedPost((prev) => (prev?.id === id ? {} : prev));

            showMessage("Post deleted.", "success");
        } catch (error) {
            showMessage(error.response?.data?.message, "error");
            console.error("Error deleting post:", error);
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
        };

        try {
            const res = await axios.post(
                `/api/member/premium/messages/${selectedPost.id}/reply`,
                comment,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );

            const created = res.data?.data || res.data;

            // update the specific post's replies locally so UI updates
            if (created && created.id) {
                setPosts((prev) =>
                    prev.map((p) =>
                        p.id === selectedPost.id
                            ? { ...p, replies: [...(p.replies || []), created] }
                            : p
                    )
                );
            } else {
                // fallback: refetch posts
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

    // allow child components to apply small updates to a post (e.g. likes)
    const handleUpdatePost = (id, patch) => {
        setPosts((prev) =>
            prev.map((p) => (p.id === id ? { ...p, ...patch } : p))
        );
    };

    // Calendar sidebar component (simple month grid with highlighted dates)
    const CalendarSidebar = ({ events = [] }) => {
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();

        // generate days for current month
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Highlight by DAY for the current month
        const eventDays = new Set(
            events.map((e) => {
                try {
                    return new Date(e.start_time).getDate(); // FIXED
                } catch (err) {
                    return null;
                }
            })
        );

        const weeks = [];
        let dayCounter = 1 - firstDay;
        while (dayCounter <= daysInMonth) {
            const week = [];
            for (let i = 0; i < 7; i++, dayCounter++) {
                if (dayCounter < 1 || dayCounter > daysInMonth) {
                    week.push(null);
                } else {
                    week.push(dayCounter);
                }
            }
            weeks.push(week);
        }

        return (
            <div className="w-80 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                <h4 className="text-sm font-semibold mb-3">Live Shows</h4>

                <div className="grid grid-cols-7 gap-1 text-xs text-center text-gray-500 mb-2">
                    {["S", "M", "T", "W", "T", "F", "S"].map((d, index) => (
                        <div key={index} className="py-1">
                            {d}
                        </div>
                    ))}
                </div>

                {/* Calendar Grid */}
                <div className="space-y-1">
                    {weeks.map((week, wi) => (
                        <div key={wi} className="grid grid-cols-7 gap-1">
                            {week.map((d, di) => (
                                <div
                                    key={di}
                                    className={`h-8 flex items-center justify-center rounded ${
                                        d && eventDays.has(d)
                                            ? "bg-yellow-400 text-black font-semibold"
                                            : "bg-transparent"
                                    }`}
                                >
                                    {d || ""}
                                </div>
                            ))}
                        </div>
                    ))}
                </div>

                {/* Upcoming section */}
                <div className="mt-4 text-sm">
                    <h5 className="font-semibold">Upcoming</h5>
                    <ul className="mt-2 space-y-2 text-xs">
                        {events.length > 0 ? (
                            events.slice(0, 5).map((ev, i) => (
                                <li key={i} className="flex justify-between">
                                    <span>
                                        {new Date(
                                            ev.start_time
                                        ).toLocaleDateString()}{" "}
                                        —{" "}
                                        {new Date(
                                            ev.start_time
                                        ).toLocaleTimeString([], {
                                            hour: "2-digit",
                                            minute: "2-digit",
                                        })}
                                    </span>

                                    <span className="text-gray-600 dark:text-gray-300">
                                        {ev.title || "Live show"}
                                    </span>
                                </li>
                            ))
                        ) : (
                            <li className="text-gray-500">
                                No scheduled live shows
                            </li>
                        )}
                    </ul>
                </div>
            </div>
        );
    };

    const [liveShows, setLiveShows] = useState([]);
    const fetchLiveShows = async () => {
        try {
            const res = await axios.get("/api/member/live-shows");
            setLiveShows(res.data || []);
        } catch (error) {
            console.error("Error fetching live shows:", error);
        }
    };

    useEffect(() => {
        fetchLiveShows();
    }, []);

    return (
        <div className="flex gap-6">
            {/* Left: Chat area */}
            <div className="flex-1 flex flex-col h-[70vh] bg-transparent">
                <div className="flex-1 overflow-y-auto p-2 space-y-3 my-5">
                    {posts.map((post) => (
                        <ChatWithComments
                            key={post.id}
                            setSelectedPost={setSelectedPost}
                            post={post}
                            newComment={newComment}
                            setNewComment={setNewComment}
                            handleCommentSubmit={handleCommentSubmit}
                            handleDeletePost={handleDeletePost}
                            commenting={commenting}
                            onUpdatePost={handleUpdatePost}
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

                {/* Sticky / bottom input area */}
                <div className="mt-2">
                    <CreatePostBox
                        handlePost={handlePost}
                        postDetails={postDetails}
                        setPostDetails={setPostDetails}
                        posting={posting}
                        expanded={expanded}
                        setExpanded={setExpanded}
                        mediaFiles={mediaFiles}
                        setMediaFiles={setMediaFiles}
                        subcategory={null}
                    />
                </div>
            </div>

            {/* Right: calendar sidebar */}
            <div className=" hidden md:flex w-80">
                <CalendarSidebar events={liveShows} />
            </div>
        </div>
    );
};

export default PremiumChat;

if (document.getElementById("premium-chat")) {
    const Index = ReactDOM.createRoot(document.getElementById("premium-chat"));

    Index.render(
        <React.StrictMode>
            <FlashMessageProvider>
                <PremiumChat />
            </FlashMessageProvider>
        </React.StrictMode>
    );
}
