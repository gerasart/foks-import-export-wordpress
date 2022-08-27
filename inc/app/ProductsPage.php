<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks;

use Foks\Model\Woocommerce\Grid\Product\VariationNameColumn;

class ProductsPage
{
    public function __construct()
    {
        new VariationNameColumn();
    }
}
