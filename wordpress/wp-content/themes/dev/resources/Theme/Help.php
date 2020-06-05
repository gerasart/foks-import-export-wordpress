<?php

namespace Theme;

class Help {

    public static function is_dev() {
        if (WP_ENV == 'development') {
            return true;
        } else {
            return false;
        }
    }

    public static function getPostTypes() {
        $args = array(
            'public' => true
            //            '_builtin' => false
        );

        $post_types = array_values( get_post_types( $args, 'names' ) );
        sort($post_types);
        return $post_types;
    }

    public static function createMultiText($texts) {
        $langs = array_keys( wpm_get_lang_option() );
        $text = '';
        foreach($texts as $lang => $lang_text) {
            $lang = ( is_string($lang) ) ? $lang : $langs[$lang];

            if(empty($lang_text)) continue;

            $text .= '[:'.$lang.']'.$lang_text;
        }
        if(!empty($text)) $text .= '[:]';
        return $text;
    }

    public static function Menu( $location = 'primary_navigation' ) {
        //        primary_navigation
        //        footer-menu
        $menuLocations = get_nav_menu_locations();
        if ( $menuLocations ) {
            $menuID = $menuLocations[ $location ];
            $menu = wp_get_nav_menu_items( $menuID );
            return $menu;
        } else {
            return false;
        }
    }

    public static function str_split_unicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function hierarchy() {
        global $wp_query;
        $templates = (new \Brain\Hierarchy\Hierarchy())->getTemplates($wp_query);
        $templates[] = 'app.php';

        $templates = array_map(function ($template) {
            if ($template === 'index') {
                $template = 'index.php';
            }
            if (strpos($template, '.blade.php')) {
                $template = str_replace('.blade', '', $template);
            }

            $template = ucwords(preg_replace('/\-([a-z])/', ' $1', basename($template)));
            return str_replace(' ', '', $template);
        }, $templates);

        $templates = array_reverse(array_unique($templates));

        $path = get_stylesheet_directory() . '/controllers';
        $path = (has_filter('sober/controller/path') ? apply_filters('sober/controller/path', rtrim($path)) : dirname(get_template_directory()) . '/app/controllers');
//        $path = basename($path);
        $path = str_replace(dirname(get_template_directory()), '', $path);

        $return = '<pre><strong>Hierarchy Debugger:</strong><ul>';
        foreach ($templates as $template) {
            $return .= "<li>" . $path . '/' . $template . "</li>";
        }
        $return .= '</ul></pre>';

        return $return;
    }


    public static function getFileUrl($value) {
        if ( is_numeric($value) ) {
            $file_id = intval($value);

            return wp_get_attachment_url($value);
        } elseif ( is_array($value) ) {
            return $value['url'];
        }

        return $value;
    }

    public static function assetSource( $url = '', $return = 'url' ) {
        $source = [
            'path' => get_theme_file_path() . '/resources/assets/',
            'url'  => get_theme_file_uri() . '/resources/assets/',
        ];

        return $source[ $return ] . $url;
    }

    public static function debug( $var ) {
        echo "<pre>";

        if ( is_array( $var ) ) {
            print_r( $var );
        } else {
            var_dump( $var );
        }

        echo "</pre>";
    }

    public static function getContactForm($form_code)
    {
        $form_id = '';

        if(is_numeric($form_code)) {
            $form_id = intval($form_code);
        } else {
            preg_match('/id\=\"([0-9]*)\"/', $form_code, $matches);

            if(count($matches)) {
                $form_id = array_pop($matches);
            }
        }

        $form = wpcf7_contact_form(intval($form_id));

//        Help::debug($form);
//        Help::debug($form->form_elements());

        return $form->form_html();
    }

    public static function getImageElement($image) {
        if ( is_string($image) ) {
            $image_id = attachment_url_to_postid($image);
            $image = self::getAttachmentData($image_id);
        } elseif ( is_integer($image) ) {
            $image = self::getAttachmentData($image);
        } elseif ( is_array($image) ) {
            $image = $image;
        } elseif ( is_object($image) ) {
            $image = json_decode(json_encode($image), true);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        if ($image['mime_type'] === 'image/svg+xml') {
            $fullpath = get_attached_file($image['ID']);
            $xml_content = file_get_contents($fullpath);

            $dom->loadXML($xml_content);

            return $dom->saveXML();
        } else {
            $img = $dom->appendChild($dom->createElement('img'));
            $img->setAttribute('src', $image['url']);
            $img->setAttribute('alt', $image['alt']);

            return $dom->saveHTML();
        }
    }

    public static function getAttachmentData($attachment_id) {
        $image_data = wp_prepare_attachment_for_js($attachment_id);

        return [
            'ID' => $image_data['id'],
            'mime_type' => $image_data['mime'],
            'alt' => $image_data['alt'],
            'url' => $image_data['url'],
        ];
    }

    public static function getContent($post_id = null) {
        $post = get_post($post_id);
        if ( $post ) {
            $content = apply_filters('the_content', $post->post_content);

            return $content;
        } else {
            return '';
        }
    }

    public static function getExcerpt($post_id, $words = 55) {
        $post = get_post($post_id);
        if ( $post ) {
            $text = !empty($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
            $trim = wp_trim_words($text, $words, '...');
            $content = apply_filters('the_content', $trim);

            return $content;
        } else {
            return '';
        }
    }

    public static function getThumbnail( $post_id ) {
        $image = get_the_post_thumbnail_url( $post_id, 'full' );
//        $placeholder = get_field( 'no_image_horizontal', 'options' );
//
//        if ( !$image && $placeholder ) {
//            $image = $placeholder;
//        }

        return $image;
    }

    public static function getIcon( $filename ) {
        $exp = explode('.', $filename);
        if ( count($exp) === 1 ) {
            $filename = $filename . '.svg';
        }

        $file = self::assetSource( 'icons/' . $filename, 'path' );

        if ( file_exists( $file ) ) {
            $dom = new \DOMDocument( '1.0', 'UTF-8' );
            $xml_content = file_get_contents( $file );
            $dom->loadXML( $xml_content );

            return $dom->saveXML();
        }

        return '';
    }

}
