<?php if ( ! defined( 'ABSPATH' ) ) die();

class Product_Widget
{
    public static function setup()
    {
        add_action( 'widgets_init', array( __CLASS__, 'register_widgets' ) );
    }

    public static function register_widgets()
    {
        register_widget( 'Product_RecentProducts' );
        register_widget( 'Product_Categories' );
    }
}

class Product_RecentProducts extends WP_Widget
{
    public function __construct()
    {
        $slug = 'product-recent';
        $name = __( 'Latest Products', PLUGIN_TEXT_DOMAIN );
        $options = array( 'description' => __( 'Your most recent Products.', PLUGIN_TEXT_DOMAIN ) );

        parent::__construct( $slug, $name, $options );
    }

    public function form( $instance )
    {
        $title          = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
        $number         = isset( $instance['number'] ) && is_numeric( $instance['number'] ) && $instance['number'] > 0 ? (int) $instance['number'] : '5';
        $show_thumbnail = isset( $instance['show_thumbnail'] ) && in_array( strtolower( $instance['show_thumbnail'] ), array( 'on', 'off' ) ) ? strtolower( $instance['show_thumbnail'] ) : '';
        $class_name     = isset( $instance['class_name'] ) ? strip_tags( $instance['class_name'] ) : 'product';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', PLUGIN_TEXT_DOMAIN ); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of products to show:', PLUGIN_TEXT_DOMAIN ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3"/>
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" <?php checked( $show_thumbnail, 'on', true ); ?> class="checkbox"/>
            <label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"><?php _e( 'Display product thumbnail?', PLUGIN_TEXT_DOMAIN ); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'class_name' ); ?>">
                <?php _e( 'Product container class name', PLUGIN_TEXT_DOMAIN ); ?>
            </label>
            <input type="text" id="<?php echo $this->get_field_id( 'class_name' ); ?>" name="<?php echo $this->get_field_name( 'class_name' ); ?>" value="<?php echo esc_attr( $class_name ); ?>" class="widefat"/>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['title']          = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number']         = ( ! empty( $new_instance['number'] ) ) && is_numeric( $new_instance['number'] ) && $new_instance['number'] > 0 ? (int) $new_instance['number'] : '5';
        $instance['show_thumbnail'] = ( ! empty( $new_instance['show_thumbnail'] ) ) && in_array( strtolower( $new_instance['show_thumbnail'] ), array( 'on', 'off' ) ) ? 'on' : '';
        $instance['class_name']     = ( ! empty( $new_instance['class_name'] ) ) ? strip_tags( $new_instance['class_name'] ) : '';

        return $instance;
    }

    public function widget( $args, $instance )
    {
        /* options */
        $title = apply_filters( 'widget_title', $instance['title'] );
        $title = ( ! empty( $title ) ) ? $title : $args['widget_name'];

        $number = $instance['number'];
        $number = ( ! empty( $number ) ) ? (int) $number : 5;

        $show_thumbnail = $instance['show_thumbnail'];
        $show_thumbnail = ( ! empty( $show_thumbnail ) ) ? strtolower( $show_thumbnail ) == 'on' : false;

        $class_name = $instance['class_name'];
        $class_name = ( ! empty( $class_name ) ) ? $class_name : 'product';

        /* before widget */
        echo $args['before_widget'];

        /* title */
        echo $args['before_title'].$title.$args['after_title'];

        /* content */
        $parameters = array(
            'post_type' => 'product',
            'posts_per_page' => $number,
        );
        $products = new WP_Query( $parameters );
        if ( $products->have_posts() ) :
            if ( $show_thumbnail == 'on' ) :
                echo '<div class="entries ' . $class_name . ' show-thumbnail clearfix">';
                while ( $products->have_posts() ) : $products->the_post();
                    ?>
                    <article class="post">
                        <a href="<?php the_permalink(); ?>">
                            <header class="post-header">
                                <h2 class="post-title"><?php the_title(); ?></h2>
                            </header>

                            <div class="post-main">
                                <div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
                            </div>
                        </a>
                    </article>
                    <?php
                endwhile;
                echo '</div>';
            else :
                echo '<div class="entries ' . $class_name . ' clearfix">';
                while ( $products->have_posts() ) : $products->the_post();
                    ?>
                    <article class="post">
                        <header class="post-header">
                            <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        </header>
                    </article>
                    <?php
                endwhile;
                echo '</div>';
            endif;
        endif; wp_reset_postdata();

        /* after widget */
        echo $args['after_widget'];
    }
}

class Product_Categories extends WP_Widget
{
    public function __construct()
    {
        $slug = 'product-categories';
        $name = __( 'Product Categories', PLUGIN_TEXT_DOMAIN );
        $options = array( 'description' => __( 'A list or dropdown of Product Categories.', PLUGIN_TEXT_DOMAIN ) );

        parent::__construct( $slug, $name, $options );
    }

