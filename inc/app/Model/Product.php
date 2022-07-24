<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

declare(strict_types=1);

namespace Foks\Model;

use Foks\Log\Logger;

class Product
{
    public const SIMPLE_TYPE = 'simple';

    public const VARIATION_TYPE = 'variation';

    /**
     * @param $productId
     * @return object
     */
    public static function getProductById($productId)
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
                    'value' => implode(', ', $value_names),
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
            'status' => get_post_meta($productId, '_stock_status', true),
            'category' => $categories[0] ?? '',
            'category_id' => Category::getCategoryId($productId),
            'price' => $price ?: '',
            'sale_price' => $sale_price ?: '',
            'quantity' => $quantity ?: 999,
            'sku' => $sku ?: '',
            'params' => $attr_data ?: [],
            'vendor' => '',
        ];
    }

    /**
     * @param $products
     * @param $categories
     *
     * @throws \Exception
     */
    public static function addProducts($products, $categories): void
    {
        $isImgOption = get_option('foks_img');
        $i = 0;

        foreach ($products as $product) {
            $i++;
            Logger::file($i, 'current', 'json');
            $productType = empty($product['variation']) ? self::SIMPLE_TYPE : self::VARIATION_TYPE;
            $slug = Translit::execute($product['name'], true);
            $post = [
                'post_content' => $product['description'],
                'post_status' => 'pending',
                'post_title' => $productType === 'simple' ? $product['name'] : $product['name'] . '-variation',
                'post_name' => $slug,
                'post_parent' => '',
                'post_type' => $productType === 'simple' ? "product" : 'product_variation',
            ];

            $getProduct = self::getProductByName($product['name']);

            if (!$getProduct) {
                $productId = wp_insert_post($post);
            } else {
                $productId = (int)$getProduct->ID;
            }

            $manageStock = $product['quantity'] ? "yes" : "no";
            Category::updateCategory($product, $productId, $categories);

            if (!$isImgOption || $isImgOption === 'false') {
                Image::addImages($productId, $product['images']);
            }

            if (!$getProduct) {
                wp_set_object_terms($productId, $productType, 'product_type');
            }

            if ($productType === self::SIMPLE_TYPE) {
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
                update_post_meta($productId, '_sku', $product['model']);
                update_post_meta($productId, '_product_attributes', []);
                Attribute::addAttributeGroup((int)$productId, $product['attributes']);
                update_post_meta($productId, '_manage_stock', $manageStock);
                update_post_meta($productId, '_backorders', "no");
                update_post_meta($productId, '_stock', $product['quantity']);
            } else if (!empty($product['variation'])) {
                ProductVariation::create((int)$productId, $product);
            }
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public static function getProductByName($name)
    {
        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_title  = '{$name}'";
        $data = $wpdb->get_results($query);

        return $data[0] ?? [];
    }
}
