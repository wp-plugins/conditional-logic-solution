<?php
class cls_users extends class_cls {
        var $id = 'cls_users';
        
        public function __construct(){
                parent::__construct();
                add_filter( 'user_row_actions', array( $this, 'actions' ) );
        }
        
        public function actions( $actions ){
                if( $this->is_controller() ){
                        $actions['edit_cls'] = sprintf('<a href="" class="cls-user-icon">&nbsp;</a>');
                }
                return $actions;
        }
        
        public function in_page(){
                global $pagenow;
                return $pagenow == 'users.php';
        }
        
        public function admin_footer(){
                if( $this->in_page() ){
                        parent::admin_footer();
                }
        }
}
new cls_users;