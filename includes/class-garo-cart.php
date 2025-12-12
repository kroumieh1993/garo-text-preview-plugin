<?php
/**
 * Cart integration class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Garo_Cart {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Display customizations in cart
        add_filter('woocommerce_get_item_data', array($this, 'display_cart_item_data'), 10, 2);
        
        // Add custom price to cart item
        add_action('woocommerce_before_calculate_totals', array($this, 'add_custom_price'));
        
        // Add customizations to order items
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'add_order_item_meta'), 10, 4);
        
        // Display customizations in admin order
        add_action('woocommerce_admin_order_item_headers', array($this, 'admin_order_item_headers'));
        add_action('woocommerce_admin_order_item_values', array($this, 'admin_order_item_values'), 10, 3);
    }
    
    /**
     * Display customization data in cart
     */
    public function display_cart_item_data($item_data, $cart_item) {
        if (isset($cart_item['garo_customizations'])) {
            foreach ($cart_item['garo_customizations'] as $customization) {
                $label = $customization['label'];
                $text = $customization['text'];
                $price = isset($customization['price']) ? floatval($customization['price']) : 0;
                
                $display_value = $text;
                if ($price > 0) {
                    $display_value .= ' (+' . wc_price($price) . ')';
                }
                
                $item_data[] = array(
                    'name' => $label,
                    'value' => $display_value,
                );
            }
        }
        
        if (isset($cart_item['garo_custom_image'])) {
            $item_data[] = array(
                'name' => __('Custom Image', 'garo-text-preview'),
                'value' => '<a href="' . esc_url($cart_item['garo_custom_image']) . '" target="_blank">' . __('View Image', 'garo-text-preview') . '</a>',
            );
        }
        
        return $item_data;
    }
    
    /**
     * Add custom price to cart item
     */
    public function add_custom_price($cart) {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }
        
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item['garo_additional_price'])) {
                $additional_price = floatval($cart_item['garo_additional_price']);
                
                if ($additional_price > 0) {
                    $product = $cart_item['data'];
                    $original_price = floatval($product->get_price());
                    $new_price = $original_price + $additional_price;
                    $product->set_price($new_price);
                }
            }
        }
    }
    
    /**
     * Add customizations to order items
     */
    public function add_order_item_meta($item, $cart_item_key, $values, $order) {
        if (isset($values['garo_customizations'])) {
            foreach ($values['garo_customizations'] as $customization) {
                $label = $customization['label'];
                $text = $customization['text'];
                $price = isset($customization['price']) ? floatval($customization['price']) : 0;
                $position_x = isset($customization['position_x']) ? $customization['position_x'] : '';
                $position_y = isset($customization['position_y']) ? $customization['position_y'] : '';
                $font = isset($customization['font']) ? $customization['font'] : '';
                
                $display_value = $text;
                if ($price > 0) {
                    $display_value .= ' (+' . wc_price($price) . ')';
                }
                
                $item->add_meta_data($label, $display_value, true);
                
                // Store additional data for potential future use
                $item->add_meta_data('_garo_customization_' . sanitize_key($label), array(
                    'text' => $text,
                    'price' => $price,
                    'position_x' => $position_x,
                    'position_y' => $position_y,
                    'font' => $font,
                ), false);
            }
        }
        
        if (isset($values['garo_custom_image'])) {
            $item->add_meta_data(__('Custom Image', 'garo-text-preview'), $values['garo_custom_image'], true);
        }
    }
    
    /**
     * Add header for customizations in admin order
     */
    public function admin_order_item_headers($order) {
        echo '<th class="garo-customizations">' . esc_html__('Customizations', 'garo-text-preview') . '</th>';
    }
    
    /**
     * Display customizations in admin order
     */
    public function admin_order_item_values($_product, $item, $item_id) {
        echo '<td class="garo-customizations">';
        
        $has_customizations = false;
        
        // Get item meta data
        $meta_data = $item->get_meta_data();
        
        foreach ($meta_data as $meta) {
            $key = $meta->key;
            $value = $meta->value;
            
            // Skip hidden meta keys
            if (strpos($key, '_garo_customization_') === 0) {
                continue;
            }
            
            // Check if this is a customization meta
            if (strpos($key, '_') !== 0) {
                $meta_display = $item->get_meta($key, true);
                if (!empty($meta_display)) {
                    echo '<div><strong>' . esc_html($key) . ':</strong> ' . wp_kses_post($meta_display) . '</div>';
                    $has_customizations = true;
                }
            }
        }
        
        if (!$has_customizations) {
            echo '—';
        }
        
        echo '</td>';
    }
}
