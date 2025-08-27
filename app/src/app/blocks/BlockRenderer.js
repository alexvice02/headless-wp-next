export default function BlockRenderer({ blocks = [] }) {
    if (!Array.isArray(blocks)) return null;
    return (
        <>
            {blocks.map((block, i) => (
                <BlockNode key={i} block={block} />
            ))}
        </>
    );
}

function BlockNode({ block }) {
    if (!block || typeof block !== "object") return null;

    const { type, attrs = {}, children = [], htmlFallback } = block;

    switch (type) {
        case "core/paragraph":
            return <p className={attrs.className || ""}>{block.text || ""}</p>;

        case "core/heading": {
            const level = Math.min(Math.max(parseInt(block.level || 2, 10), 1), 6);
            const Tag = `h${level}`;
            return (
                <Tag id={block.anchor || undefined} className={attrs.className || ""}>
                    {block.text || ""}
                </Tag>
            );
        }

        case "core/image":
            if (!block.url) {
                return htmlFallback ? (
                    <div dangerouslySetInnerHTML={{ __html: htmlFallback }} />
                ) : null;
            }
            return (
                <figure className={attrs.className || ""}>
                    <img
                        src={block.url}
                        alt={block.alt || ""}
                        width={block.width || undefined}
                        height={block.height || undefined}
                        srcSet={block.srcset || undefined}
                        sizes={block.sizes || undefined}
                        loading="lazy"
                    />
                    {block.captionHtml ? (
                        <figcaption dangerouslySetInnerHTML={{ __html: block.captionHtml }} />
                    ) : null}
                </figure>
            );

        case "core/video":
            if (!block.src && htmlFallback) {
                return <div dangerouslySetInnerHTML={{ __html: htmlFallback }} />;
            }
            return (
                <video
                    className={attrs.className || ""}
                    src={block.src || undefined}
                    poster={block.poster || undefined}
                    autoPlay={!!block.autoplay}
                    muted={!!block.muted}
                    loop={!!block.loop}
                    controls={block.controls !== false}
                    playsInline
                >
                    {Array.isArray(block.tracks)
                        ? block.tracks.map((t, i) => (
                            <track key={i} {...t} />
                        ))
                        : null}
                </video>
            );

        case "core/embed":
            return htmlFallback ? (
                <div className={attrs.className || ""} dangerouslySetInnerHTML={{ __html: htmlFallback }} />
            ) : null;

        case "core/quote":
            return (
                <blockquote className={attrs.className || ""}>
                    <BlockRenderer blocks={children} />
                    {block.citation ? <cite>{block.citation}</cite> : null}
                </blockquote>
            );

        case "core/group":
            let layout = attrs.layout || [];
            if (!layout) {
                return <div dangerouslySetInnerHTML={{ __html: htmlFallback }}></div>
            }
            return <div style={{ display: layout.type, flexWrap: layout.flexWrap, justifyContent: layout.justifyContent, flexDirection: layout.orientation === 'vertical' ? 'column' : 'row'}}>
                <BlockRenderer blocks={children} />
            </div>
        case "core/columns":
        case "core/column":
        case "core/buttons":
        case "core/button":
        case "core/list":
            if (type === "core/list" && htmlFallback) {
                return <div dangerouslySetInnerHTML={{ __html: htmlFallback }} />;
            }
            return <BlockRenderer blocks={children} />;

        default:
            return htmlFallback ? (
                <div dangerouslySetInnerHTML={{ __html: htmlFallback }} />
            ) : null;
    }
}