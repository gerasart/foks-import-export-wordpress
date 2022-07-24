<?php
declare(strict_types=1);

/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 * https://github.com/woocommerce/wc-smooth-generator
 */

namespace Foks\Model;

class ProductVariation
{
    /**
     * @param int $productId
     * @param array $data
     * @return void
     */
    public static function create(int $productId, array $data): void
    {
//        $isImgOption = get_option('foks_img');
//        $imageId = null;
//
//        if (!$isImgOption || $isImgOption === 'false') {
//            $imageId = Image::getAttachmentIdFromUrl($variationData['images'][0], $productId);
//        }
        foreach ($data['variation'] as $product) {
            $attributes = [];
            foreach ($product['attributes'] as $item) {
            }
        }
        $variation_data =  [
            'attributes' => [
                'size'  => 'M',
                'color' => 'Green',
            ],
            'sku'           => '',
            'regular_price' => '22.00',
            'sale_price'    => '',
            'stock_qty'     => 10,
        ];
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

    public static function create_product_variation($productId, $variation_data)
    {
        // Get the Variable product object (parent)
        $product = wc_get_product($productId);

        $variation_post = [
            'post_title' => $product->get_name(),
            'post_name' => 'product-' . $productId . '-variation',
            'post_status' => 'publish',
            'post_parent' => $productId,
            'post_type' => 'product_variation',
            'guid' => $product->get_permalink()
        ];

        // Creating the product variation
        $variation_id = wp_insert_post($variation_post);

        // Get an instance of the WC_Product_Variation object
        $variation = new \WC_Product_Variation($variation_id);

        // Iterating through the variations attributes
        foreach ($variation_data['attributes'] as $attribute => $term_name) {
            $taxonomy = 'pa_' . $attribute; // The attribute taxonomy

            // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
            if (!taxonomy_exists($taxonomy)) {
                register_taxonomy(
                    $taxonomy,
                    'product_variation',
                    [
                        'hierarchical' => false,
                        'label' => ucfirst($attribute),
                        'query_var' => true,
                        'rewrite' => ['slug' => sanitize_title($attribute)], // The base slug
                    ]
                );
            }

            // Check if the Term name exist and if not we create it.
            if (!term_exists($term_name, $taxonomy)) {
                wp_insert_term($term_name, $taxonomy);
            } // Create the term

            $term_slug = get_term_by('name', $term_name, $taxonomy)->slug; // Get the term slug

            // Get the post Terms names from the parent variable product.
            $post_term_names = wp_get_post_terms($productId, $taxonomy, ['fields' => 'names']);

            // Check if the post term exist and if not we set it in the parent variable product.
            if (!in_array($term_name, $post_term_names, true)) {
                wp_set_post_terms($productId, $term_name, $taxonomy, true);
            }

            // Set/save the attribute data in the product variation
            update_post_meta($variation_id, 'attribute_' . $taxonomy, $term_slug);
        }

        ## Set/save all other data

        // SKU
        if (!empty($variation_data['sku'])) {
            $variation->set_sku($variation_data['sku']);
        }

        // Prices
        if (empty($variation_data['sale_price'])) {
            $variation->set_price($variation_data['regular_price']);
        } else {
            $variation->set_price($variation_data['sale_price']);
            $variation->set_sale_price($variation_data['sale_price']);
        }
        $variation->set_regular_price($variation_data['regular_price']);

        // Stock
        if (!empty($variation_data['stock_qty'])) {
            $variation->set_stock_quantity($variation_data['stock_qty']);
            $variation->set_manage_stock(true);
            $variation->set_stock_status('');
        } else {
            $variation->set_manage_stock(false);
        }

        $variation->set_weight(''); // weight (reseting)

        $variation->save(); // Save the data
    }
}
