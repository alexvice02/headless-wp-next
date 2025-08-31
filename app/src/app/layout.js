import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import { Metadata } from 'next'

export async function generateMetadata() {
    const res = await fetch(`${process.env.WP_API_URL}/av02/v1/site-settings`);
    const settings = await res.json();

    return {
        title: settings?.name || '',
        description: settings?.description || '',
        icons: {
            icon: settings?.favicon || '',
        },
    }
}

const geistSans = Geist({
    variable: "--font-geist-sans",
    subsets: ["latin"],
});

const geistMono = Geist_Mono({
    variable: "--font-geist-mono",
    subsets: ["latin"],
});

export default async function RootLayout({ children }) {
    const res = await fetch(`${process.env.WP_API_URL}/av02/v1/theme-settings`, {
        next: { revalidate: 60 },
    })
    const colors = await res.json()

    const cssVars = colors.palette.default
        .map((color) => `--color-${color.slug}: ${color.color};`)
        .join("\n")

    return (
        <html lang="en">
        <head>
            <style>{`:root { ${cssVars} }`}</style>
        </head>
        <body className={`${geistSans.variable} ${geistMono.variable}`}>
        {children}
        </body>
        </html>
    );
}
