<?php
/**
 * Plugin Name: WP Headless - REST Extensions
 * Description: Extra REST fields, route resolver, preview, and CORS for headless setup.
 * Author: alexvice02
 * Version: 0.4.1
 */

namespace AV02\Extensions\Posts;

use AV02\Settings\Av02Settings;

add_action('rest_api_init', function () {
    $included_data = Av02Settings::get_option('api_posts_include');

    if( !$included_data || !is_array($included_data) ){
        return;
    }

    foreach (['post', 'page'] as $type) {
        if( in_array('featured_image', $included_data) ) {
            register_rest_field($type, 'featured_image', [
                'get_callback' => function (array $obj) {
                    return get_featured_image((int) $obj['id']);
                },
            ]);
        }

        if ( in_array('author_info', $included_data) ) {
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
        }

        if( in_array('categories', $included_data) ) {
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

        register_rest_field($type, 'g_blocks', [
            'get_callback' => function (array $obj) {
                $post_id = (int) ($obj['id'] ?? 0);
                if (!$post_id) {
                    return [];
                }

                $raw = (string) get_post_field('post_content', $post_id);

                $schema_version = 1;
                $cache_key = 'blocks_simplified:' . $schema_version . ':' . $post_id . ':' . md5($raw);
                $cached = wp_cache_get($cache_key, 'rest_blocks');
                if (is_array($cached)) {
                    return $cached;
                }

                $parsed = parse_blocks($raw);
                $mapped = av_map_blocks($parsed, 0);

                wp_cache_set($cache_key, $mapped, 'rest_blocks', 10 * MINUTE_IN_SECONDS);
                return $mapped;
            },
        ]);

    }
}, 100);

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

/**
 * Recursive mapping of Gutenberg blocks to structured format
 *
 * Output element format:
 * [
 *   'type'         => 'core/paragraph' | ...,
 *   'attrs'        => [...],
 *   'children'     => [...], // inner blocks (innerBlocks)
 *   'htmlFallback' => '<div>…</div>', // safe HTML
 * ]
 *
 * @param array $blocks
 * @param int $depth
 * @return array
 */
function av_map_blocks(array $blocks, int $depth = 0): array
{
    if ($depth > 10) {
        return [];
    }

    return array_values(array_map(function (array $b) use ($depth) {
        $type        = $b['blockName'] ?? null;
        $attrs       = is_array($b['attrs'] ?? null) ? $b['attrs'] : [];
        $innerHTML   = (string) ($b['innerHTML'] ?? '');
        $innerBlocks = is_array($b['innerBlocks'] ?? null) ? $b['innerBlocks'] : [];

        $htmlFallback = '';
        try {
            $html = render_block($b);
            $htmlFallback = is_string($html) ? $html : $innerHTML;
        } catch (\Throwable $e) {
            $htmlFallback = $innerHTML;
        }
        $htmlFallback = wp_kses_post($htmlFallback);

        $mapped = [
            'type'         => $type,
            'attrs'        => $attrs,
            'children'     => av_map_blocks($innerBlocks, $depth + 1),
            'htmlFallback' => $htmlFallback,
        ];

        switch ($type) {
            case 'core/paragraph':
                $mapped['text'] = trim(wp_strip_all_tags($innerHTML));
                break;

            case 'core/heading':
                $mapped['level'] = isset($attrs['level']) ? (int) $attrs['level'] : 2;
                $mapped['text']  = trim(wp_strip_all_tags($innerHTML));
                $mapped['anchor'] = isset($attrs['anchor']) ? (string) $attrs['anchor'] : null;
                break;

            case 'core/image':
                $imageData = av_map_core_image($attrs, $innerHTML);
                if (!empty($imageData)) {
                    $mapped = array_merge($mapped, $imageData);
                }
                break;

            case 'core/video':
                $mapped += [
                    'src'      => isset($attrs['src']) ? (string) $attrs['src'] : null,
                    'poster'   => isset($attrs['poster']) ? (string) $attrs['poster'] : null,
                    'tracks'   => isset($attrs['tracks']) && is_array($attrs['tracks']) ? $attrs['tracks'] : [],
                    'autoplay' => !empty($attrs['autoplay']),
                    'muted'    => !empty($attrs['muted']),
                    'loop'     => !empty($attrs['loop']),
                    'controls' => array_key_exists('controls', $attrs) ? (bool) $attrs['controls'] : true,
                ];
                break;

            case 'core/embed':
                $mapped += [
                    'url'             => isset($attrs['url']) ? (string) $attrs['url'] : null,
                    'providerName'    => isset($attrs['providerNameSlug']) ? (string) $attrs['providerNameSlug'] : null,
                    'responsive'      => true,
                    'aspectRatio'     => isset($attrs['aspectRatio']) ? (string) $attrs['aspectRatio'] : null,
                ];
                break;

            case 'core/quote':
                $mapped['citation'] = isset($attrs['citation']) ? (string) $attrs['citation'] : null;
                break;

            case 'core/list':
                $mapped['ordered'] = !empty($attrs['ordered']);
                break;

            case 'core/group':
            case 'core/columns':
            case 'core/column':
            case 'core/buttons':
            case 'core/button':
                break;

            case 'core/block':
                $refId = isset($attrs['ref']) ? (int) $attrs['ref'] : 0;
                if ($refId > 0) {
                    $reusable = get_post($refId);
                    if ($reusable && $reusable->post_type === 'wp_block') {
                        $reusable_blocks = parse_blocks($reusable->post_content);
                        $mapped['children'] = av_map_blocks($reusable_blocks, $depth + 1);
                    }
                }
                break;

            default:
                break;
        }

        return $mapped;
    }, $blocks));
}

function av_map_core_image(array $attrs, string $innerHTML): array
{
    $out = [];

    $id = isset($attrs['id']) ? (int) $attrs['id'] : 0;
    if ($id > 0) {
        $url    = wp_get_attachment_image_url($id, 'full') ?: '';
        $meta   = wp_get_attachment_metadata($id) ?: [];
        $alt    = get_post_meta($id, '_wp_attachment_image_alt', true) ?: '';
        $srcset = wp_get_attachment_image_srcset($id, 'full') ?: '';
        $sizes  = wp_get_attachment_image_sizes($id, 'full') ?: '';

        $out += [
            'id'     => $id,
            'url'    => $url,
            'alt'    => $alt,
            'width'  => isset($meta['width']) ? (int) $meta['width'] : null,
            'height' => isset($meta['height']) ? (int) $meta['height'] : null,
            'srcset' => $srcset ?: null,
            'sizes'  => $sizes ?: null,
        ];
    } else {
        if (!empty($attrs['url'])) {
            $out['url'] = (string) $attrs['url'];
            $out['alt'] = isset($attrs['alt']) ? (string) $attrs['alt'] : '';
        }
    }

    $caption = '';

    if (!empty($attrs['caption'])) {
        $caption = (string) $attrs['caption'];
    }

    if ($caption === '' && $innerHTML !== '') {
        if (preg_match('/<figcaption\b[^>]*>(.*?)<\/figcaption>/is', $innerHTML, $m)) {
            $caption = trim($m[1]);
        }
    }

    if ($caption === '' && $id > 0) {
        $attachmentCaption = wp_get_attachment_caption($id);
        if (!empty($attachmentCaption)) {
            $caption = $attachmentCaption;
        }
    }

    if ($caption !== '') {
        $out['captionHtml'] = wp_kses_post($caption);
    }

    return $out;
}
