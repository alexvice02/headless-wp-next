export default function Heading({level = 2, text = "", anchor, attrs = {}}) {
    const lvl = Math.min(Math.max(parseInt(level || 2, 10), 1), 6);
    const Tag = `h${lvl}`;
    return (
        <Tag id={anchor || undefined} className={attrs.className || ""}>
            {text}
        </Tag>
    );
}
