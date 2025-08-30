<?php

add_action('rest_api_init', function () {
    register_rest_route('av02/v1', '/site-settings', [
        'methods' => 'GET',
        'callback' => function () {
            $favicon_id = get_option('site_icon');
            $favicon_url = $favicon_id ? wp_get_attachment_url($favicon_id) : null;

            return [
                'name'        => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'url'         => get_bloginfo('url'),
                'language'    => get_bloginfo('language'),
                'custom_logo' => wp_get_attachment_url(get_theme_mod('custom_logo')),
                'favicon'     => $favicon_url,
            ];
        },
        'permission_callback' => '__return_true',
    ]);
});