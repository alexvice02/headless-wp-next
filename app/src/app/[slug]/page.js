import { notFound } from 'next/navigation';
import Navigation from "@/app/resources/components/header/Navigation";

const api = process.env.WP_API_URL;

export async function generateStaticParams() {
    const pages = await fetch(`${api}/wp-json/wp/v2/pages`).then((res) => res.json());

    return pages.map((page) => ({
        slug: page.slug,
    }));
}


export default async function Page({ params }) {
    const res = await fetch(`${api}/wp-json/wp/v2/pages?slug=${params.slug}`, {
        next: { revalidate: 60 },
    });

    if (!res.ok) return notFound();

    let page = await res.json().then((page) => page[0]);

    function simplify(wpPage) {
        return {
            id:      wpPage.id,
            slug:    wpPage.slug,
            title:   wpPage.title.rendered,
            content: wpPage.content.rendered,
        };
    }

    page = simplify(page);

    return (
        <>
            <header>
                <Navigation />
            </header>
            <main>
                <div className={'container'} style={{ maxWidth: '1000px', margin: '0 auto' }}>
                    <h1>{ page.title }</h1>
                    <div dangerouslySetInnerHTML={{ __html: page.content }} />
                </div>
            </main>
        </>
    );
}
