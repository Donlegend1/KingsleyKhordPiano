import { useEffect, useRef, useState } from "react";

// Only these are actually embeddable as a video/media iframe — any other
// pasted link (a homepage, an article, etc.) should render as a plain
// clickable link instead of trying to iframe the whole site.
const EMBEDDABLE_LINK_PATTERN = /youtu\.be\/|youtube\.com|vimeo\.com|dailymotion\.com|drive\.google\.com\/file|tiktok\.com|twitch\.tv|facebook\.com\/.*\/videos|instagram\.com\/(p|reel|tv)\//i;
const isEmbeddableLink = (url) => Boolean(url) && EMBEDDABLE_LINK_PATTERN.test(url);

const PDFJS_VERSION = "4.5.136";
let pdfjsLoadPromise = null;

function loadPdfJs() {
    if (window.pdfjsLib) return Promise.resolve(window.pdfjsLib);
    if (pdfjsLoadPromise) return pdfjsLoadPromise;

    pdfjsLoadPromise = new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.type = "module";
        script.textContent = `
            import * as pdfjsLib from "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/${PDFJS_VERSION}/pdf.min.mjs";
            pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/${PDFJS_VERSION}/pdf.worker.min.mjs";
            window.pdfjsLib = pdfjsLib;
            window.dispatchEvent(new Event("pdfjs-ready"));
        `;
        window.addEventListener("pdfjs-ready", () => resolve(window.pdfjsLib), { once: true });
        script.onerror = reject;
        document.head.appendChild(script);
    });

    return pdfjsLoadPromise;
}

function formatFileSize(bytes) {
    if (!bytes && bytes !== 0) return null;
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

// Renders the first page of a PDF onto a canvas as a preview thumbnail.
function PdfThumbnail({ url }) {
    const canvasRef = useRef(null);
    const [failed, setFailed] = useState(false);

    useEffect(() => {
        let cancelled = false;

        loadPdfJs()
            .then((pdfjsLib) => pdfjsLib.getDocument(url).promise)
            .then((pdf) => pdf.getPage(1))
            .then((page) => {
                if (cancelled || !canvasRef.current) return;

                const unscaledViewport = page.getViewport({ scale: 1 });
                const scale = 320 / unscaledViewport.width;
                const viewport = page.getViewport({ scale });
                const canvas = canvasRef.current;
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                return page.render({
                    canvasContext: canvas.getContext("2d"),
                    viewport,
                }).promise;
            })
            .catch(() => {
                if (!cancelled) setFailed(true);
            });

        return () => {
            cancelled = true;
        };
    }, [url]);

    if (failed) {
        return (
            <div className="flex items-center justify-center h-48 bg-gray-900">
                <PdfIcon className="w-14 h-14 text-gray-500" />
            </div>
        );
    }

    return (
        <div className="flex items-center justify-center h-48 bg-gray-900 overflow-hidden">
            <canvas ref={canvasRef} className="max-h-full" />
        </div>
    );
}

function PdfIcon({ className, label = "PDF" }) {
    return (
        <svg className={className} fill="currentColor" viewBox="0 0 24 24">
            <path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6z" opacity="0.3" />
            <path d="M14 2v6h6" opacity="0.5" />
            <text x="12" y="17" textAnchor="middle" fontSize="6.5" fontWeight="bold" fill="currentColor">{label}</text>
        </svg>
    );
}

// A file's badge label + fallback icon label, based on its extension.
function fileTypeLabel(extension) {
    if (extension === "pdf") return "PDF";
    if (extension === "mid" || extension === "midi") return "MIDI";
    if (extension === "doc" || extension === "docx") return "DOC";
    if (extension === "txt") return "TXT";
    return "FILE";
}

// In-app preview modal (links + file attachments). Our own /storage files are
// same-origin and safe to render unsandboxed — sandboxing them actually
// breaks Chrome's built-in PDF viewer ("This page has been blocked by
// Chrome"). Only external link embeds get the sandbox restrictions.
function PreviewModal({ preview, onClose }) {
    if (!preview) return null;
    const { url, title } = preview;
    const isOwnFile = url.startsWith("/storage/");

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70" onClick={onClose}>
            <div className="relative w-full max-w-3xl mx-4 bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-2xl" onClick={e => e.stopPropagation()}>
                <div className="flex items-center justify-between px-4 py-2 border-b dark:border-gray-700">
                    <span className="text-sm text-gray-500 truncate max-w-xs">{title || url}</span>
                    <button onClick={onClose} className="ml-4 text-gray-400 hover:text-gray-700 dark:hover:text-white text-xl font-bold">✕</button>
                </div>
                <iframe
                    src={url}
                    className="w-full"
                    style={{ height: "70vh" }}
                    allowFullScreen
                    {...(isOwnFile ? {} : { sandbox: "allow-scripts allow-same-origin allow-forms allow-popups" })}
                />
            </div>
        </div>
    );
}

