<?php
/**
 * Created by seonarnia.com.
 * User: gerasymenkoph@gmail.com
 */
namespace Foks;

use Foks\Import\Import;
use Foks\Log\Logger;

class AjaxActions extends Ajax
{
    public function __construct()
    {
        self::declaration_ajax();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function ajax_nopriv_importFoks(): void
    {
        Logger::file(0, 'total', 'json');

        $file = get_option('foks_import');
        $data = [];

        if ($file) {
            $xml = file_get_contents($file);
            Logger::file($xml, 'foks_import', 'xml');
            $file_path = FOKS_URL . '/logs/foks_import.xml';
            $data = Import::importData($file_path);
        }

        wp_send_json_success($data);
    }

    /**
     * @return void
     */
    public static function ajax_nopriv_saveSettings(): void
    {
        $post = $_POST['data'];

        $import = update_option('foks_import', $post['import']);
        $update = update_option('foks_update', $post['update']);
        $img = update_option('foks_img', $post['img']);

        $result = [
            'import' => $import,
            'update' => $update,
            'img' => $img,
        ];

        wp_send_json_success($result);
    }
}
