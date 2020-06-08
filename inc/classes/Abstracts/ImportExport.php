<?php
    /**
     * Created by PhpStorm.
     * User: gerasart
     * Date: 06.06.2020
     * Time: 01:17
     */
    
    namespace Foks\Abstracts;
    
    
    abstract class ImportExport {
        
        public static $flushCount = 50;
        public static $categoryMap = array();
        
        /**
         * @param $data
         */
        public static function insertProducts( $data ) {
        
        }
        
        /**
         * @param $data
         * @param $post_id
         */
        public static function updateCategories( $data, $post_id ) {
        
        }
        
        /**
         * @param $categories
         */
        public static function addCategories( $categories ) {
       
        }
        
        
        /**
         * @param $id
         */
        public static function isProduct( $id ) {
        
        }
        
        /**
         * @param $offers
         */
        public static function addProducts( $offers ) {
        
        }
        
        
        public static function parseFile( $file ) {
            set_time_limit( 0 );
            $xmlstr = file_get_contents( $file );
            $xml    = new \SimpleXMLElement( $xmlstr );
            
            return $xml;
        }
        
        
        public static function deleteProducts() {
        
        }
        
        
        public static function loadImageFromHost( $link, $img_path ) {
            if ( !file_exists( $img_path ) ) {
                $ch = curl_init( $link );
                $fp = fopen( $img_path, "wb" );
                if ( $fp ) {
                    $options = array( CURLOPT_FILE    => $fp,
                                      CURLOPT_HEADER  => 0,
                                      CURLOPT_TIMEOUT => 60,
                    );
                    curl_setopt_array( $ch, $options );
                    curl_exec( $ch );
                    curl_close( $ch );
                    fclose( $fp );
                }
                
                return file_exists( $img_path );
            }
            
            return true;
        }
        
        public static function translitText( $string ) {
            $replace = array(
                "А" => "A", "а" => "a", "Б" => "B", "б" => "b", "В" => "V", "в" => "v", "Г" => "G", "г" => "g", "Д" => "D", "д" => "d",
                "Е" => "E", "е" => "e", "Ё" => "E", "ё" => "e", "Ж" => "Zh", "ж" => "zh", "З" => "Z", "з" => "z",
                "И" => "I", "и" => "i",
                "Й" => "I", "й" => "i", "К" => "K", "к" => "k", "Л" => "L", "л" => "l", "М" => "M", "м" => "m", "Н" => "N", "н" => "n", "О" => "O", "о" => "o",
                "П" => "P", "п" => "p", "Р" => "R", "р" => "r", "С" => "S", "с" => "s", "Т" => "T", "т" => "t", "У" => "U", "у" => "u", "Ф" => "F", "ф" => "f",
                "Х" => "H", "х" => "h", "Ц" => "Tc", "ц" => "tc", "Ч" => "Ch", "ч" => "ch", "Ш" => "Sh", "ш" => "sh", "Щ" => "Shch", "щ" => "shch",
                "Ы" => "Y", "ы" => "y", "Э" => "E", "э" => "e", "Ю" => "Iu", "ю" => "iu", "Я" => "Ia", "я" => "ia", "ъ" => "", "ь" => "",
                "«" => "", "»" => "", "„" => "", "“" => "", "“" => "", "”" => "", "\•" => "", "%" => "", "$" => "", "_" => "-", " " => "-", "." => "-", "," => "-",
                "І" => "I", "і" => "i",
                "Ї" => "Yi", "ї" => "yi",
                "Є" => "Ye", "є" => "ye",
            );
            
            $output = iconv( "UTF-8", "UTF-8//IGNORE", strtr( $string, $replace ) );
            $output = strtolower( $output );
            $output = preg_replace( '~[^-a-z0-9_]+~u', '-', $output );
            $output = trim( $output, "-" );
            $output = str_replace( "----", "-", $output );
            $output = str_replace( "---", "-", $output );
            $output = str_replace( "--", "-", $output );
            return $output;
        }
        
    }
