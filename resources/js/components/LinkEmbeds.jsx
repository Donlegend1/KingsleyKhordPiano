const LinkEmbeds = ({ links = [] }) => {
    if (!links.length) return null;

    return (
        <div className="mt-3 space-y-3">
            {links.map((link, idx) =>
                link.embed_url ? (
                    <div
                        key={idx}
                        className="relative w-full overflow-hidden rounded-lg bg-black aspect-video"
                    >
                        <iframe
                            src={link.embed_url}
                            className="absolute inset-0 w-full h-full"
                            frameBorder="0"
                            allow="autoplay; encrypted-media; fullscreen; picture-in-picture"
                            allowFullScreen
                        />
                    </div>
                ) : (
                    <a
                        key={idx}
                        href={link.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="block text-indigo-600 underline break-all"
                    >
                        {link.url}
                    </a>
                )
            )}
        </div>
    );
};

export default LinkEmbeds;
