<?php


namespace Controllers\Traits;


use Illuminate\Support\Facades\Route;
use Sober\Controller\Utils;
use Spatie\Ssr\Engines\V8;
use Spatie\Ssr\Renderer;
use Theme\Help;
use Theme\SetupTheme;

trait SelfData {

    static $class;
    static $methods;
    static $excludeFunctions = [ '__construct', '__before', '__after', 'xhtml', 'browserSync' ];
    static $dataSelf = [];
    static $dataMethods;
    static $staticMethods;

    public static function getSelfData() {
//        var_dump(self::class);
        self::$class = new \ReflectionClass( self::class );

        // Get all public methods from class
        self::$methods = self::$class->getMethods( \ReflectionMethod::IS_PUBLIC );

        // Remove __contruct, __init, __finalize and this class methods from self::$methods
        self::$methods = array_filter( self::$methods, function ( $method ) {
            return
                $method->class !== 'Sober\Controller\Controller' &&
                $method->name !== '__construct' &&
                $method->name !== '__before' &&
                $method->name !== '__after' &&
                $method->name !== 'xhtml';
        } );

        // Get all public static methods from class
        self::$staticMethods = self::$class->getMethods( \ReflectionMethod::IS_STATIC );

        // Remove self::$staticMethods from self::$methods using array_diff
        self::$dataMethods = array_diff( self::$methods, self::$staticMethods );

        // Filter the remaining data methods
        self::$dataMethods = array_filter( self::$dataMethods, function ( $method ) {
            return $method = $method->name;
        } );

//        // For each method convert method name to snake case and add to data[key => value]
        foreach ( self::$dataMethods as $method ) {
//            // Convert method name to snake case
            $var = Utils::convertToSnakeCase( $method->name );
            $myself = new self();

//            var_dump($method);

            // Add var method name to data[]
            self::$dataSelf[ $var ] = $myself->{$method->name}();
        }

        return self::$dataSelf;
    }

    public static function getPageData( $controller, $post_id = false ) {
        global $post;
        if ( !class_exists( $controller ) ) {
            return [];
        }

        if ( $post_id ) {
            $post_query = new \WP_Query( [ 'post__in' => [$post_id] ] );
            if ( $post_query->have_posts() ) {
                while ( $post_query->have_posts() ) {
                    $post_query->the_post();
                }
            }
            $temp_post = $post;
            $post = get_post($post_id);
//            setup_postdata( $post );
//            the_post();
        }

        self::$class = new \ReflectionClass( $controller );

        // Get all public methods from class
        self::$methods = self::$class->getMethods( \ReflectionMethod::IS_PUBLIC );

        // Remove __contruct, __init, __finalize and this class methods from self::$methods
        self::$methods = array_filter( self::$methods, function ( $method ) {
            return
                $method->class !== 'Sober\Controller\Controller' &&
                $method->name !== '__construct' &&
                $method->name !== '__before' &&
                $method->name !== '__after' &&
                $method->name !== 'xhtml';
        } );

        // Get all public static methods from class
        self::$staticMethods = self::$class->getMethods( \ReflectionMethod::IS_STATIC );

        // Remove self::$staticMethods from self::$methods using array_diff
        self::$dataMethods = array_diff( self::$methods, self::$staticMethods );

        // Filter the remaining data methods
        self::$dataMethods = array_filter( self::$dataMethods, function ( $method ) {
            return $method = $method->name;
        } );

//        // For each method convert method name to snake case and add to data[key => value]
        foreach ( self::$dataMethods as $method ) {
//            // Convert method name to snake case
            $var = Utils::convertToSnakeCase( $method->name );
            $myself = new $controller();

//            var_dump($method);

            // Add var method name to data[]
            self::$dataSelf[ $var ] = $myself->{$method->name}();
        }

        $post = $temp_post;

        return self::$dataSelf;
    }

