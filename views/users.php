<script type="text/template" id="cls_users">
<form method="post" class="cls-setting-inside">
    <h2 class="cls-title">Users Conditional Logic</h2>
    <?php
    $roles = $this->get_roles();
    ?>
    <div class="contextual-help-tabs">
    <ul class="cls-tabs">
    <?php foreach( $roles as $role => $title ): ?>
    <li class="active"><a><?php _e($title); ?></a></li>
    <?php endforeach; ?>
    </ul>
    </div>
    <div class="contextual-help-tabs-wrap">
        <div class="help-tab-content active">
        <?php
        basefield(array(
                        'label' => __('Administrative'),
                        'type' => 'checkbox',
                        'choices' => array(
                                'update_core' => __('Update core wordpress installation'),
                                'import' => __('Import contents'),
                                'export' => __('Export contents'),
                                'manage_options' => __('Edit settings')
                        )
                ));
                basefield(array(
                        'label' => __('Themes'),
                        'type' => 'checkbox',
                        'choices' => array(
                                'update_themes' => __('Update themes to the newest version'),
                                'install_themes' => __('Add new themes'),
                                'switch_themes' => __('Switch themes'),
                                'delete_themes' => __('Delete themes'),
                                'edit_themes' => __('Edit theme files'),
                                'edit_theme_options' => __('Edit theme options')
                        )
                ));
        basefield(array(
                        'label' => __('Plugins'),
                        'type' => 'checkbox',
                        'choices' => array(
                                'update_plugins' => __('Update plugins to the newest version'),
                                'install_plugins' => __('Add new plugins'),
                                'activate_plugins' => __('Activate/deactivate plugins'),
                                'delete_plugins' => __('Delete plugins'),
                                'edit_plugins' => __('Edit plugin files')
                        )
                ));
                basefield(array(
                        'label' => __('Users'),
                        'type' => 'checkbox',
                        'choices' => array(
                                'list_users' => __('List users'),
                                'create_users' => __('Add users'),
                                'edit_users' => __('Edit users'),
                                'promote_users' => __('Promote users'),
                                'delete_users' => __('Delete users')
                        )
                ));
        ?>
        </div>
    </div>
    <div class="cls-inside">
    
    <div class="cls-box">
    <?php
    /*
    $admins = $this->get_users( 'administrator' );
    basefield(array(
        'type' => 'checkbox',
        'label' => __('If users are '),
        'before' => '<span class="cls-adder" title="Repeat"><i class="dashicons dashicons-plus-alt"></i></span>',
        'choices' => $admins
    ));
    basefield(array(
        'label' => __('then s/he can '),
        'type' => 'checkbox',
        'id' => 'cls_user_caps',
        'choices' => array(
            'update_core' => __('Update core wordpress installation'),
            'import' => __('Import contents'),
            'export' => __('Export contents'),
            'manage_options' => __('Edit settings'),
            'update_themes' => __('Update themes to the newest version'),
            'install_themes' => __('Add new themes'),
            'switch_themes' => __('Switch themes'),
            'delete_themes' => __('Delete themes'),
            'edit_themes' => __('Edit theme files'),
            'edit_theme_options' => __('Edit theme options'),
            'update_plugins' => __('Update plugins to the newest version'),
            'install_plugins' => __('Add new plugins'),
            'activate_plugins' => __('Activate/deactivate plugins'),
            'delete_plugins' => __('Delete plugins'),
            'edit_plugins' => __('Edit plugin files'),
            'list_users' => __('List users'),
            'create_users' => __('Add users'),
            'edit_users' => __('Edit users'),
            'promote_users' => __('Promote users'),
            'delete_users' => __('Delete users')
        )
    ));
    */
    ?>
    <div class="clear"></div>
    </div></div>
</form>
<a class="show-settings">Users Conditional Logic</a>
</script>