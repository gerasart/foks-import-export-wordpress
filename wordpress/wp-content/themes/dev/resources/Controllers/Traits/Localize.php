<?php

namespace Controllers\Traits;


use Theme\SetupTheme;

trait Localize {

    public function __after() {
        $data = [];

        foreach ($this->data as $key => $value) {
            if ( substr($key, 0, 1) !== '_' && $key !== "acf_options" ) {
                $data[$key] = $value;
            }
        }

//        print_r($data);

        SetupTheme::$footer_vars['viewData'] = $data;
    }

}
