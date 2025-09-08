# Headless WP Next

A headless WordPress setup using Next.js as the frontend framework. This project decouples the WordPress backend
 with the frontend presentation layer, providing better performance, security, and developer experience.

## Setup

1. Configure environment variables (copy `.env.example` to `.env` in `/backend/` and `/app` directories)
2. Install dependencies with `npm install` (`/app/`)
3. Start WordPress backend using Docker - `docker compose up --build`
4. Run Next.js development server with `npm run dev`

## Stack

- **Frontend**: Next.js 15.4.6, React 19.1.0
- **Backend**: WordPress (Headless CMS)

## Extensions Docs

#### General - [Docs](https://github.com/alexvice02/headless-wp-next/blob/e07bf3dc302a79233da4b45b0f60c89fb5276a9e/docs/Extensions/av02-extensions-general.md)
#### Posts - [Docs](https://github.com/alexvice02/headless-wp-next/blob/e07bf3dc302a79233da4b45b0f60c89fb5276a9e/docs/Extensions/av02-extensions-posts.md)
#### Routing - [Docs](https://github.com/alexvice02/headless-wp-next/blob/e07bf3dc302a79233da4b45b0f60c89fb5276a9e/docs/Extensions/av02-extensions-routing.md)
