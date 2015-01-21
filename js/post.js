+function($){
        var Views = CLS.views,
        Posts = Views.extend({
                parent: '#screen-meta-links',
                className: 'screen-meta-toggle cls-setting-box',
                template_id: 'cls_posts',
                events:{
                        'change .post_roles' : 'set_role',
                        'click .show-settings' : 'toggleSettings',
                        'change input' : 'save',
                        'change select' : 'save'
                },
                set_role:function(ev){
                        var target = $( ev.currentTarget ? ev.currentTarget : ev), val = target.val(),
                        cls_box = target.parents('.cls-box'),
                        caps = cls_box.find('[name*="caps"]');
                        caps.each(function(){
                                var input = $(this), name = input.attr('name');
                                input.attr('name', CLS_Posts.id + "[caps][" + target.val() + "][]");
                        });
                },
                toggleSettings:function(){
                        var form = this.$el.find('form'), isopen = form.is(':visible');
                        form[ isopen ? 'slideUp' : 'slideDown']();
                },
                render:function(){
                        Views.prototype.render.apply(this);
                        this.$el.insertAfter(this.parent);
                        if( CLS_Posts.caps ){
                                var that = this,
                                caps = CLS_Posts.caps,
                                bf1 = this.$el.find('.cls-box'),
                                add = bf1.find('.cls-adder'),
                                i = 0;
                                
                                _.each(caps, function(k, v){
                                        if( i > 0 ){
                                                add.trigger('click');
                                        }
                                        var cls = this.$el.find('.cls-box:eq(' + i + ')'),
                                        select = cls.find('.post_roles'),
                                        caps = cls.find('[name*="caps"]');
                                        select.val(v);                                        
                                        caps.each(function(){
                                                var input = $(this), val = input.val();
                                                this.checked = _.contains(k, val);
                                        });
                                        select.each(function(){
                                                that.set_role(this);
                                        });
                                        i++;
                                }, this);
                        }
                }
        });
        
        $(document)
        .on('ready', function(){
                new Posts({type: typenow});
        });
}(jQuery);