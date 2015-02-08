<?php if ( ! defined( 'ABSPATH' ) ) die();

class Product_Metabox
{
    public static function setup()
    {
        add_action( 'add_meta_boxes', [ __CLASS__, 'show_details' ] );
        add_action( 'save_post', [ __CLASS__, 'save_details' ] );

        add_filter( 'manage_product_posts_columns', [ __CLASS__, 'post_meta_column' ] );
        add_action( 'manage_product_posts_custom_column', [ __CLASS__, 'post_meta_column_content' ], 10, 2 );
        add_filter( 'manage_edit-product_sortable_columns', [ __CLASS__, 'post_meta_column_sorting' ] );
        add_filter( 'request', [ __CLASS__, 'post_meta_column_orderby' ] );
    }

    public static function show_details()
    {

        add_meta_box(
            'product_details',                      // Section ID
            __( 'Stock', PLUGIN_TEXT_DOMAIN ),      // Metabox Title
            [ __CLASS__, 'show_details_callback' ], // Callback
            'product',                              // Post Type
            'side',                                 // Context
            'default'                               // Priority
        );
    }

    public static function show_details_callback( $post )
    {
        /** Add an nonce field so we can check for it later. **/
        wp_nonce_field( plugin_basename( __FILE__ ), 'product_details_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $_product_soldout = get_post_meta( $post->ID, '_product_soldout', true );
        $_product_soldout = $_product_soldout == 'on' ? 'on' : '';

        ?>
        <input type="checkbox" id="_product_soldout" name="_product_soldout" <?php checked( $_product_soldout, 'on', true ); ?> class="checkbox" value="on" />
        <label for="_product_soldout"><?php _e( 'Sold Out?', PLUGIN_TEXT_DOMAIN ); ?></label>
        <?php
    }

    public static function save_details( $post_id )
    {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return;

        if ( ! wp_verify_nonce( $_POST['product_details_nonce'], plugin_basename( __FILE__ ) ) )
            return;

        $_product_soldout = $_POST['_product_soldout'];

        update_post_meta( $post_id, '_product_soldout', $_product_soldout );
    }

    public static function post_meta_column( $defaults )
    {
        unset($defaults['date']);

        $defaults['_product_soldout'] = __( 'Stock', PLUGIN_TEXT_DOMAIN );

        $defaults['date'] = __( 'Published', PLUGIN_TEXT_DOMAIN );

        return $defaults;
    }

    public static function post_meta_column_content( $column_name, $post_id )
    {
        $value = get_post_meta( $post_id, $column_name, true );

        switch( $column_name ) {
            case '_product_soldout':
                if ( $value == 'on' ) {
                    echo 'Sold Out';
                }
                else {
                    echo 'Ready';
                }
                break;
        }
    }

    public static function post_meta_column_sorting( $columns )
    {
        $columns['_product_soldout'] = '_product_soldout';

        return $columns;
    }

    public static function post_meta_column_orderby( $vars )
    {
        if ( isset ( $vars['orderby'] ) && in_array( $vars['orderby'], [ '_product_soldout' ] ) ) {
            $column = $vars['orderby'];
            $vars = array_merge( $vars, [
                'meta_key' => $column,
                'orderby'  => 'meta_value'
            ] );
        }

        return $vars;
    }
}