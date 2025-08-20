<?php
/*
Plugin Name: WP Headless
Plugin URI: https://github.com/alexvice02/headless-wp-next/
Version: 0.2.2
Author: alexvice02
Author URI: https://github.com/alexvice02
Text Domain: hwn
*/

const MU_PLUGIN_PATH = __DIR__;
const AV02_EXTENSIONS_MENUS_ENABLED = true;
const AV02_EXTENSIONS_POSTS_ENABLED = true;
const AV02_EXTENSIONS_ROUTING_ENABLED = true;


require MU_PLUGIN_PATH . '/av02-settings/av02-settings.php';


if( AV02_EXTENSIONS_MENUS_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-menus/av02-extensions-menus.php';
}

if( AV02_EXTENSIONS_POSTS_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-posts/av02-extensions-posts.php';
}

if( AV02_EXTENSIONS_ROUTING_ENABLED ) {
    require_once MU_PLUGIN_PATH . '/av02-extensions-routing/av02-extensions-routing.php';
}
