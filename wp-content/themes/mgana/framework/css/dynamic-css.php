<?php
/**
 * This file includes dynamic css
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


$breakpoints = mgana_get_custom_breakpoints();

$css_primary_color = mgana_get_option('primary_color', '#D4876C');
$css_secondary_color = mgana_get_option('secondary_color', '#1A1A1A');
$css_three_color = mgana_get_option('three_color', '#979797');
$css_border_color = mgana_get_option('border_color', '#ebebeb');

$device_lists = array('mobile', 'mobile_landscape', 'tablet', 'laptop', 'desktop');

$all_styles = array(
    'mobile' => array(),
    'mobile_landscape' => array(),
    'tablet' => array(),
    'laptop' => array(),
    'desktop' => array()
);

$body_font_family = mgana_get_option('body_font_family');
$headings_font_family = mgana_get_option('headings_font_family');
$three_font_family = mgana_get_option('three_font_family');


$root_style = array();

if(!empty($body_font_family['font-family'])){
	$root_style[] = '--theme-body-font-family: "' .$body_font_family['font-family'] . '"';
}
if(!empty($body_font_family['color'])){
	$root_style[] = '--theme-body-font-color: ' .$body_font_family['color'];
}
if(!empty($headings_font_family['font-family'])){
	$root_style[] = '--theme-heading-font-family: "' .$headings_font_family['font-family'] . '"';
}
if(!empty($headings_font_family['color'])){
	$root_style[] = '--theme-heading-font-color: ' .$headings_font_family['color'];
}
if(!empty($three_font_family['font-family'])){
	$root_style[] = '--theme-three-font-family: "' .$three_font_family['font-family'] . '"';
}

if(!empty($css_primary_color)){
	$root_style[] = '--theme-primary-color: ' . $css_primary_color;
	$root_style[] = '--theme-link-hover-color: ' . $css_primary_color;
}
if(!empty($css_secondary_color)){
	$root_style[] = '--theme-secondary-color: ' . $css_secondary_color;
}
if(!empty($css_three_color)){
	$root_style[] = '--theme-three-color: ' . $css_three_color;
}
if(!empty($css_border_color)){
	$root_style[] = '--theme-border-color: ' . $css_border_color;
}

$root_style[] = '--theme-newsletter-popup-width: ' . mgana_get_option('popup_max_width', 790) . 'px';
$root_style[] = '--theme-newsletter-popup-height: ' . mgana_get_option('popup_max_height', 430) . 'px';


if(!empty($root_style)){
	echo ':root{';
	echo join(';', $root_style);
	echo '}';
}

/**
 * Footer Bars
 */

$mb_footer_bar_visible = mgana_get_option('mb_footer_bar_visible', '600');

echo '@media(min-width: '.esc_attr($mb_footer_bar_visible).'px){ body.enable-footer-bars{ padding-bottom: 0} .footer-handheld-footer-bar { opacity: 0 !important; visibility: hidden !important } }';

/**
 * Body Background
 */

$body_background = mgana_get_option('body_background');
if(!empty(mgana_array_filter_recursive($body_background))){
    if(!empty($body_background['background-color'])){
        echo ':root{--theme-body-bg-color: '.esc_attr($body_background['background-color']).'}';
    }
    echo mgana_render_background_style_from_setting($body_background, 'body.mgana-body');
}

/**
 * Main_Space
 */
$main_space = mgana_get_theme_option_by_context('main_space');
if(!empty($main_space)){
    foreach ($main_space as $screen => $value ){
        $_css = '';
        $unit = !empty($value['unit'])? $value['unit']: 'px';
        $value_atts = shortcode_atts(array(
            'top' => '',
            'right' => '',
            'bottom' => '',
            'left' => '',
        ), $value);
        foreach ($value_atts as $k => $v){
            if($v !== ''){
                $_css .= 'padding-' . $k . ':' . $v . $unit . ';';
            }
        }
        if(!empty($_css)) {
            $all_styles[$screen][] = '#main #content-wrap{'. $_css .'}';
        }
    }
}

/**
 * Page Title Bar
 */
