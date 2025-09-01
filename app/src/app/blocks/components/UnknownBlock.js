export default function UnknownBlock({htmlFallback, attrs = {}}) {
    if (!htmlFallback) return null;
    return <div className={attrs.className || ""} dangerouslySetInnerHTML={{__html: htmlFallback}}/>;
}
