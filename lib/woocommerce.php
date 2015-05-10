<?php
/**
 * Woocommerce CLS Support
 *
 * @package CLS
 **/

 if( ! defined( 'ABSPATH' ) ) return; // No direct access please!!!
 
if( ! class_exists( 'cls_posts' ) ){
        require_once dirname(__FILE__) . '/post.php';
}
class cls_woocommerce extends cls_posts {
        public function __construct(){
                if( post_type_exists( 'product' ) ){
                        parent::__construct( 'product' );
                }
        }
}
new cls_woocommerce;
