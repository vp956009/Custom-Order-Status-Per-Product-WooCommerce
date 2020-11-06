<?php
/**
 * Plugin Name: Custom Order Status Per Product WooCommerce
 * Description: This plugin allows to create custom order status and more...
 * Version: 1.0
 * Copyright: 2020
 */


if (!defined('ABSPATH')) {
    die('-1');
}
if (!defined('OCCOSPP_PLUGIN_NAME')) {
    define('OCCOSPP_PLUGIN_NAME', 'WC Custom Order Status Per Product');
}
if (!defined('OCCOSPP_PLUGIN_VERSION')) {
    define('OCCOSPP_PLUGIN_VERSION', '1.0.0');
}
if (!defined('OCCOSPP_PLUGIN_FILE')) {
    define('OCCOSPP_PLUGIN_FILE', __FILE__);
}
if (!defined('OCCOSPP_PLUGIN_DIR')) {
    define('OCCOSPP_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('OCCOSPP_DOMAIN')) {
    define('OCCOSPP_DOMAIN', 'ocwcp');
}
if (!defined('OCCOSPP_BASE_NAME')) {
    define('OCCOSPP_BASE_NAME', plugin_basename(OCCOSPP_PLUGIN_FILE));
}


if (!class_exists('OCCOSPP')) {

    class OCCOSPP {
        protected static $OCCOSPP_instance;
        function __construct() {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            add_action('admin_init', array($this, 'OCCOSPP_check_plugin_state'));
        }


        function OCCOSPP_load_admin_script_style() {
            wp_enqueue_style( 'OCCOSPP_admin_css', OCCOSPP_PLUGIN_DIR . '/css/admin_style.css', false, '1.0.0' );
            wp_enqueue_script( 'OCCOSPP_admin_script', OCCOSPP_PLUGIN_DIR . '/js/back_script.js', false, '1.0.0' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker-alpha', OCCOSPP_PLUGIN_DIR . '/js/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '1.0.0', true );
        }


        function OCCOSPP_load_script_style() {
            wp_enqueue_style( 'OCCOSPP_front_css', OCCOSPP_PLUGIN_DIR . '/css/style.css', false, '1.0.0' );
            wp_enqueue_script( 'OCCOSPP_front_js', OCCOSPP_PLUGIN_DIR . '/js/front.js', false, '1.0.0' );
            wp_localize_script( 'OCCOSPP_front_js', 'ajax_url', admin_url('admin-ajax.php') );
            $translation_array_img = OCCOSPP_PLUGIN_DIR;
            wp_localize_script( 'OCCOSPP_front_js', 'object_name', $translation_array_img );   
        }




        function OCCOSPP_show_notice() {

            if ( get_transient( get_current_user_id() . 'occosppverror' ) ) {

                deactivate_plugins( plugin_basename( __FILE__ ) );

                delete_transient( get_current_user_id() . 'occospperror' );

                echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=woocommerce">WooCommerce</a> plugin installed and activated.</p></div>';

            }
        }


        function OCCOSPP_check_plugin_state(){
            if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
                set_transient( get_current_user_id() . 'occospperror', 'message' );
            }
        }



        function OCCOSPP_footer(){
            wp_enqueue_script( 'wc-add-to-cart-variation' );
            wp_enqueue_script('wc-single-product');
        }



        function init() {
            add_action( 'admin_notices', array($this, 'OCCOSPP_show_notice'));
            add_action( 'admin_enqueue_scripts', array($this, 'OCCOSPP_load_admin_script_style'));
            add_action( 'wp_enqueue_scripts',  array($this, 'OCCOSPP_load_script_style'));
            add_filter( 'wp_footer', array( $this, 'OCCOSPP_footer' ), 10, 2 );
        }
        


        function includes() {
            include_once('includes/oc-occospp-backend.php');
            include_once('includes/oc-occospp-front.php');
        }


        public static function OCCOSPP_instance() {
            if (!isset(self::$OCCOSPP_instance)) {
                self::$OCCOSPP_instance = new self();
                self::$OCCOSPP_instance->init();
                self::$OCCOSPP_instance->includes();
            }
            return self::$OCCOSPP_instance;
        }
    }
    add_action('plugins_loaded', array('OCCOSPP', 'OCCOSPP_instance'));
}