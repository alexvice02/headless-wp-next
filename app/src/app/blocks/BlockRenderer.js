import { blocksMap } from "./index";

export default function BlockRenderer({ blocks }) {
    return (
        <>
            {blocks.map((block, i) => {
                const Component = blocksMap[block.blockName];
                if (!Component) {
                    console.warn("Unknown block:", block.blockName);
                    return null;
                }
                return <Component key={i} {...block} />;
            })}
        </>
    );
}