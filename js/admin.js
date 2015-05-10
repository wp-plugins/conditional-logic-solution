+function($){
        var toggleAdmins = function(){
                var target = $(this), val = target.val(), div = $('.bf-cls_settingsselected');
                div[ val == 'selected' ? 'slideDown' : 'slideUp' ]();
        };
        var addRole = function(){
                var target = $(this), bfrole = target.parent(),
                input = bfrole.find('[type=text]'), val = input.val();
                if( val != '' ){
                        var cls_caps = $('#cls-capabilities'),
                        tabs = cls_caps.find('.cls-inner-tabs'),
                        li_tab = tabs.find('li'),
                        caps = cls_caps.find('.cls-inner-tab'),
                        clone = caps.first().clone(),
                        rolename = val.replace(/ /g, '_').toLowerCase(),
                        li = '<li>'
                                + '<a href="#cap-' + rolename + '">' + val + '</a>'
                                + '<input type="hidden" name="cls_settings[roles][]" value="' + rolename + '" />'
                                + '<span class="cls-icon cls-remove-role" data-role="' + rolename + '"><i class="dashicons dashicons-welcome-comments"></i></span>'
                                + '</li>';
                        clone.attr('id', 'cap-' + rolename).find('h3').html(val + ' Capabilities');
                        clone.find('[name]').each(function(){
                                var input = $(this);
                                this.checked = false;
                                input.attr('name', 'cls_settings[caps][' + rolename + '][]');
                        });
                        tabs.append(li);
                        clone.insertAfter(caps.last());
                        cls_caps.tabs('refresh');
                        cls_caps.tabs({active: li_tab.length});
                        input.val('');
                }
        };
        
        var removeRole = function(){
                var target = $(this),
                        role = target.attr('data-role'),
                        bfdiv = target.parents('li'), rolediv = $('#cap-' + role );
                bfdiv.remove();
                rolediv.remove();
                $('#cls-capabilities').tabs({active:0})
                $.get( cls.admin_url, {nonce: cls.nonce, role:role});
        };
        
        $(document)
        .on('ready', function(){
                $('.bf-cls_settingscontroller input:checked').each(toggleAdmins);
                $('#cls_setting_page').tabs();
                $('#cls-capabilities').tabs();
        })
        .on('click.data-api', '.bf-cls_settingscontroller input', toggleAdmins)
        .on('click.data-api', '#add_wp_role', addRole)
        .on('click.data-api', '.cls-remove-role', removeRole);
}(jQuery);