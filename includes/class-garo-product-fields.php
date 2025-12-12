<?php
/**
 * Product fields class - manages custom fields per product
 */

if (!defined('ABSPATH')) {
    exit;
}

class Garo_Product_Fields {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Add product meta box
        add_action('add_meta_boxes', array($this, 'add_product_meta_box'));
        add_action('save_post_product', array($this, 'save_product_meta'), 10, 2);
    }
    
    /**
     * Add meta box to product edit page
     */
    public function add_product_meta_box() {
        add_meta_box(
            'garo_text_preview_product_fields',
            __('Text Preview Customization', 'garo-text-preview'),
            array($this, 'render_meta_box'),
            'product',
            'normal',
            'high'
        );
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('garo_product_fields_nonce', 'garo_product_fields_nonce');
        
        $enabled = get_post_meta($post->ID, '_garo_text_preview_enabled', true);
        $selected_font = get_post_meta($post->ID, '_garo_selected_font', true);
        $custom_fields = get_post_meta($post->ID, '_garo_custom_fields', true);
        $image_upload_enabled = get_post_meta($post->ID, '_garo_image_upload_enabled', true);
        
        if (!is_array($custom_fields)) {
            $custom_fields = array();
        }
        
        $fonts = get_option('garo_text_preview_fonts', array());
        $default_font = get_option('garo_text_preview_default_font', '');
        
        if (empty($selected_font) && !empty($default_font)) {
            $selected_font = $default_font;
        }
        ?>
        <div class="garo-product-fields-container">
            <p>
                <label>
                    <input type="checkbox" name="garo_text_preview_enabled" value="1" <?php checked($enabled, '1'); ?>>
                    <?php echo esc_html__('Enable text preview customization for this product', 'garo-text-preview'); ?>
                </label>
            </p>
            
            <div id="garo-product-customization" style="<?php echo $enabled ? '' : 'display:none;'; ?>">
                <h3><?php echo esc_html__('Font Selection', 'garo-text-preview'); ?></h3>
                <p>
                    <label for="garo_selected_font"><?php echo esc_html__('Select Font for this Product:', 'garo-text-preview'); ?></label><br>
                    <select name="garo_selected_font" id="garo_selected_font" style="width: 300px;">
                        <option value=""><?php echo esc_html__('Select a Font', 'garo-text-preview'); ?></option>
                        <?php
                        if (!empty($fonts)) {
                            foreach ($fonts as $font) {
                                if (!empty($font['name'])) {
                                    $selected = ($selected_font === $font['name']) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($font['name']) . '" ' . $selected . '>' . esc_html($font['name']) . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                    <span class="description"><?php echo esc_html__('This font will be used for all text fields on this product', 'garo-text-preview'); ?></span>
                </p>
                
                <h3><?php echo esc_html__('Custom Text Fields', 'garo-text-preview'); ?></h3>
                <div id="garo-custom-fields-container">
                    <?php
                    if (!empty($custom_fields)) {
                        foreach ($custom_fields as $index => $field) {
                            $this->render_custom_field_row($index, $field);
                        }
                    } else {
                        $this->render_custom_field_row(0, array());
                    }
                    ?>
                </div>
                <button type="button" class="button" id="garo-add-custom-field"><?php echo esc_html__('Add Text Field', 'garo-text-preview'); ?></button>
                
                <h3><?php echo esc_html__('Image Upload', 'garo-text-preview'); ?></h3>
                <p>
                    <label>
                        <input type="checkbox" name="garo_image_upload_enabled" value="1" <?php checked($image_upload_enabled, '1'); ?>>
                        <?php echo esc_html__('Allow customers to upload an image for this product', 'garo-text-preview'); ?>
                    </label>
                </p>
            </div>
        </div>
        
        <script type="text/template" id="garo-custom-field-template">
            <?php $this->render_custom_field_row('{{INDEX}}', array()); ?>
        </script>
        
        <style>
            .garo-custom-field-row {
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 15px;
                background: #f9f9f9;
            }
            .garo-custom-field-row input[type="text"],
            .garo-custom-field-row input[type="number"] {
                width: 100%;
                max-width: 400px;
            }
            .garo-field-row {
                margin-bottom: 10px;
            }
            .garo-field-inline {
                display: inline-block;
                margin-right: 20px;
            }
        </style>
        <?php
    }
    
    /**
     * Render custom field row
     */
    private function render_custom_field_row($index, $field) {
        $label = isset($field['label']) ? $field['label'] : '';
        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
        $min_chars = isset($field['min_chars']) ? $field['min_chars'] : '';
        $max_chars = isset($field['max_chars']) ? $field['max_chars'] : '';
        $additional_price = isset($field['additional_price']) ? $field['additional_price'] : '';
        $position_x = isset($field['position_x']) ? $field['position_x'] : '';
        $position_y = isset($field['position_y']) ? $field['position_y'] : '';
        $required = isset($field['required']) ? $field['required'] : false;
        ?>
        <div class="garo-custom-field-row">
            <div class="garo-field-row">
                <label><?php echo esc_html__('Field Label:', 'garo-text-preview'); ?></label><br>
                <input type="text" name="garo_custom_fields[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($label); ?>" placeholder="<?php echo esc_attr__('e.g., Engraved Name', 'garo-text-preview'); ?>">
            </div>
            
            <div class="garo-field-row">
                <label><?php echo esc_html__('Placeholder Text:', 'garo-text-preview'); ?></label><br>
                <input type="text" name="garo_custom_fields[<?php echo esc_attr($index); ?>][placeholder]" value="<?php echo esc_attr($placeholder); ?>" placeholder="<?php echo esc_attr__('e.g., Enter your name', 'garo-text-preview'); ?>">
            </div>
            
            <div class="garo-field-row">
                <div class="garo-field-inline">
                    <label><?php echo esc_html__('Min Characters:', 'garo-text-preview'); ?></label><br>
                    <input type="number" name="garo_custom_fields[<?php echo esc_attr($index); ?>][min_chars]" value="<?php echo esc_attr($min_chars); ?>" min="0" style="width: 100px;">
                </div>
                
                <div class="garo-field-inline">
                    <label><?php echo esc_html__('Max Characters:', 'garo-text-preview'); ?></label><br>
                    <input type="number" name="garo_custom_fields[<?php echo esc_attr($index); ?>][max_chars]" value="<?php echo esc_attr($max_chars); ?>" min="0" style="width: 100px;">
                </div>
            </div>
            
            <div class="garo-field-row">
                <label><?php echo esc_html__('Additional Price:', 'garo-text-preview'); ?> (<?php echo get_woocommerce_currency_symbol(); ?>)</label><br>
                <input type="number" name="garo_custom_fields[<?php echo esc_attr($index); ?>][additional_price]" value="<?php echo esc_attr($additional_price); ?>" step="0.01" min="0" style="width: 150px;">
                <span class="description"><?php echo esc_html__('Extra cost for this customization field', 'garo-text-preview'); ?></span>
            </div>
            
            <div class="garo-field-row">
                <label><?php echo esc_html__('Image Position (X, Y coordinates in pixels):', 'garo-text-preview'); ?></label><br>
                <div class="garo-field-inline">
                    <label><?php echo esc_html__('X:', 'garo-text-preview'); ?></label>
                    <input type="number" name="garo_custom_fields[<?php echo esc_attr($index); ?>][position_x]" value="<?php echo esc_attr($position_x); ?>" min="0" style="width: 100px;">
                </div>
                <div class="garo-field-inline">
                    <label><?php echo esc_html__('Y:', 'garo-text-preview'); ?></label>
                    <input type="number" name="garo_custom_fields[<?php echo esc_attr($index); ?>][position_y]" value="<?php echo esc_attr($position_y); ?>" min="0" style="width: 100px;">
                </div>
                <span class="description"><?php echo esc_html__('Position where text will appear on the product image', 'garo-text-preview'); ?></span>
            </div>
            
            <div class="garo-field-row">
                <label>
                    <input type="checkbox" name="garo_custom_fields[<?php echo esc_attr($index); ?>][required]" value="1" <?php checked($required, '1'); ?>>
                    <?php echo esc_html__('Required Field', 'garo-text-preview'); ?>
                </label>
            </div>
            
            <button type="button" class="button garo-remove-custom-field"><?php echo esc_html__('Remove Field', 'garo-text-preview'); ?></button>
        </div>
        <?php
    }
    
    /**
     * Save product meta
     */
    public function save_product_meta($post_id, $post) {
        // Check nonce
        if (!isset($_POST['garo_product_fields_nonce']) || !wp_verify_nonce($_POST['garo_product_fields_nonce'], 'garo_product_fields_nonce')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save enabled status
        $enabled = isset($_POST['garo_text_preview_enabled']) ? '1' : '0';
        update_post_meta($post_id, '_garo_text_preview_enabled', $enabled);
        
        // Save selected font
        $selected_font = isset($_POST['garo_selected_font']) ? sanitize_text_field($_POST['garo_selected_font']) : '';
        update_post_meta($post_id, '_garo_selected_font', $selected_font);
        
        // Save custom fields
        $custom_fields = isset($_POST['garo_custom_fields']) ? array_values(array_filter($_POST['garo_custom_fields'], function($field) {
            return !empty($field['label']);
        })) : array();
        update_post_meta($post_id, '_garo_custom_fields', $custom_fields);
        
        // Save image upload enabled
        $image_upload_enabled = isset($_POST['garo_image_upload_enabled']) ? '1' : '0';
        update_post_meta($post_id, '_garo_image_upload_enabled', $image_upload_enabled);
    }
}
