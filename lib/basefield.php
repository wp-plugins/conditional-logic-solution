<?php
/**
 * Basefields
 *
 * Use to create form fields.
 **/

if( ! function_exists( 'basefield_attr' ) ):
        function basefield_attr( $attr = array() ){
                $attr = (array) $attr;
                $attr = array_filter( $attr, create_function( '$a', ' return !empty($a); ' ) );
                $ats = array();
                foreach( $attr as $key => $value){
                        $ats[] = "{$key}=\"" . esc_attr( $value ) . "\"";
                }
                return " " . implode(" ", $ats);
        }
endif;

if( ! function_exists( 'basefield_tag' ) ):
        function basefield_tag( $name, $attr = array(), $content = '' ){
                $tag = "<{$name}" . basefield_attr( (array) $attr );
                
                if( in_array( $name, array('img', 'input') ) ){
                        $tag .= ' /> ' . $content;
                } else {
                        $tag .= '>' . $content . "</{$name}>";
                }
                
                return $tag;
        }
endif;

if( ! function_exists( 'basefield' ) ):
        function basefield( $args = array() ){
                $args = wp_parse_args( (array) $args, array(
                        'type' => 'text',
                        'label' => '',
                        'sub' => '',
                        'name' => '',
                        'before' => '',
                        'after' => '',
                        'selected' => '',
                        'value' => '',
                        'name' => '',
                        'id' => '',
                        'div_class' => '',
                        'echo' => true
                ));
                
                $args = apply_filters( "basefield_" . $args['type'], $args );
                $args = apply_filters( "basefield_" . $args['name'], $args );
                
                if( empty($args['name']) ){
                        unset($args['name']);
                }
                extract( $args );
                
                foreach( array('div_class', 'label', 'sub', 'choices', 'echo', 'buttons', 'before', 'after', 'selected' ) as $extra ){ unset( $args[$extra]); }
                
                $label_class = apply_filters( 'basefield_label_class', array('bf-title') );
                $label_attr = array('class' => implode(" ", $label_class));
                $label = !empty( $label ) ? basefield_tag( 'label', $label_attr, $label ) : '';
                $before = apply_filters( 'basefield_before_field', $before, $args );
                $after = apply_filters( 'basefield_after_field', $after, $args );
                
                $html = $label . $before;
                switch( $type ){
                        default:
                                $args = wp_parse_args( $args, array('size' => 20,'class' => 'bf-input'));
                                $html .= basefield_tag( 'input', $args );                                
                                break;
                        case 'tof':
                                $args = wp_parse_args( array(
                                        'type' => 'checkbox',
                                        'value' => $value
                                ), $args);
                                if( !empty($selected) && $selected == $value ){
                                        $args['checked'] = 'checked';
                                }
                                $input = basefield_tag('input', $args, strip_tags($label));
                                $html = $before . basefield_tag('label', array(), $input);
                                break;
                        case 'textarea':
                                unset( $args['value'], $args['type'] );
                                $args = wp_parse_args( $args, array(
                                        'rows' => 3,
                                        'cols' => 50
                                ));
                                $html .= basefield_tag( 'textarea', $args, esc_attr( $value ) );                                
                                break;
                        case 'checkbox': case 'radio':
                                unset( $args['value'] );
                                $list = array();
                                foreach( (array) $choices as $key => $val ){
                                        $_args = $args;
                                        if( is_array($val) ){
                                                $sub_list = array();
                                                foreach( $val as $k => $v ){
                                                        $attr = $_args;
                                                        $attr['value'] = $k;
                                                        if( in_array($k, (array) $value) ){
                                                                $attr['checked'] = 'checked';
                                                        }
                                                        $input = basefield_tag( 'input', $attr, $v );
                                                        $input = basefield_tag('label', array(), $input );
                                                        $sub_list[] = basefield_tag('li', array(), $input );
                                                }
                                                if( !empty($sub_list) ){
                                                        $title = basefield_tag('label', array(), $key );
                                                        $sub_list = basefield_tag('ol', array(), implode("\r\n", $sub_list));
                                                        $list[] = basefield_tag('li', array(), $title . $sub_list );
                                                }
                                        }
                                        else {
                                                $_args['value'] = $key;
                                                if( in_array( $key, (array) $value )){
                                                        $_args['checked'] = 'checked';
                                                }
                                                $input = basefield_tag('input', $_args, $val );
                                                $input = basefield_tag('label', array(), $input );
                                                $list[] = basefield_tag('li', array(), $input);
                                        }
                                }
                                if( count( $list ) > 0 ){
                                        $html .= basefield_tag('ul', array(), implode("\r\n", $list));
                                }
                                break;
                        case 'select':
                                unset( $args['value'], $args['type'] );
                                $list = array();
                                foreach( (array) $choices as $key => $val ){
                                        $_args = array();
                                        if( is_array($val) ){
                                                $sub_list = array();
                                                foreach( $val as $k => $v ){
                                                        $attr = $_args;
                                                        $attr['value'] = $k;
                                                        if( in_array($k, (array) $value) ){
                                                                $attr['selected'] = 'selected';
                                                        }                                                        
                                                        $sub_list[] = basefield_tag('option', $attr, esc_html( $v ) );
                                                }
                                                if( !empty($sub_list) ){                                                        
                                                        $list[] = basefield_tag('optgroup', array( 'label' => $key ), implode("\r\n", $sub_list ));
                                                }
                                        }
                                        else {
                                                $_args['value'] = $key;
                                                if( in_array( $key, (array) $value )){
                                                        $_args['selected'] = 'selected';
                                                }
                                                $list[] = basefield_tag('option', $_args, esc_html( $val ) );
                                        }
                                }
                                if( count( $list ) > 0 ){
                                        $html .= basefield_tag('select', $args, implode("\r\n", $list));
                                }
                                break;
                        case 'submit':
                                foreach( (array) $buttons as $button_type => $button_label ){
                                        $attr = array('type' => $button_type, 'class' => 'bt-btn', 'value' => $button_label );
                                        $html .= basefield_tag('input', $attr );
                                }
                                break;
                        case 'group':
                                $_fields = '';
                                foreach( (array) $fields as $field){
                                        $field['echo'] = false;
                                        if( isset($args['name']) ){
                                        $field['name'] = $name . '[' . $field['name'] . ']';
                                        }
                                        $_fields .= basefield($field);
                                }
                                $html .= $_fields;
                                break;
                        case 'html':
                                $html .= $value;
                                break;
                }
                $html .= $after;
                
                if( !empty( $sub ) ) $html .= basefield_tag( 'p', array('class' => 'bf-sub'), $sub );
                      
                $name = preg_replace('%[\[\]]%', '', $name);
                $name = empty($name) ? $id : $name;
                $div_class = explode(' ', $div_class);
                $div_class[] = 'bf-' . $name;
                array_unshift( $div_class, 'bf-div' );
                
                $div_args = array('class' => implode(' ', $div_class ) );
                
                if( $type == 'html' ) $div_args = wp_parse_args( $div_args, $args );
                
                unset( $div_args['type'] );
                
                $html = basefield_tag('div', apply_filters( 'basefield_wrapper',  $div_args, $html, $args ), $html );
                if( $echo ) _e( $html );
                else return $html;
        }
