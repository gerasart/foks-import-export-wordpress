<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Ajax;

class Ajax
{
    public function __construct()
    {
        self::declaration_ajax();
    }

    /**
     * @return void
     */
    public static function declaration_ajax(): void
    {
        $class_methods = get_class_methods(static::class);

        foreach ($class_methods as $name) {
            $ajax = strpos($name, 'ajax');
            $noPrivate = strpos($name, 'nopriv');
            $short = str_replace(['ajax_', 'nopriv_'], '', $name);

            if ($ajax === 0) {
                add_action('wp_ajax_' . $short, [static::class, $name]);
            }

            if ($noPrivate === 5) {
                add_action('wp_ajax_nopriv_' . $short, [static::class, $name]);
            }
        }
    }
}
