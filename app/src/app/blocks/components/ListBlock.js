export default function ListBlock({ htmlFallback, attrs = {} }) {
  // For now, we rely on server-generated HTML for lists to preserve markers/start etc.
  if (!htmlFallback) return null;
  return <div className={attrs.className || ""} dangerouslySetInnerHTML={{ __html: htmlFallback }} />;
}