    public static function renderHtml( $data = false ) {
        $v8 = new \V8Js();
        $engine = new V8( $v8 );
        $renderer = new Renderer( $engine );
        $html = false;

        $path = SetupTheme::distPath( 'scripts/vue-server.js' );
        $polyfill = SetupTheme::distPath( 'scripts/server-polyfill.js' );
        $renderer_source = dirname( get_stylesheet_directory() ) . '/node_modules/vue-server-renderer/basic.js';
        $script = file_get_contents( $path );

//        var_dump( $renderer_source );
//        var_dump( file_exists($renderer_source) );


//        if ( isset($data['post']) ) {
//            unset($data['post']);
//        }

        $current_url = sprintf(
            '%s://%s%s',
            isset( $_SERVER['HTTPS'] ) ? 'https' : 'http',
            $_SERVER['HTTP_HOST'],
            $_SERVER['REQUEST_URI']
        );


//        $data['url'] = $current_url;
        $data['url'] = $_SERVER['REQUEST_URI'];


//        Help::debug($data);
        ob_start();
        $v8->executeString( 'var process = { env: { VUE_ENV: "server", NODE_ENV: "production" }}; this.global = { process: process };' );
        $v8->executeString( 'setTimeout = function() { return false; };' );
        $v8->executeString( 'clearTimeout = function() { return false; };' );
//        $v8->executeString('window = global;');
//        $engine->run('document = window.document');
        $renderer->debug( true );

        $renderer->context( $data );
        $renderer->entry( $path );

        $html = $renderer->render();
//        $v8->executeString('print(context.url);');
//        var_dump($html);
        echo $html;

        $html = ob_get_clean();


//        ob_start();
        // window.navigator.userAgent

//        $v8->executeString('window = { navigator: { userAgent: "" } };');
//        $v8->executeString('setTimeout = function() { return false; };');
//        $v8->executeString( file_get_contents($polyfill) );
//        $v8->executeString('window = { navigator: { userAgent: "" } };');
//        $v8->executeString( file_get_contents($renderer_source));
//        $v8->executeString( file_get_contents($renderer_source));
//        $v8->executeString('setTimeout = function() { return false; };');
//        $v8->executeString("window.location = {}; window.location.pathname = '{$_SERVER['REQUEST_URI']}';");
//        $v8->executeString("window.location.replace = function() {};");

//        $context = json_encode( $data, JSON_UNESCAPED_UNICODE );
//        $v8->executeString("context = {$context};");
////        $v8->executeString("next = {};");
//        $v8->executeString($script);
//
//        $html = ob_get_clean();

//        var_dump($html);
//
        return $html;
    }

    public static function ajax_nopriv_getPageFields() {
//        global $post;
        $page_data = [];
        $page_name = self::getPostVar( 'page' );
        $page_name = preg_replace( '/^\/[ru|ua|en]{2}\//', '/', $page_name );
//        var_dump($page_name);

        $page = self::getPostByRouterName( $page_name );
//        var_dump($page);
//        $language = self::getPostVar('language');


        if ( $page ) {
            $post = get_post( $page->ID );
//            setup_postdata( $post );

            $page_data = self::getDataByPageName( $page_name );

            if ( $post && isset( $post->post_title ) ) {
                $post->post_title = apply_filters( 'the_title', $post->post_title );
            }
            $page_data['post'] = $post;

            if ( !isset( $page_data['acf_options'] ) ) {
                $page_data['acf_options'] = get_fields( 'options' );
            }

            wp_reset_postdata();
        }


        wp_send_json_success( $page_data );
    }

    public static function getDataByPageName( $page_name ) {
        $page = self::getPostByRouterName( $page_name );

        $fields = get_fields( $page->ID );
        $controller = self::getControllerName( $page );

        $page_data = self::getPageData( $controller, $page->ID );
        if ( $fields ) {
            $page_data = array_merge( $fields, $page_data );
        }

        return $page_data;
    }

    public static function getPostByRouterName( $page_name ) {
        if ( $page_name === 'Home' || $page_name === '/' ) {
            $front_id = get_option( 'page_on_front' );
            $page = get_post( $front_id );
        } else {
            $page = get_page_by_path( $page_name );

            if ( !$page ) {
                $page = get_page_by_path( basename($page_name), OBJECT, 'projects' );
            }
        }

        return $page;
    }

    public static function getControllerName( $post_id ) {
        $post_id = (is_numeric($post_id)) ? $post_id : $post_id->ID;
        $front_id = intval( get_option( 'page_on_front' ) );
        $case = false;

        if ( $post_id === $front_id ) {
            $case = 'FrontPage';
        } else {
            $template = get_page_template_slug( $post_id );

            if ( $template ) {
                $base_name = basename( $template );
                $exc = explode( '.', $base_name )[0];

                $camel = ucwords( preg_replace( '/\-([a-z])/', ' $1', basename( $exc ) ) );
                $case = str_replace( ' ', '', $camel );
            } else {
                $post_type = get_post_type($post_id);
                $case = 'Single' . ucfirst($post_type);
            }
        }

        if ( !$case || !strlen($case) ) {
            $case = 'App';
        }

//        Controllers\TemplateAbout
        return 'Controllers\\' . $case;
    }

    public function xhtml() {
        $html = '';
        $url = $_SERVER['REQUEST_URI'];
        $page_name = preg_replace( '/^\/[ru|ua|en]{2}\//', '/', $url );

        $page = self::getPostByRouterName( $page_name );
//        var_dump($page);
//        $language = self::getPostVar('language');

        if ( $page ) {
            $page_data = self::getDataByPageName( $page_name );

            $post = get_post( $page->ID );
            if ( $post && isset( $post->post_title ) ) {
                $post->post_title = apply_filters( 'the_title', $post->post_title );
            }
            $page_data['post'] = $post;

            if ( !isset( $page_data['acf_options'] ) ) {
                $page_data['acf_options'] = get_fields( 'options' );
            }

            if ( !isset( $page_data['translations'] ) ) {
                $page_data['translations'] = $page_data['acf_options']['translates'];
            }

            if ( !isset( $page_data['ajaxurl'] ) ) {
                $page_data['ajaxurl'] = admin_url( 'admin-ajax.php' );
            }

//            var_dump($page_data);
//            wp_die();

//            $html = self::renderHtml( $page_data );
//            var_dump($html);
        }

        return $html;
    }

}
