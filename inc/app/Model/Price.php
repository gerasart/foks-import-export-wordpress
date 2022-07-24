<?php
declare(strict_types=1);

/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

namespace Foks\Model;

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
