<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 12/18/2018
 * Time: 12:29 PM
 * Description: This Class give you manipulate all elements from your wp menu. We can extend this class
 * Example: [custom_menu location=primary_navigation]
 */


namespace Theme;


class CustomMenu extends \Walker_Nav_Menu {

    public $shortcode_name = 'custom_menu';


    public function __construct() {
        add_shortcode( $this->shortcode_name, array( __CLASS__, 'custom_menu' ) );
    }

    public static function custom_menu( $atts ) {
        $params = shortcode_atts( array(
            'location' => 'primary_navigation',
        ), $atts );

        return wp_nav_menu( array(
            'items_wrap'     => '<ul>%3$s</ul>',
            'theme_location' => $params['location'],
            'menu'           => 'Main Navigation',
            'container_id'   => 'svitsoft-menu',
            'walker'         => new CustomMenu
        ) );
    }

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul>\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $default = [
            'container'       => 'div',
            'container_class' => '',
            'container_id'    => 'svitsoft-menu',
            'menu'            => 'Main Navigation',
            'menu_id'         => '',
            'menu_class'      => 'header__menu',
            'item_class'      => 'header__menu-item',
            'active_class'    => 'active',
            'items_wrap'     => '<ul>%3$s</ul>',
            'theme_location' => 'primary_navigation',
        ];

        $args = (object) array_merge( $default, (array) $args );

        $indent = ($depth) ? str_repeat( "\t", $depth ) : '';
        $classes = empty( $item->classes ) ? array() : (array)$item->classes;

        /* Add active class */
        $classes[] = $args->item_class;
        if ( in_array( 'current-menu-item', $classes ) ) {
            $classes[] = $args->active_class;
            unset( $classes['current-menu-item'] );
        }

        /* Check for children */
        $children = get_posts( array(
            'post_type'   => 'nav_menu_item',
            'nopaging'    => true,
            'numberposts' => 1,
            'meta_key'    => '_menu_item_menu_item_parent',
            'meta_value'  => $item->ID
        ) );
        if ( !empty( $children ) ) {
            $classes[] = 'has-children';
        }

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );

        $id = $id ? $item->ID : '';
        $classes[] = "li-item_id{$id}";

        $class = implode(' ', $classes);
        $output .= $indent . "<li class='{$class}'>";

        $attributes = !empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
        $attributes .= !empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
        $attributes .= !empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
        $attributes .= !empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
        $attributes .= 'class="header__link"';

        $item_output = $args->before;
        $item_output .= "<a {$attributes} ><span>";
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= "</span></a>";
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }


}
