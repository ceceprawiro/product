<?php if ( ! defined( 'ABSPATH' ) ) die();

class Product_Taxonomy
{
    public static function setup()
    {
        add_action( 'init', array( __CLASS__, 'Product_Taxonomy::register_taxonomies' ), 0 );
        add_action( 'restrict_manage_posts', array( __CLASS__, 'filter_posts_by_taxonomy' ) );
    }

    public static function register_taxonomies()
    {
        $post_type = 'product';

        /* Register first taxonomy: "Product Category" */
        $labels = array(
            'name'          => __( 'Product Categories', PLUGIN_TEXT_DOMAIN ),
            'singular_name' => __( 'Product Category', PLUGIN_TEXT_DOMAIN ),
            'menu_name'     => __( 'Categories', PLUGIN_TEXT_DOMAIN ),
        );
        $args = array(
            'labels'            => $labels,
            'show_admin_column' => true,
            'hierarchical'      => true,
            'query_var'         => true,
        );
        $taxonomy  = 'product_category';
        register_taxonomy( $taxonomy, $post_type, $args );

        /* Register second taxonomy: "Product Tag" */
        $labels = array(
            'name'          => __( 'Product Tags', PLUGIN_TEXT_DOMAIN ),
            'singular_name' => __( 'Product Tag', PLUGIN_TEXT_DOMAIN ),
            'menu_name'     => __( 'Tags', PLUGIN_TEXT_DOMAIN )
        );
        $args = array(
            'labels'            => $labels,
            'show_admin_column' => true,
            'query_var'         => true,
        );
        $taxonomy  = 'product_tag';
        register_taxonomy( $taxonomy, $post_type, $args );
    }

    public static function filter_posts_by_taxonomy()
    {
        global $typenow;

        if ( $typenow != 'product' )
            return;

        global $wp_query;

        foreach ( array( 'product_category' ) as $taxonomy ) {
            $tax = get_taxonomy( $taxonomy );
            $args = array(
                'show_option_all'    => __( 'Show', PLUGIN_TEXT_DOMAIN ).' '.$tax->labels->name,
                'orderby'            => 'name',
                'show_count'         => true,
                'hide_empty'         => true,
                // 'selected'           => $wp_query->query[$taxonomy],
                'hierarchical'       => true,
                'name'               => $taxonomy,
                'class'              => 'postform',
                'taxonomy'           => $taxonomy,
                'hide_if_empty'      => true,
                'walker'             => new Custom_Taxonomy_Walker(),
            );
            if ( isset( $wp_query->query[$taxonomy] ) )
                $args['selected'] = $wp_query->query[$taxonomy];

            wp_dropdown_categories( $args );
        }
    }
}