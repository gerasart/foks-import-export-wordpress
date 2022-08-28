<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model;

use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Woocommerce\Product;

class Settings
{
    public const VARIATION_FIELD = 'foks_variations';
    public const IMPORT_FIELD = 'foks_import';
    public const UPDATE_FIELD = 'foks_update';
    public const IS_NEED_CRON_FIELD = 'foks_is_need_cron';
    public const IMG_FIELD = 'foks_img';
    public const PRODUCT_STATUS_FIELD = 'foks_product_status';
    public const DESCRIPTION_TYPE_STATUS_FIELD = 'foks_description_type';

    /**
     * @param $data
     *
     * @return void
     */
    public static function saveSettings($data): void
    {
        update_option(self::IMPORT_FIELD, $data['import']);
        update_option(self::UPDATE_FIELD, $data['update']);
        update_option(self::IMG_FIELD, $data['img']);
        update_option(self::IS_NEED_CRON_FIELD, $data['isNeedCron']);
        update_option(self::PRODUCT_STATUS_FIELD, $data['productStatus']);

        LogResourceModel::set([
            'action' => 'save settings',
            'message' => json_encode($data),
        ]);
    }

    /**
     * @param $settings
     *
     * @return void
     */
    public static function saveVariations($settings): void
    {
        $data = json_encode($settings);
        update_option(self::VARIATION_FIELD, $data);

        LogResourceModel::set([
            'action' => 'save variations',
            'message' => $data,
        ]);
    }

    public static function saveExportSettings($settings): void
    {
        $data = json_encode($settings);
        update_option(self::DESCRIPTION_TYPE_STATUS_FIELD, $data);

        LogResourceModel::set([
            'action' => 'save variations',
            'message' => $data,
        ]);
    }

    /**
     * @return bool
     */
    public static function isNeedImage(): bool
    {
        return get_option(self::IMG_FIELD) === 'true';
    }

    /**
     * @return bool
     */
    public static function isNeedCron(): bool
    {
        return get_option(self::IS_NEED_CRON_FIELD) === 'true';
    }

    public static function getProductStatus() {
        return get_option(self::PRODUCT_STATUS_FIELD) ?: Product::PUBLISH_STATUS;
    }
}
