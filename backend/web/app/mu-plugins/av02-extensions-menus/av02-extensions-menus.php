<?php
/*
 * Plugin Name: WP Headless - REST API Menus
 * Plugin URI: https://github.com/alexvice02/headless-wp-next/tree/main/backend/web/app/mu-plugins/av02-integration-menus
 * Description: Extends WordPress REST API to support menus
 * Version: 0.3.0
 * Author: alexvice02
 * Author URI: https://github.com/alexvice02
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: wph
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function av02_integration_menus_init() {
    require_once __DIR__ . '/inc/menus.php';
}

av02_integration_menus_init();