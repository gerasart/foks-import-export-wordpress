<?php
    /**
     * Created by PhpStorm.
     * User: gerasart
     * Date: 6/5/2020
     * Time: 3:46 PM
     */
    
    namespace Foks\Import;
    
    
    use Foks\Abstracts\ImportExport;
    use Foks\Model\ModelProduct;
    
    class Import extends ImportExport {
    
        static $productsAdded = 0;
    
        static $productsUpdated = 0;
        
        public static $categoryMap = array();
        
        
        public static function addProducts( $offers ) {
            $id_category = 0;
            //Here is start adding products
            $n            = count( $offers->offer );
            $flushCounter = self::$flushCount;
            
            $attrGroupId = 0;
            
            for ( $i = 0; $i < $n; $i++ ) {
                $offer = $offers->offer[ $i ];
                
                $product_images = array();
                
                $dir_name = 'catalog/import_yml/' . implode( '/', str_split( (string)$offer['id'], 3 ) ) . '/';
                if ( !is_dir( FOKS_DIR_IMAGE . $dir_name ) ) {
                    mkdir( FOKS_DIR_IMAGE . $dir_name, 0777, true );
                }
                
                foreach ( $offer->picture as $picture ) {
                    $img_name = substr( strrchr( $picture, '/' ), 1 );
                    
                    if ( !empty( $img_name ) ) {
                        $image = self::loadImageFromHost( $picture, DIR_IMAGE . $dir_name . $img_name );
                        if ( $image ) {
                            $product_images[] = array( 'image' => $dir_name . $img_name, 'sort_order' => count( $product_images ) );
                        }
                    }
                }
                
                $image_path = array_shift( $product_images );
                if ( is_array( $image_path ) ) {
                    $image_path = $image_path['image'];
                }
                
                $productName = (string)$offer->name;
                if ( !$productName ) {
                    if ( isset( $offer->typePrefix ) ) {
                        $productName = (string)$offer->typePrefix . ' ' . (string)$offer->model;
                    } else {
                        $productName = (string)$offer->model;
                    }
                }
                
                $product_description = array(
                    'name'             => $productName,
                    'meta_title'       => $productName,
                    'meta_h1'          => $productName,
                    'meta_keyword'     => '',
                    'meta_description' => '',
                    'description'      => (string)$offer->description,
                    'tag'              => '',
                );
                
                if ( (int)$offer->categoryId == 0 || !isset( self::$categoryMap[ (int)$offer->categoryId ] ) ) {
                    foreach ( self::$categoryMap as $key => $cat ) {
                        if ( in_array( (string)$offer->categoryId, $cat ) ) {
                            $id_category = $cat['category_id'];
                        }
                    }
                } else {
                    $id_category = self::$categoryMap[ (int)$offer->categoryId ]['category_id'];
                }
                $data = array(
                    'product_description' => $product_description,
                    'product_special'     => array(),
                    'product_store'       => array( 0 ),
                    'main_category_id'    => $id_category,
                    'product_category'    => array(
                        $id_category,
                    ),
                    'product_attribute'   => array(),
                    'model'               => (!empty( $offer->vendorCode )) ? (string)$offer->vendorCode : (string)$offer['id'],
                    'image'               => $image_path,
                    'sku'                 => (!empty( $offer->vendorCode )) ? (string)$offer->vendorCode : (string)$offer['id'],
                    'keyword'             => self::translitText( $productName ),
                    'quantity'            => (isset( $offer->outlets->outlet['instock'] )) ? (int)$offer->outlets->outlet['instock'] : '999',
                    'stock_status_id'     => ($offer['available'] == 'true') ? 7 : 8,
                    'date_available'      => date( 'Y-m-d' ),
                    'price'               => (float)$offer->price,
                    'status'              => '0',
                    'images'              => $product_images,
                );
                
                if ( isset( $offer->vendor ) ) {
                    $vendor_name = (string)$offer->vendor;
                    
                    if ( !isset( $vendorMap[ $vendor_name ] ) ) {
                        $manufacturer_data = array(
                            'name'                     => $vendor_name,
                            'sort_order'               => 0,
                            'manufacturer_description' => array(
                                1 => array(
                                    'name'             => $vendor_name,
                                    'description'      => $vendor_name,
                                    'meta_title'       => $vendor_name,
                                    'meta_h1'          => $vendor_name,
                                    'meta_keyword'     => $vendor_name,
                                    'meta_description' => $vendor_name,
                                )
                            ),
                            'manufacturer_store'       => array( 0 ),
                            'keyword'                  => self::translitText( $vendor_name ),
                        );
                        
                        $vendorMap[ $vendor_name ] = ModelProduct::addManufacturer( $manufacturer_data );
                    }
                    
                    $data['manufacturer_id'] = $vendorMap[ (string)$offer->vendor ];
                }
                
                $vendorMap = ModelProduct::loadManufactures();
                
                $attrMap = ModelProduct::loadAttributes();
                
                if ( isset( $offer->param ) ) {
                    $params = $offer->param;
                    
                    foreach ( $params as $param ) {
                        $attr_name  = (string)$param['name'];
                        $attr_value = (string)$param;
                        
                        if ( array_key_exists( $attr_name, $attrMap ) === false ) {
                            $attr_data = array(
                                'sort_order'            => 0,
                                'attribute_group_id'    => $attrGroupId,
                                'attribute_description' => array(
                                    1 => array(
                                        'name' => $attr_name,
                                    )
                                ),
                            );
                            
                            $attrMap[ $attr_name ] = ModelProduct::addAttribute( $attr_data );
                        }
                        
                        $data['product_attribute'][] = array(
                            'attribute_id'                  => $attrMap[ $attr_name ],
                            'product_attribute_description' => array(
                                1 => array(
                                    'text' => $attr_value,
                                )
                            )
                        );
                    }
                }
//                if ( array_key_exists( $data['model'], $this->skuProducts ) ) {
//                    $data = $this->changeDataByColumns( $this->skuProducts[ $data['model'] ], $data );
//                    $this->model_catalog_product->editProduct( $this->skuProducts[ $data['model'] ], $data );
//                    $productsUpdated++;
//                    if ( $force ) {
//                        $this->error['warning'] .= "Update Product : " . $data['model'] . " ";
//                    }
//                } else {
//                    $this->skuProducts[ $data['model'] ] = $this->model_catalog_product->addProduct( $data );
//                    self::$productsAdded++;
//                }
                
                --$flushCounter;
                
                if ( $flushCounter <= 0 ) {
                    $loaded = $i;
                    
                    $flushCounter = self::$flushCount;
                }
            }
        }
        
        public static function addCategories( $categories ) {
            self::$categoryMap[0] = array(
                'category_id' => 0,
                'name'        => 0
            );
            $categoriesList       = array();
            foreach ( $categories->category as $category ) {
                $categoriesList[ (string)$category['id'] ] = array(
                    'parent_id' => (int)$category['parentId'],
                    'name'      => trim( (string)$category )
                );
            }
            // Compare categories level by level and create new one, if it doesn't exist
            while ( count( $categoriesList ) > 0 ) {
                $previousCount = count( $categoriesList );
                
                foreach ( $categoriesList as $source_category_id => $item ) {
                    if ( array_key_exists( (int)$item['parent_id'], self::$categoryMap ) ) {
                        $category = ModelProduct::loadCategory( self::$categoryMap[ $item['parent_id'] ]['category_id'], $item['name'] );
                        if ( $category->row ) {
                            self::$categoryMap[ (int)$source_category_id ] = array(
                                'category_id' => $category->row['category_id'],
                                'name'        => $item['name']
                            );
                        } else {
                            $category_data = array(
                                'sort_order'           => 0,
                                'parent_id'            => self::$categoryMap[ (int)$item['parent_id'] ]['category_id'],
                                'top'                  => 0,
                                'status'               => 1,
                                'column'               => '',
                                'category_description' => array(
                                    1 => array(
                                        'name'             => $item['name'],
                                        'meta_title'       => $item['name'],
                                        'meta_h1'          => $item['name'],
                                        'meta_keyword'     => '',
                                        'meta_description' => '',
                                        'description'      => '',
                                    )
                                ),
                                'keyword'              => self::translitText( $item['name'] ),
                                'category_store'       => array(
                                    0
                                ),
                            );
                            
                            if ( $category_data['parent_id'] == 0 ) {
                                $category_data['top'] = 1;
                            }
                            
                            self::$categoryMap[ (int)$source_category_id ] = array(
                                'category_id' => ModelProduct::addCategory( $category_data ),
                                'name'        => $item['name']
                            );
                        }
                        unset( $categoriesList[ $source_category_id ] );
                    }
                }
                
                if ( count( $categoriesList ) === $previousCount ) {
                    break;
                }
            }
        }
        
        public static function parseFile( $file ) {
            set_time_limit( 0 );
            $xmlstr = file_get_contents( $file );
            $xml    = new \SimpleXMLElement( $xmlstr );
            
            self::addCategories( $xml->shop->categories );
            self::addProducts( $xml->shop->offers );
            
            return $xml;
        }
    }
