<?php
/*
 * Plugin Name: Foks Import Export xml Plugin for Wordpress
 * Version: 1.2
 * Plugin URI: https://#
 * Description: Import Export Integraton.
 * Author: Gerasart
 * Author URI: https://t.me/gerasart
 */

if ( !defined( 'ABSPATH' ) ) exit;

define( 'FOKS_BASENAME', plugin_basename( __FILE__ ) );
define( 'FOKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'FOKS_URL', plugin_dir_url( __FILE__ ) );
define( 'FOKS_PAGE', $_SERVER['QUERY_STRING'] );
define( 'FOKS_NAME', 'foks' );

require_once dirname( __FILE__ ) . '/vendor/autoload.php';


use HaydenPierce\ClassFinder\ClassFinder;

class Foks {

    private static $basedir;

    public function __construct() {
        self::init();
    }

    public static function init() {
        self::$basedir = FOKS_PATH . 'inc/classes/';

        self::cc_autoload();
    }

    private static function cc_autoload() {

        $namespaces = self::getDefinedNamespaces();
        foreach ( $namespaces as $namespace => $path ) {

            $clear = substr( $namespace, 0, strlen( $namespace ) - 1 );

            ClassFinder::setAppRoot( FOKS_PATH );
            $level = error_reporting( E_ERROR );

            $classes = ClassFinder::getClassesInNamespace( $clear );
            error_reporting( $level );

            foreach ( $classes as $class ) {
                new $class();
            }
        }
    }

    private static function getDefinedNamespaces() {
        $composerJsonPath = dirname( __FILE__ ) . '/composer.json';

        $composerConfig = json_decode( file_get_contents( $composerJsonPath ) );

        $psr4 = "psr-4";
        return (array)$composerConfig->autoload->$psr4;
    }
}

new Foks();


require 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';
    
    
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
'https://github.com/gerasart/foksImportExport/',
__FILE__,
'foks'
);
$myUpdateChecker->setAuthentication('7f6f3a685557493c80fa6020c12880bb0b192e15');
$myUpdateChecker->setBranch('master');
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
