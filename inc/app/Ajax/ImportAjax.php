<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Ajax;

use Foks\Import\Import;
use Foks\Log\Logger;
use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Settings;

class ImportAjax extends Ajax
{
    /**
     * @return void
     * @throws \Exception
     */
    public static function ajax_nopriv_importFoks(): void
    {
        Logger::file(0, 'total', 'json');
        $file = get_option(Settings::IMPORT_FIELD);
        $data = [];

        if ($file) {
            $xml = file_get_contents($file);
            Logger::file($xml, Import::IMPORT_FILE, 'xml');
            $data = Import::importData(Import::IMPORT_PATH);
        }

        LogResourceModel::set([
            'action' => 'import',
            'message' => 'import complete',
        ]);

        wp_send_json_success($data);
    }
}
