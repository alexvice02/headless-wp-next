<?php
/*
Plugin Name: WP Headless
Plugin URI: https://github.com/alexvice02/headless-wp-next/
Version: 0.3.0
Author: alexvice02
Author URI: https://github.com/alexvice02
Text Domain: hwn
*/

use \AV02\Settings\Av02Settings;

const MU_PLUGIN_PATH = __DIR__;

require_once MU_PLUGIN_PATH . '/av02-settings/av02-settings.php';

define("AV02_EXTENSIONS_MENUS_ENABLED", class_exists(Av02Settings::class) ? (bool) Av02Settings::get_option('api_menu_enabled', 0) : false);
define("AV02_EXTENSIONS_POSTS_ENABLED", true);
define("AV02_EXTENSIONS_ROUTING_ENABLED", true);

if( AV02_EXTENSIONS_MENUS_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-menus/av02-extensions-menus.php';
}

if( AV02_EXTENSIONS_POSTS_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-posts/av02-extensions-posts.php';
}

if( AV02_EXTENSIONS_ROUTING_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-routing/av02-extensions-routing.php';
}
