<?php
/**
 * Created by PhpStorm.
 * User: geras
 * Date: 7/31/2019
 * Time: 4:44 PM
 */

namespace Foks\Traits;

trait LocalVars {
    public function viewData() {
        self::$admin_vars['foks'] = [
            'export' => json_encode( get_option( 'foks_export' ) ),
            'import' => json_encode( get_option( 'foks_import' ) ),
        ];
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
