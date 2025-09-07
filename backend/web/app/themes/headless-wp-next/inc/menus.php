<?php


/*
 * Register the menus
 *
*/
function hwn_register_menus () {
    $menus_api_enabled = \AV02\Settings\Av02Settings::get_option('api_menu_enabled');

    if( !$menus_api_enabled )
        return;

    $locations = \AV02\Settings\Av02Settings::get_option('api_menu_locations', []);

    if ($locations) {
        register_nav_menus(array_combine($locations, $locations));
    }
}
add_action( 'after_setup_theme', 'hwn_register_menus' );