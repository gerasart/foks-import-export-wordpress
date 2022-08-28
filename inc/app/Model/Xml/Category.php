<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Xml;

class Category
{
    /**
     * @param $categories
     * @return array
     */
    public static function execute($categories): array
    {
        $categoriesList = [];
        $data = $categories->category;

        foreach ($data as $category) {
            $categoriesList[] = [
                'parent_id' => (int)$category['parentId'],
                'name' => trim((string)$category),
                'id' => (string)$category['id'],
                'parent_name' => ''
            ];
        }

        $categories_result = [];

        foreach ($categoriesList as $item) {
            $item['parent_name'] = self::getParentCatName($categoriesList, $item['parent_id']);
            $categories_result[] = $item;
        }

        return $categories_result;
    }

    /**
     * @param array $categoriesList
     * @param int $parentId
     * @param int|null $id
     *
     * @return string
     */
    public static function getParentCatName(array $categoriesList, int $parentId, ?int $id = null): string
    {
        $catName = '';

        foreach ($categoriesList as $cat) {
            if ((int)$cat['id'] === $parentId) {
                $catName = $cat['name'];
                break;
            }

            if ($id && (int)$cat['id'] === $id) {
                $catName = $cat['name'];
            }

        }

        return $catName;
    }
}
