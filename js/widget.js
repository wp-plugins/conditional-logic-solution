+function($){
        var Views = CLS.views,
        Sidebars = Views.extend({
                template_id: 'cls_widgets',
                cls_id: 'cls_sidebars',
                tagName: 'form',
                className: 'cls-sidebars',
                events: _.extend(Views.prototype.events, {
                        'click .cls-title' : 'toggleBoxes'
                }),
                toggleBoxes:function(ev){
                        var boxes = this.$el.find('.cls-box'), isopen = boxes.is(':visible');
                        boxes[isopen ? 'slideUp' : 'slideDown']();
                },
                render:function(){
                        this.$el.attr('method', 'post');
                        Views.prototype.render.apply(this);
                        var id = this.sidebar, that = this;
                       
                        if( CLS_Widgets.sidebars && CLS_Widgets.sidebars[id] ){
                                var values = CLS_Widgets.sidebars[id],
                                bf1 = this.$el.find('.cls-box'),
                                add = bf1.find('.cls-adder');
                                var type = values.type, value = values.user;
                                
                                if( value ){
                                        _.each(type, function(v, i){
                                                if( i > 0 ){
                                                        add.trigger('click')
                                                }
                                                var cls_box = this.$el.find('.cls-box:eq(' + i + ')'),
                                                        input_type = cls_box.find('[name*="type"]'),
                                                        input_value = cls_box.find('[name*="user"]');
                                                input_type.val(v);
                                                input_type.trigger('change');
                                                input_value.val(value[i]);
                                        }, this);
                                }
                        }
                        
                        this.$el.find('[name]').each(function(){
                                var input = $(this), name = input.attr('name');                        
                                input.attr('name', that.cls_id + '[' + id + ']' + name);
                        });
                },
                save:function(){
                        var params = this.$el.serialize();
                        $.post(CLS.ajaxurl + '?action=clssettings', params);
                }
        }),
        Widgets = Sidebars.extend({
                cls_id: 'cls_widgets',
                tagName: 'div',
                render:function(){
                        var id = this.widget, that = this;
                        
                        Views.prototype.render.apply(this);
                        if( CLS_Widgets.widgets && CLS_Widgets.widgets[id] ){
                                var values = CLS_Widgets.widgets[id],
                                bf1 = this.$el.find('.cls-box'),
                                add = bf1.find('.cls-adder');
                                var type = values.type, value = values.user;
                                
                                _.each(type, function(v, i){
                                        if( i > 0 ){
                                                add.trigger('click')
                                        }
                                        var cls_box = this.$el.find('.cls-box:eq(' + i + ')'),
                                                input_type = cls_box.find('[name*="type"]'),
                                                input_value = cls_box.find('[name*="user"]');
                                        input_type.val(v);
                                        input_type.trigger('change');
                                        input_value.val(value[i]);
                                }, this);
                        }
                        
                        this.$el.find('[name]').each(function(){
                                var input = $(this), name = input.attr('name');                        
                                input.attr('name', that.cls_id + '[' + id + ']' + name);
                        });
                },
                save:function(){
                        var form = this.$el.parents('form').first(), params = form.serialize();
                        $.post(CLS.ajaxurl + '?action=clssettings', params);
                }
        });
        
        if( window.CLS_Widgets ){
                CLS.data.template = window.CLS_Widgets.templates;
        }
        
        var oldWidgets = $.fn.buildWidgets,
                oldSidebars = $.fn.buildSidebars;
                
        $.fn.buildWidgets = function(){
                return this.each(function(){
                        var widget = $(this), clone = widget.empty().clone(false);
                        widget.replaceWith(clone);
                        new Widgets({el:clone, widget:widget.data('widget')});
                });
        };
        $.fn.buildSidebars = function(){
                return this.each(function(){
                        var widget = $(this), clone = widget.empty().clone(false);
                        widget.replaceWith(clone);
                        new Sidebars({parent:widget, sidebar:widget.data('sidebar')});
                });
        };
        
        $.fn.noConfict = function(){
                $.fn.buildWidgets = oldWidgets;
                $.fn.buildSidebars = oldSidebars;
                return this;
        };
        
        $(document)
        .on('ready', function(){
                $('.cls-sidebars').each(function(){
                        var sidebar = $(this), id = sidebar.data('sidebar');
                        new Sidebars({parent:sidebar, sidebar:id});
                });
                
                $('.cls-widgets').each(function(){
                        var widget = $(this), number = widget.data('number');                        
                        if( number && parseInt(number) > 0 )  widget.buildWidgets();
                });
        });
}(jQuery);