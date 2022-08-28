<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Ajax;

class WPLogsAjax extends Ajax
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @action saveSettings
     * @return void
     */
    public static function ajax_getWPLogs(): void
    {
        $path = WP_CONTENT_DIR. '/debug.log';
        $isFileExist = file_exists($path);
        $logs = 'Wp debug empty or disabled!';

        if ($isFileExist) {
            $logs = file_get_contents($path);
        }

        wp_send_json_success($logs);
    }
}
