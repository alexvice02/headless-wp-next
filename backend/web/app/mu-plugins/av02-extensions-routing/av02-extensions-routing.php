<?php
namespace AV02\Extensions\Routing;

const DEFAULT_REST_ALLOWED_ORIGINS = 'http://localhost:3000';
const DEFAULT_NEXT_APP_URL         = 'http://localhost:3000';
const DEFAULT_NEXT_REDIRECT_STATUS = 307;
const DEFAULT_REST_PREFIX          = 'api';

const CORS_ALLOWED_METHODS         = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
const CORS_DEFAULT_ALLOW_HEADERS   = 'Content-Type, Authorization, X-WP-Nonce, Accept, X-Requested-With';
const CORS_EXPOSE_HEADERS          = 'X-WP-Total, X-WP-TotalPages, Link';
const CORS_MAX_AGE                 = 600;

const BYPASS_PATHS = [
    '/wp-login.php',
    '/wp-cron.php',
    '/robots.txt',
    '/favicon.ico',
    '/wp-admin',
    '/wp-content/',
    '/wp-includes/',
    '/wp-sitemap.xml',
    '/sitemap.xml',
    '/sitemap_index.xml',
    '/ads.txt',
    '/apple-touch-icon',
];

const REST_ENDPOINT_MAP = [
    'posts'      => '/wp/v2/posts',
    'pages'      => '/wp/v2/pages',
    'media'      => '/wp/v2/media',
    'categories' => '/wp/v2/categories',
    'tags'       => '/wp/v2/tags',
    'comments'   => '/wp/v2/comments',
    'users'      => '/wp/v2/users',
    'settings'   => '/wp/v2/settings',
    'themes'     => '/wp/v2/themes',
    'search'     => '/wp/v2/search',
    'blocks'     => '/wp/v2/blocks',
    'oembed'     => '/oembed/1.0',
];

/**
 * Safe env getter with default.
 */
function env_or(string $key, string $default): string
{
    $val = getenv($key);
    return ($val !== false && $val !== '') ? (string) $val : $default;
}

/**
 * Parse REST_ALLOWED_ORIGIN env to an array of allowed origins.
 */
function allowed_origins_from_env(): array
{
    $allowed = env_or('REST_ALLOWED_ORIGIN', DEFAULT_REST_ALLOWED_ORIGINS);
    return array_values(array_filter(array_map('trim', explode(',', $allowed))));
}

/**
 * Choose which Origin header to send back based on request and allowed list.
 */
function pick_cors_origin_to_send(array $allowedOrigins, string $requestOrigin): string
{
    if ($requestOrigin !== '' && in_array($requestOrigin, $allowedOrigins, true)) {
        return $requestOrigin;
    }
    return $allowedOrigins[0] ?? '';
}

/**
 * Sends CORS headers for REST API requests and handles preflight requests.
 */
function register_rest_cors_handler(): void
{
    // Remove WP core default CORS to replace with stricter custom logic.
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');

    add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {
        $allowedOrigins = allowed_origins_from_env();
        $requestOrigin  = $_SERVER['HTTP_ORIGIN'] ?? '';
        $originToSend   = pick_cors_origin_to_send($allowedOrigins, $requestOrigin);

        if ($originToSend !== '') {
            header('Access-Control-Allow-Origin: ' . $originToSend);
            header('Vary: Origin');
        }

        $allowCreds = filter_var(env_or('REST_ALLOW_CREDENTIALS', 'false'), FILTER_VALIDATE_BOOL);
        if ($allowCreds) {
            header('Access-Control-Allow-Credentials: true');
        }

        header('Access-Control-Allow-Methods: ' . CORS_ALLOWED_METHODS);

        $reqHeaders = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '';
        header('Access-Control-Allow-Headers: ' . ($reqHeaders !== '' ? $reqHeaders : CORS_DEFAULT_ALLOW_HEADERS));

        header('Access-Control-Expose-Headers: ' . CORS_EXPOSE_HEADERS);
        header('Access-Control-Max-Age: ' . CORS_MAX_AGE);

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS') {
            status_header(204);
            header('Content-Length: 0');
            return true; // Short-circuit actual serving for preflight.
        }

        return $served;
    }, 10, 4);
}
add_action('rest_api_init', __NAMESPACE__ . '\register_rest_cors_handler', 15);

/**
 * Check whether the URI should bypass redirect to the Next.js app.
 */
function should_bypass_redirect(string $uri): bool
{
    foreach (BYPASS_PATHS as $prefix) {
        if (stripos($uri, $prefix) === 0) {
            return true;
        }
    }
    return false;
}

/**
 * Redirects non-admin and non-AJAX requests to a Next.js application.
 */
function redirect_non_backend_to_next_app(): void
{
    if (
        is_admin()
        || wp_doing_ajax()
        || (defined('REST_REQUEST') && REST_REQUEST)
        || (defined('DOING_CRON') && DOING_CRON)
        || (defined('WP_CLI') && WP_CLI)
    ) {
        return;
    }

    $reqUri = $_SERVER['REQUEST_URI'] ?? '/';
    if (should_bypass_redirect($reqUri)) {
        return;
    }

    $nextAppUrl = env_or('NEXT_APP_URL', DEFAULT_NEXT_APP_URL);
    $status     = (int) env_or('NEXT_REDIRECT_STATUS', (string) DEFAULT_NEXT_REDIRECT_STATUS);

    wp_safe_redirect(rtrim($nextAppUrl, '/') . $reqUri, $status);
    exit;
}
add_action('template_redirect', __NAMESPACE__ . '\redirect_non_backend_to_next_app');

/**
 * Sets the URL prefix for REST API routes based on env.
 */
function rest_url_prefix_from_env(): string
{
    return env_or('REST_API_PREFIX', DEFAULT_REST_PREFIX);
}
add_filter('rest_url_prefix', __NAMESPACE__ . '\rest_url_prefix_from_env');

/**
 * Filters visible REST endpoints according to settings.
 *
 * @param array $endpoints
 * @return array
 */
function filter_enabled_rest_endpoints(array $endpoints): array
{
    $options = get_option('av02_options');
    $enabled = $options['api_enabled_endpoints'] ?? [];

    foreach (REST_ENDPOINT_MAP as $key => $pattern) {
        if (!in_array($key, $enabled, true)) {
            foreach ($endpoints as $route => $_handlers) {
                if (strpos($route, $pattern) === 0) {
                    unset($endpoints[$route]);
                }
            }
        }
    }
    return $endpoints;
}
add_filter('rest_endpoints', __NAMESPACE__ . '\filter_enabled_rest_endpoints');