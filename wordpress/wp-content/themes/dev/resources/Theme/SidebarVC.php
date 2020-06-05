<?php


namespace Theme;


use function App\template;

class SidebarVC {
    public $shortcode;

    public function __construct() {
        $this->shortcode = 'vc_sidebar';

        if ( function_exists('vc_map_get_attributes') ) {
            add_action( 'init', array( $this, 'cptui_register_vc_sidebars' ) );

            add_shortcode( 'vc_sidebar', array( $this, 'get_template' ) );
        }
    }

    public function get_template( $atts ) {
        $atts = vc_map_get_attributes( $this->shortcode, $atts );

        $css_class    = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ) );
        $post_sidebar = get_post( $atts['sidebar'] );
        if ( empty( $post_sidebar ) || $atts['sidebar'] == '0' ) {
            return;
        }

        return template( 'vc_templates.vc_sidebar', [
            'sidebar'      => $atts['sidebar'],
            'post_sidebar' => $post_sidebar,
            'css_class'    => $css_class,
        ] );
    }

    public function cptui_register_vc_sidebars() {
        /**
         * Post Type: VC Sidebar.
         */

        $labels = array(
            "name"          => __( "VC Sidebar" ),
            "singular_name" => __( "VC Sidebars" ),
        );

        $args = array(
            "label"                 => __( "VC Sidebar" ),
            "labels"                => $labels,
            "description"           => "",
            "public"                => true,
            "publicly_queryable"    => true,
            "show_ui"               => true,
            "delete_with_user"      => false,
            "show_in_rest"          => true,
            "rest_base"             => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive"           => false,
            "show_in_menu"          => true,
            "show_in_nav_menus"     => true,
            "exclude_from_search"   => true,
            "capability_type"       => "post",
            "map_meta_cap"          => true,
            "hierarchical"          => false,
            "rewrite"               => array( "slug" => "vc_sidebars", "with_front" => true ),
            "query_var"             => true,
            "menu_position"         => 30,
            "menu_icon"             => "dashicons-schedule",
            "supports"              => array( "title", "editor" ),
        );

        register_post_type( "vc_sidebars", $args );


        self::addSupport();
        self::addWidget();
    }

    public static function addSupport() {
        if ( function_exists( 'vc_default_editor_post_types' ) ) {
            $post_types   = vc_default_editor_post_types();
            $post_types[] = 'vc_sidebars';
            vc_set_default_editor_post_types( $post_types );
        }
    }

    public static function addWidget() {
        if ( function_exists( 'vc_map' ) ) {
            $sidebars = self::getSidebars();

            vc_map( array(
                'name'     => esc_html__( 'VC Sidebar' ),
                'base'     => 'vc_sidebar',
                'category' => esc_html__( 'CH' ),
                'params'   => array(
                    array(
                        'type'        => 'dropdown',
                        'heading'     => esc_html__( 'Sidebar' ),
                        'param_name'  => 'sidebar',
                        'admin_label' => true,
                        'value'       => $sidebars
                    ),
                    array(
                        'type'       => 'css_editor',
                        'heading'    => esc_html__( 'Css' ),
                        'param_name' => 'css',
                        'group'      => esc_html__( 'Design options' )
                    )
                )
            ) );
        }
    }

    public static function getSidebars() {
        $args = [
            'post_type'      => 'vc_sidebars',
            'posts_per_page' => - 1
        ];

        $stm_sidebars_array = get_posts( $args );
        $stm_sidebars       = array( esc_html__( 'Select' ) => 0 );

        if ( $stm_sidebars_array && ! is_wp_error( $stm_sidebars_array ) ) {
            foreach ( $stm_sidebars_array as $val ) {
                $stm_sidebars[ get_the_title( $val ) ] = $val->ID;
            }
        }

        return $stm_sidebars;
    }

}
