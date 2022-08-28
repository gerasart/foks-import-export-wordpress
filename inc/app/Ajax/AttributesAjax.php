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
use Foks\Import\ImportAttributes;
use Foks\Log\Logger;
use Foks\Model\Resource\AttributeResourceModel;
use Foks\Model\Settings;

class AttributesAjax extends Ajax
{
    /**
     * @action getAttributes
     * @return void
     */
    public static function ajax_getAttributes(): void
    {
        wp_send_json_success(AttributeResourceModel::getList());
    }

    /**
     * @action importAttributes
     * @return void
     */
    public static function ajax_importAttributes(): void
    {
        $isFile = file_exists(Import::IMPORT_PATH);

        if (!$isFile) {
            $file = get_option(Settings::IMG_FIELD);
            $xml = file_get_contents($file);
            Logger::file($xml, Import::IMPORT_FILE, 'xml');
        }

        ImportAttributes::execute(Import::IMPORT_PATH);

        wp_send_json_success(AttributeResourceModel::getList());
    }
}
