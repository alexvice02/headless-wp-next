import BlockRenderer from "@/app/blocks/BlockRenderer";

export default function Group({attrs = {}, children = [], htmlFallback}) {
    const layout = attrs.layout || null;
    if (!layout) {
        return htmlFallback ? <div dangerouslySetInnerHTML={{__html: htmlFallback}}/> : null;
    }
    return (
        <div
            style={{
                display: layout.type,
                flexWrap: layout.flexWrap,
                justifyContent: layout.justifyContent,
                flexDirection: layout.orientation === "vertical" ? "column" : "row",
            }}
        >
            <BlockRenderer blocks={children}/>
        </div>
    );
}
