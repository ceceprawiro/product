<?php if ( ! defined( 'ABSPATH' ) ) die();

if ( ! class_exists( 'Custom_Taxonomy_Walker' ) ) {

class Custom_Taxonomy_Walker extends Walker_CategoryDropdown
{
    public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 )
    {
        /*
        $pad = str_repeat('&nbsp;', $depth * 3);

        $cat_name = apply_filters('list_cats', $object->name, $object);
        $output .= "\t<option class=\"level-$depth\" value=\"".$object->slug."\"";

        if($object->term_id == $args['selected'])
            $output .= ' selected="selected"';

        $output .= '>';
        $output .= $pad.$cat_name;
        $output .= "</option>\n";
        */
        $pad = str_repeat( '&nbsp;', $depth * 3 );
        $cat_name = apply_filters( 'list_cats', $object->name, $object );

        if ( ! isset( $args['value'] ) ) {
            $args['value'] = ( $object->taxonomy != 'category' ? 'slug' : 'id' );
        }

        $value = ( $args['value'] == 'slug' ? $object->slug : $object->term_id );

        $output .= "\t<option class=\"level-$depth\" value=\"".$value."\"";
        if ( $value === (string) $args['selected'] ){
            $output .= ' selected="selected"';
        }

        $output .= '>';
        $output .= $pad.$cat_name;
        if ( $args['show_count'] ){
            $output .= '&nbsp;&nbsp;('. $object->count .')';
        }

        $output .= "</option>\n";
    }

    public function parents( $cat_id, $args )
    {
        $cat = get_term( $cat_id, $args['taxonomy'] );
        $parents[] = $args['value'] == 'slug' ? $cat->slug : $cat->term_id;;
        if ( $cat->parent != 0 )
            $parents = array_merge( $this->parents( $cat->parent, $args ), $parents );

        return $parents;
    }
}

}