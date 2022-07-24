<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */
namespace Foks;

class Ajax
{
    public static $front_vars = [];
    public static $admin_vars = [];

    /**
     * @return void
     */
    public static function declaration_ajax(): void
    {
        $class_methods = get_class_methods(static::class);

        foreach ($class_methods as $name) {
            $ajax = strpos($name, 'ajax');
            $nopriv = strpos($name, 'nopriv');
            $short = str_replace(['ajax_', 'nopriv_'], '', $name);

            if ($ajax === 0) {
                add_action('wp_ajax_' . $short, [static::class, $name]);
            }
            if ($nopriv === 5) {
                add_action('wp_ajax_nopriv_' . $short, [static::class, $name]);
            }
        }
    }

    /**
     * @param $var
     * @return mixed|null
     */
    public static function getPostVar($var)
    {
        return $_POST[$var] ?? null;
    }

    /**
     * @param $var
     * @return mixed|null
     */
    public static function getGetVar($var)
    {
        return $_GET[$var] ?? null;
    }

    /**
     * @return void
     */
    public static function localAdminVars(): void
    {
        echo "<script>";
        foreach (self::$admin_vars as $key => $value) {
            if (!is_string($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            echo "window.{$key} = {$value};" . "\n";
        }
        echo "</script>";
    }

    /**
     * @return void
     */
    public static function localFrontVars(): void
    {
        echo "<script>";

        foreach (self::$front_vars as $key => $value) {
            if (!is_string($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            echo "window.{$key} = {$value}" . "\n";
        }

        echo "</script>";
    }
}
