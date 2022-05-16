<?php
declare(strict_types=1);

/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

namespace Foks\Model;

class ProductVariation
{
    /**
     * @param int $productId
     * @param array $variationData
     * @return void
     * @throws \WC_Data_Exception
     */
    public static function create(int $productId, array $variationData): void
    {
        $product = wc_get_product($productId);

        $isImgOption = get_option('foks_img');
        $variation_post = [
            'post_title' => $variationData['name'],
            'post_name' => 'product-' . $productId . '-variation',
            'post_status' => 'publish',
            'post_parent' => $productId,
            'post_type' => 'product_variation',
            'guid' => $product->get_permalink()
        ];
        // Creating the product variation
        $variationId = wp_insert_post($variation_post);
        // Get an instance of the WC_Product_Variation object
        $variation = new \WC_Product_Variation($variationId);

        if ((isset($variationData['images'][0]) && !$isImgOption) || $isImgOption === 'false') {
            Image::addThumb((int)$variationId, $product['images'][0]);
        }

        Attribute::addVariationAttribute($variationData, $productId, (int)$variationId);

        // SKU
        if (!empty($variationData['sku'])) {
            $variation->set_sku($variationData['sku']);
        }

        // Prices
        if (empty($variationData['price_old'])) {
            $variation->set_price($variationData['price']);
        } else {
            $variation->set_price($variationData['price_old']);
            $variation->set_sale_price($variationData['price']);
        }

        $variation->set_regular_price($variationData['price']);

        // Stock
        if (!empty($variationData['quantity'])) {
            $variation->set_stock_quantity($variationData['quantity']);
            $variation->set_manage_stock(true);
            $variation->set_stock_status('');
        } else {
            $variation->set_manage_stock(false);
        }

        $variation->set_weight('');
        $variation->save();
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
}
