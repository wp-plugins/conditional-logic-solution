<?php
class cls_widgets extends class_cls {
        var $id = 'cls_widgets';
        var $template;
        
        public function __construct(){
                add_action( 'in_widget_form', array($this, 'widget' ), 999, 3 );
                
                if( is_admin() ){
                        add_action( 'dynamic_sidebar_before', array($this, 'before_sidebar'), 999);
                }
                
                add_filter( 'is_active_sidebar', array( $this, 'is_active_sidebar' ), 999, 2 );
                add_action( 'widget_display_callback', array( $this, 'widget_callback' ), 999, 3);
                add_filter( 'template_include', array( $this, 'get_template') );
                
                parent::__construct();
        }
        
        public function get_template( $template ){
                $this->template = $template;
                return $template;
        }
        
        public function can( $values ){
                global $current_user;
                $values = array_filter( $values, create_function('$a', ' return !empty($a); ' ) );
                $types = (array) $values['type'];
                $users = (array) $values['user'];
                $roles = array_keys( $this->get_roles() );                
                
                foreach( $types as $pos => $type ){
                        $user = (array) $users[$pos];
                        if( ( $type == 'role' && parent::can( $user ) )
                           || ( in_array( $type, $roles ) && in_array( $current_user->ID, $user ) ) ){
                                return false;
                        }
                        elseif( $type == 'template' ){
                                $template = $users[$pos];
                               
                               if( $template == 'homepage' && is_home() ) return false;
                              
                               if( $template == 'page' && is_page() ) return false;
                              
                               if( $template == '404' && is_404() ) return false;
                              
                               if( $template == 'single' && is_singular() && 'post' == get_post_type() ) return false;
                              
                               if( $template == 'archive' && is_archive() ) return false;
                              
                               if( $template == 'category' && is_category() ) return false;
                               
                               if( $template == 'tag' && is_tag() ) return false;
                               
                               if( $template == 'search' && is_search() ) return false;
                               
                               if( $template == 'author' && is_author() ) return false;                        
                               
                               $post = get_post();
                               $template_id = get_post_meta( $post->ID, '_wp_page_template', true );
                               
                               if( $template == $template_id ) return false;
                        }
                }
                return true;
        }
        
        public function is_active_sidebar( $is_active_sidebar, $index ){
                if( !is_admin() ){
                        $values = $this->values( 'cls_sidebars' );
                      
                        if( $is_active_sidebar && isset( $values[$index] ) ){
                                $value = $values[$index];
                                $is_active_sidebar = $this->can( $value );
                        }
                }
                return $is_active_sidebar;
        }
        
        public function widget_callback( $instance, $widget, $args ){
                
                if( !is_admin() ){
                        $index = $widget->id;
                        $values = $this->values( 'cls_widgets' );                       
                        if( isset( $values[$index] ) ){ 
                                $value = $values[$index];
                                $return = $this->can( $value );
                                
                                if( ! $return ) return false;
                                //return !$return ? false : $instance;
                        }
                } 
                return $instance;
        }
        
        public function save_settings(){
                if( isset($_REQUEST['cls_sidebars'] ) ){
                        $this->id = 'cls_sidebars';
                }
                else if( isset( $_REQUEST['cls_widgets'] ) ){
                        $this->id = 'cls_widgets';
                }
                $values = $this->values( $this->id );
                $req = $_REQUEST[$this->id];
                
                foreach( (array) $req as $key => $value ){
                        if( isset( $values[$key] ) ){
                                if( empty( $value ) ) unset( $values[$key] );
                                else {
                                        $values[$key] = $value;
                                }
                        }
                        else {
                                $values[$key] = $value;
                        }
                }
                
                update_option( $this->id, $values);
                return $value;
        }
        
        public function values( $for = "cls_sidebars" ){
                $values = (array) get_option( $for );
                $values = array_filter( $values, create_function( '$a', ' return !empty($a); ' ) );
                return $values;
        }
        
        public function before_sidebar($index){
                printf('<div class="cls-sidebars" data-sidebar="%s"></div>', $index);
                
        }
        
        public function widget($widget, $return, $instance){
                $number = $widget->number;
                $values = $this->values('cls_widgets');
                
                printf('<div class="cls-widgets" data-widget="%1$s" data-number="%2$s"></div>', $widget->id, $number); ?>
                        
                <script type="text/javascript">
                        +function($){
                                if( !window.CLS_Widgets ) CLS_Widgets = {};
                                CLS_Widgets.widgets = <?php _e(json_encode($values)); ?>;
                                if( $.fn.buildWidgets ) $('.cls-widgets').buildWidgets();
                        }(jQuery);
                </script>
                        
                <?php
        }
        
        public function in_page(){
                global $pagenow;
                return $pagenow == 'widgets.php';
        }
        
        public function admin_footer(){
                if( $this->in_page() ){
                        $templates = array(
                        'homepage' => __('Home Page'),
                        'single' => __('Single Post'),
                        'page' => __('Pages'),
                        'archive' => __('Archives'),
                        'category' => __('Category Archives'),
                        'tag' => __('Tag Archives'),
                        '404' => __('404 (Not Found)'),
                        'search' => __('Search Results'),
                        'author' => __('Author Template'),
                        ) + wp_get_theme()->get_page_templates();
                        
                        parent::admin_footer();
                        wp_enqueue_script( 'widget-js', CLS_HOST . '/js/widget.js' );
                        wp_localize_script('widget-js', 'CLS_Widgets', array(
                                'templates' => $templates,
                                'sidebars' => $this->values( 'cls_sidebars' ),
                                'widgets' => $this->values( 'cls_widgets' )
                        ));
                        $this->get_views( 'widgets' );
                }
        }
}
new cls_widgets;