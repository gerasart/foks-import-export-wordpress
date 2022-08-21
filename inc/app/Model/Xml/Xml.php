<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model\Xml;

class Xml
{
    /**
     * @param string $file
     *
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public static function simpleXml(string $file): \SimpleXMLElement
    {
        set_time_limit(0);
        $xmlStr = file_get_contents($file);

        return new \SimpleXMLElement($xmlStr);
    }
}
