<script type="text/template" id="cls_widgets">
    <h3 class="cls-title cls-green">Keep this hidden if ... <span class="cls-icon-right"><small class="dashicons dashicons-admin-generic"></small></span> <span class="cls-icon-loading"></span> </h3>
    <div class="cls-box">
    <?php
        $roles = array(); 
            basefield(array(
                'type' => 'select',
                'class' => 'cls_select',
                'name' => '[type][]',
                'choices' => array(
                    '' => '',
                    'role' => __('User Group'),
                    'template' => __('Current Page Template'),
                    'User' =>  $this->get_roles()
                ),
                'after' => '<span class="dashicons dashicons-leftright cls-sep"></span>' //'<span class="cls-sep">is</span>'
            ));
            
            basefield(array(
                'type' => 'select',
                'class' => 'cls_value',
                'name' => '[user][]',
                'choices' => array_merge(array('' => '' ), $this->get_roles()),
                'after' => '<span class="cls-loader"></span> <span class="cls-adder" title="Repeat"><i class="dashicons dashicons-plus-alt"></i></span>'
            ));
        ?>
    <div class="clear"></div>
    </div>
</div>
</script>