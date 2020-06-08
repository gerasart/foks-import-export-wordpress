<?php
    /**
     * Created by PhpStorm.
     * User: gerasart
     * Date: 7/26/2019
     * Time: 2:51 PM
     */
    
    namespace Foks;
    
    
    use Foks\Helpers\AjaxHelper;
    use Foks\Import\Import;
    
    class AjaxActions extends AjaxHelper {
        
        public function __construct() {
            self::declaration_ajax();
        }
        
        public static function ajax_nopriv_importFoks() {
            $file = get_option( 'foks_import' );
            $data = [];
            if ( $file ) {
                $xml = file_get_contents($file);
                file_put_contents(FOKS_PATH.'/logs/foks_import.xml', $xml);
                $file_path = FOKS_URL.'/logs/foks_import.xml';
                $data = Import::parseFile($file_path);
            }
            $cats = $data->shop->categories;
          
//            wp_send_json_success( $data->shop->categories );

//            var_dump($data);
        }
        
        public static function ajax_nopriv_saveSettings() {
            $post = $_POST['data'];
            
            $import = update_option( 'foks_import', $post['import'] );
            $update = update_option( 'foks_update', $post['update'] );
            
            $result = [
                'import' => $import,
                'update' => $update,
            ];
            wp_send_json_success( $result );
            
        }
        
    }
