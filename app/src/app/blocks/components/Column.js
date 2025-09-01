import BlockRenderer from "@/app/blocks/BlockRenderer";

export default function Column({attrs = {}, children = []}) {

    return (
        <div style={{flexGrow: 1, backgroundColor: attrs?.backgroundColor ? `var(--color-${attrs?.backgroundColor})` : 'transparent'}}>
            <BlockRenderer blocks={children}/>
        </div>
    );
}
