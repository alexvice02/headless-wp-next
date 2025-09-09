import styles from "@/app/page.module.scss";
import Link from "next/link";
import Navigation from "@/app/resources/components/header/Navigation";

export default async function SiteHeader() {
    const siteSettingsRes = await fetch(
        process.env.WP_API_URL + "/av02/v1/site-settings",
        {
            cache: "force-cache"
        }
    );

    const siteSettings = await siteSettingsRes.json();

    return (
        <header className={styles.header}>
            <div className={styles.brand}>
                <Link href="/" className={styles.brandLink}>
          <span className={styles.brandText}>
            {siteSettings?.name || "Headless WP Next"}
          </span>
                </Link>
            </div>

            <Navigation />
        </header>
    );
}