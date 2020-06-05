<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 05.10.18
 * Time: 16:07
 */

namespace Theme;

use function App\asset_path as asset_path;
use function App\config;

class SetupTheme {

    static $front_vars = [];
    static $footer_vars = [];
    static $admin_vars = [];

    public static function init() {
        self::setupControllers();
        self::register();
        self::registerWhoops();

        add_action( 'admin_head', array( __CLASS__, 'localAdminVars' ) );
        add_action( 'wp_head', array( __CLASS__, 'localFrontVars' ) );
        add_action( 'wp_footer', array( __CLASS__, 'localFooterVars' ) );

        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'themeAssets' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'adminAssets' ) );

        // Allow svg upload
        add_filter( 'upload_mimes', array(__CLASS__, 'additionalMimeTypes') );
        add_image_size( 'icon', 50, 50 );

        // Fix for Breadcrumb NavXT
        add_filter('bcn_breadcrumb_title', array(__CLASS__, 'breadcrumbTitle'), 3, 10);

        // Fix term metadata notice-error
        add_filter( 'get_term_metadata', array(__CLASS__, 'fixTermMetadata'), 10, 4 );

        // Fix for Font Awesome icons in admin menu
        add_filter('sanitize_html_class', array(__CLASS__, 'adminClassFilter'), 10, 2);

        // Important SSL fix for rest url on prod site
        if (function_exists('ssl_insecure_content_fix_url')) {
            add_filter('rest_url', 'ssl_insecure_content_fix_url');
            add_filter('wpm_get_original_home_url', 'ssl_insecure_content_fix_url');
        }

        // Set new folder for vc_templates
        if ( function_exists('vc_set_shortcodes_templates_dir') ) {
            $path = config( 'view.paths' )[0];
            vc_set_shortcodes_templates_dir( $path . '/vc_templates' );
        }
    }

    public static function registerWhoops() {
        if ( WP_DEBUG && class_exists( 'Whoops\\Run' ) && !is_admin() ) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler( new \Whoops\Handler\PrettyPageHandler );
            $whoops->register();
        }
    }

    public static function fixTermMetadata($value, $object_id, $meta_key, $single) {
        if (  empty($meta_key) && $single && !isset($value[0])) {
            return null;
        }

        return $value;
    }

    public static function adminClassFilter($sanitized, $class) {
        if ( 0 === strpos( $class, 'dashicons-' ) ) {
            return $class;
        } else {
            return $sanitized;
        }
    }

    public static function localAdminVars() {
        $filter_vars = apply_filters( 'admin_vars', self::$admin_vars );

        echo "<script>";
        foreach ( $filter_vars as $key => $value ) {
            if ( is_array( $value ) || is_object($value) ) {
                $value = json_encode( $value, JSON_UNESCAPED_UNICODE );
            } elseif ( is_string( $value ) ) {
                $value = "'$value'";
            }

            echo "window.{$key} = {$value};" . "\n";
        }
        echo "</script>";
    }

    public static function localFrontVars() {
        $filter_vars = apply_filters( 'locale_vars', self::$front_vars );

        foreach ( $filter_vars as $key => $value ) {
            echo "<script>";
            if ( !is_string( $value ) ) {
                $value = json_encode( $value, JSON_UNESCAPED_UNICODE );
            } elseif ( is_string( $value ) ) {
                $value = "'$value'";
            }

            echo "window.{$key} = {$value};" . "\n";
            echo "</script>";
        }
    }

    public static function localFooterVars() {
        $filter_vars = apply_filters( 'footer_vars', self::$footer_vars );

        foreach ( $filter_vars as $key => $value ) {
            echo "<script>";
            if ( is_array( $value ) || is_object($value) ) {
                $value = json_encode( $value, JSON_UNESCAPED_UNICODE );
            } elseif ( is_string( $value ) ) {
                $value = "'$value'";
            }

            echo "window.{$key} = {$value};" . "\n";
            echo "</script>";
        }
    }

    public static function register() {
        register_nav_menus([
            'footer-menu' => __('Footer menu', 'Theme')
        ]);
    }

    public static function init_widgets() {
        $pages_path = plugin_dir_path( __FILE__ ) . 'widgets/';
        foreach ( glob( $pages_path . '*.*' ) as $file ) {
            include_once($pages_path . basename( $file ));
            $file_name = basename( $file );
            $class_name = explode( '.', $file_name )[0];

            if ( class_exists( $class_name ) ) {
                register_widget( $class_name );
            }
        }
    }

    public static function setupControllers() {
        /**
         * Change folder connect controllers
         */
        add_filter('sober/controller/path', function () {
            return dirname(get_template_directory()) . '/resources/Controllers';
        });
        add_filter('sober/controller/namespace', function () {
            return 'Controllers';
        });
    }

    public static function localePath() {
        $path = [];

        $path['dist'] = config( 'assets' );
        $path['theme'] = config( 'theme' );
        $path['assets'] = [
            'uri'  => config('assets.sources.uri'),
            'path' => config('assets.sources.path'),
        ];

        wp_enqueue_script( 'jquery' );
        wp_localize_script( 'jquery', 'paths', $path );
    }

    public static function themeAssets() {
        $ver = microtime( 1 );

        $site_options = array(
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'siteUrl'      => get_site_url(),
            'siteHttpRoot' => $_SERVER['HTTP_HOST']
        );

        self::localePath();

//        wp_enqueue_script( 'sage/vue', asset_path( 'scripts/vue-front.js' ), [ 'jquery' ], $ver, true );
//        wp_enqueue_style( 'sage/vue', asset_path( 'styles/vue-front.css' ), false, $ver );

//        wp_enqueue_script( 'sage/main.js', asset_path( 'scripts/main.js' ), [ 'jquery' ], $ver, true );
//        wp_enqueue_style( 'sage/main.css', asset_path( 'styles/main.css' ), false, $ver );
    }

    public static function adminAssets() {
        $ver = microtime( 1 );

        wp_enqueue_style( 'admin/fontawesome', '//use.fontawesome.com/releases/v5.12.1/css/all.css', false, null );

        wp_enqueue_style( 'sage/admin', asset_path( 'styles/admin.css' ), false, $ver );
        wp_enqueue_script( 'sage/admin', asset_path( 'scripts/admin.js' ), [ 'jquery' ], $ver, true );

//        wp_enqueue_script( 'sage/vue', asset_path( 'scripts/vue-admin.js' ), [ 'jquery' ], $ver, true );
//        wp_enqueue_style( 'sage/vue', asset_path( 'styles/vue-admin.css' ), false, $ver );
    }

    public static function additionalMimeTypes( $mimes ) {
        $mimes['rar'] = 'application/x-rar-compressed';
        $mimes['svg'] = 'image/svg+xml';

        return $mimes;
    }

    public static function breadcrumbTitle($title, $type, $id) {
        if(in_array('home', $type)) {
            $front_id = get_option( 'page_on_front' );
            $title = get_the_title($front_id);
        }

        return $title;
    }

}
