<?php
/**
 * Created by PhpStorm.
 * User: geras
 * Date: 7/31/2019
 * Time: 4:44 PM
 */

namespace UniSender\Traits;

use UniSender\DBCreator;
use UniSender\Helpers;
use UniSender\IntegrationContactForm7;

trait LocalVars {
    public function viewData() {
        self::$admin_vars['foks'] = [
            'export' => json_encode( get_option( 'foks_export' ) ),
            'import' => json_encode( get_option( 'foks_import' ) ),
        ];
    }
}
