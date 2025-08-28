import BlockRenderer from "@/app/blocks/BlockRenderer";

export default function QuoteBlock({ citation, children = [], attrs = {} }) {
  return (
    <blockquote className={attrs.className || ""}>
      <BlockRenderer blocks={children} />
      {citation ? <cite>{citation}</cite> : null}
    </blockquote>
  );
}
