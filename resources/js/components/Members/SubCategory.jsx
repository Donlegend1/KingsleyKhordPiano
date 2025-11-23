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
import { FaCheck } from "react-icons/fa";

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

    const [showSkeleton, setShowSkeleton] = useState(false);
    const [mediaFiles, setMediaFiles] = useState([]);
    const [userProfile, setUserProfile] = useState(null);
    const [latestUpdates, setLatestUpdates] = useState([]);

    const lastSegment = window.location.pathname
        .split("/")
        .filter((segment) => segment !== "")
        .pop();

    const postCategories = [
        {
            category: { name: "GetStarted", value: "get_started" },
            subCategories: [
                { name: "Say Hello", value: "say_hello" },
                { name: "Ask Question", value: "ask_question" },
            ],
        },
        {
            category: { name: "Others", value: "others" },
            subCategories: [
                { name: "Post Progress", value: "post_progress" },
                { name: "Lessons", value: "lessons" },
            ],
        },
        {
            category: { name: "Forum", value: "forum" },
            subCategories: [
                { name: "Beginner", value: "beginner" },
                { name: "Intermediate", value: "intermediate" },
                { name: "Advance", value: "advance" },
            ],
        },
        {
        category: { name: "ProgressReports", value: "progress_report" },
            subCategories: [
                { name: "Progress Reports", value: "progress_report" },
            ],
        },
    ];
    const getCategoryFromSubcategory = (subcategory) => {
        const categoryMap = {};
        postCategories.forEach(({ category, subCategories }) => {
            subCategories.forEach((subCat) => {
                categoryMap[subCat.value] = category.value;
            });
        });
        return categoryMap[subcategory] || "";
    };

    const [postDetails, setPostDetails] = useState({
        body: "",
        category: getCategoryFromSubcategory(lastSegment),
        subcategory: "",
        media: [],
    });

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

    const fetchUserProfile = useCallback(async () => {
        try {
            const response = await axios.get("/api/member/profile");
            setUserProfile(response.data);
        } catch (error) {
            console.error("Error fetching user profile:", error);
        }
    }, []);

    const fetchLatestUpdates = useCallback(async () => {
        try {
            // This would be replaced with actual API endpoint for latest updates
            // For now, using mock data similar to the blade template
            setLatestUpdates([
                {
                    id: 1,
                    user: "John",
                    action: "posted an update",
                    time: "4 years ago",
                },
                {
                    id: 2,
                    user: "Adele",
                    action: "posted an update",
                    time: "4 years ago",
                },
                {
                    id: 3,
                    user: "John",
                    action: "posted an update",
                    time: "5 years ago",
                },
                {
                    id: 4,
                    user: "John",
                    action: "posted an update in the group Coffee Addicts",
                    time: "5 years ago",
                },
            ]);
        } catch (error) {
            console.error("Error fetching latest updates:", error);
        }
    }, []);

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

    // Accept an optional pageArg so callers can force a specific page (useful after create/delete)
    const fetchPostsByCategory = useCallback(
        async (pageArg) => {
            // if pageArg is provided use it, otherwise fall back to state `page`
            const targetPage = typeof pageArg !== "undefined" ? pageArg : page;

            if (!hasMore && typeof pageArg === "undefined") return;
            if (loading) return;

            setLoading(true);
            console.log(
                "Fetching posts for subcategory:",
                lastSegment,
                "page:",
                targetPage
            );

            try {
                const response = await axios.get(`/api/member/posts`, {
                    params: {
                        page: targetPage,
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
        },
        [sortBy, hasMore, loading, page]
    );

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

    useEffect(() => {
        fetchUserProfile();
        fetchLatestUpdates();
    }, [fetchUserProfile, fetchLatestUpdates]);

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
            // Force refetch the first page so the newly created post appears according to server ordering
            await fetchPostsByCategory(1);
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
            // After deleting, reload first page to reflect server state
            await fetchPostsByCategory(1);
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
            // After adding a comment refresh first page so the post's comments are in sync
            await fetchPostsByCategory(1);

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

    const getProfileCompletionData = () => {
        if (!userProfile) return { completionPercentage: 0, fields: {} };

        const totalFields = 6;
        let completedFields = 0;

        const hasProfilePhoto = !!userProfile.passport;
        const hasBiography = !!userProfile.biography;
        const hasSocialMedia = !!(
            userProfile.instagram ||
            userProfile.youtube ||
            userProfile.facebook ||
            userProfile.tiktok
        );
        const hasSkillLevel = !!userProfile.skill_level;
        const hasPhoneNumber = !!userProfile.phone_number;
        const hasCountry = !!userProfile.country;

        if (hasProfilePhoto) completedFields++;
        if (hasBiography) completedFields++;
        if (hasSocialMedia) completedFields++;
        if (hasSkillLevel) completedFields++;
        if (hasPhoneNumber) completedFields++;
        if (hasCountry) completedFields++;

        const completionPercentage = Math.round(
            (completedFields / totalFields) * 100
        );
        const strokeDashoffset =
            339.292 - (339.292 * completionPercentage) / 100;

        return {
            completionPercentage,
            strokeDashoffset,
            fields: {
                hasProfilePhoto,
                hasBiography,
                hasSocialMedia,
                hasSkillLevel,
                hasPhoneNumber,
                hasCountry,
            },
        };
    };

    return (
        <>
            <div className="flex gap-6">
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
                {/* hidden md:block mx-4 md:mx-7 w-1/3 */}
                <div className="hidden lg:block w-80 xl:w-96  flex-shrink-0 space-y-6">
                    {/* Complete Your Profile Card */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-lg font-bold text-gray-900 dark:text-white">
                                Complete Your Profile
                            </h3>
                            <a
                                href="/member/profile"
                                className="text-sm text-yellow-500 hover:text-yellow-600 font-medium transition-colors"
                            >
                                Edit Profile →
                            </a>
                        </div>

                        {/* Progress Circle */}
                        <div className="flex justify-center mb-5">
                            <div className="relative w-32 h-32">
                                <svg
                                    className="w-32 h-32 transform -rotate-90"
                                    viewBox="0 0 120 120"
                                >
                                    <circle
                                        cx="60"
                                        cy="60"
                                        r="54"
                                        stroke="#E5E7EB"
                                        strokeWidth="8"
                                        fill="none"
                                    />
                                    <circle
                                        cx="60"
                                        cy="60"
                                        r="54"
                                        stroke="#10B981"
                                        strokeWidth="8"
                                        fill="none"
                                        strokeDasharray="339.292"
                                        strokeDashoffset={
                                            getProfileCompletionData()
                                                .strokeDashoffset
                                        }
                                        strokeLinecap="round"
                                        className="transition-all duration-300"
                                    />
                                </svg>
                                <div className="absolute inset-0 flex flex-col items-center justify-center">
                                    <span className="text-3xl font-bold text-gray-900">
                                        {
                                            getProfileCompletionData()
                                                .completionPercentage
                                        }
                                    </span>
                                    <span className="text-base text-gray-500">
                                        %
                                    </span>
                                    <span className="text-xs text-gray-400 mt-1">
                                        Complete
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Checklist Items */}
                        <div className="space-y-3">
                            {/* Profile Photo */}
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    {getProfileCompletionData().fields
                                        .hasProfilePhoto ? (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-green-500 flex items-center justify-center">
                                                <FaCheck className="w-3 h-3 text-green-500" />
                                            </div>
                                            <span className="text-sm text-green-500 font-medium">
                                                Profile Photo
                                            </span>
                                        </>
                                    ) : (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            </div>
                                            <span className="text-sm text-gray-500">
                                                Profile Photo
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>

                            {/* Biography */}
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    {getProfileCompletionData().fields
                                        .hasBiography ? (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-green-500 flex items-center justify-center">
                                                <FaCheck className="w-3 h-3 text-green-500" />
                                            </div>
                                            <span className="text-sm text-green-500 font-medium">
                                                Biography
                                            </span>
                                        </>
                                    ) : (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            </div>
                                            <span className="text-sm text-gray-500">
                                                Biography
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>

                            {/* Social Media Links */}
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    {getProfileCompletionData().fields
                                        .hasSocialMedia ? (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-green-500 flex items-center justify-center">
                                                <FaCheck className="w-3 h-3 text-green-500" />
                                            </div>
                                            <span className="text-sm text-green-500 font-medium">
                                                Social Media Links
                                            </span>
                                        </>
                                    ) : (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            </div>
                                            <span className="text-sm text-gray-500">
                                                Social Media Links
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>

                            {/* Skill Level */}
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    {getProfileCompletionData().fields
                                        .hasSkillLevel ? (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-green-500 flex items-center justify-center">
                                                <FaCheck className="w-3 h-3 text-green-500" />
                                            </div>
                                            <span className="text-sm text-green-500 font-medium">
                                                Skill Level
                                            </span>
                                        </>
                                    ) : (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            </div>
                                            <span className="text-sm text-gray-500">
                                                Skill Level
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>

                            {/* Phone Number */}
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    {getProfileCompletionData().fields
                                        .hasPhoneNumber ? (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-green-500 flex items-center justify-center">
                                                <FaCheck className="w-3 h-3 text-green-500" />
                                            </div>
                                            <span className="text-sm text-green-500 font-medium">
                                                Phone Number
                                            </span>
                                        </>
                                    ) : (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            </div>
                                            <span className="text-sm text-gray-500">
                                                Phone Number
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>

                            {/* Location / Country */}
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-3">
                                    {getProfileCompletionData().fields
                                        .hasCountry ? (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-green-500 flex items-center justify-center">
                                                <FaCheck className="w-3 h-3 text-green-500" />
                                            </div>
                                            <span className="text-sm text-green-500 font-medium">
                                                Location / Country
                                            </span>
                                        </>
                                    ) : (
                                        <>
                                            <div className="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                <div className="w-2 h-2 bg-gray-400 rounded-full"></div>
                                            </div>
                                            <span className="text-sm text-gray-500">
                                                Location / Country
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Latest Updates Card */}
                    <div className="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-4">
                            Latest updates
                        </h3>

                        <div className="space-y-4">
                            {latestUpdates.map((update) => (
                                <div
                                    key={update.id}
                                    className="flex items-start gap-3"
                                >
                                    <div className="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                        <svg
                                            className="w-5 h-5 text-gray-500 dark:text-gray-400"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clipRule="evenodd"
                                            ></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-800 dark:text-gray-200">
                                            <span className="font-semibold">
                                                {update.user}
                                            </span>{" "}
                                            {update.action}
                                        </p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">
                                            {update.time}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
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
