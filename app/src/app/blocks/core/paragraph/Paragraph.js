export default function Paragraph({ attrs, innerHTML }) {
    return (
        <p
            className={attrs?.className || ""}
            dangerouslySetInnerHTML={{ __html: innerHTML }}
        />
    );
}