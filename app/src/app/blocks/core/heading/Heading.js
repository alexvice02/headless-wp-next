export default function Heading({ attrs, innerHTML }) {
    return (
        <h2
            className={attrs?.className || ""}
            dangerouslySetInnerHTML={{ __html: innerHTML }}
        />
    );
}