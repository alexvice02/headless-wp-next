import Link from "next/link";
import styles from "./page.module.scss";

export const metadata = {
    title: "Home • Headless WP Next"
};

export default function Home() {
    return (
        <div className={styles.page}>
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
                    <a
                        href="https://github.com/alexvice02/headless-wp-next"
                        target="_blank"
                        rel="noopener noreferrer"
                        className={styles.cta}
                    >
                        GitHub
                    </a>
                </nav>
            </header>

            <main className={styles.main}>
                <section className={styles.hero}>
                    <h1 className={styles.title}>Headless WordPress + Next.js Starter</h1>
                    <p className={styles.subtitle}>
                        A modern boilerplate for building fast, scalable websites with WordPress as a headless CMS and Next.js as the frontend.
                    </p>
                    <div className={styles.heroActions}>
                        <Link href="#features" className={styles.primaryBtn}>Learn More</Link>
                        <a
                            href="https://github.com/alexvice02/headless-wp-next"
                            target="_blank"
                            rel="noopener noreferrer"
                            className={styles.secondaryBtn}
                        >
                            Documentation
                        </a>
                    </div>
                </section>

                <section id="features" className={styles.section}>
                    <h2 className={styles.sectionTitle}>Core Features</h2>
                    <ul className={styles.cards}>
                        <li className={styles.card}>
                            <h3>Custom REST API Routing</h3>
                            <p>Extended WordPress REST API with custom endpoints for more control and cleaner data structures.</p>
                        </li>
                        <li className={styles.card}>
                            <h3>Custom Fields Support</h3>
                            <p>Seamless integration with Advanced Custom Fields (ACF) and similar plugins for structured content.</p>
                        </li>
                        <li className={styles.card}>
                            <h3>Gutenberg Blocks</h3>
                            <p>Fetch and render Gutenberg block data on the frontend for fully customizable layouts.</p>
                        </li>
                        <li className={styles.card}>
                            <h3>Image Optimization</h3>
                            <p>Next.js image handling for fast and efficient media delivery.</p>
                        </li>
                    </ul>
                </section>

                <section id="integrations" className={styles.sectionAlt}>
                    <h2 className={styles.sectionTitle}>Planned Integrations</h2>
                    <ul className={styles.benefitsList}>
                        <li><strong>Yoast SEO.</strong> Pull SEO metadata directly from WordPress.</li>
                        <li><strong>Contact Form 7.</strong> API-based form handling without page reloads.</li>
                        <li><strong>WPML.</strong> Multilingual support both in the API and frontend.</li>
                        <li><strong>WooCommerce.</strong> Headless eCommerce with cart and checkout support.</li>
                    </ul>
                </section>

                <section id="roadmap" className={styles.section}>
                    <h2 className={styles.sectionTitle}>Roadmap</h2>
                    <ol className={styles.roadmap}>
                        <li>
                            <span className={styles.badgeDone}>Done</span>
                            Basic homepage with navigation and section structure.
                        </li>
                        <li>
                            <span className={styles.badgeNext}>In Progress</span>
                            Custom REST API routing and ACF integration.
                        </li>
                        <li>
                            <span className={styles.badgePlanned}>Planned</span>
                            Gutenberg block rendering, WooCommerce & WPML support, public demo.
                        </li>
                    </ol>
                </section>

                <section id="demo" className={styles.sectionAlt}>
                    <h2 className={styles.sectionTitle}>Demo Showcase</h2>
                    <ul className={styles.todo}>
                        <li>[ ] Example pages with custom fields and REST API.</li>
                        <li>[ ] Gutenberg blocks rendered on the frontend.</li>
                    </ul>
                </section>
            </main>

            <footer className={styles.footer}>
                <p>© {new Date().getFullYear()} Headless WP Next</p>
            </footer>
        </div>
    );
}
