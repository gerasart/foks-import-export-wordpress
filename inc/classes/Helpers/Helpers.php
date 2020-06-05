<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 7/18/2019
 * Time: 10:28 AM
 */

namespace Foks\Helpers;

class Helpers {

    public static function LogData( $data, $name = 'logs' ) {
        $dir = FOKS_PATH . "/logs/{$name}.txt";
        file_put_contents( $dir, json_encode( $data ) . "\n", FILE_APPEND );
    }

    /* Debug functions */
    public static function debug( $var ) {
        echo "<pre>";
        var_dump( $var );
        echo "</pre>";
    }

}
