<?php
/**
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

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
