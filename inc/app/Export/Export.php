<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */
namespace Foks\Export;

use Foks\Log\Logger;
use Foks\Model\Product;

class Export implements ExportInterface
{
    public static $taxonomy = 'product_cat';

    public static function getProducts(): array
    {
        $args = [
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'return' => 'ids',
            'status' => 'publish'
        ];

        $query = new \WC_Product_Query($args);
        $products = $query->get_products();
        $result = [];

        foreach ($products as $product_id) {
            $result[] = Product::getProductById($product_id);
        }
        return $result;
    }

    /**
     * @return array
     */
    public static function getCategories(): array
    {
        $categories = get_categories([
            'taxonomy' => self::$taxonomy,
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

    /**
     * @return void
     */
    public static function generateXML(): void
    {
        $categories = self::getCategories();
        $products = self::getProducts();
        $site_url = get_site_url();
        $site_name = get_bloginfo('name');
        $currency = get_woocommerce_currency();

        $date = date('Y-m-d H:i:s');

        $output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $output .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\n";
        $output .= '<yml_catalog date="' . $date . '">' . "\n";
        $output .= '<shop>' . "\n";
        if ($site_name) {
            $output .= '<name>' . $site_name . '</name>' . "\n";
            $output .= '<company>' . $site_name . '</company>' . "\n";
        }
        $output .= '<url>' . $site_url . '</url>' . "\n";
        $output .= '<currencies>' . "\n";
        $output .= '<currency id="' . $currency . '" rate="1" />' . "\n";
        $output .= '</currencies>' . "\n";
        if ($categories) {
            $output .= '<categories>' . "\n";
            foreach ($categories as $item) {
                $output .= "\t" . '<category id="' . $item->term_id . '">' . $item->name . '</category>' . "\n";
                if (!empty($item->children)) {
                    foreach ($item->children as $child) {
                        $output .= "\t" . '<category parent_id="' . $child->category_parent . '" id="' . $child->term_id . '">' . $child->name . '</category>' . "\n";
                    }
                }
            }
            $output .= '</categories>' . "\n";
        }
        $output .= '<offers>' . "\n";
        foreach ($products as $product) {
            if ($product) {
                $output .= "\t" . '<offer id="' . $product->id . '" available="true">' . "\n";
                $output .= "\t" . '<categoryId>' . $product->category_id . '</categoryId>' . "\n";
                $output .= "\t" . '<stock_quantity>' . $product->quantity . '</stock_quantity>' . "\n";
                $output .= "\t" . '<url>' . $product->url . '</url>' . "\n";
                if ((int)$product->sale_price) :
                    $output .= "\t" . '<price>' . $product->sale_price . '</price>' . "\n";
                    $output .= "\t" . '<price_old>' . $product->price . '</price_old>' . "\n";
                else:
                    $output .= "\t" . '<price>' . $product->price . '</price>' . "\n";
                endif;
                $output .= "\t" . '<currencyId>' . $currency . '</currencyId>' . "\n";
                if ($product->thumb) :
                    $output .= "\t" . '<picture>' . $product->thumb . '</picture>' . "\n";
                endif;
                if ($product->images):
                    foreach ($product->images as $img) {
                        $output .= "\t" . '<picture>' . $img . '</picture>' . "\n";
                    }
                endif;
                if ($product->vendor) :
                    $output .= "\t" . '<vendor>' . $product->vendor . '</vendor>' . "\n";
                endif;
                $output .= "\t" . '<name>' . $product->title . '</name>' . "\n";
                $output .= "\t" . '<description>' . htmlspecialchars($product->description) . "\n";
                $output .= "\t" . '</description>' . "\n";
                if ($product->params):
                    foreach ($product->params as $attr) :
                        if (!$attr['terms']) {
                            $output .= "\t" . '<param name="' . $attr['name'] . '">' . $attr['value'] . '</param>' . "\n";
                        } else {
                            $attr_name = wc_attribute_label($attr['name']);
                            $output .= "\t" . '<param name="' . $attr_name . '">' . $attr['value'] . '</param>' . "\n";
                        }
                    endforeach;
                endif;
                $output .= "\t" . '</offer>' . "\n";
            }
        }
        $output .= '</offers>' . "\n";
        $output .= '</shop>' . "\n";
        $output .= '</yml_catalog>';

        Logger::file($output,'foks_export', 'xml');

        header("Content-Type: application/xml; charset=utf-8");

        echo $output;
    }

    /**
     * @return void
     */
    public static function getGenerateXml(): void
    {
        register_rest_route('foks', 'foksExport', [
            'methods' => 'GET',
            'callback' => __CLASS__ . '::generateXML',
            'permission_callback' => '__return_true',
        ]);
    }
}
