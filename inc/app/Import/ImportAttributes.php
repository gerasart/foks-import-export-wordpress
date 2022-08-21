<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Import;

use Foks\Model\Resource\AttributeResourceModel;
use Foks\Model\Resource\LogResourceModel;
use Foks\Model\Translit;
use Foks\Model\Woocommerce\ProductVariation;
use Foks\Model\Xml\Product as ProductXml;
use Foks\Model\Xml\Xml;

class ImportAttributes
{
    /**
     * @param string $file
     * @return void
     */
    public static function execute(string $file): void
    {
        try {
            AttributeResourceModel::delete();
            $xml = Xml::simpleXml($file);
            $products = ProductXml::execute($xml->shop->offers);
            $variations = ProductVariation::prepareVariationProducts($products);
            $attrs = [];

            foreach ($variations as $item) {
                $attributes = ProductVariation::prepareAttributes($item);
                foreach ($attributes as $attr => $value) {
                    $attrs[] = $attr;
                }
            }

            $attrsUniq = array_unique($attrs);

            foreach ($attrsUniq as $item) {
                AttributeResourceModel::set([
                    'slug' => Translit::execute($item, true),
                    'name' => $item,
                ]);
            }

        } catch (\Exception $e) {
            LogResourceModel::set([
                'action' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
