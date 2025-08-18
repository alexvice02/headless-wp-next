<?php
/**
 * Plugin Name: Headless WP Next
 * Description: Headless WordPress settings management
 * Version: 0.2.1
 * Author: alexvice02
 */

if (!defined('ABSPATH')) exit;

class Av02Settings
{
    private string $option_key = 'av02_options';
    private array $sections = [];

    public function __construct()
    {
        $this->sections = [
            'general' => [
                'title' => 'General',
                'icon' => 'dashicons-admin-generic',
                'fields' => [
                    ['id' => 'text_field', 'label' => 'Text field', 'type' => 'text'],
                    ['id' => 'checkbox_field', 'label' => 'Checkbox', 'type' => 'checkbox'],
                ],
                'blocks' => [
                    [
                        'title' => 'Basics',
                        'icon' => '🧩',
                        'fields' => [
                            ['id' => 'general_basic_note', 'label' => 'Note', 'type' => 'text'],
                        ],
                    ],
                ],
            ],
            'advanced' => [
                'title' => 'Advanced',
                'icon' => 'dashicons-admin-tools',
                'fields' => [
                    ['id' => 'select_field', 'label' => 'Select', 'type' => 'select', 'options' => [
                        'one' => 'Option 1',
                        'two' => 'Option 2',
                        'three' => 'Option 3'
                    ]],
                    ['id' => 'repeater_field', 'label' => 'Repeater', 'type' => 'repeater'],
                ]
            ],
            'api' => [
                'title' => 'API',
                'icon' => 'dashicons-rest-api',
                'tabs' => [
                    'posts' => [
                        'title' => 'Posts',
                        'icon' => 'dashicons-admin-post',
                        'fields' => [
                            ['id' => 'api_posts_include_meta', 'label' => 'Include meta', 'type' => 'checkbox']
                        ],
                        'blocks' => [
                            [
                                'title' => 'Visibility',
                                'icon' => '',
                                'fields' => [
                                    ['id' => 'api_posts_show_private', 'label' => 'Show private', 'type' => 'checkbox'],
                                ],
                            ],
                            [
                                'title' => 'Additional',
                                'icon' => '',
                                'fields' => [
                                    ['id' => 'api_posts_per_page', 'label' => 'Per page', 'type' => 'text'],
                                ],
                            ],
                        ],
                    ],
                    'menus' => [
                        'title' => 'Menus',
                        'icon' => 'dashicons-menu',
                        'fields' => [
                            ['id' => 'api_menu_enabled', 'label' => 'Enable Menus API', 'type' => 'checkbox']
                        ],
                    ]
                ],
            ],
            'integrations' => [
                'title' => 'Integrations',
                'icon' => 'dashicons-admin-plugins',
                'fields' => [

                ]
            ]
        ];

        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function add_menu(): void
    {
        add_menu_page(
                'Headless WP Next',
                'Headless WP Next',
                'manage_options',
                'headless-wp-next',
                [$this, 'settings_page'],
                'dashicons-admin-generic',
                25
        );
    }

    public function register_settings(): void
    {
        register_setting(
                $this->option_key,
                $this->option_key,
                [
                        'type'              => 'array',
                        'default'           => [],
                        'sanitize_callback' => [$this, 'sanitize_options'],
                ]
        );
    }

    public function enqueue_scripts(string $hook): void
    {
        if ($hook !== 'toplevel_page_headless-wp-next') return;
        wp_enqueue_script('headless-wp-next-repeater', plugin_dir_url(__FILE__) . 'admin/repeater.js', ['jquery'], null, true);
        wp_enqueue_style('headless-wp-next-style', plugin_dir_url(__FILE__) . 'admin/style.css');
    }

    public function sanitize_options($input): array
    {
        if (!is_array($input)) {
            $input = [];
        }

        $output = [];
        $flat_fields = $this->collect_all_fields($this->sections);

        foreach ($flat_fields as $field) {
            if (empty($field['id'])) {
                continue;
            }
            $id = $field['id'];

            if (!array_key_exists($id, $input)) {
                if (($field['type'] ?? '') === 'checkbox') {
                    $output[$id] = 0;
                }
                continue;
            }

            $val = $input[$id];
            switch ($field['type']) {
                case 'checkbox':
                    $output[$id] = $val ? 1 : 0;
                    break;
                case 'select':
                case 'text':
                    $output[$id] = is_string($val) ? sanitize_text_field($val) : '';
                    break;
                case 'repeater':
                    $items = is_array($val) ? array_map('sanitize_text_field', $val) : [];
                    $items = array_values(array_filter($items, static fn($v) => $v !== ''));
                    $output[$id] = $items;
                    break;
                default:
                    $output[$id] = is_scalar($val) ? sanitize_text_field((string) $val) : '';
            }
        }

        return $output;
    }

    private function collect_all_fields(array $sections): array
    {
        $out = [];
        foreach ($sections as $section) {
            if (!empty($section['fields'])) {
                $out = array_merge($out, $section['fields']);
            }
            if (!empty($section['blocks']) && is_array($section['blocks'])) {
                foreach ($section['blocks'] as $block) {
                    if (!empty($block['fields'])) {
                        $out = array_merge($out, $block['fields']);
                    }
                }
            }
            if (!empty($section['tabs']) && is_array($section['tabs'])) {
                foreach ($section['tabs'] as $tab) {
                    if (!empty($tab['fields'])) {
                        $out = array_merge($out, $tab['fields']);
                    }
                    if (!empty($tab['blocks']) && is_array($tab['blocks'])) {
                        foreach ($tab['blocks'] as $block) {
                            if (!empty($block['fields'])) {
                                $out = array_merge($out, $block['fields']);
                            }
                        }
                    }
                }
            }
        }
        return $out;
    }

    private function get_options(): array
    {
        return get_option($this->option_key, []);
    }

    private function render_field(array $field): void
    {
        $options = $this->get_options();
        $id = $field['id'];
        $val = $options[$id] ?? '';

        switch ($field['type']) {
            case 'text':
                echo "<input type='text' name='" . esc_attr("{$this->option_key}[{$id}]") . "' value='" . esc_attr($val) . "' class='regular-text' />";
                break;

            case 'checkbox':
                $checked = !empty($val) ? 'checked' : '';
                echo "<label><input type='checkbox' name='" . esc_attr("{$this->option_key}[{$id}]") . "' value='1' $checked> " . esc_html($field['label']) . "</label>";
                break;

            case 'select':
                echo "<select name='" . esc_attr("{$this->option_key}[{$id}]") . "'>";
                foreach (($field['options'] ?? []) as $k => $label) {
                    $selected = selected($val, $k, false);
                    echo "<option value='" . esc_attr($k) . "' $selected>" . esc_html($label) . "</option>";
                }
                echo "</select>";
                break;

            case 'repeater':
                $items = is_array($val) ? $val : [];
                $data_name = "{$this->option_key}[{$id}][]";
                echo "<div class='hwn-repeater-wrapper' data-name='" . esc_attr($data_name) . "'>";
                if (!empty($items)) {
                    foreach ($items as $text) {
                        echo "<div class='repeater-item'>
                                <input type='text' name='" . esc_attr("{$this->option_key}[{$id}][]") . "' value='" . esc_attr($text) . "' />
                                <button type='button' class='button remove-item'>✕</button>
                              </div>";
                    }
                }
                echo "</div>";
                echo "<button type='button' class='button add-item' data-target='" . esc_attr($id) . "'>+ Add</button>";
                break;
        }
    }

    private function render_icon_html(?string $icon): string
    {
        $icon = is_string($icon ?? null) ? trim($icon) : '';
        if ($icon === '') {
            return '';
        }

        if (filter_var($icon, FILTER_VALIDATE_URL)) {
            return "<img src='" . esc_url($icon) . "' class='hwn-icon-img' alt='' />";
        }

        if (str_starts_with($icon, 'dashicons-')) {
            return "<span class='dashicons " . esc_attr($icon) . "' aria-hidden='true'></span>";
        }

        return "<span class='hwn-icon-emoji'>" . esc_html($icon) . "</span>";
    }

    private function render_fields_group(array $fields): void
    {
        foreach ($fields as $field) {
            echo "<div class='hwn-field'>";
            if (($field['type'] ?? '') !== 'checkbox') {
                echo "<label class='hwn-label'>" . esc_html($field['label'] ?? '') . "</label>";
            }
            $this->render_field($field);
            echo "</div>";
        }
    }

    private function render_blocks(?array $blocks): void
    {
        if (empty($blocks) || !is_array($blocks)) {
            return;
        }

        foreach ($blocks as $block) {
            $title = $block['title'] ?? '';
            $icon  = $block['icon'] ?? '';
            echo "<div class='hwn-block'>";
            if ($title !== '' || $icon !== '') {
                $icon_html = $this->render_icon_html($icon);
                echo "<h3 class='hwn-block-title'>" . $icon_html . "<span>" . esc_html($title) . "</span></h3>";
            }
            echo "<div class='hwn-block-body'>";
            $this->render_fields_group($block['fields'] ?? []);
            echo "</div>";
            echo "</div>";
        }
    }

    public function settings_page(): void
    {
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : array_key_first($this->sections);
        if (!isset($this->sections[$active_tab])) {
            $active_tab = array_key_first($this->sections);
        }
        $section = $this->sections[$active_tab];
        ?>
        <div class="wrap hwn-admin">
            <h1>Settings</h1>

            <h2 class="nav-tab-wrapper">
                <?php
                foreach ($this->sections as $key => $sec) {
                    $active = ($active_tab === $key) ? 'nav-tab-active' : '';
                    $url = esc_url(add_query_arg(['page' => 'headless-wp-next', 'tab' => $key], admin_url('admin.php')));
                    $icon_html = $this->render_icon_html($sec['icon'] ?? '');
                    echo "<a href='{$url}' class='nav-tab {$active}'>" . $icon_html . "<span>" . esc_html($sec['title']) . "</span></a>";
                }
                ?>
            </h2>

            <form method="post" action="options.php" class="hwn-default-layout">
                <?php
                settings_fields($this->option_key);

                if (!empty($section['tabs']) && is_array($section['tabs'])) {
                    $sub_tabs = $section['tabs'];
                    $active_sub = isset($_GET['sub']) ? sanitize_key($_GET['sub']) : array_key_first($sub_tabs);
                    if (!isset($sub_tabs[$active_sub])) {
                        $active_sub = array_key_first($sub_tabs);
                    }

                    echo "<div class='hwn-vertical-layout'>";

                    echo "  <div class='hwn-vertical-nav'>";
                    echo "    <ul>";
                    foreach ($sub_tabs as $sub_key => $sub) {
                        $is_active = $active_sub === $sub_key ? 'active' : '';
                        $url = esc_url(add_query_arg([
                                'page' => 'headless-wp-next',
                                'tab'  => $active_tab,
                                'sub'  => $sub_key
                        ], admin_url('admin.php')));
                        $icon_html = $this->render_icon_html($sub['icon'] ?? '');
                        echo "<li class='{$is_active}'><a href='{$url}'>" . $icon_html . "<span>" . esc_html($sub['title']) . "</span></a></li>";
                    }
                    echo "    </ul>";
                    echo "  </div>";

                    $current_sub = $sub_tabs[$active_sub];
                    echo "  <div class='hwn-vertical-content'>";
                    $head_icon = $this->render_icon_html($section['icon'] ?? '');
                    $sub_icon  = $this->render_icon_html($current_sub['icon'] ?? '');
                    echo "    <h2 class='hwn-vertical-title'>" . "<span>" . esc_html($section['title']) . "</span> — " . "<span>" . esc_html($current_sub['title']) . "</span></h2>";

                    echo "    <div class='hwn-section'>";
                    $this->render_blocks($current_sub['blocks'] ?? []);
                    if (!empty($current_sub['fields'])) {
                        echo "<div class='hwn-block'>";
                        echo "  <div class='hwn-block-body'>";
                        $this->render_fields_group($current_sub['fields']);
                        echo "  </div>";
                        echo "</div>";
                    }
                    echo "    </div>";

                    submit_button();
                    echo "  </div>";
                    echo "</div>";
                } else {
                    echo "<div class='hwn-section'>";
                    $this->render_blocks($section['blocks'] ?? []);
                    if (!empty($section['fields'])) {
                        echo "<div class='hwn-block'>";
                        echo "  <div class='hwn-block-body'>";
                        $this->render_fields_group($section['fields']);
                        echo "  </div>";
                        echo "</div>";
                    }
                    echo "</div>";
                    submit_button();
                }
                ?>
            </form>
        </div>
        <?php
    }
}

new Av02Settings();