<?php
/**
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model;

class ProductVariation
{
    /**
     * @param array $data
     * @return void
     */
    public static function create( array $data): void
    {
        $id = wc_get_product_id_by_sku($data['sku']);

        if (!$id) {
            $product = new \WC_Product_Variable();

            $product->set_name($data['name']);
            $product->set_sku($data['sku']);
            $id = $product->save();
            $attributes = self::prepeareAttributes($data);
            $attrs = [];

            foreach ($attributes as $key => $value) {
                $attribute = new \WC_Product_Attribute();
                $attribute->set_name($key);
                $attribute->set_options($value);
                $attribute->set_position($index);
                $attribute->set_visible(true);
                $attribute->set_variation(false); // here it is
                $attrs[] = $attribute;
                $index++;
            }

            $product->set_attributes( $attrs );
            $product->save();

            //todo without foreach;
//            foreach ($data['variation'] as $variation) {
//                self::createVariation($id, $variation, $data);
//            }
//            die;
        }
    }

    public static function createVariation($productId, $variationData, $data) {
        $isImgOption = get_option('foks_img');
        $attributes = self::prepeareAttributes($data);

        // Get an instance of the WC_Product_Variation object
        $product = new \WC_Product_Variation();
        $product->set_parent_id( $productId );
        // SKU
        if (!empty($variationData['sku'])) {
            $product->set_sku($variationData['sku']);
        }

        // Prices
        if (empty($variationData['price_old'])) {
            $product->set_price($variationData['price']);
        } else {
            $product->set_price($variationData['price_old']);
            $product->set_sale_price($variationData['price']);
        }

        $product->set_regular_price($variationData['price']);

        // Stock
        if (!empty($variationData['quantity'])) {
            $product->set_stock_quantity($variationData['quantity']);
            $product->set_manage_stock(true);
            $product->set_stock_status('');
        } else {
            $product->set_manage_stock(false);
        }

        $product->set_weight('');
        $index = 0;
        $attrs = [];

        foreach ($attributes as $key => $value) {
            $attribute = new \WC_Product_Attribute();
            $attribute->set_name($key);
            $attribute->set_options($value);
            $attribute->set_position($index);
            $attribute->set_visible(true);
            $attribute->set_variation(false); // here it is
            $attrs[] = $attribute;
            $index++;
        }

        $product->set_attributes($attrs);
        $product->save();


//        $product->set_attributes(array($attribute));

//        $variationId = $variation->save();

//        Attribute::addVariationAttribute($variationData, $productId, (int)$variationId);

//        if ((isset($variationData['images'][0]) && !$isImgOption) || $isImgOption === 'false') {
//            Image::addThumb((int)$variationId, $product['images'][0]);
//        }
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

    public static function prepeareAttributes($variations) {
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
