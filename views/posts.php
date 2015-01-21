<script type="text/template" id="cls_posts">
<form method="post" class="cls-setting-inside">
    <h2 class="cls-title"><img src="<?php _e(CLS_HOST); ?>/css/imgs/logo.png" /> <?php _e($this->title); ?> Conditional Logic </h2>
    <span class="cls-icon-loading"></span> 
    <br>
    <div class="cls-inside">
    <?php
        $title = strtolower( $this->title );
        $roles = array_map('strtolower', $this->get_roles());
        $the_roles = $roles;
        unset( $the_roles['administrator'] );
        $values = $this->values();
        
        basefield(array(
            'name' => "{$this->id}[comments]",
            'type' => 'tof',
            'value' => 1,
            'label' => __('Disable comments.'),
            'selected' => isset($values['comments']),
            'sub' => "Will remove comment area in all {$title} including previous comments."
        ));
        
        if( $this->post_type != 'page' ){
        
            basefield(array(
                'type' => 'text',
                'class' => 'input-num',
                'name' => "{$this->id}[summary]",
                'before' => __('Summary length must not be more than ' ),
                'after' => __(' words.' ),
                'value'=> (int) $values['summary']
            ));
            basefield(array(
                'type' => 'select',
                'name' => "{$this->id}[order]",
                'before' => __('Show '. $title .' archive in ' ),
                'after' => ' order.',
                'choices' => array(
                    'post_date' => __('most recent'),
                    'menu_order' => __('menu'),
                    'post_title' => __('alphabetical')
                )
            ));
            
            basefield(array(
                'type' => 'text',
                'name' => "{$this->id}[per_page]",
                'class' => 'input-num',
                'before' => __('Limit ' . $title . ' up to '),
                'after' => ' items per page.',
                'value' => (int) $values['per_page']
            ));
        
        }
        
        basefield(array(
            'type' => 'checkbox',
            'name' => "{$this->id}[read][]",
            'before' => __("Users can read {$title} if they are: "),
            'choices' => array('guest' => 'guests') + $roles,
            'value' => (array) $values['read']
        ));
        basefield(array(
            'type' => 'checkbox',
            'name' => "{$this->id}[can][]",
            'before' => __("Enable <strong>{$this->title} Conditional Logic</strong> metabox if users are: "),
            'choices' => $roles,
            'sub' => "Will allow selected user group to modify individual {$this->post_type} capabilities.",
            'value' => (array) $values['can']
        ));
    ?>
    
    </div>
    <div class="clear"></div>
</form>
<a class="show-settings"><?php _e($this->title); ?></a>
</script>