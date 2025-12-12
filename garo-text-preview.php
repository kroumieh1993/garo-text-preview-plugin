<?php
/**
 * Plugin Name: Garo Text Preview Plugin
 * Plugin URI: https://github.com/kroumieh1993/garo-text-preview-plugin
 * Description: A WooCommerce plugin for customizing jewelry with text preview and font selection
 * Version: 1.0.0
 * Author: Garo
 * Author URI: https://github.com/kroumieh1993
 * Text Domain: garo-text-preview
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GARO_TEXT_PREVIEW_VERSION', '1.0.0');
define('GARO_TEXT_PREVIEW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GARO_TEXT_PREVIEW_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GARO_TEXT_PREVIEW_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Garo_Text_Preview_Plugin {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Check for WooCommerce
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Load plugin files
        $this->load_dependencies();
        
        // Initialize components
        $this->init_hooks();
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once GARO_TEXT_PREVIEW_PLUGIN_DIR . 'includes/class-garo-admin.php';
        require_once GARO_TEXT_PREVIEW_PLUGIN_DIR . 'includes/class-garo-product-fields.php';
        require_once GARO_TEXT_PREVIEW_PLUGIN_DIR . 'includes/class-garo-frontend.php';
        require_once GARO_TEXT_PREVIEW_PLUGIN_DIR . 'includes/class-garo-cart.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
        
        // Initialize classes
        Garo_Admin::get_instance();
        Garo_Product_Fields::get_instance();
        Garo_Frontend::get_instance();
        Garo_Cart::get_instance();
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options if they don't exist
        if (!get_option('garo_text_preview_fonts')) {
            update_option('garo_text_preview_fonts', array());
        }
        if (!get_option('garo_text_preview_global_fields')) {
            update_option('garo_text_preview_global_fields', array());
        }
        
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Admin scripts
     */
    public function admin_enqueue_scripts($hook) {
        // Load on product edit page and plugin settings page
        if ('post.php' === $hook || 'post-new.php' === $hook || strpos($hook, 'garo-text-preview') !== false) {
            wp_enqueue_style('garo-admin-css', GARO_TEXT_PREVIEW_PLUGIN_URL . 'assets/css/admin.css', array(), GARO_TEXT_PREVIEW_VERSION);
            wp_enqueue_script('garo-admin-js', GARO_TEXT_PREVIEW_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), GARO_TEXT_PREVIEW_VERSION, true);
            
            wp_localize_script('garo-admin-js', 'garoAdmin', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('garo_admin_nonce')
            ));
        }
    }
    
    /**
     * Frontend scripts
     */
    public function frontend_enqueue_scripts() {
        if (is_product()) {
            wp_enqueue_style('garo-frontend-css', GARO_TEXT_PREVIEW_PLUGIN_URL . 'assets/css/frontend.css', array(), GARO_TEXT_PREVIEW_VERSION);
            wp_enqueue_script('garo-frontend-js', GARO_TEXT_PREVIEW_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), GARO_TEXT_PREVIEW_VERSION, true);
            
            wp_localize_script('garo-frontend-js', 'garoFrontend', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('garo_frontend_nonce')
            ));
        }
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        echo '<div class="error"><p><strong>' . esc_html__('Garo Text Preview Plugin', 'garo-text-preview') . '</strong> ' . esc_html__('requires WooCommerce to be installed and active.', 'garo-text-preview') . '</p></div>';
    }
}

// Initialize plugin
function garo_text_preview_plugin() {
    return Garo_Text_Preview_Plugin::get_instance();
}

// Start the plugin
garo_text_preview_plugin();
