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
        $slug = Translit::execute($data['name'], true);
        $existProduct = Product::getProductByName($slug);

        try {
            if (!$existProduct) {
                $product = new \WC_Product_Variable();
                $product->set_name($data['name']);
                $product->set_description($data['description']);
                $product->set_slug($slug);
                $product->set_sku($data['sku']);
                $productId = $product->save();
            } else {
                $product = wc_get_product((int)$existProduct->ID);
                $productId = $product->get_id();
                $variationIds = self::getProductVariationIds($product->get_id());
                self::removeVariations($variationIds);
            }

            $isImgOption = Settings::isNeedImage();

            Category::updateCategory($data, $productId, $categories);

            if ($isImgOption) {
                Image::addImages($productId, $data['images']);
            }

            if ($product instanceof \WC_Product) {
                self::updateProduct($product, $data);
            } else {
                LogResourceModel::set([
                    'action' => 'error',
                    'message' => __CLASS__ . ': ' . __METHOD__ . "Product id: $productId wrong instance: " . gettype($product),
                ]);
            }
        } catch (\WC_Data_Exception $e) {
            LogResourceModel::set([
                'action' => 'error',
                'message' => __CLASS__ . ': ' . __METHOD__ . ': ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * @param \WC_Product $product
     * @param array $data
     *
     * @return void
     */
    public static function updateProduct(\WC_Product $product, array $data): void
    {
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

        $product->set_status(Settings::getProductStatus());
        $parentId = $product->save();

        update_post_meta($parentId, '_foks_id', $data['foks_id']);

        Product::updateProductStatus($parentId);
        $i = 0;

        foreach ($data['variation'] as $variation) {
            try {
                self::setVariation($parentId, $variation, $variationOptions, $i);
                $i++;
            } catch (\WC_Data_Exception $e) {
                LogResourceModel::set([
                    'action' => 'error',
                    'message' => __CLASS__ . ': ' . __METHOD__ . " foks id: {$data['foks_id']}: " . $e->getMessage(),
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

                Product::updateProductStatus($productId);

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

    /**
     * @param int $productId
     *
     * @return array
     */
    public static function getProductVariationIds(int $productId): array
    {
        $product = wc_get_product($productId);

        if ($product instanceof \WC_Product && self::isVariableProduct($product)) {
            $variations = $product->get_available_variations();

            return wp_list_pluck($variations, 'variation_id');
        }

        LogResourceModel::set([
            'action' => 'error',
            'message' => __CLASS__ . ': ' . __METHOD__ . ': is not WC_Product_Variable -> ' . $productId,
        ]);

        return [];
    }

    /**
     * @param array $variationIds
     *
     * @return void
     */
    public static function removeVariations(array $variationIds): void
    {
        if ($variationIds) {
            foreach ($variationIds as $variationId) {
                if (Product::PRODUCT_VARIATION === get_post_type($variationId)) {
                    $variation = wc_get_product($variationId);
                    $variation->delete(true);
                }
            }
        }
    }

    /**
     * @param \WC_Product $product
     *
     * @return bool
     */
    public static function isVariableProduct(\WC_Product $product): bool
    {
        return is_a($product, \WC_Product_Variable::class);
    }
}
