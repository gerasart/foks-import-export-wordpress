<?php
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 08.01.19
 * Time: 18:15
 */

namespace Theme\Helpers;


class AcfFunctions {

    public static function getExistFields($args = []) {
        $groups = acf_get_field_groups($args);
        $exists = [];

        if ( !empty($groups) ) {
            $fields = acf_get_fields( $groups[0]['key'] );

            foreach ($fields as $field) {
                $name = $field['name'];
                if (!empty($name)) {
                    $exists[] = $name;
                }
            }
        }

        return $exists;
    }

}
