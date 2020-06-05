<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 7/26/2019
 * Time: 2:51 PM
 */

namespace Foks;


use Foks\Helpers\AjaxHelper;

class AjaxActions extends AjaxHelper {

    public function __construct() {
        self::declaration_ajax();
    }

    public static function ajax_nopriv_saveSettings() {
        $post = $_POST['data'];
        $result = update_option( 'foks_import', $post );
        wp_send_json_success( $result );

    }

}
