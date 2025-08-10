<?php
/*
Plugin Name: WP Headless – API extensions
Plugin URI: https://github.com/alexvice02/headless-wp-next/tree/main/backend/web/app/mu-plugins
Description: Extends WordPress REST API to support default WP functionality and custom plugins
Version: 0.1.0
Author: alexvice02
Author URI: https://github.com/alexvice02
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wph
*/


/* API rewrites */

/**
 * Recursively replaces the domain prefix in all data strings.
 */
function av02_replace_wp_urls_recursive($data, string $from, string $to)
{
    if (is_string($data)) {
        return str_starts_with($data, $from) ? $to . substr($data, strlen($from)) : $data;
    }

    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $data[$k] = av02_replace_wp_urls_recursive($v, $from, $to);
        }
        return $data;
    }

    if (is_object($data)) {
        foreach ($data as $k => $v) {
            $data->$k = av02_replace_wp_urls_recursive($v, $from, $to);
        }
        return $data;
    }

    return $data;
}

/**
 * Global backend domain substitution to FRONT_SITEURL in REST responses.
 */
add_filter('rest_post_dispatch', function ($result, $server, $request) {
    if ($request->get_method() !== 'GET' || !($result instanceof WP_REST_Response)) {
        return $result;
    }

    $backend = home_url();
    $front = function_exists('env') ? (env('FRONT_SITEURL') ?: '') : '';
    if (!$front) {
        $front = getenv('FRONT_SITEURL') ?: '';
    }

    if (!$front || $front === $backend) {
        return $result;
    }

    $data = $result->get_data();
    $data = av02_replace_wp_urls_recursive($data, untrailingslashit($backend), untrailingslashit($front));
    $result->set_data($data);

    return $result;
}, 10, 3);


/* Extensions */
require_once __DIR__ . '/av02-extensions-menus/av02-extensions-menus.php';