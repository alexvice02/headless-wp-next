<?php
/**
 * Plugin Name: Headless WP Next
 * Description: Headless WordPress settings management
 * Version: 0.2.0
 * Author: alexvice02
 */

if (!defined('ABSPATH')) exit;

class Av02Settings {
    private $option_key = 'av02_options';
    private $sections   = [];

    public function __construct() {
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
        ];

        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function add_menu() {
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

    public function register_settings() {
        register_setting($this->option_key, $this->option_key);
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'toplevel_page_headless-wp-next') return;
        wp_enqueue_script('headless-wp-next-repeater', plugin_dir_url(__FILE__) . 'admin/repeater.js', ['jquery'], null, true);
        wp_enqueue_style('headless-wp-next-style', plugin_dir_url(__FILE__) . 'admin/style.css');
    }

    private function get_options() {
        return get_option($this->option_key, []);
    }

    private function render_field($field) {
        $options = $this->get_options();
        $id   = $field['id'];
        $val  = $options[$id] ?? '';

        switch ($field['type']) {
            case 'text':
                echo "<input type='text' name='{$this->option_key}[{$id}]' value='" . esc_attr($val) . "' class='regular-text' />";
                break;

            case 'checkbox':
                $checked = !empty($val) ? 'checked' : '';
                echo "<label><input type='checkbox' name='{$this->option_key}[{$id}]' value='1' $checked> {$field['label']}</label>";
                break;

            case 'select':
                echo "<select name='{$this->option_key}[{$id}]'>";
                foreach ($field['options'] as $k => $label) {
                    $selected = selected($val, $k, false);
                    echo "<option value='$k' $selected>$label</option>";
                }
                echo "</select>";
                break;

            case 'repeater':
                $items = is_array($val) ? $val : [];
                echo "<div class='hwn-repeater-wrapper' data-name='{$this->option_key}[{$id}][]'>";
                if (!empty($items)) {
                    foreach ($items as $text) {
                        echo "<div class='repeater-item'>
                                <input type='text' name='{$this->option_key}[{$id}][]' value='" . esc_attr($text) . "' />
                                <button type='button' class='button remove-item'>✕</button>
                              </div>";
                    }
                }
                echo "</div>";
                echo "<button type='button' class='button add-item' data-target='{$id}'>+ Add</button>";
                break;
        }
    }

    public function settings_page() {
        ?>
        <div class="wrap hwn-admin">
            <h1>Settings</h1>

            <h2 class="nav-tab-wrapper">
                <?php
                $active_tab = $_GET['tab'] ?? array_key_first($this->sections);
                foreach ($this->sections as $key => $section) {
                    $active = ($active_tab === $key) ? 'nav-tab-active' : '';
                    echo "<a href='?page=headless-wp-next&tab=$key' class='nav-tab $active'>{$section['title']}</a>";
                }
                ?>
            </h2>

            <form method="post" action="options.php">
                <?php
                settings_fields($this->option_key);
                $section = $this->sections[$active_tab];
                echo "<div class='hwn-section'>";
                foreach ($section['fields'] as $field) {
                    echo "<div class='hwn-field'>";
                    if ($field['type'] !== 'checkbox') {
                        echo "<label class='hwn-label'>{$field['label']}</label>";
                    }
                    $this->render_field($field);
                    echo "</div>";
                }
                echo "</div>";
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

new Av02Settings();