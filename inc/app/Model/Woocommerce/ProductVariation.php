<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce;

use Foks\Model\Resource\AttributeResourceModel;
use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Settings;
use Foks\Model\Translit;

class ProductVariation
{
    /**
     * @param array $data
     * @param array $categories
     *
     * @return void
      */
    public static function create(array $data, array $categories): void
    {
        $productId = wc_get_product_id_by_sku($data['sku']);

        try {
            if (!$productId) {
                $slug = Translit::execute($data['name'], true);

                $post_data = [
                    'post_title' => $data['name'],
                    'post_name' => $slug,
                    'post_content' => $data['description'],
                    'post_status' => 'publish',
                    'ping_status' => 'closed',
                    'post_type' => 'product',
                ];

                $productId = wp_insert_post($post_data);

                self::updateProduct($productId, $data, $categories);
            } else {
                self::updateProduct($productId, $data, $categories);
            }
        } catch (\WC_Data_Exception $e) {
            LogResourceModel::set([
                'action' => 'error',
                'message' => __CLASS__ . ': ' . __METHOD__ . ': ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * @param int $productId
     * @param array $data
     * @param array $categories
     *
     * @return void
     * @throws \WC_Data_Exception
     */
    public static function updateProduct(int $productId, array $data, array $categories): void
    {
        $isImgOption = Settings::isNeedImage();

        Category::updateCategory($data, $productId, $categories);

        if ($isImgOption) {
            Image::addImages($productId, $data['images']);
        }

        $product = new \WC_Product_Variable($productId);
        $product->set_name($data['name']);
        $product->set_sku($data['sku']);

        $attributes = self::prepareAttributes($data);
        $attrs = [];
        $index = 0;
        $variationOptions = self::variationOptions();

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $isVariation = in_array((string)$key, $variationOptions, true);
                $attribute = new \WC_Product_Attribute();
                $attribute->set_name($key);
                $attribute->set_options($value);
                $attribute->set_position($index);
                $attribute->set_visible(true);
                $attribute->set_variation($isVariation);
                $attrs[] = $attribute;

                $index++;
            }

            $product->set_attributes($attrs);
        }

        $parentId = $product->save();

        $i = 0;

        foreach ($data['variation'] as $variation) {
            try {
                self::setVariation($parentId, $variation, $variationOptions, $i);
                $i++;
            } catch (\WC_Data_Exception $e) {
                LogResourceModel::set([
                    'action' => 'error',
                    'message' => __CLASS__ . ': ' . __METHOD__ . ': ' . $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @param int $parentId
     * @param array $data
     * @param array $variationOptions
     * @param int $index
     *
     * @return void
     * @throws \WC_Data_Exception
     */
    public static function setVariation(int $parentId, array $data, array $variationOptions, int $index): void
    {
        $isLoadImage = Settings::isNeedImage();

        if (!empty($variationOptions)) {
            $attributes = [];

            foreach ($data['attributes'] as $attribute) {
                $isVariation = in_array((string)$attribute['name'], $variationOptions, true);

                if ($isVariation) {
                    $attributes[sanitize_title($attribute['name'])] = $attribute['value'];
                }
            }

            if ($attributes) {
                $variation = new \WC_Product_Variation();
                $variation->set_parent_id($parentId);
                $variation->set_attributes($attributes);

                if ($data['old_price']) {
                    $variation->set_regular_price($data['old_price']);
                    $variation->set_sale_price($data['price']);
                } else {
                    $variation->set_regular_price($data['price']);
                }

                $variation->set_sku("{$data['sku']}-$parentId-$index");
                $productId = $variation->save();

                if ($isLoadImage && isset($data['images'][0])) {
                    Image::addThumb($productId, $data['images'][0]);
                }
            }
        }
    }

    /**
     * @param array $products
     *
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
     * @param array $variations
     *
     * @return array
     */
    public static function prepareAttributes(array $variations): array
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

    /**
     * @return array|false|string[]
     */
    public static function variationOptions()
    {
        $variations = get_option(Settings::VARIATION_FIELD);
        $row = AttributeResourceModel::getNameByIds(json_decode($variations));

        return $row->names ? explode(',', $row->names) : [];
    }
}