    public function form( $instance )
    {
        $title    = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
        $dropdown = isset( $instance['dropdown'] ) && in_array( strtolower( $instance['dropdown'] ), array( 'on', 'off' ) ) ? strtolower( $instance['dropdown'] ) : '';
        $count    = isset( $instance['count'] ) && in_array( strtolower( $instance['count'] ), array( 'on', 'off' ) ) ? strtolower( $instance['count'] ) : '';
        $hierarchical = isset( $instance['hierarchical'] ) && in_array( strtolower( $instance['hierarchical'] ), array( 'on', 'off' ) ) ? strtolower( $instance['hierarchical'] ) : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', PLUGIN_TEXT_DOMAIN ); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat"/>
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" <?php checked( $dropdown, 'on', true ); ?> class="checkbox"/>
            <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown', PLUGIN_TEXT_DOMAIN ); ?></label><br>

            <input type="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" <?php checked( $count, 'on', true ); ?> class="checkbox"/>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show product counts', PLUGIN_TEXT_DOMAIN ); ?></label><br/>

            <input type="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>" <?php checked( $hierarchical, 'on', true ); ?> class="checkbox"/>
            <label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show hierarchy', PLUGIN_TEXT_DOMAIN ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = array();
        $instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['dropdown'] = ( ! empty( $new_instance['dropdown'] ) ) && in_array( strtolower( $new_instance['dropdown'] ), array( 'on', 'off' ) ) ? 'on' : '';
        $instance['count']    = ( ! empty( $new_instance['count'] ) ) && in_array( strtolower( $new_instance['count'] ), array( 'on', 'off' ) ) ? 'on' : '';
        $instance['hierarchical'] = ( ! empty( $new_instance['hierarchical'] ) ) && in_array( strtolower( $new_instance['hierarchical'] ), array( 'on', 'off' ) ) ? 'on' : '';

        return $instance;
    }

    public function widget( $args, $instance )
    {
        /* options */
        $title = apply_filters( 'widget_title', $instance['title'] );
        $title = ( ! empty( $title ) ) ? $title : $args['widget_name'];

        $dropdown = $instance['dropdown'];
        $dropdown = ( ! empty( $dropdown ) ) ? strtolower( $dropdown ) == 'on' : false;

        $format   = $dropdown ? 'option' : 'html';

        $count = apply_filters( 'widget_count', $instance['count'] );
        $count = ( ! empty( $count ) ) ? strtolower( $count ) == 'on' : false;

        $hierarchical = $instance['hierarchical'];
        $hierarchical = ( ! empty( $hierarchical ) ) ? strtolower( $hierarchical ) == 'on' : false;

        /* before widget */
        echo $args['before_widget'];

        /* title */
        echo $args['before_title'].$title.$args['after_title'];

        /* content */
        if ( $dropdown ) {
            global $wp_query;

            $tax_slug = 'product_category';
            $tax_obj  = get_taxonomy( $tax_slug );
            $tax_name = $tax_obj->labels->singular_name;
            $terms    = get_terms( $tax_slug );

            $parameters = array(
                'show_option_all'    => __( "Select {$tax_name}" ),
                'orderby'            => 'name',
                'show_count'         => $count,
                'hide_empty'         => true,
                'selected'           => $wp_query->query[$tax_slug],
                'hierarchical'       => $hierarchical,
                'name'               => $tax_slug,
                'class'              => 'postform',
                'taxonomy'           => $tax_slug,
                'hide_if_empty'      => true,
                'walker'             => new Custom_Taxonomy_Walker(),
            );

            echo '<form action="'.get_option( 'home' ).'/" method="get">';
            wp_dropdown_categories( $parameters );
            echo '</form>';

            ?><script type="text/javascript">
            /* <![CDATA[ */
                var product_category_dropdown = document.getElementById( "product_category" );
                function onCatChange() {
                    if ( product_category_dropdown.options[product_category_dropdown.selectedIndex].value != 0 ) {
                        location.href = "<?php echo get_option( 'home' );?>/?<?php echo $tax_slug; ?>="+product_category_dropdown.options[product_category_dropdown.selectedIndex].value;
                    }
                }
                product_category_dropdown.onchange = onCatChange;
            /* ]]> */
            </script><?php
        } else {
            $parameters = array(
                'style' => 'list',
                'show_count' => $count,
                'hierarchical' => $hierarchical,
                'title_li' => '',
                'show_option_none' => '',
                'taxonomy' => 'product_category',
            );

            echo '<ul>'; wp_list_categories( $parameters ); echo '</ul>';
        }

        /* after widget */
        echo $args['after_widget'];
    }
}