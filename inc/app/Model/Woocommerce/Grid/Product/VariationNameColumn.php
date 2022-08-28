<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce\Grid\Product;

class VariationNameColumn
{
    public function __construct()
    {
        add_action('manage_product_posts_custom_column', [__CLASS__, 'showProductType'], 20, 2);
    }

    /**
     * @param $column
     * @param $postid
     *
     * @return void
     */
    public static function showProductType($column, $postid): void
    {
        if ($column === 'name') {
            // Get product
            $product = wc_get_product($postid);
            // Get type
            $product_type = $product->get_type();
            // Output
            echo '&nbsp;<span>&ndash; ' . ucfirst($product_type) . '</span>';
        }
    }
}
