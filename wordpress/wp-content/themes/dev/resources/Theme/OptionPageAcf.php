<?php

namespace Theme;

use Theme\Traits\AjaxHelper;

class OptionPageAcf {

    use AjaxHelper;

    static $translate_enabled = false;

    public function __construct() {

        if ( function_exists( 'acf_add_options_page' ) ) {

            // Theme settings
            acf_add_options_page( array(
                'page_title' => __( 'Theme Settings', 'Theme' ),
                'menu_title' => __( 'Theme Settings', 'Theme' ),
                'menu_slug'  => 'theme-settings',
                'capability' => 'edit_posts',
                'position'   => 40,
                'redirect'   => false,
            ) );

            if ( function_exists( 'wpm_translate_string' ) ) {
                self::$translate_enabled = true;

                self::addTranslationPage();

                self::declaration_ajax();
            }
        }
    }

    public static function addTranslationPage() {
        acf_add_options_sub_page( array(
            'page_title'  => __( 'Translations', 'Theme' ),
            'menu_slug'   => 'translation',
            'parent_slug' => 'theme-settings',
        ) );

        self::addFields();
        self::addNewTranslations();

        add_action( 'wp_footer', [ __CLASS__, 'localizeTranslations' ] );
    }

    public static function localizeTranslations() {
        $field = get_field( 'translates', 'options' );

//        $translations = [];
//        foreach ( $field as $row ) {
//            $translations[ $row['slug'] ] = $row['title'];
//        }

        $value = json_encode( $field, JSON_UNESCAPED_UNICODE );

        echo "<script>";
        echo "window.translations = {$value};";
        echo "</script>";
    }