// Updated renderTextWithLinks — opens in-app instead of new tab
function renderTextWithLinks(content, onLinkClick) {
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    const parts = content.split(urlRegex);
    return parts.map((part, i) => {
        if (!urlRegex.test(part)) return part;

        if (isEmbeddableLink(part)) {
            return (
                <button
                    key={i}
                    onClick={() => onLinkClick(part)}
                    className="text-indigo-600 underline break-all cursor-pointer bg-transparent border-none p-0"
                >
                    {part}
                </button>
            );
        }

        return (
            <a
                key={i}
                href={part}
                target="_blank"
                rel="noopener noreferrer"
                className="text-indigo-600 underline break-all"
            >
                {part}
            </a>
        );
    });
}

function FileCard({ block, onPreview }) {
    const [menuOpen, setMenuOpen] = useState(false);
    const fileName = block.original_name || block.content.split("/").pop();
    const extension = fileName.split(".").pop().toLowerCase();
    const isPdf = extension === "pdf";
    const label = fileTypeLabel(extension);
    const url = `/storage/${block.content}`;
    const size = formatFileSize(block.file_size);

    return (
        <div className="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <button type="button" onClick={() => onPreview({ url, title: fileName })} className="block w-full text-left">
                {isPdf ? (
                    <PdfThumbnail url={url} />
                ) : (
                    <div className="flex items-center justify-center h-48 bg-gray-900">
                        <PdfIcon className="w-14 h-14 text-gray-500" label={label} />
                    </div>
                )}
            </button>

            <div className="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/40">
                <span className="w-9 h-9 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-300 flex items-center justify-center flex-shrink-0">
                    <PdfIcon className="w-5 h-5" label={label} />
                </span>

                <button type="button" onClick={() => onPreview({ url, title: fileName })} className="min-w-0 flex-1 text-left">
                    <p className="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{fileName}</p>
                    <p className="text-xs text-gray-400">
                        {size ? `${size} · ` : ""}Click to view
                    </p>
                </button>

                <a
                    href={url}
                    download
                    onClick={(e) => e.stopPropagation()}
                    className="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-300 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0"
                    title="Download"
                >
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 13v8l-4-4m4 4 4-4M4.393 15.269A7 7 0 1115.71 8h1.79a4.5 4.5 0 012.436 8.284" />
                    </svg>
                </a>

                <div className="relative flex-shrink-0" onClick={(e) => e.stopPropagation()}>
                    <button
                        type="button"
                        onClick={() => setMenuOpen((v) => !v)}
                        className="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-300 flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                    >
                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4zm0 6a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                    {menuOpen && (
                        <div
                            className="absolute right-0 bottom-9 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-10"
                            onMouseLeave={() => setMenuOpen(false)}
                        >
                            <a href={url} target="_blank" rel="noopener noreferrer" className="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                Open in new tab
                            </a>
                            <a href={url} download className="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                Download
                            </a>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

// Main component (replace your existing block renderer)
export default function PostBlocks({ post }) {
    const [preview, setPreview] = useState(null);
    const openPreview = (url) => setPreview({ url, title: url });

    return (
        <>
            <PreviewModal preview={preview} onClose={() => setPreview(null)} />

            <div className="space-y-3">
            {post.blocks.map((block, idx) => {
                switch (block.type) {
                    case "text":
                        return (
                            <p key={idx} className="whitespace-pre-wrap text-gray-900 dark:text-gray-300">
                                {renderTextWithLinks(block.content, openPreview)}
                            </p>
                        );

                    case "image":
                        return (
                            <img
                                key={idx}
                                src={`/storage/${block.content}`}
                                className="rounded-lg w-full"
                            />
                        );

                    case "video":
                        return (
                            <video key={idx} controls className="w-full rounded-lg">
                                <source src={`/storage/${block.content}`} />
                            </video>
                        );

                    case "audio":
                        return (
                            <audio key={idx} controls>
                                <source src={`/storage/${block.content}`} />
                            </audio>
                        );

                    case "file":
                        return <FileCard key={idx} block={block} onPreview={setPreview} />;

                    case "link": {
                        const rawUrl = block.content || "";

                        if (rawUrl && isEmbeddableLink(rawUrl)) {
                            const embedSrc = block.embed_url || rawUrl;
                            const isVertical = /instagram\.com|tiktok\.com/.test(embedSrc);
                            return (
                                <div
                                    key={idx}
                                    className={`relative rounded-lg overflow-hidden ${isVertical ? "aspect-[9/16] max-w-sm mx-auto" : "aspect-video"}`}
                                >
                                    <iframe
                                        src={block.embed_url}
                                        className="w-full h-full"
                                        frameBorder="0"
                                        allow="autoplay; fullscreen; picture-in-picture"
                                        allowFullScreen
                                    />
                                </div>
                            );
                        }

                        return rawUrl ? (
                            <a
                                key={idx}
                                href={rawUrl}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-indigo-600 underline break-all"
                            >
                                {rawUrl}
                            </a>
                        ) : null;
                    }

                    default:
                        return null;
                }
            })}
            </div>
        </>
    );
}
