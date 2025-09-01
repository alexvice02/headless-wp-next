import { notFound } from 'next/navigation';
import SiteHeader from "@/app/resources/components/header/SiteHeader";
import styles from "@/app/page.module.scss";
import BlockRenderer from "@/app/blocks/BlockRenderer";

export async function generateStaticParams() {
    const api = process.env.WP_API_URL;
    const pages = await fetch(`${api}/wp/v2/pages`).then((res) => res.json());

    return pages.map((page) => ({
        slug: page.slug,
    }));
}

export default async function Page({ params }) {
    const api = process.env.WP_API_URL;
    const res = await fetch(`${api}/wp/v2/pages?slug=${params.slug}`, {
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
            blocks:  wpPage.g_blocks
        };
    }

    page = simplify(page);

    return (
        <>
            <SiteHeader></SiteHeader>
            <main className={styles.main}>
                <div className={styles.container}>
                    <h1 className={styles.pageTitle}>{ page.title }</h1>
                    <div className={styles.pageContent}>
                        <BlockRenderer blocks={page.blocks} />
                    </div>
                </div>
            </main>
        </>
    );
}
