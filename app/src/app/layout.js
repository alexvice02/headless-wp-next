import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import { Metadata } from 'next'

export async function generateMetadata() {
    const res = await fetch(`${process.env.WP_API_URL}/av02/v1/site-settings`);
    const settings = await res.json()

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

export default function RootLayout({ children }) {
    return (
        <html lang="en">
        <body className={`${geistSans.variable} ${geistMono.variable}`}>
        {children}
        </body>
        </html>
    );
}
