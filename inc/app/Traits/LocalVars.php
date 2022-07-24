<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */

declare(strict_types=1);

namespace Foks\Traits;

trait LocalVars
{
    /**
     * @return void
     */
    public function viewData(): void
    {
        $import = get_option('foks_import');
        $update = get_option('foks_update');
        $img = get_option('foks_img');

        self::$admin_vars['foks'] = [
            'import' => $import ?: '',
            'update' => $update ?: '1',
            'export' => get_site_url() . '/wp-json/foks/foksExport',
            'logs_url' => FOKS_URL . 'logs/',
            'img' => $img === 'true' && (boolean)$img
        ];
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
}
