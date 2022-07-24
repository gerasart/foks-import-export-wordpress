<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

declare(strict_types=1);

namespace Foks\Model;

use Foks\Import\Import;

class Category
{
    /**
     * @param $productId
     * @return int
     */
    public static function getCategoryId($productId): int
    {
        $terms = get_the_terms($productId, 'product_cat');
        $categoryId = 0;

        if ($terms) {
            foreach ($terms as $term) {
                if ($term->term_id) {
                    $categoryId = $term->term_id;
                    break;
                }
            }
        }

        return $categoryId;
    }

    /**
     * @param $category_data
     *
     * @return mixed
     */
    public static function addCategories($category_data)
    {
        foreach ($category_data as $cat) {
            if (!$cat['parent_id']) {
                $term_exist = term_exists((string)$cat['parent_name'], 'product_cat');

                if (!$term_exist) {
                    wp_insert_term((string)$cat['name'], 'product_cat');
                }

            }
        }

        foreach ($category_data as $cat) {
            if ($cat['parent_id']) {
                $parent = term_exists((string)$cat['parent_name'], 'product_cat');
                $term_exist = term_exists((string)$cat['name'], 'product_cat');

                if ($parent) {
                    $parent_arr = [
                        'description' => (string)$cat['name'],
                        'parent' => $parent['term_id'],
                        'slug' => Translit::execute((string)$cat['name'], true)
                    ];

                    if (!$term_exist) {
                        wp_insert_term((string)$cat['name'], 'product_cat', $parent_arr);
                    }
                } else if (!$term_exist) {
                    wp_insert_term((string)$cat['name'], 'product_cat');
                }

            }
        }

        return $category_data;
    }

    /**
     * @param $product
     * @param $product_id
     * @param $categories
     *
     * @return mixed
     */
    public static function updateCategory($product, $product_id, $categories)
    {
        $term_name = Import::getParentCatName($categories, $product['category'], $product['category']);
        $cat = term_exists($term_name, 'product_cat');

        if ($cat) {
            wp_set_post_terms($product_id, (string)$cat['term_id'], 'product_cat');
        }

        return $cat;
    }
}
