<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Import;

use Foks\Log\Logger;
use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Woocommerce\Category;
use Foks\Model\Woocommerce\Product;
use Foks\Model\Woocommerce\ProductVariation;
use Foks\Model\Xml\Category as CategoryXml;
use Foks\Model\Xml\Product as ProductXml;
use Foks\Model\Xml\Xml;

class Import
{
    public const IMPORT_FILE = 'foks_import';
    public const IMPORT_PATH =  FOKS_PATH . 'logs/'.self::IMPORT_FILE.'.xml';
    public const IMPORT_URL =  FOKS_URL . 'logs/'.self::IMPORT_FILE.'.xml';

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
     * @param string $file
     * @return array
     */
    public static function parseFile(string $file): array
    {
        try {
            $xml = Xml::simpleXml($file);

            return [
                'products' => ProductXml::execute($xml->shop->offers),
                'categories' => CategoryXml::execute($xml->shop->categories)
            ];
        } catch (\Exception $e) {
            LogResourceModel::set([
                'action' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return [
            'product' => [],
            'categories' => [],
        ];
    }
}
