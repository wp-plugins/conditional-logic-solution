<script type="text/template" id="cls_ui_widgets">
    <span class="cls-ui-icon"></span>
</script>
<script type="text/template" id="cls_ui_widget">
    <h3 class="cls-title cls-green">Conditional Logic Solution <span class="cls-icon-right"><small class="dashicons dashicons-admin-generic"></small></span> </h3>
</script>
<script type="text/template" id="cls_widget_fields">
    <p class="description">Keep this hidden if... <span class="cls-icon-loading"></span></p>
    <div class="cls-box">
    <?php
        $roles = array();
        echo '<div class="bf-half">';
            basefield(array(
                'type' => 'select',
                'class' => 'cls_select',
                'name' => '[type][]',
                'choices' => array(
                    '' => 'Select',
                    'role' => __('User Group'),
                    'template' => __('Current Page Template'),
                    'User' =>  $this->get_roles()
                ),
                'after' => '<span class="dashicons dashicons-leftright"></span>'
               // 'after' => '<span class="dashicons dashicons-leftright cls-sep"></span>' //'<span class="cls-sep">is</span>'
            ));
        echo '</div>';
        echo '<div class="bf-half">';
            basefield(array(
                'type' => 'select',
                'class' => 'cls_value',
                'name' => '[user][]',
                'choices' => array('' => ' '),
                //'choices' => array_merge(array('' => '' ), $this->get_roles()),
                'after' => '<span class="cls-loader"></span> <span class="cls-adder" title="Repeat"><i class="dashicons dashicons-plus-alt"></i></span>'
            ));
        echo '</div>';
        ?>
    <div class="clear"></div>
    </div>
</script>

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