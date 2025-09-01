export default function Paragraph({text = "", attrs = {}}) {
    return <p className={attrs.className || ""}>{text}</p>;
}
