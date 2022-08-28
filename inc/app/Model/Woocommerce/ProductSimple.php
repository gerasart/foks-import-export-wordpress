<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce;

use Foks\Model\Settings;
use Foks\Model\Translit;

class ProductSimple
{
    /**
     * @param array $product
     * @param array $categories
     *
     * @return void
     */
    public static function create(array $product, array $categories): void
    {
        $isLoadImage = Settings::isNeedImage();
        $slug = Translit::execute($product['name'], true);

        $existProduct = Product::getProductByName($slug);

        if (!$existProduct) {
            $post = [
                'post_content' => $product['description'],
                'post_status' => Settings::getProductStatus(),
                'post_title' => $product['name'],
                'post_name' => $slug,
                'post_parent' => '',
                'post_type' => "product",
            ];

            $productId = wp_insert_post($post);
        } else {
            $productId = (int)$existProduct->ID;
            Product::updateProductStatus($productId);
        }

        $manageStock = $product['quantity'] ? "yes" : "no";
        Category::updateCategory($product, $productId, $categories);

        if ($isLoadImage) {
            Image::addImages($productId, $product['images']);
        }

        wp_set_object_terms($productId, 'simple', 'product_type');

        update_post_meta($productId, '_foks_id', $product['foks_id']);
        update_post_meta($productId, '_visibility', 'visible');
        update_post_meta($productId, '_stock_status', $product['quantity'] ? 'instock' : 'outofstock');

        if ($product['price_old']) {
            update_post_meta($productId, '_sale_price', $product['price']);
            update_post_meta($productId, '_price', $product['price']);
            update_post_meta($productId, '_regular_price', $product['price_old']);
        } else {
            update_post_meta($productId, '_price', $product['price']);
            update_post_meta($productId, '_regular_price', $product['price']);
        }

        update_post_meta($productId, '_featured', "no");
        update_post_meta($productId, '_sku', $product['sku'] . '-' . $productId);
        update_post_meta($productId, '_product_attributes', []);
        Attribute::addAttributeGroup($productId, $product['attributes']);
        update_post_meta($productId, '_manage_stock', $manageStock);
        update_post_meta($productId, '_backorders', "no");
        update_post_meta($productId, '_stock', $product['quantity']);
    }
}
