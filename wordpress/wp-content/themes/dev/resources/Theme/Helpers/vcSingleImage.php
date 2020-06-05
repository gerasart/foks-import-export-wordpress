<?php

namespace Theme\Helpers;

use Theme\Help;

//VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Single_image' );

class vcSingleImage {

    static $image_id = 0;
    static $atts = [];
    static $elements = [
        'img' => false,
        'a' => false,
        'caption' => false,
    ];

    function __construct( $settings = [] ) {
//        parent::__construct( $settings );
    }

    public static function getElementsAttr($atts) {
        self::$atts = $atts;
        self::$elements['img'] = self::getImage();
        self::$elements['a'] = self::getLink();
        self::$elements['caption'] = self::getCaption();

        return self::$elements;
    }

    public static function getImage($atts = false) {
        $atts = $atts ? $atts : self::$atts;
        $default_src = vc_asset_url( 'vc/no_image.png' );
        $post_id = get_the_ID();
        $custom_src = false;

        $img = [];
        $img_id = $atts['image'];
        $img_size = $atts['img_size'];


        if ( !$img_size ) {
            $img_size = 'medium';
        }
        switch ( $atts['source'] ) {
            case 'featured_image':
                if ( $post_id && has_post_thumbnail( $post_id ) ) {
                    $img_id = get_post_thumbnail_id( $post_id );
                }
            case 'media_library':
                $img = wpb_getImageBySize( array(
                    'attach_id' => $img_id,
                    'thumb_size' => $img_size,
                    'class' => 'vc_single_image-img',
                ) );

                break;

            case 'external_link':
//                $style = $atts['external_style'];;
//                $border_color = $atts['external_border_color'];;

                $dimensions = vcExtractDimensions( $atts['external_img_size'] );
                $hwstring = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : '';

                $custom_src = $atts['custom_src'] ? esc_attr( $atts['custom_src'] ) : $default_src;

                $img['thumbnail'] = '<img class="vc_single_image-img" ' . $hwstring . ' src="' . $custom_src . '" />';

                break;

            default:
                $img['thumbnail'] = '<img class="vc_img-placeholder vc_single_image-img" src="' . $default_src . '" />';
        }

        // Parse attributes
        if(empty($img['thumbnail'])) {
            $thumb = wp_get_attachment_image($img_id, $img_size);
        } else {
            $thumb = $img['thumbnail'];
        }

        $image = self::getHtmlAttributes($thumb);
        $image['data-full'] = $custom_src ? $custom_src : wp_get_attachment_image_src( $img_id, 'full' )[0];

        if ( !empty($atts['image_alt']) ) {
            $image['alt'] = $atts['image_alt'];
        }

        return $image;
    }

    public static function getLink($atts = false) {
        $atts = $atts ? $atts : self::$atts;
        $a_attrs = [];
        $link = '';

        switch ( $atts['onclick'] ) {
            case 'link_image':
                wp_enqueue_script( 'prettyphoto' );
                wp_enqueue_style( 'prettyphoto' );

                $a_attrs['class'] = 'prettyphoto';
                $a_attrs['data-rel'] = 'prettyPhoto[rel-' . get_the_ID() . '-' . rand() . ']';
            // backward compatibility
//                if ( vc_has_class( 'prettyphoto', $atts['el_class'] ) ) {
//                    // $link is already defined
//                } elseif ( 'external_link' === $atts['source'] ) {
//                    $link = $atts['custom_src'];
//                } else {
//                    $link = wp_get_attachment_image_src( $atts['img_id'], 'full' )[0];
//                }

            case 'img_link_large':
                $link = self::$elements['img']['data-full'];

                break;

            case 'zoom':
                wp_enqueue_script( 'vc_image_zoom' );

                self::$elements['img']['data-vc-zoom'] = self::$elements['img']['data-full'];

                break;

            default:
                $link = $atts['link'];

                break;
        }

        $a_attrs['href'] = $link;
        $a_attrs['target'] = $atts['img_link_target'];

        return $a_attrs;
    }

    public static function getHtmlAttributes($html) {
        $attributes = [];

        if (!empty($html)) {
            $attributes = current((array) new \SimpleXMLElement($html));
        }

        return $attributes;
    }

    public static function getCaption($atts = false) {
        $atts = $atts ? $atts : self::$atts;
        if ( $atts['add_caption'] === 'yes' && empty($atts['image_caption'])) {
            $img_id = $atts['image'];
            $post = get_post( $img_id );
            return wpm_translate_string($post->post_excerpt);
        } else {
            return $atts['image_caption'];
        }
    }

}
