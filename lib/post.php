<?php
/**
 * CLS for Posts & Pages
 **/

class cls_posts extends class_cls {
        var $usercaps = array();
        
        public function __construct( $post_type = 'post' ){ 
                $this->id = "cls_{$post_type}";
                $this->post_type = $post_type;
                $this->post_object = get_post_type_object( $post_type );
                $this->title = $this->post_object->labels->name;
                $this->init();
                
                parent::__construct();
        }
        
        public function is_post( $wp_query = '' ){
                
                if( ! $wp_query ){
                        global $wp_query;
                }
                
                $post_type = $wp_query->get( 'post_type' );
                
                if( empty( $post_type ) ) $post_type = $wp_query->query_vars['post_type'];
                if( empty( $post_type ) ) $post_type = get_post_type();
                
                return ( (empty( $post_type ) && $this->post_type == 'post' )
                        || ( $this->post_type == $post_type )
                        || ( $this->post_type == 'page' && $wp_query->is_page )
                        || ( ( $wp_query->is_home || $wp_query->is_category || $wp_query->is_archive
                              || $wp_query->is_tag || $wp_query->is_year || $wp_query->is_month
                              || $wp_query->is_time ) && $this->post_type == 'post' ) );
        }
        
        public function init(){
            
                        $this->values = $values = (object) $this->values();
                        $this->usercaps = (array) $this->values->usercaps;
                        
                        if( isset($values->comments) && $values->comments ){
                                remove_post_type_support( $this->post_type, 'comments' );
                                add_filter( 'comments_open', array( $this, 'comments_open' ) );
                                add_filter( 'comments_array', array( $this, 'comments_array' ) );
                                add_filter( 'get_comments_number', create_function('', ' return 0; ') );
                        }
                        
                        add_filter( 'excerpt_length', array( $this, 'excerpt_length' ), 500 );
                        
                        // Posts Per Page
                        add_filter( 'option_posts_per_page', array( $this, 'posts_per_page' ) );
                        
                        // Posts Orderby
                        add_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );
                        
                        add_filter( 'parse_query', array( $this, 'parse_query' ) );
                        add_filter( 'posts_where', array( $this, 'posts_where' ) );
                        
                        //Pre get_posts
                        add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
                        
                        if( $this->post_type == 'page' ){
                                add_filter( 'wp_list_pages_excludes', array( $this, 'exclude_pages' ) );
                        }
                        
