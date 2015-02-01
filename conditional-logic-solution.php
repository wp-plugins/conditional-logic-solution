<?php
/**
 * Plugin Name: Conditional Logic Solution
 * Plugin URI: http://irenemitchell.com/conditional-logic-solution/
 * Description: The complete control solution for wordpress powered site. CLS stands by it's name. It is a conditional logic design to empower site owners to have absolute control in most areas, if not all, of their site. It provides control to modify what users can and cannot do. And control contents visibility according to user, user group, and currently use page template.
 * Version: 1.0
 * Author: Irene A. Mitchell
 * Author URI: http://irenemitchell.com
 * Requires at least: 3.8
 * 
 * License: GPLv2 or later
 **/


 if( !defined( 'CLS_HOST' ) ) define( 'CLS_HOST', WP_PLUGIN_URL . '/conditional-logic-solution' );
 
 // Include basefield APi
 if( ! function_exists( 'basefield' ) ) require_once dirname(__FILE__) . '/lib/basefield.php'; 
 
 if( ! class_exists( 'ConditionalLogic_Solutions' ) ):
 
        class ConditionalLogic_Solutions {
                var $allcaps = array();
                
                public function __construct(){
                        add_action( 'init', array( $this, 'init' ), 500 );
                        add_action( 'admin_init', array( $this, 'admin_init' ) );
                        add_action( 'admin_menu', array( $this, 'menu' ) );
                }
                
                public function init(){
                        
                        // Remove delete roles before the code below
                        $this->remove_roles();
                        
                        global $wp_roles, $current_user;                       
                        $settings = (array) get_option( 'cls_settings' );
                        $settings = array_filter($settings, create_function('$a', ' return !empty($a); ' ) );
                        $this->caps = $caps = (array) $settings['caps'];
                        $apps_dir = dirname(__FILE__) . "/lib";
                        require_once "{$apps_dir}/class-cls.php";
                        
                        $allcaps = array(
                                'update_core',
                                'import','export',
                                'manage_options',
                                'update_themes',
                                'install_themes',
                                'switch_themes',
                                'delete_themes',
                                'edit_themes',
                                'edit_theme_options',
                                'update_plugins',
                                'install_plugins',
                                'activate_plugins',
                                'delete_plugins',
                                'edit_plugins',
                                'list_users',
                                'create_users',
                                'edit_users',
                                'promote_users',
                                'delete_users',
                                'edit_posts',
                                'delete_posts',
                                'publish_posts',
                                'edit_others_posts',
                                'delete_others_posts',
                                'read_private_posts',
                                'edit_private_posts',
                                'delete_private_posts',
                                'edit_published_posts',
                                'delete_published_posts',
                                'manage_categories',
                                'edit_pages',
                                'delete_pages',
                                'publish_pages',
                                'edit_others_pages',
                                'delete_others_pages',
                                'read_private_pages',
                                'edit_private_pages',
                                'delete_private_pages',
                                'edit_published_pages',
                                'delete_published_pages'
                        );
                       
                       /**
                        * Check if new role is added then add it
                        * to wp_roles
                        * */
         
                        if( isset( $settings['roles'] ) && !isset($_REQUEST['cls_settings'] ) ){
                                $cls_roles = (array) $settings['roles'];
                                
                                foreach( $cls_roles as $role ){
                                        if( !isset( $wp_roles->roles[$role] ) ){
                                                $role_name = ucwords(str_replace('-', ' ', $role));
                                                $args = array( 'name' => $role_name, 'capabilities' => $wp_roles->roles['subscriber']['capabilities'] );
                                                $args['capabilities'][$role] = 1;                                                
                                                $wp_roles->add_role( $role, $role_name, $args['capabilities'] );
                                        }
                                }
                        }
                        
                        /**
                         * Alter core capabilities
                         **/
                        foreach( $wp_roles->roles as $role => $role_object ){
                                if( isset( $caps[$role]) ){
                                        $cap = array_fill_keys($caps[$role], 1);                         
                                        $role_caps = $role_object['capabilities'];
                                        
                                        foreach( $role_caps as $_cap => $true ){                                               
                                                if( in_array( $_cap, $allcaps ) ){
                                                        unset( $role_caps[$_cap] );
                                                }
                                        }
                                        $role_caps = wp_parse_args( $cap, $role_caps);                                        
                                        $wp_roles->roles[$role]['capabilities'] = $role_caps;
                                        $this->caps[$role] = $role_caps;
                                        $this->caps[$role][$role] = 1;                                       
                                }
                        }                      
                        
                        add_filter( 'user_has_cap', array( $this, 'user_caps' ), 500, 3);
                        add_filter( 'posts_where', array( $this, 'posts_where') );
                        
                        foreach( (array) $settings['apps'] as $apps ){
                                $app_file = "{$apps_dir}/{$apps}.php";
                                if( file_exists( $app_file ) ){
                                        require_once $app_file;
                                }
                                else {
                                        do_action( "cls_{$apps}_include" );
                                }
                        }
                        
                        // Clear all settings
                        $this->clear_settings();
                }
                
                public function remove_roles(){
                        global $wp_roles;
                        $nonce = $_REQUEST['nonce'];
                        
                        if( !empty( $nonce ) && wp_verify_nonce( $nonce, 'cls_del' ) ){
                                $role = $_REQUEST['role'];
                                $wp_roles->remove_role( $role );
                                exit( 'OK' );
                        }
                }
                
                public function clear_settings(){
                        $nonce = $_REQUEST['nonce'];
                        
                        if( !empty( $nonce ) && wp_verify_nonce( $nonce, 'clear_nonce' ) ){
                                delete_option( 'cls_settings' );
                                do_action( 'cls_clear_all_settings' );
                                wp_redirect( remove_query_arg( 'nonce' ) );
                        }
                }
                
                public function user_caps( $caps, $cap, $args ){
                        global $current_user;
                        $role = $current_user->roles[0];
                        
                        if( ! class_cls::is_controller() && isset( $this->caps[$role] ) ){
                                $caps = $this->caps[$role];
                        }
        
                        return $caps;
                }
                
                public function posts_where( $where ){
                        global $wp_query, $wpdb, $current_user;
                        $role = $current_user->roles[0];
                        
                        if( ! class_cls::is_controller() && isset( $this->caps[$role]) ){
                                $post_type = $wp_query->get( 'post_type' );
                                $caps = $this->caps[$role];
                                $list_others = true;
                                
                                if( empty( $post_type ) || $post_type == 'post' ){
                                        if( !isset($caps['list_others_posts']) ){
                                             $list_others = false;
                                        }
                                }
                                
                                if( !$list_others ){
                                        $where .= " AND ({$wpdb->posts}.post_author='{$current_user->ID}')";
                                        add_filter( 'wp_count_posts', array( $this, 'count_posts'), 500, 2);
                                        //add_filter( "views_edit-post", array( $this, 'view_menu' ) );
                                }
                        }                        
                        return $where;
                }
                
                public function count_posts( $count, $post_type){
                        global $wpdb, $current_user;
                        if( in_array( $post_type, array( 'post', 'page', 'attachment' ) ) ){
                                $sql = "SELECT `post_status`, `post_author` FROM {$wpdb->posts} WHERE `post_type`='{$post_type}' AND `post_status`!='auto-draft' AND {$wpdb->posts}.post_author='{$current_user->ID}'";
                                $types = $wpdb->get_results( $sql );
                                $_types = array();
                                        
                                foreach( $types as $type ){
                                        if( !isset( $_types[$type->post_status]) ){
                                                $_types[$type->post_status] = 0;
                                        }
                                        $_types[$type->post_status] += 1;
                                } 
                                $count = (object) $_types;
                        }
                        return $count;
                }
                
                public function admin_init(){
                        register_setting( 'cls_settings', 'cls_settings', create_function('$a', ' return $a; ' ) );
                }
                
                public function menu(){
                        if( class_cls::is_controller() ){
                                add_submenu_page( 'options-general.php', __('Conditional Logic'), __('Conditional Logic'),
                                        'manage_options', 'conditional-logic',
                                        create_function('', ' require_once dirname(__FILE__) . "/views/settings.php"; '));
                        }
                }
        }
        new ConditionalLogic_Solutions;
 endif;