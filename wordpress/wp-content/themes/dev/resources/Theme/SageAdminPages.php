<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 14.09.18
 * Time: 15:06
 */

namespace Theme;

use HaydenPierce\ClassFinder\ClassFinder;

class SageAdminPages {

    private static $page_title;

    private static $page_subtitle;

    static $page_slug;

    public static function init() {
        self::$page_title    = __( 'Обновление', 'Theme' );
        self::$page_subtitle = __( 'Обновление', 'Theme' );
        self::$page_slug = self::createPageSlug(get_class());


        add_action('admin_menu', array(__CLASS__, 'addMainPage'));

        self::registerSubpage();
    }

    public static function createPageTitle($class) {
        $true_name = self::getTrueClassName($class);
        $lower = ucwords(preg_replace('/[A-Z]/', ' $0', $true_name));

        return substr($lower, 1);
    }

    public static function createPageSlug($class) {
        $true_name = self::getTrueClassName($class);
        $lower = strtolower(preg_replace('/[A-Z]/', '_$0', $true_name));

        return substr($lower, 1);
    }

    public static function getTrueClassName( $class = null ) {
        $reflect = new \ReflectionClass( $class );

        return $reflect->getShortName();
    }

    public static function addMainPage() {
        add_menu_page(self::$page_title, self::$page_title, 'edit_posts', self::$page_slug, null, 'dashicons-upload');
    }

    public static function registerSubpage() {
//        $pages_path = plugin_dir_path(__FILE__) . get_class() . '/';
//        foreach (glob($pages_path . '*.*') as $file) {
//            include_once ( $pages_path . basename($file) );
//            $name = explode('.', basename($file))[0];
//            $this->{$name} = new $name($this);
//        }

        $classes = ClassFinder::getClassesInNamespace(__NAMESPACE__ . '\admin');
        foreach($classes as $class) {
            new $class();
        }
    }

    public static function renderView( $file, $args = array() ) {
        $fullpath = 'classes/admin/templates/' . $file;

//        if ( file_exists( $fullpath ) ) {
        $templates[] = "{$fullpath}.php";
//        var_dump(locate_template( $templates ));
//        var_dump(locate_template( $templates ));
//            self::getTemplatePart($fullpath, $args);
            Help::render_template_part($fullpath, null, $args);
//        }
    }

    public static function adminBrowserSync() {
        self::renderView( 'browser_sync.php' );
    }
    
    public static function getTemplateName($class) {
        $short = self::getTrueClassName($class);
        $name = strtolower( preg_replace("/([A-Z])/", "-$1", $short) );

        $template = substr($name, 1);
        return $template;
    }

    public static function getTemplatePart($file, $template_args = array(), $cache_args = array()) {
        $template_args = wp_parse_args( $template_args );
        $cache_args = wp_parse_args( $cache_args );
        if ( $cache_args ) {
            foreach ( $template_args as $key => $value ) {
                if ( is_scalar( $value ) || is_array( $value ) ) {
                    $cache_args[$key] = $value;
                } else if ( is_object( $value ) && method_exists( $value, 'get_id' ) ) {
                    $cache_args[$key] = call_user_func( 'get_id', $value );
                }
            }
            if ( ( $cache = wp_cache_get( $file, serialize( $cache_args ) ) ) !== false ) {
                if ( ! empty( $template_args['return'] ) )
                    return $cache;
                echo $cache;
                return;
            }
        }

//        $file_handle = $file;
//        do_action( 'start_operation', 'hm_template_part::' . $file_handle );

//        if ( file_exists( get_stylesheet_directory() . '/' . $file . '.php' ) )
//            $file = get_stylesheet_directory() . '/' . $file . '.php';
//        elseif ( file_exists( get_template_directory() . '/' . $file . '.php' ) )
//            $file = get_template_directory() . '/' . $file . '.php';

        ob_start();
        $return = require( $file );
        $data = ob_get_clean();

//        do_action( 'end_operation', 'hm_template_part::' . $file_handle );

        if ( $cache_args ) {
            wp_cache_set( $file, $data, serialize( $cache_args ), 3600 );
        }

        if ( ! empty( $template_args['return'] ) )
            if ( $return === false )
                return false;
            else
                return $data;

        echo $data;
    }

}
