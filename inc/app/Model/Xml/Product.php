<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Xml;

class Product
{
    /**
     * @param $offers
     * @return array
     */
    public static function execute($offers): array
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
}
