import BlockRenderer from "@/app/blocks/BlockRenderer";

export default function Columns({attrs = {}, children = []}) {
    return (
        <div
            style={{
                display: "flex",
                flexWrap: !attrs.isStackedOnMobile ? "nowrap" : "wrap",
                flexDirection: "row",
                gap: "10px",
            }}
        >
            <BlockRenderer blocks={children}/>
        </div>
    );
}
