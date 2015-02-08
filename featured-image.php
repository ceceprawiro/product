<?php if ( ! defined( 'ABSPATH' ) ) die();

class Product_FeaturedImage
{
    public static function setup()
    {
        add_action( 'transition_post_status',  array( __CLASS__, 'on_all_status_transitions' ), 10, 3 );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'featured_image_enqueues' ) );
    }

    public static function on_all_status_transitions( $new_status, $old_status, $post )
    {
        $setting = get_option( 'product' );
        $featured_image = isset( $setting['featured_image'] ) && in_array( strtolower( $setting['featured_image'] ), array( 'on', 'off' ) ) ? strtolower( $setting['featured_image'] ) : '';
        if ( $featured_image != 'on' )
            return;

        if ( in_array( $post->post_type, array( 'product' ) ) && $new_status === 'publish' && ! has_post_thumbnail( $post->ID ) )
            wp_die(
                __( 'You cannot publish without a featured image.', PLUGIN_TEXT_DOMAIN ),
                '',
                array( 'back_link' => true )
            );
    }

    public static function featured_image_enqueues( $hook )
    {
        $setting = get_option( 'product' );
        $featured_image = isset( $setting['featured_image'] ) && in_array( strtolower( $setting['featured_image'] ), array( 'on', 'off' ) ) ? strtolower( $setting['featured_image'] ) : '';
        if ( $featured_image != 'on' )
            return;

        if ( $hook !== 'post.php' && $hook !== 'post-new.php' )
            return;

        global $post;

        if ( in_array( $post->post_type, array( 'product' ) ) ) {
            wp_register_script( 'featured_image_script', plugins_url( '/featured_image_script.js', __FILE__ ), array( 'jquery' ) );
            wp_enqueue_script( 'featured_image_script' );
            wp_localize_script(
                'featured_image_script',
                'objectL10n',
                array(
                    'jsWarningHtml' => __( '<strong>This entry has no featured image.</strong> Please set one. You need to set a featured image before publishing.', PLUGIN_TEXT_DOMAIN ),
                )
            );
        }
    }
}