endif;

if( ! function_exists( 'basefield_form' ) ):
        function basefield_form( $args = array() ){
                $args = wp_parse_args( $args, array(
                        'method' => 'post',
                        'data-ajax' => false,
                        'fields' => array(),
                        'echo' => true
                ));
                $fields = $args['fields'];
                $echo = $args['echo'];
                
                foreach( array('echo', 'fields') as $clear ){ unset( $args[$clear]); }
                
                $form = '<form' . basefield_attr( $args ) . '>';
                foreach( $fields as $field ){
                        $field['echo'] = false;
                        $form .= basefield( $field );
                }
                $form .= '</form>';
                
                if( $echo ) _e( $form );
                else return $form;
        }
endif;
if( ! function_exists( 'ajax_get_attachment_image' ) ):
        function ajax_get_attachment_image(){
                $id = (int) $_REQUEST['id'];
                $width = (int) $_REQUEST['width'];
                $height = (int) $_REQUEST['height'];
                $size = 'full';
                if( $width > 0 && $height > 0 ){
                        $size = array($width, $height);
                }
                
                if( $id > 0 ){
                        $image = wp_get_attachment_image_src( $id, $size );                        
                        if( $image ) echo json_encode( $image );
                }
                exit;
        }
        add_action( 'wp_ajax_get_image', 'ajax_get_attachment_image' );
endif;