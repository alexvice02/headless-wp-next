<?php

define('HWN_VERSION', '0.4.1');
define('THEME_URI', get_template_directory());

add_action('after_setup_theme', function() {
    add_theme_support('menus');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');

    add_theme_support('post-formats', ['aside','gallery','link','image','quote','status','video','audio','chat']);
});


add_action('init', function () {
    add_post_type_support('post', 'post-formats');

     add_post_type_support('page', 'post-formats');
     register_taxonomy_for_object_type('post_format', 'page');
}, 11);


add_filter('block_editor_settings_all', function ($settings, $context) {
    $post_type = null;
    if (!empty($context->post)) {
        $post_type = get_post_type($context->post);
    } elseif (!empty($context->post_type)) {
        $post_type = $context->post_type;
    }

    if (!$post_type) {
        $post_type = 'post';
    }

    if (post_type_supports($post_type, 'post-formats')) {
        $formats = get_theme_support('post-formats');
        $slugs = (is_array($formats) && isset($formats[0]) && is_array($formats[0])) ? array_values($formats[0]) : [];

        if (!empty($slugs)) {
            if (!isset($settings['supports'])) {
                $settings['supports'] = [];
            }
            $settings['supports']['postFormats'] = true;
            $settings['allowedPostFormats'] = $slugs;
        }
    }

    return $settings;
}, 10, 2);



/* Modules */
require_once( THEME_URI . '/inc/menus.php' );