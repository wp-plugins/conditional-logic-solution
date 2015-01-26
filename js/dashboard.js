+function($){
        var Views = CLS.views, Icon = CLS.icon;
        var Dashboard = Views.extend({
                template_id: 'cls-dashboard',
                events: _.extend(Views.prototype.events, {
                        'click .cls-title' : 'toggleBoxes'
                }),
                toggleBoxes:function(ev){
                        var boxes = this.$el.find('.cls-box, .cls-desc'), isopen = boxes.is(':visible');
                        boxes[isopen ? 'slideUp' : 'slideDown']();
                },
                save:function(ev){
                        var target = $(ev.currentTarget), val = target.val(), other;
                        
                        if( val == '' || val == null ){
                                if( target.is('.cls_value') ) other = $('.cls_select', target.parents('div:eq(1)'));
                                else other = $('.cls_value', target.parents('form').first() );
                                
                                other.val('');
                        }
                        this._save();
                },
                render:function(){
                        var cls_id = this.cls_id;
                        this.data = {id:cls_id};
                        Views.prototype.render.apply(this);                       
                        
                        if( CLS.values && CLS.values[cls_id] ){
                                var values = CLS.values[cls_id],
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
                        
                        this.$el.find('form [name]').each(function(){
                                var input = $(this), name = input.attr('name');                        
                                input.attr('name', 'cls_dashboard[' + cls_id + ']' + name);
                        });
                }
        });
        
        $(document)
        .on('ready', function(){
               $('#dashboard-widgets .postbox').each(function(){
                        var _this = $(this), id = _this.attr('id');
                        new Dashboard({parent: _this, cls_id:id});
               });
        });
}(jQuery);