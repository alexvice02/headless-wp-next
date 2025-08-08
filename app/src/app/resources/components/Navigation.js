import process from "next/dist/build/webpack/loaders/resolve-url-loader/lib/postcss";
import Link from "next/link";

export default async function Navigation() {
    const api = process.env.WP_API_URL;
    const res = await fetch(`${api}/wp-json/wp/v2/pages`, {
        next: { revalidate: 60 },
    });
    let pages = await res.json();

    return (
        <ul style={{display: 'flex', gap: '10px', listStyle: 'none', padding: "10px 20px", margin: 0, justifyContent: 'center'}}>
            {
                pages.map((p) => (<li key={p.id}><Link href={`/${p.slug}`}>{p.title.rendered}</Link></li>))
            }
        </ul>
    )
}