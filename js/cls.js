+function($){
        var Data = {};
        if( CLS ){
                Data = _.extend(Data, CLS);
        }
        var Views = Backbone.View.extend({
                template: _.template,
                events:{
                        'change select' : 'save',
                        'click .cls-remove' : 'remove'
                },
                initialize:function(options){
                        _.extend(this, options);                        
                        this.render()
                },
                _template:function(id, data){
                        data = !data ? {} : data
                        var template = this.template( $('#' + id).html(), {data:data});
                        return template
                },
                render:function(){
                        var that = this, append = this.append ? this.append : 'html'
                        if( this.template_id ){
                                var templates = typeof this.template_id == 'object' ? this.template_id : [this.template_id];
                                _.each(templates, function(k){
                                        var template = that._template( k, that.data );
                                        that.$el[append]( template );
                                })
                        }
                        else if( this.html ){
                                this.$el[append](this.html);
                        }
                        else if( this.text ){
                                this.$el[append](this.text);
                        }
                        if( this.parent ) this.$el.appendTo(this.parent);
                },
                remove:function(ev){ 
                        var target = $(ev.currentTarget);
                        target.each(doRemove);
                        this._save();
                        
                },
                _save:function(){
                        var form = this.$el.find('form'), params = form.serialize(),
                                icon = $('.cls-icon-loading', this.$el);
                                
                        icon.show();
                        $.post(CLS.ajaxurl + '?action=clssettings', params, function(res){
                                icon.hide();
                                });
                },
                save:function(){
                        return this._save();
                },
                saveSettings:function(params){
                        var that = this;                        
                        this.target.addClass('icon-loading');
                        $.post(ajaxurl + '?action=clssettings', params, function(res){
                                that.target.removeClass('icon-loading');
                        })
                }
        });
        
        var doRepeat = function(){
                var target = $(this), cls_box = target.parents('.cls-box'),
                clone = cls_box.clone();
                clone.find('.cls-adder').replaceWith('<span class="cls-remove" title="Remove"><i class="dashicons dashicons-dismiss"></i></span>');
                clone.appendTo(cls_box.parent());
        };
        var doRemove = function(){
                var target = $(this), cls_box = target.parents('.cls-box').first();
                cls_box.remove();
        };
        var getValues = function(){
                var target = $(this), val = target.val(),
                cls_box = target.parents('.cls-box').first(),
                loader = cls_box.find('.cls-loader'),
                values = cls_box.find('.cls_value');
                loader.addClass('cls-loading');
                
                values.find('option').not(':eq(0)').remove();
                Data = CLS.data;
               
                if( !Data[val] ){
                        values.append('<option value="">Nothing found...</option>');
                        loader.removeClass('cls-loading');
                }
                else {
                        _.each(Data[val], function(k,v){
                                k = val == 'template' ? k : k.toLowerCase();
                                values.append('<option value="' + v + '">' + k + '</option>');
                        }, this);
                        
                        loader.removeClass('cls-loading');
                }
        };
        
        var doRename = function(){
                var target = $(this), val = target.val(),
                        cls_box = target.parents('.cls-box');
                        
                cls_box
                .find('[name*="caps"]')
                .each(function(){
                        var input = $(this);
                        input.attr('name', "user_caps[" + val + "][]" );
                });
        };
        
        CLS.views = Views;
        CLS.data = Data;
        
        $(document)
        .on('ready', function(){
                if( CLS.usercaps ){
                        var usercaps = CLS.usercaps,
                        bfdiv1 = $('.cls-box'),
                        adder = bfdiv1.find('.cls-adder'), i = 0;
                        
                        _.each(usercaps, function(caps, id){
                                if( i > 0 ){
                                        adder.trigger('click');
                                }
                                var bfdiv = $('.cls-box:eq(' + i + ')'),
                                user = bfdiv.find('.cls_usercaps'),
                                cap = bfdiv.find('[type=checkbox]');
                                user.val(id);
                                cap.each(function(){
                                        var input = $(this), val = input.val();
                                        this.checked = _.contains(caps, val);
                                });
                                user.trigger('change');
                                i++;
                        });
                }
        })
        .on('click.data-api', '.cls-adder', doRepeat)
        .on('click.data-api', '.cls-remove', doRemove)
        .on('change.data-api', '.cls_select', getValues)
        .on('change.data-api', '.cls_usercaps', doRename);
}(jQuery);