$page_title_bar_func = 'mgana_get_option';
if( mgana_string_to_bool(mgana_get_theme_option_by_context('page_title_bar_style', 'no')) ){
    $page_title_bar_func = 'mgana_get_theme_option_by_context';
}
if( mgana_is_blog() ){
    if( mgana_string_to_bool( mgana_get_option('blog_post_override_page_title_bar', 'off') ) ) {
        $page_title_bar_func = 'mgana_get_theme_option_by_context';
    }
}
elseif ( function_exists('is_product') && is_product() ){
    if( mgana_string_to_bool( mgana_get_option('single_product_override_page_title_bar', 'off') ) ) {
        $page_title_bar_func = 'mgana_get_theme_option_by_context';
    }
}
elseif ( post_type_exists('la_portfolio') && is_singular('la_portfolio') ){
    if( mgana_string_to_bool( mgana_get_option('single_portfolio_override_page_title_bar', 'off') ) ) {
        $page_title_bar_func = 'mgana_get_theme_option_by_context';
    }
}
elseif ( is_singular('post') ){
    if( mgana_string_to_bool( mgana_get_option('single_post_override_page_title_bar', 'off') ) ) {
        $page_title_bar_func = 'mgana_get_theme_option_by_context';
    }
}
elseif ( function_exists('is_woocommerce') && is_woocommerce() ) {
    if( mgana_string_to_bool( mgana_get_option('woo_override_page_title_bar', 'off') ) ) {
        $page_title_bar_func = 'mgana_get_theme_option_by_context';
    }
}
elseif ( post_type_exists('la_portfolio') && (is_post_type_archive('la_portfolio') || ( is_tax() && is_tax( get_object_taxonomies( 'la_portfolio' ) ) )) ) {
    if( mgana_string_to_bool( mgana_get_option('archive_portfolio_override_page_title_bar', 'off') ) ) {
        $page_title_bar_func = 'mgana_get_theme_option_by_context';
    }
}

$page_title_bar_space = call_user_func($page_title_bar_func, 'page_title_bar_space');

if(!empty($page_title_bar_space)){
    foreach ($page_title_bar_space as $screen => $value ){
        $_css = '';
        $unit = !empty($value['unit'])? $value['unit']: 'px';
        $value_atts = shortcode_atts(array(
            'top' => '',
            'right' => '',
            'bottom' => '',
            'left' => '',
        ), $value);
        foreach ($value_atts as $k => $v){
            if($v !== ''){
                $_css .= 'padding-' . $k . ':' . $v . $unit . ';';
            }
        }
        if(!empty($_css)) {
            $all_styles[$screen][] = '.section-page-header .page-header-inner{'. $_css .'}';
        }
    }
}

$page_title_bar_border = call_user_func($page_title_bar_func, 'page_title_bar_border');
$page_title_bar_background = call_user_func($page_title_bar_func, 'page_title_bar_background');

$page_title_bar_heading_color = call_user_func($page_title_bar_func, 'page_title_bar_heading_color', $css_secondary_color);
$page_title_bar_text_color = call_user_func($page_title_bar_func, 'page_title_bar_text_color', $css_secondary_color);
$page_title_bar_link_color = call_user_func($page_title_bar_func, 'page_title_bar_link_color', $css_secondary_color);
$page_title_bar_link_hover_color = call_user_func($page_title_bar_func, 'page_title_bar_link_hover_color', $css_primary_color);


if(!empty(mgana_array_filter_recursive($page_title_bar_border))){
    echo mgana_render_border_style_from_setting($page_title_bar_border, '.section-page-header');
}

if(!empty(mgana_array_filter_recursive($page_title_bar_background))){
    echo mgana_render_background_style_from_setting($page_title_bar_background, '.section-page-header');
}

/**
 * Build Typography - Page Header
 */
$page_title_bar_heading_fonts = call_user_func($page_title_bar_func, 'page_title_bar_heading_fonts');
$page_title_bar_breadcrumb_fonts = call_user_func($page_title_bar_func, 'page_title_bar_breadcrumb_fonts');
foreach ($device_lists as $screen){

    $_css = mgana_render_typography_style_from_setting( $page_title_bar_heading_fonts, '.section-page-header .page-title', $screen );
    if(!empty($_css)) {
        $all_styles[$screen][] = $_css;
    }

    $_css = mgana_render_typography_style_from_setting( $page_title_bar_breadcrumb_fonts, '.section-page-header .site-breadcrumbs', $screen );
    if(!empty($_css)) {
        $all_styles[$screen][] = $_css;
    }

}

if(!empty($page_title_bar_heading_color)){
    echo '.section-page-header .page-title { color: '.esc_attr($page_title_bar_heading_color).' }';
}

if(!empty($page_title_bar_text_color)){
    echo '.section-page-header { color: '.esc_attr($page_title_bar_text_color).' }';
}

