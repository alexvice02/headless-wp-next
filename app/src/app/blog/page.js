import styles from "@/app/page.module.scss";
import SiteHeader from "@/app/resources/components/header/SiteHeader";

export default function BlogPage() {
    return (
        <div className={styles.page}>
            <SiteHeader />
            <main className={styles.main}>
                <section className={styles.section}>
                    <h1>Blog page</h1>
                </section>
            </main>
        </div>
    )
}