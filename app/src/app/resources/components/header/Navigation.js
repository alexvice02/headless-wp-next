import Link from "next/link";

export default async function Navigation({ menuLocation = 'header_menu' }) {
    const api = process.env.WP_API_URL;

    let params = new URLSearchParams({
        'location': menuLocation
    });

    const res = await fetch(`${api}/av02/v1/menus?${params.toString()}`, {
        next: { revalidate: 3600 },
        method: 'GET'
    });
    let nav = await res.json();

    return (
        <ul style={{display: 'flex', gap: '10px', listStyle: 'none', padding: "10px 20px", margin: 0, justifyContent: 'center'}}>
            {
                nav.map((item) => (<li key={item.id}><Link target={item.target} href={item.url}>{item.title}</Link></li>))
            }
        </ul>
    )
}