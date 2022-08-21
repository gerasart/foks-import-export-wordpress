<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce;

class ProductSimple
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    public static function getProductByName(string $name)
    {
        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_title  = '$name'";
        $data = $wpdb->get_results($query);

        return $data[0] ?? [];
    }

    /**
     * @param array $product
     * @param array $categories
     * @param bool $isLoadImage
     * @return void
     */
    public static function create(array $product, array $categories, bool $isLoadImage): void
    {
        $post = [
            'post_content' => $product['description'],
            'post_status' => "pending",
            'post_title' => $product['name'],
            'post_name' => Translit::execute($product['name'], true),
            'post_parent' => '',
            'post_type' => "product",
        ];

        $is_product = self::getProductByName($product['name']);

        if (!$is_product) {
            $product_id = wp_insert_post($post);
        } else {
            $product_id = (int)$is_product->ID;
        }

        $manageStock = $product['quantity'] ? "yes" : "no";

        Category::updateCategory($product, $product_id, $categories);

        if ($isLoadImage) {
            Image::addImages($product_id, $product['images']);
        }

        if (!$is_product) {
            wp_set_object_terms($product_id, 'simple', 'product_type');
        }

        update_post_meta($product_id, '_foks_id', $product['foks_id']);
        update_post_meta($product_id, '_visibility', 'visible');
        update_post_meta($product_id, '_stock_status', $product['quantity'] ? 'instock' : 'outofstock');

        if ($product['price_old']) {
            update_post_meta($product_id, '_sale_price', $product['price']);
            update_post_meta($product_id, '_price', $product['price']);
            update_post_meta($product_id, '_regular_price', $product['price_old']);
        } else {
            update_post_meta($product_id, '_price', $product['price']);
            update_post_meta($product_id, '_regular_price', $product['price']);
        }

        update_post_meta($product_id, '_featured', "no");
        update_post_meta($product_id, '_sku', $product['model']);
        update_post_meta($product_id, '_product_attributes', []);
        Attribute::addAttributeGroup($product_id, $product['attributes']);
        update_post_meta($product_id, '_manage_stock', $manageStock);
        update_post_meta($product_id, '_backorders', "no");
        update_post_meta($product_id, '_stock', $product['quantity']);
    }
}
