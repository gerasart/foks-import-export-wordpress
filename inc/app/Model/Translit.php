<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

declare(strict_types=1);

namespace Foks\Model;

class Translit
{
    /**
     * @param $title
     * @param $url
     * @return string
     */
    public static function execute($title, $url = null): string
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

        if (!$url) {
            return strtr($title, $words) ?: '';
        }

        $title = mb_strtolower($title);
        $title = strtr($title, $words);
        $title = mb_ereg_replace('[^-0-9a-z]', '-', $title);
        $title = mb_ereg_replace('[-]+', '-', $title);

        return trim($title, '-');
    }
}
