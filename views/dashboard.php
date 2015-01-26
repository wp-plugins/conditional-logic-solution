<script type="text/template" id="cls-dashboard">
<form method="post" class="cls-wrapper cls-dashboard">        
    <h3 class="cls-title cls-green">Conditional Logic Solution <span class="cls-icon-right"><small class="dashicons dashicons-admin-generic"></small></span></h3>
    <p class="cls-desc description">Keep this hidden if... <span class="cls-icon-loading"></span></p> 
    <div class="cls-box">
        <?php
        $roles = array_keys( $this->get_roles() );
        basefield(array(
            //'label' => __('Keep this hidden if '),
            'type' => 'select',
            'class' => 'cls_select',
            'name' => '[type][]',
            'choices' => array(
                '' => '',
                'role' => __('User Group'),
                'USERS' => $this->get_roles()
            ),
            'after' => '<span class="dashicons dashicons-leftright"></span>',
            'value' => ''
        ));
        
        basefield(array(
            'type' => 'select',
            'class' => 'cls_value',
            'name' => '[user][]',
            'choices' => array_merge(array('' => ''), $this->get_roles()),
            'after' => '<span class="cls-loader"></span> <span class="cls-adder" title="Repeat"><i class="dashicons dashicons-plus-alt"></i></span>'
        ));
        ?>
        <div class="clear"></div>
    </div>
</form>
</script>