<?php if ( ! defined( 'ABSPATH' ) ) die();

class Product_Option
{
    public static function setup()
    {
        add_action( 'admin_menu', array( __CLASS__, 'my_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'my_admin_init' ) );
    }

    public static function my_admin_menu()
    {
        add_options_page(
            'Product',          // Page Title
            'Product',          // Menu Title
            'manage_options',   // Capability
            'product',      // Menu Slug
            array( __CLASS__, 'product_page' ) // Callback
        );
    }


    public static function my_admin_init()
    {
        register_setting(
            'product_group', // Option Group
            'product',       // Option Name
            array( __CLASS__, 'validate' )  // Callback
        );

        add_settings_section(
            'product_section', // ID
            '', // 'Featured Image',      // Title

            array( __CLASS__, 'product_section_callback' ), // Callback
            'product'          // Page
        );

        add_settings_field(
            'featured_image',     // ID
            'Featured Image',     // Title
            array( __CLASS__, 'product_field_callback' ), // Callback
            'product',        // Page
            'product_section', // Section
            array( __( 'Mandatory <span class="description">(You need to set a featured image before publishing any product)</span>.' ) ) // Arguments
        );
    }

    public static function validate( $input )
    {
        return $input;
    }

    public static function product_section_callback()
    {
        // echo 'Some help text goes here.';
    }

    public static function product_field_callback( $args )
    {
        $setting = get_option( 'product' );

        $featured_image = isset( $setting['featured_image'] ) && in_array( strtolower( $setting['featured_image'] ), array( 'on', 'off' ) ) ? strtolower( $setting['featured_image'] ) : '';
        echo '<label>';
        echo '<input type="checkbox" name="product[featured_image]" value="on" ' . checked( 'on', $featured_image, false ) . '/>';
        echo $args[0];
        echo '</label>';
    }

    public static function product_page()
    {
        ?>
        <div class="wrap">
            <h2>Product Options</h2>
            <form action="options.php" method="POST">
                <?php settings_fields( 'product_group' ); ?>
                <?php do_settings_sections( 'product' ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}