if(!empty($page_title_bar_link_color)){
    echo '.section-page-header a { color: '.esc_attr($page_title_bar_link_color).' }';
}
if(!empty($page_title_bar_link_hover_color)){
    echo '.section-page-header a:hover { color: '.esc_attr($page_title_bar_link_hover_color).' }';
}

/**
 * Popup Style
 */
$popup_background = mgana_get_option('popup_background');
if(!empty(mgana_array_filter_recursive($popup_background))){
    echo mgana_render_background_style_from_setting($popup_background, '.open-newsletter-popup .lightcase-inlineWrap');
}


/**
 * Shop Item Space
 */
$shop_item_space = mgana_get_option('shop_item_space');
if(!empty($shop_item_space)){
    foreach ($shop_item_space as $screen => $value ){
        $_css = '';
        $_css2 = '';
        $unit = !empty($value['unit'])? $value['unit']: 'px';

        $value_atts = shortcode_atts(array(
            'top' => '',
            'right' => '',
            'bottom' => '',
            'left' => ''
        ), $value);


        foreach ($value_atts as $k => $v){
            if($v !== ''){
                $_css .= 'padding-' . $k . ':' . $v . $unit . ';';
                if($k == 'left' || $k == 'right'){
                    $_css2 .= 'margin-' . $k . ':-' . $v . $unit . ';';
                }
            }
        }

        if(!empty($_css)) {
            $all_styles[$screen][] = '.la-shop-products .ul_products.products{'. $_css2 .'}';
            $all_styles[$screen][] = '.la-shop-products .ul_products.products li.product_item{'. $_css .'}';
        }
    }
}
/**
 * Blog Item Image Height
 */
$blog_item_space = mgana_get_option('blog_item_space');
if(!empty($blog_item_space)){
    foreach ($blog_item_space as $screen => $value ){
        $_css = '';
        $_css2 = '';
        $unit = !empty($value['unit'])? $value['unit']: 'px';

        $value_atts = shortcode_atts(array(
            'top' => '',
            'right' => '',
            'bottom' => '',
            'left' => '',
        ), $value);
        foreach ($value_atts as $k => $v){
            if($v !== ''){
                $_css .= 'padding-' . $k . ':' . $v . $unit . ';';
                if($k == 'left' || $k == 'right'){
                    $_css2 .= 'margin-' . $k . ':-' . $v . $unit . ';';
                }
            }
        }
        if(!empty($_css)) {
            $all_styles[$screen][] = '.lastudio-posts.blog__entries{'. $_css2 .'}';
            $all_styles[$screen][] = '.lastudio-posts.blog__entries .loop__item{'. $_css .'}';
        }
    }
}

$blog_thumbnail_height_mode = mgana_get_option('blog_thumbnail_height_mode', 'original');
$blog_thumbnail_height_custom = mgana_get_option('blog_thumbnail_height_custom', '70%');
$blog_thumbnail_height = '70%';

switch ($blog_thumbnail_height_mode){
    case '1-1':
        $blog_thumbnail_height = '100%';
        break;
    case '4-3':
        $blog_thumbnail_height = '75%';
        break;
    case '3-4':
        $blog_thumbnail_height = '133.34%';
        break;
    case '16-9':
        $blog_thumbnail_height = '56.25%';
        break;
    case '9-16':
        $blog_thumbnail_height = '177.78%';
        break;
    case 'custom':
        $blog_thumbnail_height = $blog_thumbnail_height_custom;
        break;
}
if($blog_thumbnail_height_mode != 'original'){
    $all_styles['mobile'][] = '.lastudio-posts.blog__entries .post-thumbnail .blog_item--thumbnail, .lastudio-posts.blog__entries .post-thumbnail .blog_item--thumbnail .slick-slide .sinmer{ padding-bottom: '.$blog_thumbnail_height.'}';
}

/**
 * Build Typography
 */