                        add_filter( 'user_has_cap', array( $this, 'user_cap' ), 500, 3);
                        add_filter( 'wp_nav_menu_objects', array( $this, 'nav_menu' ), 100, 2 );
                        add_filter( 'wp_page_menu', array( $this, 'page_menu' ), 100, 2);
                        add_action( 'add_meta_boxes', array( $this, 'meta_boxes' ) );
                        add_action( 'save_post', array( $this, 'save_post' ) );
        }
        
        public function comments_open( $open ){
                if( get_post_type() == $this->post_type && isset( $this->values->comments ) && $this->values->comments ){
                        $open = false;
                }
                return $open;
        }
        
        public function comments_array( $array ){
                if( get_post_type() == $this->post_type && isset( $this->values->comments ) && $this->values->comments ){
                        $array = array();
                }
                return $array;
        }
        
        public function excerpt_length($length){ 
                if( get_post_type() == $this->post_type ){
                        $length = (int) $this->values->summary;
                }
                return $length;
        }
        
        public function posts_per_page( $per_page ){
                global $wp_query;
                
                if( !is_admin() && $wp_query->is_main_query() ){                       
                        
                        if( !is_search() && $this->is_post() ){                                
                                if( (int) $this->values->per_page > 0 ) $per_page = (int) $this->values->per_page;
                        }                        
                }
                
                return $per_page;
        }
        
        public function posts_orderby( $orderby ){
                global $wpdb;
                
                if( $this->is_post() ){
                        $order = $this->values->order;
                        $_order = 'DESC';
                        
                        if( $order == 'post_title' ) $_order = 'ASC';
                        
                        $orderby = "{$wpdb->posts}.{$order} {$_order}";
                }

                return $orderby;
        }
        
        public function parse_query( $query ){
                global $wpdb, $current_user;
                
                if( !$this->is_controller() ){
                        
                        if( $this->is_post() ){
                                $ids = $this->get_post_usercaps( 'read' );
                                
                                if( count( $ids ) > 0 ){
                                        $query->set( 'post__not_in', $ids);
                                        add_filter( 'wp_count_posts', array( $this, 'count_posts'), 500, 2);
                                }
                        }
                }
                
                return $query;
        }
        
        public function posts_where( $where ){
                global $wpdb;               
                
                if( !$this->is_controller() && $this->is_post() ){
                        
                        if( ! $this->can( 'read' ) ){ 
                                $where .= " AND {$wpdb->posts}.post_type !='{$this->post_type}'";
                        }
                }
        
                return $where;
        }
        
        public function pre_get_posts( $query ){
                
                if( !$this->is_controller() ){            
                        if( $this->is_post() ){
                                $ids = $this->get_post_usercaps( 'read' );
                                
                                if( count( $ids ) > 0 ){
                                        $query->set( 'post__not_in', $ids);
                                        add_filter( 'wp_count_posts', array( $this, 'count_posts'), 500, 2);
                                }
                        }
                }
                
                return $query;
        }
        
        /**
         * Remove non-readable pages.
         **/
        
        public function exclude_pages( $exclude ){
      
                $reader = $this->get_post_usercaps( 'read' );
                
                if( count( $reader ) > 0 ){
                        $exclude = array_merge( $exclude, $reader );
                }
                return $exclude;
        }
        
        public function count_posts( $count, $post_type){
                global $wpdb, $current_user;
                if( $post_type == $this->post_type ){
                        $ids = $this->get_post_usercaps( 'read' );
                        
                        $sql = "SELECT `post_status`, `post_author` FROM {$wpdb->posts} WHERE `post_type`='{$this->post_type}' AND `post_status`!='auto-draft' AND {$wpdb->posts}.ID NOT IN (". implode(", ", $ids ) . ")";
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
        
        public function view_menu( $menu ){
                if( !$this->can( 'list_others' ) ){ unset( $menu['mine'] ); }
                return $menu;
        }
        
        public function _find_post( $objects ){
                foreach( (object) $objects as $pos => $object ){
                        $object_post = get_post( $object->object_id );
                       
                        if( !is_wp_error( $object_post ) && is_object( $object_post ) ){
                                if( $object_post->post_type == $this->post_type ){
                                        $ids = $this->get_post_usercaps( 'read' );
                                        
                                        if( count( $ids ) > 0 && in_array( $object_post->ID, $ids ) ){
                                                unset( $objects[$pos] );
                                        }
                                }
                        }
                }
                return $objects;
        }
        
        public function nav_menu( $object, $args ){
                $object = $this->_find_post( $object );
                return $object;
        }
        
        public function page_menu( $menu, $args ){
                if( $this->post_type == 'page' && !$this->can( 'read' ) ){
                        $menu = '';
                }
                return $menu;
        }
        
        public function can( $cap ){
                global $current_user;
                $caps = (array) $this->values->caps;
                $role = $current_user->roles[0];
                
                if( $cap == 'read' ){
                        $reader = (array) $this->values->read;
                        return parent::can( $reader );
                }
                elseif( parent::can( array_keys( $caps ) ) ){
                        $_caps = (array) $caps[$role];
                        
                        if( !empty( $_caps ) ){
                                return in_array( $cap, $_caps );
                        }
                }
                return $this->is_controller();
        }
        
        public function user_cap( $caps, $cap, $args){
                $post = get_post();
                
                if( is_object( $post ) ){
                        if( isset( $caps["edit_others_{$this->post_type}s"] ) ){
                                $edit = $this->get_post_usercaps( 'edit' );
                                
                                if( in_array( $post->ID, $edit ) ){
                                        unset( $caps["edit_others_{$this->post_type}s"] );
                                }
                        }
                        
                        if( isset( $caps["delete_others_{$this->post_type}s"] ) ){
                                $delete = $this->get_post_usercaps( 'delete' );
                                if( in_array( $post->ID, $delete) ){
                                        unset( $caps["delete_others_{$this->post_type}s"] );
                                }
                        }
                }
                return $caps;
        }
        
        public function save_settings(){
                if( isset($_REQUEST[$this->id] ) ){
                        $req = $_REQUEST[$this->id];              
                        $old = $this->values();
                        $values = wp_parse_args( $req, $old );
                        if( !isset($req['comments']) ){
                                unset($values['comments']);
                        }
                        update_option( $this->id, $values );
                }
        }
        
        public function values(){
                global $wp_roles;
                $values = parent::values();
                $_roles = array_keys($this->get_roles());
                array_unshift($_roles, 'guest');
                
                if( empty($values) ){
                        $_caps = array();
                        foreach( $wp_roles->roles as $role => $roles){
                                $caps = array_keys( $roles['capabilities'] );
                                
                                if( in_array("edit_{$this->post_type}s", $caps) && $role != 'administrator' ){
                                        array_unshift( $caps, 'list_others' );
                                        $_caps[$role] = $caps;
                                }
                        }
                        $_caps = array_filter( $_caps, create_function('$a', ' return !empty($a); ' ) );
                        
                        $values = array(
                                'summary' => 40,
                                'order' => 'post_date',
                                'per_page' => get_option( 'posts_per_page' ),
                                'read' => $_roles,
                                'caps' => $_caps
                        );
                }
                return $values;
        }
        
        public function caps(){
                $caps = array('list_others');
                foreach( array('edit', 'delete', 'publish', 'read_private', 'edit_others', 'delete_others', 'edit_private', 'delete_private', 'edit_published', 'delete_published') as $cap ){
                        $caps[] = "{$cap}_{$this->post_type}s";
                }
                return $caps;
        }
        
        public function meta_boxes(){
                if( $this->is_controller() || ( isset($this->values->can) && parent::can( $this->values->can ) ) ){
                      add_meta_box( "cls-meta", __($this->title . ' Conditional Logic' ), array($this, 'metabox'), $this->post_type, 'side', 'low');  
                }                
        }
        
        public function metabox($post){
                $usercaps = get_post_meta( $post->ID, 'user_caps', true );                
                $users = array( 'guest' => __('Guests') ) + $this->get_users();
                
                foreach( $users as $role => $user ){
                        $users[ucfirst($role)] = $user;
                        $users[$role] = ucfirst($role).'s';
                }
                array_unshift($users, '&nbsp;');
                
                ?>
                
                <div class="cls-box">
                <?php
                        basefield(array(
                                'label' => __('Disallow '),
                                'type' => 'select',
                                'class' => 'cls_usercaps',
                                'choices' => $users,
                                'after' => ' to <span class="cls-adder" title="Repeat"><i class="dashicons dashicons-plus-alt"></i></span>',
                        ));
                        echo '<div class="clear"><br></div>';
                        basefield(array(
                                'type' => 'checkbox',
                                'name' => 'caps',
                                'choices' => array(
                                        'read' => __('read'),
                                        'edit' => __('edit'),
                                        'delete' => __('delete')
                                ),
                                'after' => __(" this {$this->post_type}." )
                        ));
                        
                        basefield(array(
                                'type' => 'hidden',
                                'name' => 'cls_post_nonce',
                                'value' => wp_create_nonce( "cls_{$this->post_type}" )
                        ));
                ?>
                <div class="clearfix"></div></div>
                <?php
                
                wp_enqueue_style( 'cls-css', CLS_HOST . '/css/admin.css' );
                wp_enqueue_script( 'cls-js', CLS_HOST . '/js/cls.js' );
                wp_localize_script( 'cls-js', 'CLS', array(
                        'usercaps' => $usercaps
                ));
        }
        
        public function save_post($post_id){
                if ( wp_is_post_revision( $post_id ) )
                        return;
                    
                if( !in_array($_POST['post_type'], (array) $this->post_type) ) return;
                
                $nonce = $_REQUEST['cls_post_nonce'];
                
                if( !empty($nonce) && wp_verify_nonce( $nonce, "cls_{$this->post_type}" ) ){
                        
                        $usercaps = $_REQUEST['user_caps'];
                        update_post_meta( $post_id, 'user_caps', $usercaps);
                        
                        $old_usercaps = (array) $this->values->usercaps;
                        
                        if( !empty($usercaps) ){
                                $old_usercaps[$post_id] = $usercaps;
                        }
                        else {
                                unset( $old_usercaps[$post_id]);
                        }
                        
                        $this->values->usercaps = $old_usercaps;
                        update_option( $this->id, (array) $this->values );
                }
        }
        
        public function get_post_usercaps( $cap = 'read' ){
                global $current_user;
                $role = $current_user->roles[0];
                $user_id = $current_user->ID;
                $ids = array();
                
                if( empty( $role) || $current_user->ID == 0 ){
                        $role = $user_id = 'guest';
                }
               
                if( !empty( $this->usercaps ) ){
                        
                        foreach( $this->usercaps as $post_id => $usercaps ){
                                $users = array_keys( $usercaps );                                
                                
                                if( parent::can( $users ) ){
                                        // Role
                                        if( isset( $usercaps[$role] ) && in_array( $cap, $usercaps[$role] ) ) $ids[] = $post_id;
                                        
                                        // USERID
                                        if( isset( $usercaps[$user_id] ) && in_array( $cap, $usercaps[$user_id] ) ) $ids[] = $post_id;                                        
                                }
                        }
                }
             
                return $ids;
        }
        
        public function in_page(){
                global $pagenow;
                if( $this->is_controller() && $pagenow == 'edit.php' ){
                        $post_type = get_post_type();
                        
                        if( empty( $post_type ) ){
                                $post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : '';
                        }
                        
                        if( $post_type == $this->post_type || $this->post_type == 'post' && empty( $post_type ) ){
                                return true;
                        }
                }
                return false;
        }
        
        public function admin_footer(){
                if( $this->in_page() ){
                        $values = $this->values();
                        $caps = (array) $values['caps'];
                        parent::admin_footer();
                        wp_enqueue_script( 'post-js', CLS_HOST . '/js/post.js' );
                        wp_localize_script( 'post-js', 'CLS_Posts', array(
                                'post_type' => $this->post_type,
                                'caps' => $caps,
                                'id' => $this->id
                        ));
                        $this->get_views( 'posts' );
                }
        }
}
new cls_posts( 'post' );
new cls_posts( 'page' );