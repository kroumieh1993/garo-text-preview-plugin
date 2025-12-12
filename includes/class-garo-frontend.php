<?php
/**
 * Frontend display class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Garo_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Display customization fields on product page
        add_action('woocommerce_before_add_to_cart_button', array($this, 'display_customization_fields'));
        
        // Display preview on product images
        add_action('woocommerce_product_thumbnails', array($this, 'display_image_preview'), 5);
        
        // Validate fields before add to cart
        add_filter('woocommerce_add_to_cart_validation', array($this, 'validate_customization_fields'), 10, 3);
        
        // Add custom data to cart item
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 10, 2);
        
        // Load fonts
        add_action('wp_head', array($this, 'load_custom_fonts'));
    }
    
    /**
     * Load custom fonts
     */
    public function load_custom_fonts() {
        $fonts = get_option('garo_text_preview_fonts', array());
        if (!empty($fonts)) {
            foreach ($fonts as $font) {
                if (!empty($font['url'])) {
                    // Check if it's a Google Font or custom font
                    if (strpos($font['url'], 'fonts.googleapis.com') !== false || strpos($font['url'], 'fonts.google.com') !== false) {
                        echo '<link rel="stylesheet" href="' . esc_url($font['url']) . '">' . "\n";
                    } else {
                        // Custom font file
                        echo '<style>@font-face { font-family: "' . esc_attr($font['name']) . '"; src: url("' . esc_url($font['url']) . '"); }</style>' . "\n";
                    }
                }
            }
        }
    }
    
    /**
     * Display customization fields
     */
    public function display_customization_fields() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        $enabled = get_post_meta($product_id, '_garo_text_preview_enabled', true);
        
        if ($enabled !== '1') {
            return;
        }
        
        $custom_fields = get_post_meta($product_id, '_garo_custom_fields', true);
        $image_upload_enabled = get_post_meta($product_id, '_garo_image_upload_enabled', true);
        $global_fields = $this->get_applicable_global_fields($product_id);
        
        // Merge custom fields with global fields
        $all_fields = array_merge((array)$custom_fields, (array)$global_fields);
        
        if (empty($all_fields) && $image_upload_enabled !== '1') {
            return;
        }
        
        $fonts = get_option('garo_text_preview_fonts', array());
        $default_font_global = get_option('garo_text_preview_default_font', '');
        ?>
        <div class="garo-customization-container">
            
            <?php if (!empty($all_fields)): ?>
                <?php foreach ($all_fields as $index => $field): ?>
                    <?php
                    $label = isset($field['label']) ? $field['label'] : '';
                    $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
                    $min_chars = isset($field['min_chars']) ? intval($field['min_chars']) : 0;
                    $max_chars = isset($field['max_chars']) ? intval($field['max_chars']) : 0;
                    $additional_price = isset($field['additional_price']) ? floatval($field['additional_price']) : 0;
                    $required = isset($field['required']) ? $field['required'] : false;
                    $field_type = isset($field['type']) ? $field['type'] : 'text';
                    $is_global = isset($field['is_global']) ? $field['is_global'] : false;
                    $default_font = isset($field['default_font']) ? $field['default_font'] : $default_font_global;
                    ?>
                    <div class="garo-field-group">
                        <label for="garo_field_<?php echo esc_attr($index); ?>">
                            <?php echo esc_html($label); ?>
                            <?php if ($required): ?>
                                <span class="required">*</span>
                            <?php endif; ?>
                            <?php if ($additional_price > 0): ?>
                                <span class="garo-additional-price">(+<?php echo wc_price($additional_price); ?>)</span>
                            <?php endif; ?>
                        </label>
                        
                        <?php if ($field_type === 'textarea'): ?>
                            <textarea 
                                name="garo_custom_text[<?php echo esc_attr($index); ?>]" 
                                id="garo_field_<?php echo esc_attr($index); ?>" 
                                class="garo-custom-field"
                                placeholder="<?php echo esc_attr($placeholder); ?>"
                                data-min="<?php echo esc_attr($min_chars); ?>"
                                data-max="<?php echo esc_attr($max_chars); ?>"
                                data-required="<?php echo $required ? '1' : '0'; ?>"
                                data-price="<?php echo esc_attr($additional_price); ?>"
                                data-field-index="<?php echo esc_attr($index); ?>"
                                <?php if ($max_chars > 0): ?>maxlength="<?php echo esc_attr($max_chars); ?>"<?php endif; ?>
                                rows="2"
                            ></textarea>
                        <?php else: ?>
                            <input 
                                type="text" 
                                name="garo_custom_text[<?php echo esc_attr($index); ?>]" 
                                id="garo_field_<?php echo esc_attr($index); ?>" 
                                class="garo-custom-field"
                                placeholder="<?php echo esc_attr($placeholder); ?>"
                                data-min="<?php echo esc_attr($min_chars); ?>"
                                data-max="<?php echo esc_attr($max_chars); ?>"
                                data-required="<?php echo $required ? '1' : '0'; ?>"
                                data-price="<?php echo esc_attr($additional_price); ?>"
                                data-field-index="<?php echo esc_attr($index); ?>"
                                <?php if ($max_chars > 0): ?>maxlength="<?php echo esc_attr($max_chars); ?>"<?php endif; ?>
                            >
                        <?php endif; ?>
                        
                        <select 
                            name="garo_custom_font[<?php echo esc_attr($index); ?>]" 
                            id="garo_font_<?php echo esc_attr($index); ?>" 
                            class="garo-font-dropdown"
                            data-field-index="<?php echo esc_attr($index); ?>"
                        >
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
                        
                        <?php if ($min_chars > 0 || $max_chars > 0): ?>
                            <small class="garo-field-hint">
                                <?php
                                if ($min_chars > 0 && $max_chars > 0) {
                                    printf(esc_html__('Between %d and %d characters', 'garo-text-preview'), $min_chars, $max_chars);
                                } elseif ($min_chars > 0) {
                                    printf(esc_html__('Minimum %d characters', 'garo-text-preview'), $min_chars);
                                } elseif ($max_chars > 0) {
                                    printf(esc_html__('Maximum %d characters', 'garo-text-preview'), $max_chars);
                                }
                                ?>
                            </small>
                        <?php endif; ?>
                        
                        <input type="hidden" name="garo_field_data[<?php echo esc_attr($index); ?>][label]" value="<?php echo esc_attr($label); ?>">
                        <input type="hidden" name="garo_field_data[<?php echo esc_attr($index); ?>][price]" value="<?php echo esc_attr($additional_price); ?>">
                        <input type="hidden" name="garo_field_data[<?php echo esc_attr($index); ?>][position_x]" value="<?php echo esc_attr(isset($field['position_x']) ? $field['position_x'] : ''); ?>">
                        <input type="hidden" name="garo_field_data[<?php echo esc_attr($index); ?>][position_y]" value="<?php echo esc_attr(isset($field['position_y']) ? $field['position_y'] : ''); ?>">
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if ($image_upload_enabled === '1'): ?>
                <div class="garo-field-group">
                    <label for="garo_custom_image"><?php echo esc_html__('Upload Custom Image', 'garo-text-preview'); ?></label>
                    <input type="file" name="garo_custom_image" id="garo_custom_image" class="garo-image-upload" accept="image/*">
                    <small class="garo-field-hint"><?php echo esc_html__('Upload a custom image if needed', 'garo-text-preview'); ?></small>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Display image preview overlay on product images
     */
    public function display_image_preview() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        $enabled = get_post_meta($product_id, '_garo_text_preview_enabled', true);
        
        if ($enabled !== '1') {
            return;
        }
        
        $custom_fields = get_post_meta($product_id, '_garo_custom_fields', true);
        $global_fields = $this->get_applicable_global_fields($product_id);
        $all_fields = array_merge((array)$custom_fields, (array)$global_fields);
        
        if (empty($all_fields)) {
            return;
        }
        ?>
        <div id="garo-image-preview-overlay" style="display: none;">
            <?php foreach ($all_fields as $index => $field): ?>
                <?php
                $position_x = isset($field['position_x']) ? intval($field['position_x']) : 0;
                $position_y = isset($field['position_y']) ? intval($field['position_y']) : 0;
                $default_font = isset($field['default_font']) ? $field['default_font'] : '';
                ?>
                <div 
                    class="garo-preview-text" 
                    id="garo_preview_<?php echo esc_attr($index); ?>"
                    data-field-index="<?php echo esc_attr($index); ?>"
                    style="position: absolute; left: <?php echo esc_attr($position_x); ?>px; top: <?php echo esc_attr($position_y); ?>px; font-family: <?php echo esc_attr($default_font); ?>; font-size: 24px; color: #333; font-weight: bold; text-shadow: 0 0 2px rgba(255,255,255,0.8); pointer-events: none; white-space: nowrap;"
                ></div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Get applicable global fields for product
     */
    private function get_applicable_global_fields($product_id) {
        $global_fields = get_option('garo_text_preview_global_fields', array());
        $applicable_fields = array();
        
        if (empty($global_fields)) {
            return $applicable_fields;
        }
        
        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
        
        foreach ($global_fields as $field) {
            $apply_to = isset($field['apply_to']) ? $field['apply_to'] : 'all';
            
            if ($apply_to === 'all') {
                $field['is_global'] = true;
                $applicable_fields[] = $field;
            } elseif ($apply_to === 'categories' && !empty($field['categories'])) {
                // Check if product is in any of the specified categories
                $field_categories = (array)$field['categories'];
                if (array_intersect($product_categories, $field_categories)) {
                    $field['is_global'] = true;
                    $applicable_fields[] = $field;
                }
            }
        }
        
        return $applicable_fields;
    }
    
    /**
     * Validate customization fields
     */
    public function validate_customization_fields($passed, $product_id, $quantity) {
        if (!isset($_POST['garo_custom_text'])) {
            return $passed;
        }
        
        $custom_fields = get_post_meta($product_id, '_garo_custom_fields', true);
        $global_fields = $this->get_applicable_global_fields($product_id);
        $all_fields = array_merge((array)$custom_fields, (array)$global_fields);
        
        if (empty($all_fields)) {
            return $passed;
        }
        
        $custom_text = $_POST['garo_custom_text'];
        
        foreach ($all_fields as $index => $field) {
            $label = isset($field['label']) ? $field['label'] : '';
            $min_chars = isset($field['min_chars']) ? intval($field['min_chars']) : 0;
            $max_chars = isset($field['max_chars']) ? intval($field['max_chars']) : 0;
            $required = isset($field['required']) ? $field['required'] : false;
            $text = isset($custom_text[$index]) ? trim($custom_text[$index]) : '';
            
            // Check required
            if ($required && empty($text)) {
                wc_add_notice(sprintf(__('%s is required.', 'garo-text-preview'), $label), 'error');
                $passed = false;
            }
            
            // Check min length
            if (!empty($text) && $min_chars > 0 && strlen($text) < $min_chars) {
                wc_add_notice(sprintf(__('%s must be at least %d characters.', 'garo-text-preview'), $label, $min_chars), 'error');
                $passed = false;
            }
            
            // Check max length
            if (!empty($text) && $max_chars > 0 && strlen($text) > $max_chars) {
                wc_add_notice(sprintf(__('%s must not exceed %d characters.', 'garo-text-preview'), $label, $max_chars), 'error');
                $passed = false;
            }
        }
        
        return $passed;
    }
    
    /**
     * Add custom data to cart item
     */
    public function add_cart_item_data($cart_item_data, $product_id) {
        if (isset($_POST['garo_custom_text'])) {
            $custom_text = $_POST['garo_custom_text'];
            $field_data = isset($_POST['garo_field_data']) ? $_POST['garo_field_data'] : array();
            $custom_fonts = isset($_POST['garo_custom_font']) ? $_POST['garo_custom_font'] : array();
            
            $customizations = array();
            $total_additional_price = 0;
            
            foreach ($custom_text as $index => $text) {
                if (!empty($text)) {
                    $price = isset($field_data[$index]['price']) ? floatval($field_data[$index]['price']) : 0;
                    $total_additional_price += $price;
                    
                    $customizations[] = array(
                        'label' => isset($field_data[$index]['label']) ? $field_data[$index]['label'] : '',
                        'text' => sanitize_text_field($text),
                        'price' => $price,
                        'position_x' => isset($field_data[$index]['position_x']) ? $field_data[$index]['position_x'] : '',
                        'position_y' => isset($field_data[$index]['position_y']) ? $field_data[$index]['position_y'] : '',
                        'font' => isset($custom_fonts[$index]) ? sanitize_text_field($custom_fonts[$index]) : '',
                    );
                }
            }
            
            if (!empty($customizations)) {
                $cart_item_data['garo_customizations'] = $customizations;
                $cart_item_data['garo_additional_price'] = $total_additional_price;
            }
        }
        
        // Handle image upload
        if (isset($_FILES['garo_custom_image']) && !empty($_FILES['garo_custom_image']['name'])) {
            $uploaded_file = $_FILES['garo_custom_image'];
            
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            
            $upload_overrides = array('test_form' => false);
            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
            
            if ($movefile && !isset($movefile['error'])) {
                $cart_item_data['garo_custom_image'] = $movefile['url'];
            }
        }
        
        return $cart_item_data;
    }
}
