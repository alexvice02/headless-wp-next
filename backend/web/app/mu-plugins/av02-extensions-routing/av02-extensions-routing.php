<?php

namespace AV02\Extensions\Routing;


/**
 * Sends CORS headers for REST API requests and handles preflight requests.
 *
 * @return void
 */
function rest_send_cors_headers(): void
{
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');

    add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {
        $allowed = getenv('REST_ALLOWED_ORIGIN') ?: 'http://localhost:3000';
        $allowedOrigins = array_filter(array_map('trim', explode(',', $allowed)));

        $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $originToSend = '';

        if ($requestOrigin && in_array($requestOrigin, $allowedOrigins, true)) {
            $originToSend = $requestOrigin;
        } elseif (!empty($allowedOrigins)) {
            $originToSend = $allowedOrigins[0];
        }

        if ($originToSend !== '') {
            header('Access-Control-Allow-Origin: ' . $originToSend);
            header('Vary: Origin');
        }

        $allowCreds = filter_var(getenv('REST_ALLOW_CREDENTIALS') ?: 'false', FILTER_VALIDATE_BOOL);
        if ($allowCreds) {
            header('Access-Control-Allow-Credentials: true');
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

        $reqHeaders = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '';
        if ($reqHeaders !== '') {
            header('Access-Control-Allow-Headers: ' . $reqHeaders);
        } else {
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-WP-Nonce, Accept, X-Requested-With');
        }

        header('Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages, Link');
        header('Access-Control-Max-Age: 600');

        if (('OPTIONS' === ($_SERVER['REQUEST_METHOD'] ?? 'GET'))) {
            status_header(204);
            header('Content-Length: 0');
            return true;
        }

        return $served;
    }, 10, 4);
}
add_action('rest_api_init', __NAMESPACE__ . '\rest_send_cors_headers', 15);


/**
 * Redirects non-admin and non-AJAX requests to a Next.js application.
 *
 * @return void
 */
function redirect_to_next_app(): void
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

    $bypassList = [
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

    foreach ($bypassList as $bypass) {
        if (stripos($reqUri, $bypass) === 0) {
            return;
        }
    }

    $nextAppUrl = getenv('NEXT_APP_URL') ?: 'http://localhost:3000';

    $status = (int) (getenv('NEXT_REDIRECT_STATUS') ?: 307);
    wp_safe_redirect(rtrim($nextAppUrl, '/') . $reqUri, $status);
    exit;
}
add_action('template_redirect', __NAMESPACE__ . '\redirect_to_next_app');


/**
 * Sets the URL prefix for REST API routes.
 *
 * @return string
 */
function set_rest_url_prefix(): string
{
    return getenv('REST_API_PREFIX') ?: 'api';
}
add_filter('rest_url_prefix', __NAMESPACE__ . '\set_rest_url_prefix');