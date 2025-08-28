export default function ImageBlock({ url, alt = "", width, height, srcset, sizes, captionHtml, attrs = {} }) {
  if (!url) return null;
  return (
    <figure className={attrs.className || ""}>
      <img
        src={url}
        alt={alt || ""}
        width={width || undefined}
        height={height || undefined}
        srcSet={srcset || undefined}
        sizes={sizes || undefined}
        loading="lazy"
      />
      {captionHtml ? <figcaption dangerouslySetInnerHTML={{ __html: captionHtml }} /> : null}
    </figure>
  );
}