    public static function addNewTranslations() {
        if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'translation' ) {
            $new_translation = get_field( 'new_translation', 'options' );

            if ( $new_translation ) {
                foreach ( $new_translation as $row ) {
                    $isset = self::checkExistTranslation( $row['slug'] );
                    if ( !$isset ) {
                        add_row( 'translates', $row, 'options' );
                    }
                }

                update_field( 'new_translation', [], 'options' );
            }
        }
    }

    public static function checkExistTranslation( $slug, $name = 'translates' ) {
        $labels = get_field( $name, 'options' );
        $value = false;

        if ( $labels && is_array( $labels ) ) {
            foreach ( $labels as $key => $label ) {
                if ( $label['slug'] === $slug ) {
                    $value = $label['title'];
                }
            }
        }

        return $value;
    }

    /**
     * @param $slug
     * Translate
     *
     * @return bool|string
     */
    public static function translate( $slug ) {
        $replace   = str_replace( '_', ' ', $slug );
        $value     = ucfirst( $replace );
        $new_value = false;

        $key = strtolower( str_replace(' ', '_', $slug) );

        if ( function_exists( 'get_field' ) && self::$translate_enabled ) {
            $new_value = self::checkExistTranslation( $key );

            if ( ! $new_value ) {
                $field_name    = 'new_translation';
                $new_translate = self::checkExistTranslation( $key, $field_name );

                if ( ! $new_translate ) {
                    $row = [
                        'slug'  => $key,
                        'title' => $value
                    ];

                    add_row( $field_name, $row, 'options' );
                }
            }
        }

        return ( $new_value ) ? $new_value : $value;
    }

    public static function ajax_nopriv_addNewTraslation() {
        $slug = self::getPostVar('slug');

        $translation = self::translate($slug);

        wp_send_json_success(['translation' => $translation]);
    }

    public static function addFields() {
        if ( function_exists( 'acf_add_local_field_group' ) ):

            acf_add_local_field_group( array(
                'key'                   => 'group_5bdac7b37853d',
                'title'                 => 'Translations',
                'fields'                => array(
                    array(
                        'key'               => 'field_5bdac7c29bc18',
                        'label'             => 'Current',
                        'name'              => '',
                        'type'              => 'tab',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'placement'         => 'top',
                        'endpoint'          => 0,
                    ),
                    array(
                        'key'               => 'field_5bdac7ee9bc1a',
                        'label'             => 'Translation',
                        'name'              => 'translates',
                        'type'              => 'repeater',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'collapsed'         => '',
                        'min'               => 0,
                        'max'               => 0,
                        'layout'            => 'table',
                        'button_label'      => '',
                        'sub_fields'        => array(
                            array(
                                'key'                  => 'field_5bdac7fa9bc1b',
                                'label'                => 'Slug',
                                'name'                 => 'slug',
                                'type'                 => 'text',
                                'instructions'         => '',
                                'required'             => 0,
                                'conditional_logic'    => 0,
                                'wrapper'              => array(
                                    'width' => '',
                                    'class' => '',
                                    'id'    => '',
                                ),
                                'default_value'        => '',
                                'placeholder'          => '',
                                'prepend'              => '',
                                'append'               => '',
                                'maxlength'            => '',
                                'disallow_translate'   => 1,
                                'show_column'          => 0,
                                'show_column_sortable' => 0,
                                'show_column_weight'   => 1000,
                                'allow_quickedit'      => 0,
                                'allow_bulkedit'       => 0,
                            ),
                            array(
                                'key'                  => 'field_5bdac8139bc1c',
                                'label'                => 'Title',
                                'name'                 => 'title',
                                'type'                 => 'text',
                                'instructions'         => '',
                                'required'             => 0,
                                'conditional_logic'    => 0,
                                'wrapper'              => array(
                                    'width' => '',
                                    'class' => '',
                                    'id'    => '',
                                ),
                                'default_value'        => '',
                                'placeholder'          => '',
                                'prepend'              => '',
                                'append'               => '',
                                'maxlength'            => '',
                                'disallow_translate'   => 0,
                                'show_column'          => 0,
                                'show_column_sortable' => 0,
                                'show_column_weight'   => 1000,
                                'allow_quickedit'      => 0,
                                'allow_bulkedit'       => 0,
                            ),
                        ),
                    ),
                    array(
                        'key'               => 'field_5bdac7dc9bc19',
                        'label'             => 'New',
                        'name'              => '',
                        'type'              => 'tab',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'placement'         => 'top',
                        'endpoint'          => 0,
                    ),
                    array(
                        'key'               => 'field_5bdac8289bc1d',
                        'label'             => 'New translation',
                        'name'              => 'new_translation',
                        'type'              => 'repeater',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'collapsed'         => '',
                        'min'               => 0,
                        'max'               => 0,
                        'layout'            => 'table',
                        'button_label'      => '',
                        'sub_fields'        => array(
                            array(
                                'key'                  => 'field_5bdac8289bc1e',
                                'label'                => 'Slug',
                                'name'                 => 'slug',
                                'type'                 => 'text',
                                'instructions'         => '',
                                'required'             => 0,
                                'conditional_logic'    => 0,
                                'wrapper'              => array(
                                    'width' => '',
                                    'class' => '',
                                    'id'    => '',
                                ),
                                'default_value'        => '',
                                'placeholder'          => '',
                                'prepend'              => '',
                                'append'               => '',
                                'maxlength'            => '',
                                'disallow_translate'   => 1,
                                'show_column'          => 0,
                                'show_column_sortable' => 0,
                                'show_column_weight'   => 1000,
                                'allow_quickedit'      => 0,
                                'allow_bulkedit'       => 0,
                            ),
                            array(
                                'key'                  => 'field_5bdac8289bc1f',
                                'label'                => 'Title',
                                'name'                 => 'title',
                                'type'                 => 'text',
                                'instructions'         => '',
                                'required'             => 0,
                                'conditional_logic'    => 0,
                                'wrapper'              => array(
                                    'width' => '',
                                    'class' => '',
                                    'id'    => '',
                                ),
                                'default_value'        => '',
                                'placeholder'          => '',
                                'prepend'              => '',
                                'append'               => '',
                                'maxlength'            => '',
                                'disallow_translate'   => 0,
                                'show_column'          => 0,
                                'show_column_sortable' => 0,
                                'show_column_weight'   => 1000,
                                'allow_quickedit'      => 0,
                                'allow_bulkedit'       => 0,
                            ),
                        ),
                    ),
                ),
                'location'              => array(
                    array(
                        array(
                            'param'    => 'options_page',
                            'operator' => '==',
                            'value'    => 'translation',
                        ),
                    ),
                ),
                'menu_order'            => 0,
                'position'              => 'normal',
                'style'                 => 'default',
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen'        => '',
                'active'                => 1,
                'description'           => '',
            ) );

        endif;
    }
}
