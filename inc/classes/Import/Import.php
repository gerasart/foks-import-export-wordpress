<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

namespace Foks\Import;

use Foks\Log\Logger;
use Foks\Model\Category;
use Foks\Model\Product;
use Foks\Model\ProductVariation;

class Import
{
    /**
     * @param $offers
     * @return array
     */
    public static function parseProducts($offers): array
    {
        $n = count($offers->offer);
        $result = [];

        for ($i = 0; $i < $n; $i++) {
            $offer = $offers->offer[$i];
            $product_images = [];

            foreach ($offer->picture as $picture) {
                $product_images[] = (string)$picture;
            }

            $productName = (string)$offer->name;

            if (!$productName) {
                if (isset($offer->typePrefix)) {
                    $productName = (string)$offer->typePrefix . ' ' . (string)$offer->model;
                } else {
                    $productName = (string)$offer->model;
                }
            }

            $product_description = (string)$offer->description;
            $id_category = (int)$offer->categoryId;
            $data = [
                'foks_id' => (string)$offer['id'],
                'name' => $productName,
                'description' => $product_description,
                'category' => $id_category,
                'model' => (!empty($offer->vendorCode)) ? (string)$offer->vendorCode : (string)$offer['id'],
                'thumb' => $product_images[0],
                'sku' => (!empty($offer->vendorCode)) ? (string)$offer->vendorCode : (string)$offer['id'],
                'quantity' => (isset($offer->stock_quantity)) ? (int)$offer->stock_quantity : 0,
                'date_available' => date('Y-m-d'),
                'price' => (float)$offer->price,
                'price_old' => (float)$offer->price_old,
                'status' => '0',
                'images' => $product_images,
                'attributes' => [],
                'manufacturer' => '',
                'master' => isset($offer->master) ? (int)$offer->master : 0,
                'group_id' => isset($offer['group_id']) ? (int)$offer['group_id'] : 0,
                'variation' => [],
            ];

            if (isset($offer->vendor)) {
                $data['manufacturer'] = (string)$offer->vendor;
            }

            if (isset($offer->param)) {
                $params = $offer->param;

                foreach ($params as $param) {
                    $attr_name = (string)$param['name'];
                    $attr_value = (string)$param;

                    $data['attributes'][] = [
                        'name' => $attr_name,
                        'value' => $attr_value
                    ];
                }
            }

            $result[$i] = $data;

        }

        return $result;
    }

    /**
     * @param $categories
     * @return array
     */
    public static function parseCategories($categories): array
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
     * @param $categoriesList
     * @param $parent_id
     * @param $id
     * @return string
     */
    public static function getParentCatName($categoriesList, $parent_id, $id = null): string
    {
        $catName = '';

        foreach ($categoriesList as $cat) {
            if ((int)$cat['id'] === $parent_id) {
                $catName = $cat['name'];
                break;
            }

            if ($id && (int)$cat['id'] === $id) {
                $catName = $cat['name'];
            }

        }

        return $catName;
    }

    /**
     * @param $file
     * @return array
     *
     * @throws \Exception
     */
    public static function parseFile($file): array
    {
        $xml = self::parseSimpleXml($file);

        return [
            'products' => self::parseProducts($xml->shop->offers),
            'categories' => self::parseCategories($xml->shop->categories)
        ];
    }

    /**
     * @param $file
     * @return array
     * @throws \Exception
     */
    public static function importData($file): array
    {
        $data = self::parseFile($file);
        $categories = Category::addCategories($data['categories']);
        $products = ProductVariation::prepareVariationProducts($data['products']);
        Logger::file(count($products), 'total', 'json');
        Product::addProducts($products, $categories);

        return $data;
    }

    /**
     * @param $file
     *
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public static function parseSimpleXml($file): \SimpleXMLElement
    {
        set_time_limit(0);
        $xmlStr = file_get_contents($file);

        return new \SimpleXMLElement($xmlStr);
    }
}
