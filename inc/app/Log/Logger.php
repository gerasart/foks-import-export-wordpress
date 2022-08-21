<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Log;

class Logger implements LoggerInterface
{
    /**
     * {@inheritDoc}
     */
    public static function debug($data, string $name, string $format = 'log'): void
    {
        $filePath = FOKS_PATH . "/logs/$name.$format";

        file_put_contents($filePath, json_encode($data) . "\n", FILE_APPEND);
    }

    public static function file($data, string $name, string $format = 'log') {
        $filePath = FOKS_PATH . "/logs/$name.$format";

        file_put_contents($filePath, $data);
    }
}
