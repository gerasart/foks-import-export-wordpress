<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Ajax;

use Foks\Model\Settings;

class SettingsAjax extends Ajax
{
    /**
     * @action saveSettings
     * @return void
     */
    public static function ajax_saveSettings(): void
    {
        $post = $_POST['data'] ?? '';

        if ($post) {
            Settings::saveSettings($post);

            wp_send_json_success($post);
        }

        wp_send_json_error();
    }

    public static function ajax_saveVariations(): void
    {
        $post = $_POST['data'] ?? '';
        Settings::saveVariations($post);

        wp_send_json_success($post);
    }
}
