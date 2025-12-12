<?php
/**
 * Admin settings class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Garo_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Text Preview Settings', 'garo-text-preview'),
            __('Text Preview', 'garo-text-preview'),
            'manage_options',
            'garo-text-preview-settings',
            array($this, 'settings_page'),
            'dashicons-edit',
            56
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('garo_text_preview_settings', 'garo_text_preview_fonts');
        register_setting('garo_text_preview_settings', 'garo_text_preview_global_fields');
        register_setting('garo_text_preview_settings', 'garo_text_preview_default_font');
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Save settings
        if (isset($_POST['garo_save_settings']) && check_admin_referer('garo_settings_nonce')) {
            $this->save_settings();
            echo '<div class="updated"><p>' . esc_html__('Settings saved successfully.', 'garo-text-preview') . '</p></div>';
        }
        
        $fonts = get_option('garo_text_preview_fonts', array());
        $global_fields = get_option('garo_text_preview_global_fields', array());
        $default_font = get_option('garo_text_preview_default_font', '');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Text Preview Settings', 'garo-text-preview'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('garo_settings_nonce'); ?>
                
                <h2><?php echo esc_html__('Font Management', 'garo-text-preview'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Available Fonts', 'garo-text-preview'); ?></th>
                        <td>
                            <div id="garo-fonts-container">
                                <?php
                                if (!empty($fonts)) {
                                    foreach ($fonts as $index => $font) {
                                        $this->render_font_row($index, $font);
                                    }
                                } else {
                                    $this->render_font_row(0, array('name' => '', 'url' => ''));
                                }
                                ?>
                            </div>
                            <button type="button" class="button" id="garo-add-font"><?php echo esc_html__('Add Font', 'garo-text-preview'); ?></button>
                            <p class="description"><?php echo esc_html__('Add links to font files (e.g., Google Fonts URLs or custom font files)', 'garo-text-preview'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Default Font', 'garo-text-preview'); ?></th>
                        <td>
                            <select name="garo_default_font" id="garo-default-font">
                                <option value=""><?php echo esc_html__('Select Default Font', 'garo-text-preview'); ?></option>
                                <?php
                                if (!empty($fonts)) {
                                    foreach ($fonts as $font) {
                                        if (!empty($font['name'])) {
                                            $selected = ($default_font === $font['name']) ? 'selected' : '';
                                            echo '<option value="' . esc_attr($font['name']) . '" ' . $selected . '>' . esc_html($font['name']) . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <p class="description"><?php echo esc_html__('Select the default font to be used', 'garo-text-preview'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php echo esc_html__('Global Fields', 'garo-text-preview'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Fields for All Products', 'garo-text-preview'); ?></th>
                        <td>
                            <div id="garo-global-fields-container">
                                <?php
                                if (!empty($global_fields)) {
                                    foreach ($global_fields as $index => $field) {
                                        $this->render_global_field_row($index, $field);
                                    }
                                } else {
                                    $this->render_global_field_row(0, array());
                                }
                                ?>
                            </div>
                            <button type="button" class="button" id="garo-add-global-field"><?php echo esc_html__('Add Global Field', 'garo-text-preview'); ?></button>
                            <p class="description"><?php echo esc_html__('Define fields that will be available for all products or specific categories', 'garo-text-preview'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="garo_save_settings" class="button-primary" value="<?php echo esc_attr__('Save Settings', 'garo-text-preview'); ?>">
                </p>
            </form>
        </div>
        
        <script type="text/template" id="garo-font-row-template">
            <?php $this->render_font_row('{{INDEX}}', array('name' => '', 'url' => '')); ?>
        </script>
        
        <script type="text/template" id="garo-global-field-row-template">
            <?php $this->render_global_field_row('{{INDEX}}', array()); ?>
        </script>
        <?php
    }
    
    /**
     * Render font row
     */
    private function render_font_row($index, $font) {
        $name = isset($font['name']) ? $font['name'] : '';
        $url = isset($font['url']) ? $font['url'] : '';
        ?>
        <div class="garo-font-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">
            <input type="text" name="garo_fonts[<?php echo esc_attr($index); ?>][name]" placeholder="<?php echo esc_attr__('Font Name', 'garo-text-preview'); ?>" value="<?php echo esc_attr($name); ?>" style="width: 30%; margin-right: 10px;">
            <input type="text" name="garo_fonts[<?php echo esc_attr($index); ?>][url]" placeholder="<?php echo esc_attr__('Font URL', 'garo-text-preview'); ?>" value="<?php echo esc_attr($url); ?>" style="width: 50%; margin-right: 10px;">
            <button type="button" class="button garo-remove-font"><?php echo esc_html__('Remove', 'garo-text-preview'); ?></button>
        </div>
        <?php
    }
    
    /**
     * Render global field row
     */
    private function render_global_field_row($index, $field) {
        $label = isset($field['label']) ? $field['label'] : '';
        $type = isset($field['type']) ? $field['type'] : 'text';
        $apply_to = isset($field['apply_to']) ? $field['apply_to'] : 'all';
        $categories = isset($field['categories']) ? $field['categories'] : array();
        ?>
        <div class="garo-global-field-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9;">
            <p>
                <label><?php echo esc_html__('Field Label:', 'garo-text-preview'); ?></label><br>
                <input type="text" name="garo_global_fields[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($label); ?>" style="width: 80%;">
            </p>
            <p>
                <label><?php echo esc_html__('Field Type:', 'garo-text-preview'); ?></label><br>
                <select name="garo_global_fields[<?php echo esc_attr($index); ?>][type]" style="width: 200px;">
                    <option value="text" <?php selected($type, 'text'); ?>><?php echo esc_html__('Text Field', 'garo-text-preview'); ?></option>
                    <option value="textarea" <?php selected($type, 'textarea'); ?>><?php echo esc_html__('Text Area', 'garo-text-preview'); ?></option>
                </select>
            </p>
            <p>
                <label><?php echo esc_html__('Apply To:', 'garo-text-preview'); ?></label><br>
                <select name="garo_global_fields[<?php echo esc_attr($index); ?>][apply_to]" class="garo-apply-to" style="width: 200px;">
                    <option value="all" <?php selected($apply_to, 'all'); ?>><?php echo esc_html__('All Products', 'garo-text-preview'); ?></option>
                    <option value="categories" <?php selected($apply_to, 'categories'); ?>><?php echo esc_html__('Specific Categories', 'garo-text-preview'); ?></option>
                </select>
            </p>
            <p class="garo-categories-field" style="<?php echo ($apply_to === 'categories') ? '' : 'display:none;'; ?>">
                <label><?php echo esc_html__('Categories:', 'garo-text-preview'); ?></label><br>
                <select name="garo_global_fields[<?php echo esc_attr($index); ?>][categories][]" multiple style="width: 80%; height: 100px;">
                    <?php
                    $product_categories = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false));
                    if (!empty($product_categories)) {
                        foreach ($product_categories as $category) {
                            $selected = in_array($category->term_id, (array)$categories) ? 'selected' : '';
                            echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </p>
            <button type="button" class="button garo-remove-global-field"><?php echo esc_html__('Remove Field', 'garo-text-preview'); ?></button>
        </div>
        <?php
    }
    
    /**
     * Save settings
     */
    private function save_settings() {
        // Save fonts
        $fonts = isset($_POST['garo_fonts']) ? array_values(array_filter($_POST['garo_fonts'], function($font) {
            return !empty($font['name']) && !empty($font['url']);
        })) : array();
        update_option('garo_text_preview_fonts', $fonts);
        
        // Save default font
        $default_font = isset($_POST['garo_default_font']) ? sanitize_text_field($_POST['garo_default_font']) : '';
        update_option('garo_text_preview_default_font', $default_font);
        
        // Save global fields
        $global_fields = isset($_POST['garo_global_fields']) ? array_values(array_filter($_POST['garo_global_fields'], function($field) {
            return !empty($field['label']);
        })) : array();
        update_option('garo_text_preview_global_fields', $global_fields);
    }
}
