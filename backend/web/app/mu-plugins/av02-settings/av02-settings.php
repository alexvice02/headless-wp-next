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
                        'fields' => [
                                ['id' => 'text_field', 'label' => 'Text field', 'type' => 'text'],
                                ['id' => 'checkbox_field', 'label' => 'Checkbox', 'type' => 'checkbox'],
                        ]
                ],
                'advanced' => [
                        'title' => 'Advanced',
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
                        'tabs' => [
                                'posts' => [
                                        'title' => 'Posts',
                                        'fields' => [
                                                ['id' => 'api_posts_include_meta', 'label' => 'Include meta', 'type' => 'checkbox']
                                        ],
                                ],
                                'menus' => [
                                        'title' => 'Menus',
                                        'fields' => [
                                                ['id' => 'api_menu_enabled', 'label' => 'Enable Menus API', 'type' => 'checkbox']
                                        ],
                                ]
                        ],
                ],
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
            if (!empty($section['tabs']) && is_array($section['tabs'])) {
                foreach ($section['tabs'] as $tab) {
                    if (!empty($tab['fields'])) {
                        $out = array_merge($out, $tab['fields']);
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
                echo "<input type='text' name='{$this->option_key}[{$id}]' value='" . esc_attr($val) . "' class='regular-text' />";
                break;

            case 'checkbox':
                $checked = !empty($val) ? 'checked' : '';
                echo "<label><input type='checkbox' name='{$this->option_key}[{$id}]' value='1' $checked> " . esc_html($field['label']) . "</label>";
                break;

            case 'select':
                echo "<select name='{$this->option_key}[{$id}]'>";
                foreach (($field['options'] ?? []) as $k => $label) {
                    $selected = selected($val, $k, false);
                    echo "<option value='" . esc_attr($k) . "' $selected>" . esc_html($label) . "</option>";
                }
                echo "</select>";
                break;

            case 'repeater':
                $items = is_array($val) ? $val : [];
                echo "<div class='hwn-repeater-wrapper' data-name='" . esc_attr("{$this->option_key}[{$id}][]") . "'>";
                if (!empty($items)) {
                    foreach ($items as $text) {
                        echo "<div class='repeater-item'>
                                <input type='text' name='{$this->option_key}[{$id}][]' value='" . esc_attr($text) . "' />
                                <button type='button' class='button remove-item'>✕</button>
                              </div>";
                    }
                }
                echo "</div>";
                echo "<button type='button' class='button add-item' data-target='" . esc_attr($id) . "'>+ Add</button>";
                break;
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
                    echo "<a href='{$url}' class='nav-tab {$active}'>" . esc_html($sec['title']) . "</a>";
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
                                'tab' => $active_tab,
                                'sub' => $sub_key
                        ], admin_url('admin.php')));
                        echo "<li class='{$is_active}'><a href='{$url}'>" . esc_html($sub['title']) . "</a></li>";
                    }
                    echo "    </ul>";
                    echo "  </div>";

                    echo "  <div class='hwn-vertical-content'>";
                    echo "    <h2 class='hwn-vertical-title'>" . esc_html($section['title']) . " — " . esc_html($sub_tabs[$active_sub]['title']) . "</h2>";
                    echo "    <div class='hwn-section'>";
                    foreach ($sub_tabs[$active_sub]['fields'] as $field) {
                        echo "<div class='hwn-field'>";
                        if (($field['type'] ?? '') !== 'checkbox') {
                            echo "<label class='hwn-label'>" . esc_html($field['label']) . "</label>";
                        }
                        $this->render_field($field);
                        echo "</div>";
                    }
                    echo "    </div>";
                    submit_button();
                    echo "  </div>";
                    echo "</div>";
                } else {
                    echo "<div class='hwn-section'>";
                    foreach (($section['fields'] ?? []) as $field) {
                        echo "<div class='hwn-field'>";
                        if (($field['type'] ?? '') !== 'checkbox') {
                            echo "<label class='hwn-label'>" . esc_html($field['label']) . "</label>";
                        }
                        $this->render_field($field);
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