<?php
/**
 * CLS for Dashboard
 **/
class cls_dashboard extends class_cls {
        var $id = 'cls_dashboard';
        
        public function __construct(){
                parent::__construct();
                add_action( 'admin_head', array( $this, 'admin_head' ), 500 );
        }
        
        public function admin_head(){
                global $wp_meta_boxes, $current_user;
                $dashboards = (array) $wp_meta_boxes['dashboard'];
                $values = $this->values();
              
                foreach( $dashboards as $location => $dashboard ){
                        foreach( $dashboard as $context => $widgets ){
                                foreach( $widgets as $widget_id => $widget ){
                                        if( isset($values[$widget_id] ) ){
                                                $value = $values[$widget_id];
                                                $users = (array) $value['user'];
                                                if( !$this->is_controller() && $this->can( $users ) ){
                                                        unset( $wp_meta_boxes['dashboard'][$location][$context][$widget_id] );
                                                }
                                        }
                                }
                        }
                }
        }
        
        public function in_page(){
                global $pagenow;
                return $this->is_controller() && $pagenow == 'index.php';
        }
        
        public function admin_footer(){                
                if( $this->in_page() ){
                        parent::admin_footer();
                        wp_enqueue_script( 'dashboard-js', CLS_HOST . '/js/dashboard.js' );
                        $this->get_views( 'dashboard' );
                }
        }
}
new cls_dashboard;