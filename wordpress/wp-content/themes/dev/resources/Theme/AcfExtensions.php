<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 15.06.18
 * Time: 13:56
 */

namespace Theme;


class AcfExtensions {

    public function __construct() {
//        add_filter('acf/settings/google_api_key', array(__CLASS__, 'getMapKey'));

        add_filter('acf/load_field/type=select', array ( __CLASS__, 'field_choices' ));

        add_action( 'acf/render_field/type=file', array(__CLASS__, 'videoPreview'), 10, 1 );

        // Fix for empty meta of object_id = 0
        add_filter('acf/pre_load_meta', array(__CLASS__, 'fixEmptyMeta'), 10, 2);
    }


    public static function fixEmptyMeta($value, $object_id) {
        if ( !isset($object_id) || !$object_id ) {
            return [];
        }

        return $value;
    }

    public static function debug($arr) {
        echo '<pre>';
        var_dump( $arr );
        echo '</pre>';
    }

    public static function videoPreview($field) {
        $classes = explode(' ', $field["wrapper"]["class"]);
        $value = $field['value'];

        if ( in_array('video', $classes) && !empty($value) ) {
            $file_url = Help::getFileUrl($value);

            $video = wp_video_shortcode( [
               'src' => $file_url,
               'width' => 400,
               'height' => 230,
            ] );

            echo '<div style="margin-top: 30px;">'. $video . '</div>';
        }
    }

    public static function getMapKey() {
//        return get_field('google_map_key', 'options');
    }

    /**
     * Load options from contact field
     * @param $field
     * @return mixed
     */
    public static function field_choices($field) {
//        self::debug($field);
        $field['choices'] = [''];

        $args = [
            'post_type' => 'wpcf7_contact_form',
            'posts_per_page' => -1,
        ];
        $forms = get_posts($args);

        if ( $field['wrapper']['class'] === 'contact_form' ) {
            foreach ($forms as $form) {
                $field['choices'][$form->ID] = $form->post_title;
            }
        }

        return $field;
    }

}
