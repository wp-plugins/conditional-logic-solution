<?php
global $wp_roles;

        wp_enqueue_style( 'cls-css', CLS_HOST . '/css/admin.css' );
        wp_enqueue_script( 'jquery-ui' );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'admin-js', CLS_HOST . '/js/admin.js' );
        wp_localize_script( 'admin-js', 'cls', array(
            'admin_url' => admin_url(),
            'nonce' => wp_create_nonce( 'cls_del')
        ));
        $clear_url = add_query_arg( 'nonce', wp_create_nonce( 'clear_nonce' ), admin_url( 'options-general.php?page=conditional-logic' ) );
        ?>
    
<div class="wrap cls">
        <form method="post" action="options.php" id="cls_setting_page">
                <?php settings_fields( 'cls_settings', 'cls_settings' ); ?>
                <div class="pull-right">
                        <input type="submit" class="button-primary" value="SAVE CHANGES" />
                        <a href="<?php _e( $clear_url ); ?>" class="button">CLEAR ALL SETTINGS</a>
                </div>
                
                <ul class="cls-tabs">
                        <li class="active"><a href="#cls-general"><img src="<?php _e(CLS_HOST); ?>/css/imgs/logo.png" /></a></li>
                        <li><a href="#cls-capabilities">Roles & Capabilities</a></li>
                        <li><a href="#cls-faqs">FAQs</a></li>
                </ul>
        <div id="cls-general" class="cls-tab-content">
                <h2>Conditional Logic Solution</h2>
                <p class="description">The complete control solution for wordpress powered site.</p>
                <br>
        <?php
            $settings = get_option( 'cls_settings' );
            $user_id = get_current_user_id();
            $cls_roles = (array) $settings['roles'];
            
            basefield(array(
                'label' => '<span class="dashicons dashicons-universal-access"></span> '. __('Who controls CLS?'),
                'type' => 'radio',
                'name' => 'cls_settings[controller]',
                'before' => '<p class="bf-sub">* Users who have access to CLS becomes superadmin. Only allow access to your most trusted users.</p>',
                'choices' => array(
                     $user_id => __('Only Me'),
                     'administrator' => __('Administrators'),
                     'selected' => __('Selected Administrators')
                ),
                'value' => isset( $settings['controller'] ) ? $settings['controller'] : $user_id
            ));
            
            $users = class_cls::get_users( 'administrator' );
            
            basefield(array(
                'name' => 'cls_settings[selected][]',
                'type' => empty( $users ) ? 'html' : 'checkbox',
                'label' => empty( $users ) ? "Ooop! It looks like your the only administrator." : __('Choose your trusted administrators.'),
                
                'choices' => $users
            ));
            
            basefield(array(
                'name' => 'cls_settings[selected][]',
                'type' => 'hidden',
                'value' => $user_id
            ));
        
            $apps = array(
                'dashboard' => '<span class="dashicons dashicons-dashboard"></span> ' . __('DASHBOARD') . 
                        '<p class="description">Allows you to control your dashbord widgets visibility for selected user or user group.</p>',
                'post' => '<span class="dashicons dashicons-admin-post"></span> '. __('POSTS') . ' and <span class=" dashicons dashicons-admin-page"></span>'. __('PAGES') .
                        '<p class="description">Allows you to modify, control it\'s visibility, and change your users capabilities for posts and pages.</p>',
                'widgets' => '<span class="dashicons dashicons-visibility"></span> '. __('SIDEBARS & WIDGETS') .
                        '<p class="description">Allows you to control the visibility of your sidebars and sidebar widgets per user, user group and page templates.</p>'
            );
            $apps = apply_filters( "cls_apps", $apps );
            
            basefield(array(
                'type' => 'checkbox',
                'name' => "cls_settings[apps][]",
                'class' => 'apps',
                'choices' => $apps,
                'value' => (array) $settings['apps'],
                'label' => __('Because you are in control, you choose which to activate.')
            ));
        ?>
        </div>
        <div id="cls-capabilities" class="cls-tab-content">
            <div class="cls-right">
                <?php
                        basefield(array(
                            'placeholder' => __('Enter role name'),
                            'after' => '<span class="button" id="add_wp_role">Add</span>'
                        ));
                ?>
            </div>
            <p class="bf-sub">The settings below allows you to modify what your users can and cannot do within your site.</p>
            <p class="bf-sub">Leave them as is if you don't want to make any changes.</p>
            <hr />
            <div class="clear"></div>
            <ul class="cls-inner-tabs">
            <?php foreach( $wp_roles->roles as $role => $role_object): ?>
            <li>
                <?php
                    $role_title = ucwords(str_replace('_', ' ', $role_object["name"]));
                ?>
                <a href="#cap-<?php _e($role); ?>"><?php _e($role_title); ?></a>
                <?php
                if(in_array($role, $cls_roles)):
                    basefield(array(
                        'name' => 'cls_settings[roles][]',
                        'type' => 'hidden',
                        'value' => $role,
                        'after' => '<span class="cls-icon cls-remove-role" data-role="'. $role . '" title="Remove"><i class="dashicons dashicons-welcome-comments"></i></span>'
                    ));
                endif;
                ?>
            </li>
            <?php endforeach; ?>
            </ul>
            
            <?php foreach( $wp_roles->roles as $role => $role_object ): ?>
            <div id="cap-<?php _e($role); ?>" class="cls-inner-tab">
                <h2><?php _e(ucwords(str_replace('_', ' ', $role_object["name"]))); ?>s Capabilities</h2>
                <br/>
                
                <?php if( $role == 'administrator' ): ?>
                        <p class="bf-sub">* Only administrators who are not assigned as controllers will be affected by changes.</p>
                <?php endif; ?>
                
                <?php                
                $caps = array_keys($role_object['capabilities']);
                basefield(array(
                        'label' => __('Administrative'),
                        'type' => 'checkbox',
                        'name' => "cls_settings[caps][{$role}][]",
                        'choices' => array(
                                'update_core' => __('Update core wordpress installation'),
                                'import' => __('Import contents'),
                                'export' => __('Export contents'),
                                'manage_options' => __('Edit settings')
                        ),
                        'value' => $caps
                ));
                basefield(array(
                        'label' => __('Themes'),
                        'type' => 'checkbox',
                        'name' => "cls_settings[caps][{$role}][]",
                        'choices' => array(
                                'update_themes' => __('Update themes to the newest version'),
                                'install_themes' => __('Add new themes'),
                                'switch_themes' => __('Switch themes'),
                                'delete_themes' => __('Delete themes'),
                                'edit_themes' => __('Edit theme files'),
                                'edit_theme_options' => __('Edit theme options')
                        ),
                        'value' => $caps
                ));
                basefield(array(
                        'label' => __('Plugins'),
                        'type' => 'checkbox',
                        'name' => "cls_settings[caps][{$role}][]",
                        'choices' => array(
                                'update_plugins' => __('Update plugins to the newest version'),
                                'install_plugins' => __('Add new plugins'),
                                'activate_plugins' => __('Activate/deactivate plugins'),
                                'delete_plugins' => __('Delete plugins'),
                                'edit_plugins' => __('Edit plugin files')
                        ),
                        'value' => $caps
                ));
                basefield(array(
                        'label' => __('Users'),
                        'type' => 'checkbox',
                        'name' => "cls_settings[caps][{$role}][]",
                        'choices' => array(
                                'list_users' => __('List users'),
                                'create_users' => __('Add users'),
                                'edit_users' => __('Edit users'),
                                'promote_users' => __('Promote users'),
                                'delete_users' => __('Delete users')
                        ),
                        'value' => $caps
                ));
                
                if( empty( $settings ) && ( in_array( 'edit_posts', $caps ) || in_array( 'edit_pages', $caps ) ) ){
                        $caps = array_merge($caps, array('list_others_posts', 'list_others_pages' ) );
                }
                
                basefield(array(
                    'label' => __('Posts'),
                    'type' => 'checkbox',
                    'name' => "cls_settings[caps][{$role}][]",
                    'choices' => array(
                        'edit_posts' => __('Add New'),
                        'delete_posts' => __('Delete'),
                        'publish_posts' => __('Published'),
                        'list_others_posts' => __('List others posts'),
                        'edit_others_posts' => __('Edit Others Posts'),
                        'delete_others_posts' => __('Delete Others Posts'),
                        'read_private_posts' => __('Read private posts'),
                        'edit_private_posts' => __('Edit private posts'),
                        'delete_private_posts' => __('Delete private posts'),
                        'edit_published_posts' => __('Edit published posts'),
                        'delete_published_posts' => __('Delete published posts'),
                        'manage_categories' => __('Manage categories and tags')
                    ),
                    'value' => $caps
                ));
                basefield(array(
                    'label' => __('Pages'),
                    'type' => 'checkbox',
                    'name' => "cls_settings[caps][{$role}][]",
                    'choices' => array(
                        'edit_pages' => __('Add New'),
                        'delete_pages' => __('Delete'),
                        'publish_pages' => __('Publish'),
                        'list_others_pages' => __('List others pages'),
                        'edit_others_pages' => __('Edit others pages'),
                        'delete_others_pages' => __('Delete others pages'),
                        'read_private_pages' => __('Read private pages'),
                        'edit_private_pages' => __('Edit private pages'),
                        'delete_private_pages' => __('Delete private pages'),
                        'edit_published_pages' => __('Edit published pages'),
                        'delete_published_pages' => __('Delete published pages')
                    ),
                    'value' => $caps
                ));
                do_action( "cls_capabilities", $role, $values );
                ?>
            </div>
            <?php endforeach; ?>
            <div class="clear"></div>
        </div>
        <div id="cls-faqs" class="cls-tab-content">
            <div class="cls-info-box pull-right">
                <h3>Need more?</h3>
                <p class="">If you think CLS is not enough do send your request right <a href="" target="_blank">here</a>.</p>
            </div>
            <h3>Where to ...</h3>
            <div class="cls-faq-box">
                <p class="bf-title">Set the control for dashbord widgets.</p>
                    <p>Navigate to your <a href="<?php _e(admin_url()); ?>">dashboard</a> page then open a dashboard widget to set the condition.</p>
                <p class="bf-title">Set the control for posts and pages.</p>
                    <p>Navigate to your <a href="<?php _e(admin_url('edit.php')); ?>">posts</a> or <a href="<?php _e(admin_url('edit.php?post_type=page')); ?>">pages</a> page. At the top right corner, click the <img src="<?php _e(CLS_HOST); ?>/css/imgs/logo.png" width="30" /> to pop down the settings.</p>
                <p class="bf-title">Set the control for sidebars and sidebar widgets.</p>
                    <p>Navigate to <a href="<?php _e(admin_url('widgets.php')); ?>">widgets</a> page. Open the sidebar or sidebar widget you wish to set control to.</p>
            </div>
        </div>
    </form>
</div>