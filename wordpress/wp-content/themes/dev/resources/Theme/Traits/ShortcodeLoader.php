<?php


namespace Theme\Traits;


trait ShortcodeLoader {

    static $shortcode_dir = 'vc_templates';
    static $prefix = 'shortcode_';
    static $short;


    public static function declaration_shortcodes() {
        $class_methods = get_class_methods( get_called_class() );

        foreach ( $class_methods as $name ) {
            $pos = strpos( $name, self::$prefix );
            $short = str_replace( self::$prefix, '', $name );

            if ( $pos === 0 ) {
                self::$short = $short;
                add_shortcode( self::$short, array( get_called_class(), $name ) );
            }
        }
    }

    public static function getFullPath($name = '') {
        $dir = self::$shortcode_dir;
        $file = str_replace( self::$prefix, '', $name );

        return get_stylesheet_directory() . "/{$dir}/{$file}";
    }

    public static function getShortcodeName($name = '') {
        return str_replace( self::$prefix, '', $name );
    }
}
