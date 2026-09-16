import React, { useEffect, useState } from "react";
import ReactDOM from "react-dom/client";
import axios from "axios";
import CreatePostBox from "./CreatePostBox.jsx";
import {
    useFlashMessage,
    FlashMessageProvider,
} from "./Alert/FlashMessageContext";

const StandalonePostComposer = ({ subcategory, hideTrigger, parentPostId }) => {
    const { showMessage } = useFlashMessage();
    const [posting, setPosting] = useState(false);
    const [expanded, setExpanded] = useState(false);
    const [blocks, setBlocks] = useState([]);
    const [postDetails, setPostDetails] = useState({
        blocks: [],
        body: "",
        category: "general",
        subcategory,
        media: [],
    });

    useEffect(() => {
        if (!hideTrigger) return;
        window.openPostComposer = () => setExpanded(true);
        return () => {
            delete window.openPostComposer;
        };
    }, [hideTrigger]);

    const handlePost = async (data) => {
        setPosting(true);

        try {
            await axios.post("/api/member/post", data, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            showMessage("Posted successfully.", "success");
            setBlocks([]);
            setExpanded(false);
            window.dispatchEvent(
                new CustomEvent("community:post-created", { detail: { subcategory, parentPostId } }),
            );
        } catch (error) {
            showMessage("Error creating post.", "error");
            console.error("Error creating post:", error);
        } finally {
            setPosting(false);
        }
    };

    return (
        <CreatePostBox
            handlePost={handlePost}
            postDetails={postDetails}
            setPostDetails={setPostDetails}
            posting={posting}
            expanded={expanded}
            setExpanded={setExpanded}
            blocks={blocks}
            setBlocks={setBlocks}
            fetchPosts={() => {}}
            fixedSubcategory={subcategory}
            hideTrigger={hideTrigger}
            parentPostId={parentPostId}
        />
    );
};

export default StandalonePostComposer;

const mountEl = document.getElementById("post-composer");
if (mountEl) {
    const subcategory = mountEl.dataset.subcategory || "activity_feed";
    const hideTrigger = mountEl.dataset.hideTrigger === "1";
    const parentPostId = mountEl.dataset.parentPostId || null;
    ReactDOM.createRoot(mountEl).render(
        <React.StrictMode>
            <FlashMessageProvider>
                <StandalonePostComposer subcategory={subcategory} hideTrigger={hideTrigger} parentPostId={parentPostId} />
            </FlashMessageProvider>
        </React.StrictMode>,
    );
}
