<?php

define('HWN_VERSION', '0.3.0');
define('THEME_URI', get_template_directory());

add_action( 'after_setup_theme', function() {
    add_theme_support('menus');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
});


/* Modules */
require_once( THEME_URI . '/inc/menus.php' );