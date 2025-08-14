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
Text Domain: hwn
*/

const MU_PLUGIN_PATH = __DIR__;
const AV02_EXTENSIONS_MENUS_ENABLED = true;
const AV02_EXTENSIONS_POSTS_ENABLED = true;
const AV02_EXTENSIONS_ROUTING_ENABLED = true;

/* Extensions */
if( AV02_EXTENSIONS_MENUS_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-menus/av02-extensions-menus.php';
}

if( AV02_EXTENSIONS_POSTS_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-posts/av02-extensions-posts.php';
}

if( AV02_EXTENSIONS_ROUTING_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-routing/av02-extensions-routing.php';
}
