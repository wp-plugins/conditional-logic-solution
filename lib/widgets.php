<?php
class cls_widgets extends class_cls {
        var $id = 'cls_widgets';
        
        public function __construct(){
                add_action( 'in_widget_form', array($this, 'widget' ), 999, 3 );
                
                if( is_admin() ){
                        add_action( 'dynamic_sidebar_after', array($this, 'before_sidebar'), 999);
                }
                
                add_filter( 'is_active_sidebar', array( $this, 'is_active_sidebar' ), 999, 2 );
                add_action( 'widget_display_callback', array( $this, 'widget_callback' ), 999, 3);
                
                parent::__construct();
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
                                if( ( is_home() && $template == 'homepage' )
                                   || ( is_archive() && $template == 'archive' )
                                   || ( is_category() && $template == 'category' )
                                   || ( is_search() && $template == 'search' )
                                   || ( is_tag() && $template == 'tag' )
                                   || ( is_404() && $template == '404' ) ){
                                        return false;
                                }
                        }
                }
                return true;
        }
        
        public function is_active_sidebar( $is_active_sidebar, $index ){
                $values = $this->values( 'cls_sidebars' );
                if( $is_active_sidebar && isset( $values[$index] ) ){
                        $value = $values[$index];
                        $is_active_sidebar = $this->can( $value );
                }
                return $is_active_sidebar;
        }
        
        public function widget_callback( $instance, $widget, $args ){ 
                $index = $widget->id;
                $values = $this->values( 'cls_widgets' );
               
                if( isset( $values[$index] ) ){ 
                        $value = $values[$index];
                        $return = $this->can( $value );                        
                        return !$return ? false : $instance;
                }
                return $instance;
        }
        
        public function save_settings(){
                if( isset($_REQUEST['cls_sidebars'] ) ){
                        $this->id = 'cls_sidebars';
                }
                if( isset( $_REQUEST['cls_widgets'] ) ){
                        $this->id = 'cls_widgets';
                }
                parent::save_settings();
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
                        'archive' => __('Archive'),
                        'category' => __('Category'),
                        'tag' => __('Tag'),
                        '404' => __('404 Error Page'),
                        'search' => __('Search Template')
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