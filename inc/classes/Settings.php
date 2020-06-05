<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 7/25/2019
 * Time: 6:13 PM
 */

namespace Foks;

class Settings {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'settingPage' ] );
        add_action( "admin_menu", [ $this, 'display_theme_panel_fields' ] );
    }

    public function settingPage() {
        add_menu_page(
            'FoksImportExport',
            'FoksImportExport',
            'manage_options',
            FOKS_NAME,
            [ $this, FOKS_NAME ],
            FOKS_URL . '/assets/img/icon.png'
        );
    }


    public function display_theme_panel_fields() {
        register_setting( "section", "current_plugins" );
    }

    public function foks() {
        include FOKS_PATH . 'views/index.php';
    }


}
