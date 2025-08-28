export default function VideoBlock({ src, poster, tracks = [], autoplay, muted, loop, controls = true, attrs = {} }) {
  if (!src) return null;
  return (
    <video
      className={attrs.className || ""}
      src={src || undefined}
      poster={poster || undefined}
      autoPlay={!!autoplay}
      muted={!!muted}
      loop={!!loop}
      controls={controls !== false}
      playsInline
    >
      {Array.isArray(tracks) ? tracks.map((t, i) => <track key={i} {...t} />) : null}
    </video>
  );
}
