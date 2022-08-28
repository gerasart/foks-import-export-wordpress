<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce;

use Foks\Log\Logger;
use Foks\Model\Settings;

class Product
{
    public const SIMPLE_TYPE = 'simple';
    public const VARIATION_TYPE = 'variation';
    public const PRODUCT_VARIATION = 'product_variation';
    public const DEFAULT_QUANTITY = 999;
    public const PUBLISH_STATUS = 'publish';
    public const PENDING_STATUS = 'pending';
    public const DRAFT_STATUS = 'draft';

    public const PRODUCT_STATUSES = [
        self::PUBLISH_STATUS,
        self::PENDING_STATUS,
        self::DRAFT_STATUS
    ];

    /**
     * @param array $products
     * @param array $categories
     */
    public static function addProducts(array $products, array $categories): void
    {
        $i = 0;

        foreach ($products as $product) {
            $i++;
            Logger::file($i, 'current', 'json');
            $productType = empty($product['variation']) || count($product['variation']) === 1 ? self::SIMPLE_TYPE : self::VARIATION_TYPE;
            $isVariation = $productType === self::VARIATION_TYPE;

            if ($isVariation) {
                ProductVariation::create($product, $categories);
            } else {
                ProductSimple::create($product, $categories);
            }
        }
    }

    /**
     * @param int $productId
     *
     * @return object
     */
    public static function getProductById(int $productId)
    {
        $thumb = has_post_thumbnail($productId) ? get_the_post_thumbnail_url($productId, 'full') : false;
        $categories = wp_get_post_terms($productId, 'product_cat', ['fields' => 'names']);
        $product = new \WC_Product($productId);
        $attachment_ids = $product->get_gallery_image_ids();
        $images = [];

        if (!empty($attachment_ids)) {
            foreach ($attachment_ids as $attachment_id) {
                $images[] = wp_get_attachment_image_url($attachment_id, 'full');
            }
        }

        $price = get_post_meta($productId, '_regular_price', true);
        $sale_price = get_post_meta($productId, '_sale_price', true);
        $quantity = get_post_meta($productId, '_stock', true);
        $sku = get_post_meta($productId, '_sku', true);
        $attributes = $product->get_attributes();
        $attr_data = [];

        if ($attributes) {
            foreach ($attributes as $item) {
                $value_names = $item->get_options();

                if ($item->get_terms()) {
                    $value_names = [];
                    foreach ($item->get_terms() as $term) {
                        $value_names[] = $term->name;
                    }
                }

                $attr_data[] = [
                    'name' => $item->get_name(),
                    'value' => $value_names,
                    'terms' => $item->get_terms(),
                    'slug' => $item->get_data()
                ];
            }
        }

        return (object)[
            'id' => $productId,
            'title' => html_entity_decode(get_the_title($productId)),
            'url' => get_the_permalink($productId),
            'thumb' => $thumb ?: '',
            'images' => $images ?: [],
            'description' => $product->get_description(),
            'short_description' => $product->get_short_description(),
            'status' => get_post_meta($productId, '_stock_status', true),
            'category' => $categories[0] ?? '',
            'category_id' => Category::getCategoryId((int)$productId),
            'price' => $price ?: '',
            'sale_price' => $sale_price ?: '',
            'quantity' => $quantity ?: self::DEFAULT_QUANTITY,
            'sku' => $sku ?: $product->get_sku(),
            'params' => $attr_data ?: [],
            'vendor' => '',
        ];
    }

    public static function getProducts(): array
    {
        $args = [
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'ids',
            'status' => 'publish'
        ];

        $query = new \WC_Product_Query($args);
        $products = $query->get_products();
        $result = [];

        foreach ($products as $productId) {
            $result[] = self::getProductById((int)$productId);
        }
        return $result;
    }

    /**
     * @return int
     */
    public static function deleteProducts(): int
    {
        global $wpdb;

        $sqlRelations = "DELETE relations.*, taxes.*, terms.*
                FROM wp_term_relationships AS relations
                         INNER JOIN wp_term_taxonomy AS taxes
                                    ON relations.term_taxonomy_id = taxes.term_taxonomy_id
                         INNER JOIN wp_terms AS terms
                                    ON taxes.term_id = terms.term_id
                WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_type IN ('product', 'product_variation'));";

        $sqlMeta = "DELETE
                FROM wp_postmeta
                WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type IN ('product', 'product_variation'));";

        $sqlPosts = "DELETE
                FROM wp_posts
                WHERE post_type IN ('product', 'product_variation');";
        $requests = [$sqlRelations, $sqlMeta, $sqlPosts];

        foreach ($requests as $sql) {
            $wpdb->query($sql);
        }

        return 1;
    }


    /**
     * @param int $productId
     *
     * @return void
     */
    public static function updateProductStatus(int $productId): void
    {
        wp_update_post([
            'ID' => $productId,
            'post_status' => Settings::getProductStatus()
        ]);
    }

    /**
     * @param string $name
     *
     * @return array|false|object|\stdClass
     */
    public static function getProductByName(string $name)
    {
        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_name  = '$name' LIMIT 1";
        $data = $wpdb->get_row($query);

        return $data ?: false;
    }
}
