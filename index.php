<?php if ( ! defined( 'ABSPATH' ) ) die();

/*
Plugin Name: Product
Description: Product Gallery post type.
Text Domain: product
*/

/**
 * Loading Helper
 */
require ('inc/helper.php');

/**
 * Load files
 */
require_once 'post-type.php';
require_once 'taxonomy.php';
require_once 'metabox.php';
require_once 'widget.php';
require_once 'walker.php';
require_once 'featured-image.php';
require_once 'option.php';

/**
 * Init variables
 */
if ( ! defined( 'PLUGIN_TEXT_DOMAIN' ) )
    define( 'PLUGIN_TEXT_DOMAIN', 'product' );

/**
 * Main class
 */
if ( ! class_exists( 'Product' ) ) {

class Product
{
    private static $instance;

    private function __construct() {}

    public static function get_instance()
    {
        if ( ! isset( self::$instance ) ) {
            $c = __CLASS__;
            self::$instance = new $c();
            self::$instance->init();
        }

        return self::$instance;
    }

    private static function init()
    {
        register_activation_hook( __FILE__, array( 'Product', 'activate' ) );
        register_deactivation_hook( __FILE__, array( 'Product', 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( 'Product', 'uninstall' ) );

        add_action( 'init', array( __CLASS__, 'load_text_domain' ) );

        Product_PostType::setup();
        Product_Taxonomy::setup();
        Product_Metabox::setup();
        Product_Widget::setup();
        Product_Option::setup();
        Product_FeaturedImage::setup();
    }

    public static function activate()
    {
        self::compatibility();
    }

    public static function deactivate() { /* Nothing todo */ }

    public static function uninstall() { /* Nothing todo */ }

    public static function compatibility( $required_wp_version = '3.6' )
    {
        if ( version_compare( $GLOBALS['wp_version'], $required_wp_version, '<' ) )
            wp_die( sprintf( __( 'This plugin requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', PLUGIN_TEXT_DOMAIN ), $GLOBALS['wp_version'] ), '', array( 'back_link' => true, ) );
    }

    public static function load_text_domain()
    {
        load_plugin_textdomain( PLUGIN_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}
Product::get_instance();

}