/** @type {import('next').NextConfig} */
const nextConfig = {
    env: {
        WP_API_URL: process.env.WP_API_URL,
    }
}

export default nextConfig;
