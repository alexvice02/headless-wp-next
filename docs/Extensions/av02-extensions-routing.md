# `av02-extensions-routing` Extension

## 🎯 Purpose
- Handles CORS headers for REST API requests with stricter custom logic.
- Redirects all non-admin, non-AJAX, and non-REST requests to a Next.js frontend.
- Allows configuring the REST API prefix (default: /api).
- Provides a mechanism to filter and disable specific REST endpoints for security and optimization.

## ⚙️ Configuration

### CORS:
- `REST_ALLOWED_ORIGIN` → comma-separated list of allowed origins (default: http://localhost:3000).
- `REST_ALLOW_CREDENTIALS` → `true|false` (default: false).

### Next.js redirect:
- `NEXT_APP_URL` → URL of the Next.js app (default: http://localhost:3000).
- `NEXT_REDIRECT_STATUS` → HTTP status code for redirect (default: 307).

### REST API prefix:
- REST_API_PREFIX → overrides /api.

### Endpoints visibility:

In `Headless WP Next --> API --> WordPress API --> Enabled Endpoints`, you can enable/disable specific REST endpoints.