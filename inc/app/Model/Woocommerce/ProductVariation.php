<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce;

use Foks\Model\Translit;

class ProductVariation
{

    public static function create(array $data, $categories, $isImgOption): void
    {
        $id = wc_get_product_id_by_sku($data['sku']);

        if (!$id) {
            $post_data = [
                'post_name' => Translit::execute($data['name'], true),
                'post_title' => $data['name'],
                'post_content' => $data['description'],
                'post_status' => 'pending',
                'ping_status' => 'closed',
                'post_type' => 'product',
            ];

            $productId = wp_insert_post($post_data);
            Category::updateCategory($data, $productId, $categories);

            if ($isImgOption) {
                Image::addImages($productId, $data['images']);
            }

            $product = new \WC_Product_Variable($productId);

            $product->set_name($data['name']);
            $product->set_sku($data['sku']);
            $product->save();
            $attributes = self::prepareAttributes($data);
            $attrs = [];
            $index = 0;

            foreach ($attributes as $key => $value) {
                $attribute = new \WC_Product_Attribute();
                $attribute->set_name($key);
                $attribute->set_options($value);
                $attribute->set_position($index);
                $attribute->set_visible(true);
                $attribute->set_variation(true); //TODO temp
                $attrs[] = $attribute;
                $index++;
            }

            $product->set_attributes($attrs);
            $parentId = $product->save();

            foreach ($data['variation'] as $variation) {
                try {
                    self::setVariation($parentId, $variation, $attributes);
                } catch (\WC_Data_Exception $e) {

                }
            }
        }
    }

    /**
     * @param $parentId
     * @param $data
     * @param $attributes
     * @return void
     * @throws \WC_Data_Exception
     */
    public static function setVariation($parentId, $data, $attributes): void
    {
        foreach ($attributes as $key => $values) {
            foreach ($values as $val) {
                $variation = new \WC_Product_Variation();
                $variation->set_parent_id($parentId);
                $variation->set_attributes([$key => $val]);
                $variation->set_regular_price($data['price']);
                $variation->set_sku($data['sku'] . '-' . $parentId);
                $productId = $variation->save();

//                if (isset($data['images'][0])) {
//                    Image::addThumb((int)$productId, $data['images'][0]);
//                }
            }
        }
    }

    /**
     * @param array $products
     * @return array
     */
    public static function prepareVariationProducts(array $products): array
    {
        $simpleProducts = [];
        $result = [];
        $variations = [];

        foreach ($products as $product) {
            if ($product['group_id'] && !$product['master']) {
                $variations[$product['group_id']][] = $product;
            } else {
                $simpleProducts[] = $product;
            }
        }

        foreach ($simpleProducts as $product) {
            $product['variation'] = $variations[$product['group_id']] ?? [];
            $result[] = $product;
        }

        return $result;
    }

    /**
     * @param $variations
     * @return array
     */
    public static function prepareAttributes($variations): array
    {
        $attributes = [];
        $duplicate = [];

        foreach ($variations['variation'] as $variation) {
            foreach ($variation['attributes'] as $attribute) {
                if (!in_array($attribute['value'], $duplicate, true)) {
                    $attributes[$attribute['name']][] = $attribute['value'];
                    $duplicate[] = $attribute['value'];
                }
            }
        }

        return $attributes;
    }
}
