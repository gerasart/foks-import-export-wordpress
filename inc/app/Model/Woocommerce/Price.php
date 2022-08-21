<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Woocommerce;

class Price
{
    /**
     * @param $price
     * @return string
     */
    public static function formatPrice($price) : string
    {
        if ($price) {
            $args = [
                'decimal_separator' => wc_get_price_decimal_separator(),
                'thousand_separator' => wc_get_price_thousand_separator(),
                'decimals' => wc_get_price_decimals(),
            ];

            return number_format((int)$price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator']);
        }

        return '';
    }
}
