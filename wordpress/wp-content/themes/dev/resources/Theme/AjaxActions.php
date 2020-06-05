<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 7/19/2019
 * Time: 10:38 AM
 */

namespace Theme;

use Theme\Traits\AjaxHelper;

class AjaxActions {

    use AjaxHelper;

    public function __construct() {
        self::declaration_ajax();
    }

    public static function ajax_nopriv_test() {
        wp_send_json_success( ['message' => 'test'] );
    }

}
