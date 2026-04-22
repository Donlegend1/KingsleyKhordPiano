import { useState } from "react";

// In-app link player modal
function LinkModal({ url, onClose }) {
    if (!url) return null;
    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70" onClick={onClose}>
            <div className="relative w-full max-w-3xl mx-4 bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-2xl" onClick={e => e.stopPropagation()}>
                <div className="flex items-center justify-between px-4 py-2 border-b dark:border-gray-700">
                    <span className="text-sm text-gray-500 truncate max-w-xs">{url}</span>
                    <button onClick={onClose} className="ml-4 text-gray-400 hover:text-gray-700 dark:hover:text-white text-xl font-bold">✕</button>
                </div>
                <iframe
                    src={url}
                    className="w-full"
                    style={{ height: "70vh" }}
                    allowFullScreen
                    sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
                />
            </div>
        </div>
    );
}

// Updated renderTextWithLinks — opens in-app instead of new tab
function renderTextWithLinks(content, onLinkClick) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    const parts = content.split(urlRegex);
    return parts.map((part, i) =>
        urlRegex.test(part) ? (
            <button
                key={i}
                onClick={() => onLinkClick(part)}
                className="text-indigo-600 underline break-all cursor-pointer bg-transparent border-none p-0"
            >
                {part}
            </button>
        ) : (
            part
        )
    );
}

// Main component (replace your existing block renderer)
export default function PostBlocks({ post }) {
    const [modalUrl, setModalUrl] = useState(null);

    return (
        <>
            <LinkModal url={modalUrl} onClose={() => setModalUrl(null)} />

            {post.blocks.map((block, idx) => {
                switch (block.type) {
                    case "text":
                        return (
                            <p key={idx} className="whitespace-pre-wrap dark:text-gray-300">
                                {renderTextWithLinks(block.content, setModalUrl)}
                            </p>
                        );

                    case "image":
                        return (
                            <img
                                key={idx}
                                src={`/${block.content}`}
                                className="rounded-lg w-full"
                            />
                        );

                    case "video":
                        return (
                            <video key={idx} controls className="w-full rounded-lg">
                                <source src={`/${block.content}`} />
                            </video>
                        );

                    case "audio":
                        return (
                            <audio key={idx} controls>
                                <source src={`/${block.content}`} />
                            </audio>
                        );

                    case "link":
                        return block.content ? (
                            <div
                                key={idx}
                                className="relative aspect-video rounded-lg overflow-hidden cursor-pointer"
                                onClick={() => setModalUrl(block.embed_url || block.content)}
                            >
                                <iframe
                                    src={block.embed_url}
                                    className="w-full h-full pointer-events-none"
                                    frameBorder="0"
                                    allow="autoplay; fullscreen; picture-in-picture"
                                    allowFullScreen
                                />
                                {/* Overlay to intercept clicks */}
                                <div className="absolute inset-0" />
                            </div>
                        ) : (
                            <button
                                key={idx}
                                onClick={() => setModalUrl(block.content)}
                                className="text-indigo-600 underline break-all bg-transparent border-none p-0 cursor-pointer"
                            >
                                {block.content}
                            </button>
                        );

                    default:
                        return null;
                }
            })}
        </>
    );
}
