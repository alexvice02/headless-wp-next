import styles from "@/app/page.module.scss";
import Link from "next/link";

export default function SiteHeader () {


    return (
        <header className={styles.header}>
            <div className={styles.brand}>
                <Link href="/" className={styles.brandLink}>
                    <span className={styles.brandText}>Headless WP Next</span>
                </Link>
            </div>

            <nav className={styles.nav}>
                <Link href="#features" className={styles.navLink}>Features</Link>
                <Link href="#integrations" className={styles.navLink}>Integrations</Link>
                <Link href="#roadmap" className={styles.navLink}>Roadmap</Link>
                <Link href="#demo" className={styles.navLink}>Demo</Link>
                <Link
                    href="https://github.com/alexvice02/headless-wp-next"
                    target="_blank"
                    rel="noopener noreferrer"
                    className={styles.cta}
                >
                    GitHub
                </Link>
            </nav>
        </header>
    )
}