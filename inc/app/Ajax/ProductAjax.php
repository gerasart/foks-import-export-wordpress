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
use Foks\Model\Woocommerce\Product;

class ProductAjax extends Ajax
{
    /**
     * @action removeProducts
     * @return void
     */
    public static function ajax_removeProducts(): void
    {
        Product::deleteProducts();

        LogResourceModel::set([
            'action' => 'remove',
            'message' => 'remove products',
        ]);

        wp_send_json_success(['status' => 'ok']);
    }
}
