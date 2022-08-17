<?php
/**
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Log;

interface LoggerInterface
{
    /**
     * @param mixed $data
     * @param string $name
     * @param string $format
     * @return void
     */
    public static function debug($data, string $name, string $format = 'log'): void;
}
