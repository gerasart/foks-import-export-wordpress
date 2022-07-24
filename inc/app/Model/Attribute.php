<?php
declare(strict_types=1);

/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

namespace Foks\Model;

class Attribute
{
    /**
     * @param int $productId
     * @param array $attrs
     */
    public static function addAttributeGroup(int $productId, array $attrs): void
    {
        $product_attributes = [];
        $i = 0;

        foreach ($attrs as $attr) {
            $product_attributes[sanitize_title($attr['name'])] = [
                'name' => wc_clean($attr['name']), // set attribute name
                'value' => $attr['value'], // set attribute value
                'position' => $i,
                'is_visible' => 1,
                'is_variation' => 0,
                'is_taxonomy' => 0
            ];
            $i++;
        }

        update_post_meta($productId, '_product_attributes', $product_attributes);
    }

    /**
     * @param array $variationData
     * @param int $productId
     * @param int $variationId
     * @return void
     */
    public static function addVariationAttribute(array $variationData, int $productId, int $variationId): void
    {
        // Iterating through the variations attributes
        foreach ($variationData['attributes'] as $attribute) {
            $taxonomy = 'pa_' . Translit::execute($attribute['name']); // The attribute taxonomy

            if (!taxonomy_exists($taxonomy)) {
                register_taxonomy(
                    $taxonomy,
                    'product_variation',
                    [
                        'hierarchical' => false,
                        'label' => ucfirst($attribute['name']),
                        'query_var' => true,
                        'rewrite' => ['slug' => sanitize_title($attribute['name'])], // The base slug
                    ]
                );
            }

            // Check if the Term name exist and if not we create it.
            if (!term_exists($attribute['value'], $taxonomy)) {
                wp_insert_term($attribute['value'], $taxonomy);
            } // Create the term

            $term_slug = get_term_by('name', $attribute['value'], $taxonomy)->slug; // Get the term slug
            // Get the post Terms names from the parent variable product.
            $post_term_names = wp_get_post_terms($productId, $taxonomy, array('fields' => 'names'));

            // Check if the post term exist and if not we set it in the parent variable product.
            if (!in_array($attribute['value'], $post_term_names, true)) {
                wp_set_post_terms($productId, $attribute['value'], $taxonomy, true);
            }

            // Set/save the attribute data in the product variation
            update_post_meta($variationId, 'attribute_' . $taxonomy, $term_slug);
        }
    }
}
