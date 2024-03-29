<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\ViewModel;

use Foks\Export\Export;
use Foks\Import\Import;
use Foks\Model\Settings;
use Foks\Model\Woocommerce\Product;

class SettingsView
{
    public static $admin_vars = [];

    /**
     * @return void
     */
    public static function viewData(): void
    {
        $import = get_option(Settings::IMPORT_FIELD);
        $update = get_option(Settings::UPDATE_FIELD);
        $variations = get_option(Settings::VARIATION_FIELD);
        $productStatus = get_option(Settings::PRODUCT_STATUS_FIELD);

        self::$admin_vars['settings'] = [
            'import' => $import ?: '',
            'update' => $update ?: '1',
            'export' => get_site_url() . '/wp-json/foks/foksExport',
            'logs_url' => FOKS_URL . 'logs/',
            'img' => Settings::isNeedImage(),
            'isNeedCron' => Settings::isNeedCron(),
            'variations' => $variations ? json_decode($variations) : '',
            'isImportFile' => file_exists(Import::IMPORT_PATH),
            'isExportFile' => file_exists(Export::EXPORT_PATH),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'version' => FOKS_VERSION,
            'exportUrl' => Export::EXPORT_URL,
            'productStatus' => $productStatus ?: Product::PENDING_STATUS,
            'statuses' => Product::PRODUCT_STATUSES,
        ];
    }

    /**
     * @return void
     */
    public static function localAdminVars(): void
    {
        echo "<script>";

        foreach (self::$admin_vars as $key => $value) {
            if (!is_string($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            echo "window.{$key} = {$value};" . "\n";
        }

        echo "</script>";
    }
}
