<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

declare(strict_types=1);

namespace Foks\Export;

interface ExportInterface
{
    /**
     * @return array
     */
    public static function getProducts(): array;

    /**
     * @return array
     */
    public static function getCategories(): array;
}
