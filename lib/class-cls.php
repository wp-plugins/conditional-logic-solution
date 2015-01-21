<?php
/**
 * Main Controll class
 **/

class class_cls {
        
        public function __construct(){
                add_action( 'cls_clear_all_settings', array( $this, 'clear' ) );
                add_action( 'admin_footer', array( $this, 'admin_footer' ) );
                add_action( 'wp_ajax_clssettings', array($this, 'save_settings') );
        }
        
        public function is_controller( $current_user = false ){
                if( !$current_user ){ global $current_user; }

                $user_id = (int) $current_user->ID;
                $role = $current_user->roles[0];
                $cls_settings = (array) get_option( 'cls_settings' );
                $is_controller = $role === 'administrator';
              
                if( $is_controller && isset( $cls_settings['controller'] ) ){ 
                        $controller = $cls_settings['controller'];
                        if( $user_id == $controller || $role == $controller ){
                                return true;
                        }
                        elseif( $controller == 'selected' ){
                                $selected = (array) $cls_settings['selected'];
                                return in_array($user_id, $selected);
                        }
                }
                
                return $is_controller;
        }
        
        public function can( $array = array() ){
                global $current_user;
                $user_id = (int) $current_user->ID;
                $role = $current_user->roles[0];
               
                // Set user_id to 'guest' if guest users, otherwise it will return true
                if( $user_id == 0 ) $user_id = $role = 'guest';
              
                return !$this->is_controller() ? in_array($user_id, $array) || in_array( $role, $array ) : true;
        }
        
        public function save_settings(){
                if( isset($_REQUEST[$this->id] ) ){
                        $req = $_REQUEST[$this->id];              
                        $old = $this->values();
                        $values = wp_parse_args( $req, $old );
                        update_option( $this->id, $values );
                }
        }
        
        public function values(){
                $values = (array) get_option( $this->id );
                $values = array_filter( $values, create_function( '$a', ' return !empty($a); ' ) );
                return $values;
        }
        
        public function clear(){
                delete_option( $this->id );
        }
        
        public function get_roles(){
                global $wp_roles;
                $roles = array_map(create_function('$a', ' return $a["name"] . "s"; '), $wp_roles->roles);
                return $roles;
        }
        
        public function get_views( $view ){
                $view_dir = dirname(dirname(__FILE__)) . '/views';
                $view = "{$view_dir}/{$view}.php";
                
                if( file_exists( $view ) ){
                        require_once $view;
                }
        }
        
        public function get_users( $user_role = '' ){
                global $wpdb;

                $roles = self::get_roles();
                $users = array();
                $total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->users}");
                $cls_settings = (array) get_option( 'cls_settings' );
                $controller = $cls_settings['controller'];
                
                if( $controller == 'administrator' ) {
                        unset( $roles['administrator'] );
                }
                else if( $controller == 'selected' ){
                        $controller = (array) $cls_settings['selected'];
                }
                
                $controller = (array) $controller;
                
                foreach( array_keys($roles) as $role ){
                        $user = get_users( array('role' => $role, 'number' => $total ) );
                        
                        if( $user && count( $user ) > 0 ){
                                $users[$role] = array();
                                foreach( $user as $_user ){
                                        if( !in_array( $_user->ID, $controller ) ){
                                                $users[$role][$_user->ID] = $_user->display_name;
                                        }
                                }
                        }
                }
                
                if( !empty($user_role) ){
                        return $users[$user_role];
                }
                return $users;
        }
        
        /**
         * Must be overriden in a sub-class
         **/
        public function in_page(){ return true; }
        
        public function admin_footer(){
                global $pagenow;
                
                if( $this->in_page() ){
                        
                        $users = $this->get_users();
                       
                        $local = wp_parse_args( array(
                                        'role' => array('guest' => 'Guest') + $this->get_roles()),
                                        $users  );
                        $local['admin_url'] = admin_url();
                        $local['ajaxurl'] = admin_url( '/admin-ajax.php' );
                        $local['values'] = $this->values();
                        $local['nonce'] = wp_create_nonce( 'cls_del' );
                        
                        wp_enqueue_style( 'cls-css', CLS_HOST . '/css/admin.css' );
                        wp_enqueue_script( 'underscore' );
                        wp_enqueue_script( 'backbone' );
                        wp_enqueue_script( 'cls-js', CLS_HOST . '/js/cls.js' );
                        wp_localize_script( 'cls-js', 'CLS', $local);
                }
        }
}