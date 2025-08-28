import BlockRenderer from "@/app/blocks/BlockRenderer";

export default function Column({ children = [] }) {
  return (
    <div style={{ flexGrow: 1 }}>
      <BlockRenderer blocks={children} />
    </div>
  );
}
