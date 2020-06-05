<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 13.09.18
 * Time: 11:03
 */

namespace Theme;

class ChangeUrl {

    static $filters;

    public function __construct() {
        if ( function_exists( 'ssl_insecure_content_fix_url' ) ) {
            self::setFilters();

            if ( preg_match('/^wp\.docker\.localhost/', $_SERVER['HTTP_HOST']) ) {
                return;
            }


            add_filter( 'rest_url', 'ssl_insecure_content_fix_url' );
            add_filter( 'home_url', 'ssl_insecure_content_fix_url' );


            foreach (self::$filters as $filter) {
                add_filter( $filter, array(__CLASS__, 'sslFix') );
            }

            add_filter( 'set_url_scheme', array(__CLASS__, 'fixUrlSheme') );
        }
    }

    public static function setFilters() {
        self::$filters = [
            'theme_file_uri',
            'stylesheet_directory_uri',
            'admin_url',
            'plugins_url',
            'content_url',
            'includes_url',
            'wp_get_attachment_url',
            'wp_get_attachment_thumb_url',
        ];
    }

    public static function sslFix($url) {
        return str_replace('http:', 'https:', $url);
    }

    public static function fixUrlSheme($url) {
        $parts = explode('.', $url);
        $formats = array('css', 'js', 'jpg', 'jpeg', 'png', 'gif');
        $last = array_pop($parts);

        if ( in_array($last, $formats) ) {
            return self::sslFix($url);
        }

        return $url;
    }

}
