import { getBlockComponent } from "@/app/blocks/registry";
import { registerDefaultBlocks } from "@/app/blocks/register-defaults";
import UnknownBlock from "@/app/blocks/components/UnknownBlock";

registerDefaultBlocks();

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

    const Component = type ? getBlockComponent(type) : null;

    if (Component) {
        return <Component {...block} attrs={attrs} children={children} htmlFallback={htmlFallback} />;
    }

    if (type === "core/embed") {
        return htmlFallback ? (
            <div className={attrs.className || ""} dangerouslySetInnerHTML={{ __html: htmlFallback }} />
        ) : null;
    }

    return <UnknownBlock htmlFallback={htmlFallback} attrs={attrs} />;
}