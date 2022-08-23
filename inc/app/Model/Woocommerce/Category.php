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
use Foks\Model\Xml\Category as CategoryXml;

class Category
{
    public const TAXONOMY_ENTITY = 'product_cat';

    /**
     * @param int $productId
     *
     * @return int
     */
    public static function getCategoryId(int $productId): int
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
     * @param array $categoryData
     *
     * @return array
     */
    public static function addCategories(array $categoryData): array
    {
        foreach ($categoryData as $cat) {
            if (!$cat['parent_id']) {
                $term_exist = term_exists((string)$cat['parent_name'], 'product_cat');

                if (!$term_exist) {
                    wp_insert_term((string)$cat['name'], 'product_cat');
                }

            }
        }

        foreach ($categoryData as $cat) {
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

        return $categoryData;
    }

    /**
     * @param array $product
     * @param int $productId
     * @param array $categories
     *
     * @return mixed
     */
    public static function updateCategory(array $product, int $productId, array $categories)
    {
        $term_name = CategoryXml::getParentCatName($categories, $product['category'], $product['category']);
        $cat = term_exists($term_name, 'product_cat');

        if ($cat) {
            wp_set_post_terms($productId, (string)$cat['term_id'], 'product_cat');
        }

        return $cat;
    }

    /**
     * @return array
     */
    public static function getCategories(): array
    {
        $categories = get_categories([
            'taxonomy' => self::TAXONOMY_ENTITY,
            'type' => 'post',
            'child_of' => 0,
            'parent' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'number' => 0,
            'pad_counts' => false,
        ]);
        $topCategories = [];
        $subCategories = [];

        foreach ($categories as $cat) {
            if (isset($cat->category_parent) && $cat->category_parent == 0) {
                $topCategories[$cat->term_id] = $cat;
                $topCategories[$cat->term_id]->children = [];
            } else if ($cat) {
                $subCategories[] = $cat;
            }
        }

        if ($subCategories) {
            foreach ($subCategories as $sub_cat) {
                if (isset($topCategories[$sub_cat->category_parent])) {
                    $topCategories[$sub_cat->category_parent]->children[] = ($sub_cat);
                }
            }
        }

        return $topCategories;
    }
}
