<?php

namespace AV02\Extensions\Menus;

add_action('rest_api_init', function () {
    register_rest_route('av02/v1', '/menus', [
        'methods' => 'GET',
        'callback' => __NAMESPACE__ . '\\get_menus',
        'permission_callback' => '__return_true',
        'args' => [
            'location' => [
                'type' => 'string',
                'required' => false,
            ]
        ]
    ]);

    register_rest_route('av02/v1', '/menus/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => __NAMESPACE__ . '\\get_menu_items',
        'permission_callback' => '__return_true',
        'args' => [
            'id' => [
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                }
            ],
        ],
    ]);

    register_rest_route('av02/v1', '/menu-locations', [
        'methods' => 'GET',
        'callback' => __NAMESPACE__ . '\\get_menu_locations',
        'permission_callback' => '__return_true',
    ]);
});

function get_menus($request)
{
    if (!empty($request['location'])) {
        $locations = get_nav_menu_locations();
        if (isset($locations[$request['location']])) {
            $menu_id = $locations[$request['location']];
            $menu_items = wp_get_nav_menu_items($menu_id);

            if (empty($menu_items)) {
                return new \WP_Error('no_menu_items', 'No menu items found', ['status' => 404]);
            }

            $formatted_items = array_map(function ($item) {
                return [
                    'id' => $item->ID,
                    'title' => $item->title,
                    'url' => $item->url,
                    'target' => $item->target,
                    'parent' => $item->menu_item_parent,
                    'order' => $item->menu_order,
                ];
            }, $menu_items);

            return rest_ensure_response($formatted_items);
        }
        return new \WP_Error('location_not_found', 'Menu location not found', ['status' => 404]);
    }

    $menus = wp_get_nav_menus();

    if (empty($menus)) {
        return new \WP_Error('no_menus', 'No menus found', ['status' => 404]);
    }

    return rest_ensure_response($menus);
}

function get_menu_items($request)
{
    $menu_id = $request['id'];
    $menu_items = wp_get_nav_menu_items($menu_id);

    if (empty($menu_items)) {
        return new \WP_Error('no_menu_items', 'No menu items found', ['status' => 404]);
    }

    $formatted_items = array_map(function ($item) {
        return [
            'id' => $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
            'parent' => $item->menu_item_parent,
            'order' => $item->menu_order,
        ];
    }, $menu_items);

    return rest_ensure_response($formatted_items);
}

function get_menu_locations()
{
    $locations = get_nav_menu_locations();
    $registered_locations = get_registered_nav_menus();

    if (empty($locations) || empty($registered_locations)) {
        return new \WP_Error('no_locations', 'No menu locations found', ['status' => 404]);
    }

    $formatted_locations = [];
    foreach ($registered_locations as $location => $description) {
        $formatted_locations[$location] = [
            'description' => $description,
            'menu_id' => isset($locations[$location]) ? $locations[$location] : null,
        ];
    }

    return rest_ensure_response($formatted_locations);
}