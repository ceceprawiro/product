<?php if ( ! defined( 'ABSPATH' ) ) die();

class Product_PostType
{
    public static function setup()
    {
        add_action( 'init', array( __CLASS__, 'register_post_type' ) );
        add_filter( 'post_updated_messages', array( __CLASS__, 'interaction_message' ) );
    }

    public static function register_post_type()
    {
        $labels = array(
            'name'               => __( 'Products', PLUGIN_TEXT_DOMAIN ),
            'singular_name'      => __( 'Product', PLUGIN_TEXT_DOMAIN ),
            'add_new'            => __( 'Add New',  PLUGIN_TEXT_DOMAIN),
            'add_new_item'       => __( 'Add New Product', PLUGIN_TEXT_DOMAIN ),
            'edit_item'          => __( 'Edit Product', PLUGIN_TEXT_DOMAIN ),
            'new_item'           => __( 'New Product', PLUGIN_TEXT_DOMAIN ),
            'view_item'          => __( 'View Product', PLUGIN_TEXT_DOMAIN ),
            'search_items'       => __( 'Search Products', PLUGIN_TEXT_DOMAIN ),
            'not_found'          => __( 'No products found', PLUGIN_TEXT_DOMAIN ),
            'not_found_in_trash' => __( 'No products found in Trash', PLUGIN_TEXT_DOMAIN ),
        );

        $slug = get_theme_mod( 'product_permalink' );
        $slug = empty( $slug ) ? 'product' : $slug;

        $args = [
            'labels'        => $labels,
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
            'has_archive'   => true,
            'rewrite'       => array( 'slug' => $slug ),
        ];
        register_post_type( 'product', $args );
    }

    public static function interaction_message( $messages )
    {
        global $post, $post_ID;

        $messages['product'] = [
            0  => '',
            1  => sprintf( __( 'Product updated. <a href="%s">View product</a>' ), esc_url( get_permalink( $post_ID ), PLUGIN_TEXT_DOMAIN ) ),
            2  => __( 'Custom field updated.', PLUGIN_TEXT_DOMAIN ),
            3  => __( 'Custom field deleted.', PLUGIN_TEXT_DOMAIN ),
            4  => __( 'Product updated.', PLUGIN_TEXT_DOMAIN ),
            5  => isset( $_GET['revision'] ) ? sprintf( __( 'Product restored to revision from %s', PLUGIN_TEXT_DOMAIN ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6  => sprintf( __( 'Product published. <a href="%s">View product</a>', PLUGIN_TEXT_DOMAIN ), esc_url( get_permalink( $post_ID ) ) ),
            7  => __( 'Product saved.', PLUGIN_TEXT_DOMAIN ),
            8  => sprintf( __( 'Product submitted. <a target="_blank" href="%s">Preview product</a>', PLUGIN_TEXT_DOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
            9  => sprintf( __( 'Product scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview product</a>', PLUGIN_TEXT_DOMAIN ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
            10 => sprintf( __( 'Product draft updated. <a target="_blank" href="%s">Preview product</a>', PLUGIN_TEXT_DOMAIN ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
        ];

        return $messages;
    }
}