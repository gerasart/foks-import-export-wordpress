<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 1/23/2019
 * Time: 2:01 PM
 */

namespace Foks;

use Foks\Traits\LocalVars;

class Setup {

    use LocalVars;

    static $front_vars = [];
    static $admin_vars = [];


    public function __construct() {
        if ( FOKS_PAGE === 'page='.FOKS_NAME ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
            add_action( 'admin_head', array( __CLASS__, 'localAdminVars' ) );

            add_action('init', [$this, 'init']);
        }
        add_filter( 'plugin_action_links_' . FOKS_BASENAME, [ $this, 'plugin_action_links' ] );
    }

    public function init() {
        $this->viewData();
    }


    public function plugin_action_links( $links ) {
        $settings_link = '<a href="' . menu_page_url( $this->subpage, false ) . '">' . esc_html( __( 'Settings', 'custom' ) ) . '</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }


    public function enqueue_admin() {
        wp_enqueue_script( FOKS_NAME, FOKS_URL . 'dist/scripts/vue.js', array( 'jquery'), time(), true);
        wp_enqueue_style( FOKS_NAME,FOKS_URL . 'dist/styles/vue.css');
    }

    public static function localAdminVars() {
        echo "<script>";
        foreach (self::$admin_vars as $key => $value) {
            if ( !is_string($value) ) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            echo "window.{$key} = {$value};" . "\n";
        }
        echo "</script>";
    }

}
