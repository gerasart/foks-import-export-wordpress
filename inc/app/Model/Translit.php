<?php
/*
 * Copyright (c) 2022.
 * Created by metasync.site.
 * Developer: gerasymenkoph@gmail.com
 * Link: https://t.me/gerasart
 */

declare(strict_types=1);

namespace Foks\Model;

class Translit
{
    /**
     * @param string $title
     * @param bool $isUrl
     * @return string
     */
    public static function execute(string $title, bool $isUrl = false): string
    {
        $words = [
            "Є" => "EH", "І" => "I", "і" => "i", "№" => "#", "є" => "eh",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
            "Е" => "E", "Ё" => "JO", "Ж" => "ZH",
            "З" => "Z", "И" => "I", "Й" => "JJ", "К" => "K", "Л" => "L",
            "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "KH",
            "Ц" => "C", "Ч" => "CH", "Ш" => "SH", "Щ" => "SHH", "Ъ" => "'",
            "Ы" => "Y", "Ь" => "", "Э" => "EH", "Ю" => "YU", "Я" => "YA",
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
            "е" => "e", "ё" => "jo", "ж" => "zh",
            "з" => "z", "и" => "i", "й" => "jj", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "kh",
            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
            "ы" => "y", "ь" => "", "э" => "eh", "ю" => "yu", "я" => "ya",
            "—" => "-", "«" => "", "»" => "", "…" => ""
        ];

        if (!$isUrl) {
            return strtr($title, $words) ?: '';
        }

        $result = wc_strtolower($title);
        $result = strtr($result, $words);
        $result = mb_ereg_replace('[^-0-9a-z]', '-', $result);
        $result = mb_ereg_replace('[-]+', '-', $result);

        return trim($result, '-');
    }
}