$typography_selectors = array(
    'body_font_family'                  => 'body',
    'three_font_family'                 => '.three_font_family,.highlight_font_family',
    'headings_font_family'              => 'h1,h2,h3,h4,h5,h6,.theme-heading, .widget-title, .comments-title, .comment-reply-title, .entry-title',
    'heading1_font_family'              => 'h1',
    'heading2_font_family'              => 'h2',
    'heading3_font_family'              => 'h3',
    'heading4_font_family'              => 'h4',
    'blog_entry_title_font_family'      => '.lastudio-posts.blog__entries .entry-title',
    'blog_entry_meta_font_family'       => '.lastudio-posts.blog__entries .post-meta',
    'blog_entry_content_font_family'    => '.lastudio-posts.blog__entries .entry-excerpt',
    'blog_post_meta_font_family'        => '.single-post-article > .post-meta__item, .single-post-article > .post-meta .post-meta__item',
    'blog_post_content_font_family'     => 'body:not(.page-use-builder) .single-post-article > .entry',
);

foreach ($device_lists as $screen){
    foreach ($typography_selectors as $opt_key => $typography_selector ){
        $_css = mgana_render_typography_style_from_setting( mgana_get_option($opt_key), $typography_selector, $screen );
        if(!empty($_css)) {
            $all_styles[$screen][] = $_css;
        }
    }
}

/**
 * Build Typography - Custom Selector
 */
$extra_typography = mgana_get_option('extra_typography');
if(!empty($extra_typography)){
    foreach ($extra_typography as $item){
        if(!empty($item['selector']) && !empty($item['fonts'])){
            $css_custom_selector = rtrim(trim($item['selector']), ',');
            if(!empty($css_custom_selector)){
                foreach ($device_lists as $screen){
                    $_css = mgana_render_typography_style_from_setting( $item['fonts'], $css_custom_selector, $screen );
                    if(!empty($_css)) {
                        $all_styles[$screen][] = $_css;
                    }
                }
            }
        }
    }
}

$product_main_image_width = mgana_get_option('woocommerce_product_page_main_image_width');
if(!empty($product_main_image_width)){
    if(!empty($product_main_image_width['tablet']) && !empty($product_main_image_width['tablet']['width']) && !empty($product_main_image_width['tablet']['unit'])){
        $all_styles['tablet'][] = '.s_product_content_top > .product-main-image{width:'.esc_attr($product_main_image_width['tablet']['width'] . $product_main_image_width['tablet']['unit']).'}';
        $all_styles['tablet'][] = '.s_product_content_top > .product--summary{-ms-flex-positive:1;-webkit-flex-grow:1;flex-grow:1}';
    }
    if(!empty($product_main_image_width['laptop']) && !empty($product_main_image_width['laptop']['width']) && !empty($product_main_image_width['laptop']['unit'])){
        $all_styles['laptop'][] = '.s_product_content_top > .product-main-image{width:'.esc_attr($product_main_image_width['laptop']['width'] . $product_main_image_width['laptop']['unit']).'}';
        $all_styles['laptop'][] = '.s_product_content_top > .product--summary{-ms-flex-positive:1;-webkit-flex-grow:1;flex-grow:1}';
    }
    if(!empty($product_main_image_width['desktop']) && !empty($product_main_image_width['desktop']['width']) && !empty($product_main_image_width['desktop']['unit'])){
        $all_styles['desktop'][] = '.s_product_content_top > .product-main-image{width:'.esc_attr($product_main_image_width['desktop']['width'] . $product_main_image_width['desktop']['unit']).'}';
        $all_styles['desktop'][] = '.s_product_content_top > .product--summary{-ms-flex-positive:1;-webkit-flex-grow:1;flex-grow:1}';
    }
}

/**
 * Print the styles
 */
/**
 * MOBILE FIRST
 */
if(!empty($all_styles['mobile'])){
    echo join('', $all_styles['mobile']);
}

/**
 * MOBILE LANDSCAPE AND TABLET PORTRAIT
 */
if(!empty($all_styles['mobile_landscape'])){
    echo '@media (min-width: '.$breakpoints['sm'].'px) {';
    echo join('', $all_styles['mobile_landscape']);
    echo '}';
}

/**
 * TABLET LANDSCAPE
 */
if(!empty($all_styles['tablet'])){
    echo '@media (min-width: '.$breakpoints['md'].'px) {';
    echo join('', $all_styles['tablet']);
    echo '}';
}

/**
 * LAPTOP LANDSCAPE
 */
if(!empty($all_styles['laptop'])){
    echo '@media (min-width: '.$breakpoints['lg'].'px) {';
    echo join('', $all_styles['laptop']);
    echo '}';
}

/**
 * DESKTOP LANDSCAPE
 */
if(!empty($all_styles['desktop'])){
    echo '@media (min-width: '.$breakpoints['xl'].'px) {';
    echo join('', $all_styles['desktop']);
    echo '}';
}