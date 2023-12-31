<?php

/**
 * Dynamic CSS Function.
 *
 * @since       1.0.0
 * @return      mixed variable ($GLOBALS['lahb-dynamic-styles'])
 */
function lahb_styling_tab_output( $atts = array(), $el = '', $class = '', $class_hover = '' ) {

    $el 		 = str_replace( '-', '_', sanitize_title( $el ) );

    if(empty($class_hover)){
        $tmp_class_h = '';
        $tmp_class_hovers = explode(',', $class);
        if(!empty($tmp_class_hovers)){
            $tmp_class_hovers = array_map('trim', $tmp_class_hovers);
        }
        if(!empty($tmp_class_hovers)){
            $is_first = true;
            foreach ($tmp_class_hovers as $aa){
                if(!$is_first){
                    $tmp_class_h .= ',';
                }
                $tmp_class_h .= $aa . ':hover';
                $is_first = false;
            }
        }
        $class_hover = $tmp_class_h;
    }

    /**
     * Fetch variables
     *
     * @since       1.0.0
     */

    $variables = array();

    $is_header_area = false;

    $is_vertical_area = false;

    if(!empty($atts['component_name']) && ($atts['component_name'] == 'header-area' || $atts['component_name'] == 'header-column')){
        $is_header_area = true;
    }

    if(!empty($atts['component_name']) && ($atts['component_name'] == 'vertical-area')){
        $is_vertical_area = true;
    }

    if ( is_array( $atts ) ) {
        foreach ($atts as $var_name => $var_val) {
            if (is_numeric($var_val) && $var_val == 0) {
                $var_val = $var_val . 'px';
            }
            // desktop
            if (strpos($var_name, $el . '_md') !== false) {
                $var_name = str_replace($el . '_md', '_all', $var_name);
                $variables[$var_name] = $var_val;
            } // tablets
            elseif (strpos($var_name, $el . '_sm') !== false) {
                $var_name = str_replace($el . '_sm', '_tablets', $var_name);
                $variables[$var_name] = $var_val;
            } // mobiles
            elseif (strpos($var_name, $el . '_xs') !== false) {
                $var_name = str_replace($el . '_xs', '_mobiles', $var_name);
                $variables[$var_name] = $var_val;
            }
        }
    }

    if(empty($variables)){
        return '';
    }

    $breakpoints = lahb_get_custom_breakpoints();

    extract( $variables );

    $styles = $tablet_styles = $mobile_styles = $desktop_style = $desktop_style_hover = $tablet_style = $tablet_style_hover = $mobile_style = $mobile_style_hover = '';

    /**
     * Support Multilingual RTL
     */
    $ltr_left_attr = 'left';
    $ltr_right_attr = 'right';

    if(lahb_is_multilingual() && is_rtl()){
        $ltr_left_attr = 'right';
        $ltr_right_attr = 'left';
    }

    /**
     * Desktop variables
     *
     * @since       1.0.0
     */

    // Desktop typography
    $desktop_style .= ! empty( $color_all ) ? 'color:' . $color_all . ';' : '' ;
    $desktop_style .= ! empty( $fill_all ) ? 'fill:' . $fill_all . ';' : '' ;
    $desktop_style .= ! empty( $font_size_all ) ? 'font-size:' . $font_size_all . ';' : '' ;
    $desktop_style .= ! empty( $font_weight_all ) ? 'font-weight:' . $font_weight_all . ';' : '' ;
    $desktop_style .= ! empty( $font_style_all ) ? 'font-style:' . $font_style_all . ';' : '' ;
    $desktop_style .= ! empty( $text_align_all ) ? 'text-align:' . $text_align_all . ';' : '' ;
    $desktop_style .= ! empty( $text_transform_all ) ? 'text-transform:' . $text_transform_all . ';' : '' ;
    $desktop_style .= ! empty( $text_decoration_all ) ? 'text-decoration:' . $text_decoration_all . ';' : '' ;
    $desktop_style .= ! empty( $width_all ) ? 'width:' . $width_all . ';' : '' ;
    $desktop_style .= ! empty( $max_width_all ) ? 'max-width:' . $max_width_all . ';' : '' ;
    $desktop_style .= ! empty( $height_all ) ? 'height:' . $height_all . ';' : '' ;
    $desktop_style .= ! empty( $line_height_all ) ? 'line-height:' . $line_height_all . ';' : '' ;
    $desktop_style .= ! empty( $letter_spacing_all ) ? 'letter-spacing:' . $letter_spacing_all . ';' : '' ;
    $desktop_style .= ! empty( $overflow_all ) ? 'overflow:' . $overflow_all . ';' : '' ;
    $desktop_style .= ! empty( $word_break_all ) ? 'word-break:' . $word_break_all . ';' : '' ;

    // Desktop background
    $desktop_style .= ! empty( $background_color_all ) ? 'background-color:' . $background_color_all . ';' : '' ;
    if ( ! empty( $background_image_all ) ) :
        $desktop_style .= 'background-image: url(' . str_replace(array('https://', 'http://'), '//', wp_get_attachment_url( $background_image_all )) . ');';
        $desktop_style .= ! empty( $background_position_all ) ? 'background-position:' . $background_position_all . ';' : 'background-position:center center;' ;
        $desktop_style .= ! empty( $background_repeat_all ) ? 'background-repeat:' . $background_repeat_all . ';' : 'background-repeat: no-repeat;' ;
        $desktop_style .= ! empty( $background_cover_all ) && $background_cover_all != 'true' ? '' : 'background-size: cover;' ;
    endif;

    // Desktop box
    $desktop_style .= ! empty( $margin_top_all ) ? 'margin-top:' . LAHB_Helper::css_sanatize( $margin_top_all ) . ';' : '' ;
    $desktop_style .= ! empty( $margin_right_all ) ? 'margin-'.$ltr_right_attr.':' . LAHB_Helper::css_sanatize( $margin_right_all ) . ';' : '' ;
    $desktop_style .= ! empty( $margin_bottom_all ) ? 'margin-bottom:' . LAHB_Helper::css_sanatize( $margin_bottom_all ) . ';' : '' ;
    $desktop_style .= ! empty( $margin_left_all ) ? 'margin-'.$ltr_left_attr.':' . LAHB_Helper::css_sanatize( $margin_left_all ) . ';' : '' ;

    $desktop_style .= ! empty( $padding_top_all ) ? 'padding-top:' . LAHB_Helper::css_sanatize( $padding_top_all ) . ';' : '' ;
    $desktop_style .= ! empty( $padding_right_all ) ? 'padding-'.$ltr_right_attr.':' . LAHB_Helper::css_sanatize( $padding_right_all ) . ';' : '' ;
    $desktop_style .= ! empty( $padding_bottom_all ) ? 'padding-bottom:' . LAHB_Helper::css_sanatize( $padding_bottom_all ) . ';' : '' ;
    $desktop_style .= ! empty( $padding_left_all ) ? 'padding-'.$ltr_left_attr.':' . LAHB_Helper::css_sanatize( $padding_left_all ) . ';' : '' ;

    $desktop_style .= ! empty( $top_left_radius_all ) ? 'border-top-'.$ltr_left_attr.'-radius:' . LAHB_Helper::css_sanatize( $top_left_radius_all ) . ';' : '' ;
    $desktop_style .= ! empty( $top_right_radius_all ) ? 'border-top-'.$ltr_right_attr.'-radius:' . LAHB_Helper::css_sanatize( $top_right_radius_all ) . ';' : '' ;
    $desktop_style .= ! empty( $bottom_right_radius_all ) ? 'border-bottom-'.$ltr_right_attr.'-radius:' . LAHB_Helper::css_sanatize( $bottom_right_radius_all ) . ';' : '' ;
    $desktop_style .= ! empty( $bottom_left_radius_all ) ? 'border-bottom-'.$ltr_left_attr.'-radius:' . LAHB_Helper::css_sanatize( $bottom_left_radius_all ) . ';' : '' ;

    $desktop_style .= ! empty( $border_style_all ) ? 'border-style:' . $border_style_all . ';' : '' ;
    $desktop_style .= ! empty( $border_color_all ) ? 'border-color:' . $border_color_all . ';' : '' ;
    $desktop_style .= ! empty( $border_top_all ) ? 'border-top-width:' . LAHB_Helper::css_sanatize( $border_top_all ) . ';' : '' ;
    $desktop_style .= ! empty( $border_right_all ) ? 'border-'.$ltr_right_attr.'-width:' . LAHB_Helper::css_sanatize( $border_right_all ) . ';' : '' ;
    $desktop_style .= ! empty( $border_bottom_all ) ? 'border-bottom-width:' . LAHB_Helper::css_sanatize( $border_bottom_all ) . ';' : '' ;
    $desktop_style .= ! empty( $border_left_all ) ? 'border-'.$ltr_left_attr.'-width:' . LAHB_Helper::css_sanatize( $border_left_all ) . ';' : '' ;
    $desktop_style .= ! empty( $position_top_all ) ? 'top:' . LAHB_Helper::css_sanatize( $position_top_all ) . ';' : '' ;
    $desktop_style .= ! empty( $position_right_all ) ? $ltr_right_attr.':' . LAHB_Helper::css_sanatize( $position_right_all ) . ';' : '' ;
    $desktop_style .= ! empty( $position_bottom_all ) ? 'bottom:' . LAHB_Helper::css_sanatize( $position_bottom_all ) . ';' : '' ;
    $desktop_style .= ! empty( $position_left_all ) ?  $ltr_left_attr .':' . LAHB_Helper::css_sanatize( $position_left_all ) . ';' : '' ;
    $desktop_style .= ! empty( $float_all ) ? 'float:' . $float_all . ';' : '' ;
    if (! empty( $position_top_all ) || ! empty( $position_right_all ) || ! empty( $position_bottom_all ) || ! empty( $position_left_all ) ) {
        $desktop_style .= 'transform: none;';
    }

    // Desktop Shadow Box
    $box_shadow_xoffset_all = ! empty( $box_shadow_xoffset_all ) ? $box_shadow_xoffset_all : '0' ;
    $box_shadow_yoffset_all = ! empty( $box_shadow_yoffset_all ) ? $box_shadow_yoffset_all : '0' ;
    $box_shadow_blur_all 	= ! empty( $box_shadow_blur_all ) ? $box_shadow_blur_all : '0' ;
    $box_shadow_spread_all 	= ! empty( $box_shadow_spread_all ) ? $box_shadow_spread_all : '0' ;
    $box_shadow_status_all 	= isset( $box_shadow_status_all ) && $box_shadow_status_all == 'true' ? 'inset' : '';
    $box_shadow_color_all 	= ! empty( $box_shadow_color_all ) ? $box_shadow_color_all : '' ;
    if ( ! empty( $box_shadow_color_all ) ) {
        $desktop_style .= 'box-shadow: ' . $box_shadow_xoffset_all . ' ' . $box_shadow_yoffset_all . ' ' . $box_shadow_blur_all . ' ' . $box_shadow_spread_all . ' ' . $box_shadow_color_all . ' ' . $box_shadow_status_all . ';';
    }

    // Desktop gradient
    $gradient_color1_all = ! empty( $gradient_color1_all ) ? $gradient_color1_all : '';
    $gradient_color2_all = ! empty( $gradient_color2_all ) ? $gradient_color2_all : '';
    $gradient_color3_all = ! empty( $gradient_color3_all ) ? $gradient_color3_all : '';
    $gradient_color4_all = ! empty( $gradient_color4_all ) ? $gradient_color4_all : '';
    $gradient_direction_all = ! empty( $gradient_direction_all ) ? $gradient_direction_all : '';
    $gradient_all = lahb_gradient( $gradient_color1_all, $gradient_color2_all, $gradient_color3_all, $gradient_color4_all, $gradient_direction_all );
    if (!empty($gradient_all)) {
        $desktop_style .= $gradient_all;
    }

    // Dektop hover styles
    $desktop_style_hover .= ! empty( $background_color_hover_all ) ? 'background-color:' . $background_color_hover_all . ';' : '' ;
    $desktop_style_hover .= ! empty( $color_hover_all ) ? 'color:' . $color_hover_all . ';' : '' ;
    $desktop_style_hover .= ! empty( $fill_hover_all ) ? 'fill:' . $fill_hover_all . ';' : '' ;
    $desktop_style_hover .= ! empty( $border_hover_all ) ? 'border-color:' . $border_hover_all . ';' : '' ;

    if ( ! empty( $desktop_style ) ) :
        $styles .= $class . ' { '  . $desktop_style  . '}';
    endif;

    if ( ! empty( $desktop_style_hover ) ) :
        $styles .= $class_hover . ' { ' . $desktop_style_hover . '}';
    endif;


    /**
     * Tablets variables
     *
     * @since       1.0.0
     */

    // tablet typography
    $tablet_style .= ! empty( $color_tablets ) ? 'color:' . $color_tablets . ';' : '' ;
    $tablet_style .= ! empty( $fill_tablets ) ? 'fill:' . $fill_tablets . ';' : '' ;
    $tablet_style .= ! empty( $font_size_tablets ) ? 'font-size:' . $font_size_tablets . ';' : '' ;
    $tablet_style .= ! empty( $font_weight_tablets ) ? 'font-weight:' . $font_weight_tablets . ';' : '' ;
    $tablet_style .= ! empty( $font_style_tablets ) ? 'font-style:' . $font_style_tablets . ';' : '' ;
    $tablet_style .= ! empty( $text_align_tablets ) ? 'text-align:' . $text_align_tablets . ';' : '' ;
    $tablet_style .= ! empty( $text_transform_tablets ) ? 'text-transform:' . $text_transform_tablets . ';' : '' ;
    $tablet_style .= ! empty( $text_decoration_tablets ) ? 'text-decoration:' . $text_decoration_tablets . ';' : '' ;
    $tablet_style .= ! empty( $width_tablets ) ? 'width:' . $width_tablets . ';' : '' ;
    $tablet_style .= ! empty( $max_width_tablets ) ? 'max-width:' . $max_width_tablets . ';' : '' ;
    $tablet_style .= ! empty( $height_tablets ) ? 'height:' . $height_tablets . ';' : '' ;
    $tablet_style .= ! empty( $line_height_tablets ) ? 'line-height:' . $line_height_tablets . ';' : '' ;
    $tablet_style .= ! empty( $letter_spacing_tablets ) ? 'letter-spacing:' . $letter_spacing_tablets . ';' : '' ;
    $tablet_style .= ! empty( $overflow_tablets ) ? 'overflow:' . $overflow_tablets . ';' : '' ;
    $tablet_style .= ! empty( $word_break_tablets ) ? 'word-break:' . $word_break_tablets . ';' : '' ;

    // tablet background
    $tablet_style .= ! empty( $background_color_tablets ) ? 'background-color:' . $background_color_tablets . ';' : '' ;
    if ( ! empty( $background_image_tablets ) ) :
        $tablet_style .= 'background-image: url(' . str_replace(array('https://', 'http://'), '//', wp_get_attachment_url( $background_image_tablets )) . ');';
        $tablet_style .= ! empty( $background_position_tablets ) ? 'background-position:' . $background_position_tablets . ';' : 'center center;' ;
        $tablet_style .= ! empty( $background_repeat_tablets ) ? 'background-repeat:' . $background_repeat_tablets . ';' : 'no-repeat;' ;
        $tablet_style .= ! empty( $background_cover_tablets ) && $background_cover_tablets == 'true' ? 'background-size: cover;' : '' ;
    endif;

    // tablet box
    $tablet_style .= ! empty( $margin_top_tablets ) ? 'margin-top:' . LAHB_Helper::css_sanatize( $margin_top_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $margin_right_tablets ) ? 'margin-'.$ltr_right_attr.':' . LAHB_Helper::css_sanatize( $margin_right_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $margin_bottom_tablets ) ? 'margin-bottom:' . LAHB_Helper::css_sanatize( $margin_bottom_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $margin_left_tablets ) ? 'margin-'.$ltr_left_attr.':' . LAHB_Helper::css_sanatize( $margin_left_tablets ) . ';' : '' ;

    $tablet_style .= ! empty( $padding_top_tablets ) ? 'padding-top:' . LAHB_Helper::css_sanatize( $padding_top_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $padding_right_tablets ) ? 'padding-'.$ltr_right_attr.':' . LAHB_Helper::css_sanatize( $padding_right_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $padding_bottom_tablets ) ? 'padding-bottom:' . LAHB_Helper::css_sanatize( $padding_bottom_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $padding_left_tablets ) ? 'padding-'.$ltr_left_attr.':' . LAHB_Helper::css_sanatize( $padding_left_tablets ) . ';' : '' ;

    $tablet_style .= ! empty( $top_left_radius_tablets ) ? 'border-top-'.$ltr_left_attr.'-radius:' . LAHB_Helper::css_sanatize( $top_left_radius_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $top_right_radius_tablets ) ? 'border-top-'.$ltr_right_attr.'-radius:' . LAHB_Helper::css_sanatize( $top_right_radius_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $bottom_right_radius_tablets ) ? 'border-bottom-'.$ltr_right_attr.'-radius:' . LAHB_Helper::css_sanatize( $bottom_right_radius_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $bottom_left_radius_tablets ) ? 'border-bottom-left-radius:' . LAHB_Helper::css_sanatize( $bottom_left_radius_tablets ) . ';' : '' ;

    $tablet_style .= ! empty( $border_style_tablets ) ? 'border-style:' . $border_style_tablets . ';' : '' ;
    $tablet_style .= ! empty( $border_color_tablets ) ? 'border-color:' . $border_color_tablets . ';' : '' ;
    $tablet_style .= ! empty( $border_top_tablets ) ? 'border-top-width:' . LAHB_Helper::css_sanatize( $border_top_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $border_right_tablets ) ? 'border-'.$ltr_right_attr.'-width:' . LAHB_Helper::css_sanatize( $border_right_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $border_bottom_tablets ) ? 'border-bottom-width:' . LAHB_Helper::css_sanatize( $border_bottom_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $border_left_tablets ) ? 'border-'.$ltr_left_attr.'-width:' . LAHB_Helper::css_sanatize( $border_left_tablets ) . ';' : '' ;

    $tablet_style .= ! empty( $position_top_tablets ) ? 'top:' . LAHB_Helper::css_sanatize( $position_top_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $position_right_tablets ) ? $ltr_right_attr . ':' . LAHB_Helper::css_sanatize( $position_right_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $position_bottom_tablets ) ? 'bottom:' . LAHB_Helper::css_sanatize( $position_bottom_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $position_left_tablets ) ? $ltr_left_attr.':' . LAHB_Helper::css_sanatize( $position_left_tablets ) . ';' : '' ;
    $tablet_style .= ! empty( $float_tablets ) ? 'float:' . $float_tablets . ';' : '' ;
    if (! empty( $position_top_tablets ) || ! empty( $position_right_tablets ) || ! empty( $position_bottom_tablets ) || ! empty( $position_left_tablets ) ) {
        $tablet_style .= 'transform: none;';
    }

    // Tablet Shadow Box
    $box_shadow_xoffset_tablets = ! empty( $box_shadow_xoffset_tablets ) ? $box_shadow_xoffset_tablets : '0' ;
    $box_shadow_yoffset_tablets = ! empty( $box_shadow_yoffset_tablets ) ? $box_shadow_yoffset_tablets : '0' ;
    $box_shadow_blur_tablets 	= ! empty( $box_shadow_blur_tablets ) ? $box_shadow_blur_tablets : '0' ;
    $box_shadow_spread_tablets 	= ! empty( $box_shadow_spread_tablets ) ? $box_shadow_spread_tablets : '0' ;
    $box_shadow_status_tablets 	= isset( $box_shadow_status_tablets ) && $box_shadow_status_tablets == 'true' ? 'inset' : '';
    $box_shadow_color_tablets 	= ! empty( $box_shadow_color_tablets ) ? $box_shadow_color_tablets : '' ;
    if ( ! empty( $box_shadow_color_tablets ) ) {
        $tablet_style .= 'box-shadow: ' . $box_shadow_xoffset_tablets . ' ' . $box_shadow_yoffset_tablets . ' ' . $box_shadow_blur_tablets . ' ' . $box_shadow_spread_tablets . ' ' . $box_shadow_color_tablets . ' ' . $box_shadow_status_tablets . ';';
    }

    // Tablets gradient
    $gradient_color1_tablets = ! empty( $gradient_color1_tablets ) ? $gradient_color1_tablets : '';
    $gradient_color2_tablets = ! empty( $gradient_color2_tablets ) ? $gradient_color2_tablets : '';
    $gradient_color3_tablets = ! empty( $gradient_color3_tablets ) ? $gradient_color3_tablets : '';
    $gradient_color4_tablets = ! empty( $gradient_color4_tablets ) ? $gradient_color4_tablets : '';
    $gradient_direction_tablets = ! empty( $gradient_direction_tablets ) ? $gradient_direction_tablets : '';
    $gradient_tablets = lahb_gradient( $gradient_color1_tablets, $gradient_color2_tablets, $gradient_color3_tablets, $gradient_color4_tablets, $gradient_direction_tablets );
    if (!empty($gradient_tablets)) {
        $tablet_style .= $gradient_tablets;
    }

    // tablet hover styles
    $tablet_style_hover .= ! empty( $background_color_hover_tablets ) ? 'background-color:' . $background_color_hover_tablets . ';' : '' ;
    $tablet_style_hover .= ! empty( $color_hover_tablets ) ? 'color:' . $color_hover_tablets . ';' : '' ;
    $tablet_style_hover .= ! empty( $fill_hover_tablets ) ? 'fill:' . $fill_hover_tablets . ';' : '' ;
    $tablet_style_hover .= ! empty( $border_hover_tablets ) ? 'border-color:' . $border_hover_tablets . ';' : '' ;

    if ( ! empty( $tablet_style ) ) :
        $tablet_styles .= '@media only screen and ( max-width:'.($breakpoints['lg'] - 1).'px ) {' . $class . ' { '  . $tablet_style  . '} }';
    endif;

    if ( ! empty( $tablet_style_hover ) ) :
        $tablet_styles .= '@media only screen and ( max-width:'.($breakpoints['lg'] - 1).'px ) {' . $class_hover . ':hover { ' . $tablet_style_hover . '} }';
    endif;


    /**
     * Mobiles variables
     *
     * @since       1.0.0
     */

    // mobiles typography
    $mobile_style .= ! empty( $color_mobiles ) ? 'color:' . $color_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $fill_mobiles ) ? 'fill:' . $fill_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $font_size_mobiles ) ? 'font-size:' . $font_size_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $font_weight_mobiles ) ? 'font-weight:' . $font_weight_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $font_style_mobiles ) ? 'font-style:' . $font_style_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $text_align_mobiles ) ? 'text-align:' . $text_align_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $text_transform_mobiles ) ? 'text-transform:' . $text_transform_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $text_decoration_mobiles ) ? 'text-decoration:' . $text_decoration_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $width_mobiles ) ? 'width:' . $width_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $max_width_mobiles ) ? 'max-width:' . $max_width_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $height_mobiles ) ? 'height:' . $height_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $line_height_mobiles ) ? 'line-height:' . $line_height_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $letter_spacing_mobiles ) ? 'letter-spacing:' . $letter_spacing_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $overflow_mobiles ) ? 'overflow:' . $overflow_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $word_break_mobiles ) ? 'word-break:' . $word_break_mobiles . ';' : '' ;

    // mobiles background
    $mobile_style .= ! empty( $background_color_mobiles ) ? 'background-color:' . $background_color_mobiles . ';' : '' ;
    if ( ! empty( $background_image_mobiles ) ) :
        $mobile_style .= 'background-image: url(' . str_replace(array('https://', 'http://'), '//', wp_get_attachment_url( $background_image_mobiles )) . ');';
        $mobile_style .= ! empty( $background_position_mobiles ) ? 'background-position:' . $background_position_mobiles . ';' : 'center center;' ;
        $mobile_style .= ! empty( $background_repeat_mobiles ) ? 'background-repeat:' . $background_repeat_mobiles . ';' : 'no-repeat;' ;
        $mobile_style .= ! empty( $background_cover_mobiles ) && $background_cover_mobiles == 'true' ? 'background-size: cover;' : '' ;
    endif;

    // mobiles box
    $mobile_style .= ! empty( $margin_top_mobiles ) ? 'margin-top:' . LAHB_Helper::css_sanatize( $margin_top_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $margin_right_mobiles ) ? 'margin-'.$ltr_right_attr.':' . LAHB_Helper::css_sanatize( $margin_right_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $margin_bottom_mobiles ) ? 'margin-bottom:' . LAHB_Helper::css_sanatize( $margin_bottom_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $margin_left_mobiles ) ? 'margin-'.$ltr_left_attr.':' . LAHB_Helper::css_sanatize( $margin_left_mobiles ) . ';' : '' ;

    $mobile_style .= ! empty( $padding_top_mobiles ) ? 'padding-top:' . LAHB_Helper::css_sanatize( $padding_top_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $padding_right_mobiles ) ? 'padding-'.$ltr_right_attr.':' . LAHB_Helper::css_sanatize( $padding_right_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $padding_bottom_mobiles ) ? 'padding-bottom:' . LAHB_Helper::css_sanatize( $padding_bottom_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $padding_left_mobiles ) ? 'padding-'.$ltr_left_attr.':' . LAHB_Helper::css_sanatize( $padding_left_mobiles ) . ';' : '' ;

    $mobile_style .= ! empty( $top_left_radius_mobiles ) ? 'border-top-'.$ltr_left_attr.'-radius:' . LAHB_Helper::css_sanatize( $top_left_radius_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $top_right_radius_mobiles ) ? 'border-top-'.$ltr_right_attr.'-radius:' . LAHB_Helper::css_sanatize( $top_right_radius_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $bottom_right_radius_mobiles ) ? 'border-bottom-'.$ltr_right_attr.'-radius:' . LAHB_Helper::css_sanatize( $bottom_right_radius_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $bottom_left_radius_mobiles ) ? 'border-bottom-'.$ltr_left_attr.'-radius:' . LAHB_Helper::css_sanatize( $bottom_left_radius_mobiles ) . ';' : '' ;

    $mobile_style .= ! empty( $border_style_mobiles ) ? 'border-style:' . $border_style_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $border_color_mobiles ) ? 'border-color:' . $border_color_mobiles . ';' : '' ;
    $mobile_style .= ! empty( $border_top_mobiles ) ? 'border-top-width:' . LAHB_Helper::css_sanatize( $border_top_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $border_right_mobiles ) ? 'border-'.$ltr_right_attr.'-width:' . LAHB_Helper::css_sanatize( $border_right_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $border_bottom_mobiles ) ? 'border-bottom-width:' . LAHB_Helper::css_sanatize( $border_bottom_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $border_left_mobiles ) ? 'border-'.$ltr_left_attr.'-width:' . LAHB_Helper::css_sanatize( $border_left_mobiles ) . ';' : '' ;

    $mobile_style .= ! empty( $position_top_mobiles ) ? 'top:' . LAHB_Helper::css_sanatize( $position_top_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $position_right_mobiles ) ? $ltr_right_attr .':' . LAHB_Helper::css_sanatize( $position_right_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $position_bottom_mobiles ) ? 'bottom:' . LAHB_Helper::css_sanatize( $position_bottom_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $position_left_mobiles ) ? $ltr_left_attr. ':' . LAHB_Helper::css_sanatize( $position_left_mobiles ) . ';' : '' ;
    $mobile_style .= ! empty( $float_mobiles ) ? 'float:' . $float_mobiles . ';' : '' ;
    if ( ! empty( $position_top_mobiles ) || ! empty( $position_right_mobiles ) || ! empty( $position_bottom_mobiles ) || ! empty( $position_left_mobiles ) ) {
        $mobile_style .= 'transform: none;';
    }

    // Mobile Shadow Box
    $box_shadow_xoffset_mobiles = ! empty( $box_shadow_xoffset_mobiles ) ? $box_shadow_xoffset_mobiles : '0' ;
    $box_shadow_yoffset_mobiles = ! empty( $box_shadow_yoffset_mobiles ) ? $box_shadow_yoffset_mobiles : '0' ;
    $box_shadow_blur_mobiles 	= ! empty( $box_shadow_blur_mobiles ) ? $box_shadow_blur_mobiles : '0' ;
    $box_shadow_spread_mobiles 	= ! empty( $box_shadow_spread_mobiles ) ? $box_shadow_spread_mobiles : '0' ;
    $box_shadow_status_mobiles 	= isset( $box_shadow_status_mobiles ) && $box_shadow_status_mobiles == 'true' ? 'inset' : '';
    $box_shadow_color_mobiles 	= ! empty( $box_shadow_color_mobiles ) ? $box_shadow_color_mobiles : '' ;
    if ( ! empty( $box_shadow_color_mobiles ) ) {
        $mobile_style .= 'box-shadow: ' . $box_shadow_xoffset_mobiles . ' ' . $box_shadow_yoffset_mobiles . ' ' . $box_shadow_blur_mobiles . ' ' . $box_shadow_spread_mobiles . ' ' . $box_shadow_color_mobiles . ' ' . $box_shadow_status_mobiles . ';';
    }

    // Mobiles gradient
    $gradient_color1_mobiles = ! empty( $gradient_color1_mobiles ) ? $gradient_color1_mobiles : '';
    $gradient_color2_mobiles = ! empty( $gradient_color2_mobiles ) ? $gradient_color2_mobiles : '';
    $gradient_color3_mobiles = ! empty( $gradient_color3_mobiles ) ? $gradient_color3_mobiles : '';
    $gradient_color4_mobiles = ! empty( $gradient_color4_mobiles ) ? $gradient_color4_mobiles : '';
    $gradient_direction_mobiles = ! empty( $gradient_direction_mobiles ) ? $gradient_direction_mobiles : '';
    $gradient_mobiles = lahb_gradient( $gradient_color1_mobiles, $gradient_color2_mobiles, $gradient_color3_mobiles, $gradient_color4_mobiles, $gradient_direction_mobiles );
    if (!empty($gradient_mobiles)) {
        $mobile_style .= $gradient_mobiles;
    }

    // mobiles hover styles
    $mobile_style_hover .= ! empty( $background_color_hover_mobiles ) ? 'background-color:' . $background_color_hover_mobiles . ';' : '' ;
    $mobile_style_hover .= ! empty( $color_hover_mobiles ) ? 'color:' . $color_hover_mobiles . ';' : '' ;
    $mobile_style_hover .= ! empty( $fill_hover_mobiles ) ? 'fill:' . $fill_hover_mobiles . ';' : '' ;
    $mobile_style_hover .= ! empty( $border_hover_mobiles ) ? 'border-color:' . $border_hover_mobiles . ';' : '' ;

    /**
     * Return styles
     *
     * @since       1.0.0
     */

    if ( ! empty( $mobile_style ) ) :
        $mobile_styles .= '@media ( max-width: '.($breakpoints['md'] - 1).'px ) {' . $class . ' { ' . $mobile_style  . '} }';
    endif;

    if ( ! empty( $mobile_style_hover ) ) :
        $mobile_styles .= '@media ( max-width: '.($breakpoints['md'] - 1).'px ) {' . $class_hover . ':hover { ' . $mobile_style_hover . '} }';
    endif;

    static $has_run = null;

    //	if( $is_header_area && $has_run === null ) {
    //		if( !empty($height_all) ) {
    //			$styles = ':root { --header-height: '.$height_all.' }' . $styles;
    //			$has_run = true;
    //		}
    //		if(!empty($height_tablets)) {
    //			$tablet_styles = '@media only screen and ( max-width:'.($breakpoints['lg'] - 1).'px ) { :root { --header-height: '.$height_tablets.'} }' . $tablet_styles;
    //			$has_run = true;
    //		}
    //		if(!empty($height_mobiles)) {
    //			$mobile_styles = '@media ( max-width: '.($breakpoints['md'] - 1).'px ) { :root { --header-height: '.$height_mobiles.'} } ' . $mobile_styles;
    //			$has_run = true;
    //		}
    //	}

    if(isset($atts['area_vertical'])){
        return $styles;
    }
    else{
        if(isset($atts['component_name']) && $atts['component_name'] == 'vertical-area'){
            return $tablet_styles . $mobile_styles;
        }
        else{
            return $styles . $tablet_styles . $mobile_styles;
        }
    }
}
