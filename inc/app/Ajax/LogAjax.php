<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Ajax;

use Foks\Model\Resource\LogResourceModel;

class LogAjax extends Ajax
{
    /**
     * @action removeLogs
     * @return void
     */
    public static function ajax_removeLogs(): void
    {
        LogResourceModel::delete();

        wp_send_json_success(['status' => 'ok']);
    }

    /**
     * @action getLogs
     * @return void
     */
    public static function ajax_getLogs(): void
    {
        wp_send_json_success(LogResourceModel::getList());
    }
}
