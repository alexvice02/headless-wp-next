export default function ImageBlock({ attrs }) {
    if (!attrs?.url) return null;

    return (
        <figure className={attrs?.className || ""}>
            <img
                src={attrs.url}
                alt={attrs?.alt || ""}
                width={attrs?.width}
                height={attrs?.height}
            />
            {attrs?.caption && <figcaption>{attrs.caption}</figcaption>}
        </figure>
    );
}