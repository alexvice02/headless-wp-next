<?php
/**
 * Plugin Name: WP Headless - REST Extensions
 * Description: Extra REST fields, route resolver, preview, and CORS for headless setup.
 * Author: alexvice02
 * Version: 0.2.2
 */

namespace AV02\Extensions\Posts;

add_action('rest_api_init', function () {
    foreach (['post', 'page'] as $type) {
        register_rest_field($type, 'featured_image', [
            'get_callback' => function (array $obj) {
                return get_featured_image((int) $obj['id']);
            },
        ]);
        register_rest_field($type, 'author_info', [
            'get_callback' => function (array $obj) {
                $post = get_post((int) $obj['id']);
                if (!$post) return null;
                return [
                    'id'    => (int) $post->post_author,
                    'name'  => get_the_author_meta('display_name', $post->post_author),
                    'avatar'=> get_avatar_url($post->post_author, ['size' => 96]),
                ];
            },
        ]);
        register_rest_field($type, 'terms', [
            'get_callback' => function (array $obj) use ($type) {
                $id = (int) $obj['id'];
                $data = [];
                foreach (get_object_taxonomies($type, 'objects') as $tax) {
                    if (!$tax->public) continue;
                    $terms = get_the_terms($id, $tax->name) ?: [];
                    $data[$tax->name] = array_map(fn($t) => [
                        'id'   => (int) $t->term_id,
                        'slug' => $t->slug,
                        'name' => $t->name,
                    ], is_array($terms) ? $terms : []);
                }
                return $data;
            },
        ]);
    }
});

/**
 * Return featured image payload with common sizes and alt text.
 */
function get_featured_image(int $postId): ?array
{
    $thumbId = get_post_thumbnail_id($postId);
    if (!$thumbId) return null;

    $sizes = ['full', 'large', 'medium', 'thumbnail'];
    $out = [
        'id'  => (int) $thumbId,
        'alt' => get_post_meta($thumbId, '_wp_attachment_image_alt', true) ?: '',
        'src' => [],
    ];
    foreach ($sizes as $s) {
        $src = wp_get_attachment_image_src($thumbId, $s);
        if ($src) {
            $out['src'][$s] = [
                'url'    => $src[0],
                'width'  => (int) $src[1],
                'height' => (int) $src[2],
            ];
        }
    }
    return $out;
}