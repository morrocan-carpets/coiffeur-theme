<?php
/**
 * WooCommerce helper functions
 * This functions only load if WooCommerce is enabled because
 * they should be used within Woo loops only.
 *
 * @package Mgana WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!function_exists('mgana_modify_sale_flash')){
    function mgana_modify_sale_flash( $output ){
        return str_replace('class="onsale"', 'class="la-custom-badge onsale"', $output);
    }
}
add_filter('woocommerce_sale_flash', 'mgana_modify_sale_flash');

if(!function_exists('mgana_modify_product_list_preset')){
    function mgana_modify_product_list_preset( $preset ){
        $preset = array(
            '1' => esc_html__( 'Default', 'mgana' )
        );
        return $preset;
    }
}
add_filter('LaStudioElement/products/control/list_style', 'mgana_modify_product_list_preset');

if(!function_exists('mgana_modify_product_grid_preset')){
    function mgana_modify_product_grid_preset( $preset ){
        return array(
            '1' => esc_html__( 'Type 1', 'mgana' ),
            '2' => esc_html__( 'Type 2', 'mgana' ),
            '3' => esc_html__( 'Type 3', 'mgana' ),
            '4' => esc_html__( 'Type 4', 'mgana' ),
            '5' => esc_html__( 'Type 5', 'mgana' ),
            'mini' => esc_html__( 'Minimalist', 'mgana' )
        );
    }
}
add_filter('LaStudioElement/products/control/grid_style', 'mgana_modify_product_grid_preset');

if(!function_exists('mgana_modify_product_masonry_preset')){
    function mgana_modify_product_masonry_preset( $preset ){
        return array(
            '1' => esc_html__( 'Type 1', 'mgana' ),
            '2' => esc_html__( 'Type 2', 'mgana' ),
            '3' => esc_html__( 'Type 3', 'mgana' ),
            '4' => esc_html__( 'Type 4', 'mgana' ),
            '5' => esc_html__( 'Type 5', 'mgana' )
        );
    }
}
add_filter('LaStudioElement/products/control/masonry_style', 'mgana_modify_product_masonry_preset');

add_filter('woocommerce_product_description_heading', '__return_empty_string');
add_filter('woocommerce_product_additional_information_heading', '__return_empty_string');

if(!function_exists('mgana_woo_get_product_per_page_array')){
    function mgana_woo_get_product_per_page_array(){
        $per_page_array = apply_filters('mgana/filter/get_product_per_page_array', mgana_get_option('product_per_page_allow', ''));
        if(!empty($per_page_array)){
            $per_page_array = explode(',', $per_page_array);
            $per_page_array = array_map('trim', $per_page_array);
            $per_page_array = array_map('absint', $per_page_array);
            asort($per_page_array);
            return $per_page_array;
        }
        else{
            return array();
        }
    }
}

if(!function_exists('mgana_woo_get_product_per_row_array')){
    function mgana_woo_get_product_per_row_array(){
        $per_page_array = apply_filters('mgana/filter/get_product_per_row_array', mgana_get_option('product_per_row_allow', ''));
        if(!empty($per_page_array)){
            $per_page_array = explode(',', $per_page_array);
            $per_page_array = array_map('trim', $per_page_array);
            $per_page_array = array_map('absint', $per_page_array);
            asort($per_page_array);
            return $per_page_array;
        }
        else{
            return array();
        }
    }
}

if(!function_exists('mgana_woo_get_product_per_page')){
    function mgana_woo_get_product_per_page(){
        return apply_filters('mgana/filter/get_product_per_page', mgana_get_option('product_per_page_default', 9));
    }
}

if(!function_exists('mgana_get_base_shop_url')){
    function mgana_get_base_shop_url( ){

        if(function_exists('la_get_base_shop_url')){
            return la_get_base_shop_url();
        }

        return get_post_type_archive_link( 'product' );
    }
}

if(!function_exists('mgana_get_wc_attribute_for_compare')){
    function mgana_get_wc_attribute_for_compare(){
        return array(
            'image'         => esc_html__( 'Image', 'mgana' ),
            'title'         => esc_html__( 'Title', 'mgana' ),
            'add-to-cart'   => esc_html__( 'Add to cart', 'mgana' ),
            'price'         => esc_html__( 'Price', 'mgana' ),
            'sku'           => esc_html__( 'Sku', 'mgana' ),
            'description'   => esc_html__( 'Description', 'mgana' ),
            'stock'         => esc_html__( 'Availability', 'mgana' ),
            'weight'        => esc_html__( 'Weight', 'mgana' ),
            'dimensions'    => esc_html__( 'Dimensions', 'mgana' )
        );
    }
}

if(!function_exists('mgana_get_wc_attribute_taxonomies')){
    function mgana_get_wc_attribute_taxonomies( ){
        $attributes = array();
        if( function_exists( 'wc_get_attribute_taxonomies' ) && function_exists( 'wc_attribute_taxonomy_name' ) ) {
            $attribute_taxonomies = wc_get_attribute_taxonomies();
            if(!empty($attribute_taxonomies)){
                foreach( $attribute_taxonomies as $attribute ) {
                    $tax = wc_attribute_taxonomy_name( $attribute->attribute_name );
                    $attributes[$tax] = ucfirst( $attribute->attribute_name );
                }
            }
        }

        return $attributes;
    }
}

/**
 * This function allow get property of `woocommerce_loop` inside the loop
 * @since 1.0.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */

if(!function_exists('mgana_get_wc_loop_prop')){
    function mgana_get_wc_loop_prop( $prop, $default = ''){
        return isset( $GLOBALS['woocommerce_loop'], $GLOBALS['woocommerce_loop'][ $prop ] ) ? $GLOBALS['woocommerce_loop'][ $prop ] : $default;
    }
}

/**
 * This function allow set property of `woocommerce_loop`
 * @since 1.0.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */

if(!function_exists('mgana_set_wc_loop_prop')){
    function mgana_set_wc_loop_prop( $prop, $value = ''){
        if(isset($GLOBALS['woocommerce_loop'])){
            $GLOBALS['woocommerce_loop'][ $prop ] = $value;
        }
    }
}
/**
 * Override template product title
 */
if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {
    function woocommerce_template_loop_product_title() {
        the_title( sprintf( '<h3 class="product_item--title"><a href="%s">', esc_url( get_the_permalink() ) ), '</a></h3>' );
    }
}


if(!function_exists('mgana_wc_filter_show_page_title')){
    function mgana_wc_filter_show_page_title( $show ){
        if( is_singular('product') && mgana_string_to_bool( mgana_get_option('product_single_hide_page_title', 'no') ) ){
            return false;
        }
        if( is_shop() ){
            $shop_page_id = wc_get_page_id( 'shop' );
            if($shop_page_id > 0 && mgana_string_to_bool( mgana_get_post_meta($shop_page_id,'hide_page_title') ) ){
                return false;
            }
        }
        return $show;
    }
    add_filter('mgana/filter/show_page_title', 'mgana_wc_filter_show_page_title', 10, 1 );
}

if(!function_exists('mgana_wc_filter_show_breadcrumbs')){
    function mgana_wc_filter_show_breadcrumbs( $show ){
        if( is_singular('product') && mgana_string_to_bool( mgana_get_option('product_single_hide_breadcrumb', 'no') ) ){
            return false;
        }
        if( is_shop() ){
            $shop_page_id = wc_get_page_id( 'shop' );
            if($shop_page_id > 0 && mgana_string_to_bool( mgana_get_post_meta($shop_page_id,'hide_breadcrumb') ) ){
                return false;
            }
        }
        return $show;
    }
    add_filter('mgana/filter/show_breadcrumbs', 'mgana_wc_filter_show_breadcrumbs', 10, 1 );
}


if(!function_exists('mgana_wc_allow_translate_text_in_swatches')){

    function mgana_wc_allow_translate_text_in_swatches( $text ){
        return esc_html_x( 'Choose an option', 'front-view', 'mgana' );
    }

    add_filter('LaStudio/swatches/args/show_option_none', 'mgana_wc_allow_translate_text_in_swatches', 10, 1);
}

/**
 * Override page title bar from global settings
 * What we need to do now is
 * 1. checking in single content types
 *  1.1) post
 *  1.2) product
 *  1.3) portfolio
 * 2. checking in archives
 *  2.1) shop
 *  2.2) portfolio
 *
 * TIPS: List functions will be use to check
 * `is_product`, `is_single_la_portfolio`, `is_shop`, `is_woocommerce`, `is_product_taxonomy`, `is_archive_la_portfolio`, `is_tax_la_portfolio`
 */

if(!function_exists('mgana_wc_override_page_title_bar_from_context')){
    function mgana_wc_override_page_title_bar_from_context( $value, $key ){

        $array_key_allow = array(
            'page_title_bar_style',
            'page_title_bar_layout',
            'page_title_bar_border',
            'page_title_bar_background',
            'page_title_bar_space',
            'page_title_bar_heading_fonts',
            'page_title_bar_breadcrumb_fonts',
            'page_title_bar_heading_color',
            'page_title_bar_text_color',
            'page_title_bar_link_color',
            'page_title_bar_link_hover_color'
        );

        $array_key_alternative = array(
            'page_title_bar_layout',
            'page_title_bar_border',
            'page_title_bar_background',
            'page_title_bar_space',
            'page_title_bar_heading_fonts',
            'page_title_bar_breadcrumb_fonts',
            'page_title_bar_heading_color',
            'page_title_bar_text_color',
            'page_title_bar_link_color',
            'page_title_bar_link_hover_color'
        );

        /**
         * Firstly, we need to check the `$key` input
         */
        if( !in_array($key, $array_key_allow) ){
            return $value;
        }

        /**
         * Secondary, we need to check the `$context` input
         */

        if( !is_woocommerce() ){
            return $value;
        }
        if($key == 'page_title_bar_layout' && function_exists('dokan_is_store_page') && dokan_is_store_page()){
            return 'hide';
        }

        $func_name = 'mgana_get_post_meta';
        $queried_object_id = get_queried_object_id();

        if( is_product_taxonomy() ){
            $func_name = 'mgana_get_term_meta';
        }

        if( is_shop() ){
            $queried_object_id = wc_get_page_id( 'shop' );
        }

        if ( 'page_title_bar_layout' == $key ) {
            $page_title_bar_layout = call_user_func($func_name, $queried_object_id, $key);
            if($page_title_bar_layout && $page_title_bar_layout != 'inherit'){
                return $page_title_bar_layout;
            }
        }

        if( 'yes' == call_user_func($func_name ,$queried_object_id, 'page_title_bar_style') && in_array($key, $array_key_alternative) ){
            return $value;
        }

        $key_override = $new_key = false;

        if( is_product() ){
            $key_override = 'single_product_override_page_title_bar';
            $new_key = 'single_product_' . $key;
        }
        elseif ( is_shop() || is_product_taxonomy() ) {
            $key_override = 'woo_override_page_title_bar';
            $new_key = 'woo_' . $key;
        }

        if(false != $key_override){
            if( 'on' == mgana_get_option($key_override, 'off') ){
                return mgana_get_option($new_key, $value);
            }
        }

        return $value;
    }

    add_filter('mgana/filter/get_theme_option_by_context', 'mgana_wc_override_page_title_bar_from_context', 20, 2);
}

if(!function_exists('mgana_override_woothumbnail_size_name')){
    function mgana_override_woothumbnail_size_name( ) {
        return 'woocommerce_gallery_thumbnail';
    }
    add_filter('woocommerce_gallery_thumbnail_size', 'mgana_override_woothumbnail_size_name', 0);
}


if(!function_exists('mgana_override_woothumbnail_size')){
    function mgana_override_woothumbnail_size( $size ) {
        if(!function_exists('wc_get_theme_support')){
            return $size;
        }
        $size['width'] = absint( wc_get_theme_support( 'gallery_thumbnail_image_width', 100 ) );
        $cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );

        if ( 'uncropped' === $cropping ) {
            $size['height'] = 0;
            $size['crop']   = 0;
        }
        elseif ( 'custom' === $cropping ) {
            $width          = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
            $height         = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
            $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
            $size['crop']   = 1;
        }
        else {
            $cropping_split = explode( ':', $cropping );
            $width          = max( 1, current( $cropping_split ) );
            $height         = max( 1, end( $cropping_split ) );
            $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
            $size['crop']   = 1;
        }

        return $size;
    }
    add_filter('woocommerce_get_image_size_gallery_thumbnail', 'mgana_override_woothumbnail_size');
}

if(!function_exists('mgana_override_woothumbnail_single')){
    function mgana_override_woothumbnail_single( $size ) {
        if(!function_exists('wc_get_theme_support')){
            return $size;
        }
        $size['width'] = absint( wc_get_theme_support( 'single_image_width', get_option( 'woocommerce_single_image_width', 600 ) ) );
        $cropping      = get_option( 'woocommerce_thumbnail_cropping', '1:1' );

        if ( 'uncropped' === $cropping ) {
            $size['height'] = 0;
            $size['crop']   = 0;
        }
        elseif ( 'custom' === $cropping ) {
            $width          = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_width', '4' ) );
            $height         = max( 1, get_option( 'woocommerce_thumbnail_cropping_custom_height', '3' ) );
            $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
            $size['crop']   = 1;
        }
        else {
            $cropping_split = explode( ':', $cropping );
            $width          = max( 1, current( $cropping_split ) );
            $height         = max( 1, end( $cropping_split ) );
            $size['height'] = absint( round( ( $size['width'] / $width ) * $height ) );
            $size['crop']   = 1;
        }

        return $size;
    }
    add_filter('woocommerce_get_image_size_single', 'mgana_override_woothumbnail_single', 0);
}



if ( !function_exists('mgana_modify_text_woocommerce_catalog_orderby') ){
    function mgana_modify_text_woocommerce_catalog_orderby( $data ) {
        $data = array(
            'menu_order' => esc_html__( 'Sort by Default', 'mgana' ),
            'popularity' => esc_html__( 'Sort by Popularity', 'mgana' ),
            'rating'     => esc_html__( 'Sort by Rated', 'mgana' ),
            'date'       => esc_html__( 'Sort by Latest', 'mgana' ),
            'price'      => sprintf( wp_kses( __( 'Sort by Price: %s', 'mgana' ), array( 'i' => array( 'class' => array() ) ) ), '<i class="lastudioicon-arrow-up"></i>' ),
            'price-desc' => sprintf( wp_kses( __( 'Sort by Price: %s', 'mgana' ), array( 'i' => array( 'class' => array() ) ) ), '<i class="lastudioicon-arrow-down"></i>' ),
        );
        return $data;
    }

    add_filter('woocommerce_catalog_orderby', 'mgana_modify_text_woocommerce_catalog_orderby');
}

if(!function_exists('mgana_add_custom_badge_for_product')){
    function mgana_add_custom_badge_for_product(){
        global $product;
        $product_badges = mgana_get_post_meta($product->get_id(), 'product_badges');
        if(empty($product_badges)){
            return;
        }
        $_tmp_badges = array();
        foreach($product_badges as $badge){
            if(!empty($badge['text'])){
                $_tmp_badges[] = $badge;
            }
        }
        if(empty($_tmp_badges)){
            return;
        }
        foreach($_tmp_badges as $i => $badge){
            $attribute = array();
            if(!empty($badge['bg'])){
                $attribute[] = 'background-color:' . esc_attr($badge['bg']);
            }
            if(!empty($badge['color'])){
                $attribute[] = 'color:' . esc_attr($badge['color']);
            }
            $el_class = ($i%2==0) ? 'odd' : 'even';
            if(!empty($badge['el_class'])){
                $el_class .= ' ';
                $el_class .= $badge['el_class'];
            }

            echo sprintf(
                '<span class="la-custom-badge %1$s" style="%3$s"><span>%2$s</span></span>',
                esc_attr($el_class),
                esc_html($badge['text']),
                (!empty($attribute) ? esc_attr(implode(';', $attribute)) : '')
            );
        }
    }
    add_action( 'woocommerce_before_shop_loop_item_title', 'mgana_add_custom_badge_for_product', 9 );
    add_action( 'woocommerce_before_single_product_summary', 'mgana_add_custom_badge_for_product', 9 );
}

if(!function_exists('mgana_wc_add_custom_countdown_to_product_details')){
    function mgana_wc_add_custom_countdown_to_product_details(){
        global $product;
        if($product->is_on_sale()){
            $sale_price_dates_to = $product->get_date_on_sale_to() && ( $date = $product->get_date_on_sale_to()->getOffsetTimestamp() ) ? $date : '';
            $now = current_time('timestamp');
            if(!empty($sale_price_dates_to)){ ?>
                <div class="prod-countdown-timer js-el" data-la_component="CountDownTimer">
                    <div class="lastudio-countdown-timer" data-ct="<?php echo esc_attr($now); ?>" data-due-date="<?php echo esc_attr($sale_price_dates_to); ?>">
                        <?php if($sale_price_dates_to - $now > 86400): ?><div class="lastudio-countdown-timer__item item-days">
                            <div class="lastudio-countdown-timer__item-value" data-value="days"><span class="lastudio-countdown-timer__digit">0</span><span class="lastudio-countdown-timer__digit">0</span></div>
                            <div class="lastudio-countdown-timer__item-label"><?php esc_html_e('Days', 'mgana') ?></div></div><?php endif; ?>
                        <div class="lastudio-countdown-timer__item item-hours">
                            <div class="lastudio-countdown-timer__item-value" data-value="hours"><span class="lastudio-countdown-timer__digit">0</span><span class="lastudio-countdown-timer__digit">0</span></div>
                            <div class="lastudio-countdown-timer__item-label"><?php esc_html_e('Hours', 'mgana');?></div></div>
                        <div class="lastudio-countdown-timer__item item-minutes">
                            <div class="lastudio-countdown-timer__item-value" data-value="minutes"><span class="lastudio-countdown-timer__digit">0</span><span class="lastudio-countdown-timer__digit">0</span></div>
                            <div class="lastudio-countdown-timer__item-label"><?php esc_html_e('Mins', 'mgana'); ?></div></div>
                        <div class="lastudio-countdown-timer__item item-seconds">
                            <div class="lastudio-countdown-timer__item-value" data-value="seconds"><span class="lastudio-countdown-timer__digit">0</span><span class="lastudio-countdown-timer__digit">0</span></div>
                            <div class="lastudio-countdown-timer__item-label"><?php esc_html_e('Secs', 'mgana'); ?></div></div>
                    </div>
                </div>
                <?php
            }
        }
    }
}

if(!function_exists('mgana_wc_add_custom_stock_to_product_details')){
    function mgana_wc_add_custom_stock_to_product_details(){
        global $product;
        $stock_sold = ($total_sales = $product->get_total_sales()) ? $total_sales : 0;
        if($stock_sold > 0){
            $availability = sprintf(__('%s Sold', 'mgana'), $stock_sold );
            echo str_replace('">', '"><span>' . $availability . '</span><i></i>', wc_get_stock_html( $product ));
        }
        else{
            echo wc_get_stock_html( $product );
        }
    }
}

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 8 );
add_action( 'woocommerce_single_product_summary', 'mgana_wc_add_custom_stock_to_product_details', 7 );
add_action( 'woocommerce_single_product_summary', 'mgana_wc_add_custom_countdown_to_product_details', 25 );


if(!function_exists('mgana_add_custom_block_to_cart_page')){
    function mgana_add_custom_block_to_cart_page(){
        ?>
        <div class="lasf-extra-cart lasf-extra-cart--calc">
            <h2><?php esc_html_e('Estimate Shipping', 'mgana'); ?></h2>
            <p><?php esc_html_e('Enter your destination to get shipping', 'mgana'); ?></p>
            <div class="lasf-extra-cart-box"></div>
        </div>
        <div class="lasf-extra-cart lasf-extra-cart--coupon">
            <h2><?php esc_html_e('Discount code', 'mgana'); ?></h2>
            <p><?php esc_html_e('Enter your coupon if you have one', 'mgana'); ?></p>
            <div class="lasf-extra-cart-box"></div>
        </div>
        <?php
    }
    add_action('woocommerce_cart_collaterals', 'mgana_add_custom_block_to_cart_page', 5);
}

if(!function_exists('mgana_add_custom_step_into_woocommerce')){
    function mgana_add_custom_step_into_woocommerce(){
        if( wp_doing_ajax() || ! empty( $_GET['wc-ajax'] ) ){
            return;
        }
?>
        <div class="row section-checkout-step">
            <div class="col-xs-12">
                <ul>
                    <li class="step-1"><span class="step-name"><span><span class="step-num"><?php esc_html_e('01', 'mgana') ?></span><span><?php esc_html_e('Shopping Cart', 'mgana') ?></span></span></span>
                    </li><li class="step-2"><span class="step-name"><span><span class="step-num"><?php esc_html_e('02', 'mgana') ?></span><span><?php esc_html_e('Check out', 'mgana') ?></span></span></span>
                    </li><li class="step-3"><span class="step-name"><span><span class="step-num"><?php esc_html_e('03', 'mgana') ?></span><span><?php esc_html_e('Order completed', 'mgana') ?></span></span></span></li>
                </ul>
            </div>
        </div>
<?php
    }
}
//add_action('woocommerce_check_cart_items', 'mgana_add_custom_step_into_woocommerce');

if(!function_exists('mgana_add_custom_heading_to_checkout_order_review')){
    function mgana_add_custom_heading_to_checkout_order_review(){
        ?><h3 id="order_review_heading_ref"><?php esc_html_e( 'Your order', 'mgana' ); ?></h3><?php
    }
}
add_action('woocommerce_checkout_order_review', 'mgana_add_custom_heading_to_checkout_order_review', 0);

if(!function_exists('mgana_override_woocommerce_product_get_rating_html')){
    function mgana_override_woocommerce_product_get_rating_html( $html ) {
        if(!empty($html)){
            $html = '<div class="product_item--rating">'.$html.'</div>';
        }
        return $html;
    }
}
add_filter('woocommerce_product_get_rating_html', 'mgana_override_woocommerce_product_get_rating_html');


if(!function_exists('mgana_callback_func_to_show_custom_block')){
    function mgana_callback_func_to_show_custom_block( $block = array(), $hook_name = '', $priority = 10 ){
        if(!empty($block['content']) && !empty($hook_name)){
            echo '<div class="la-custom-block '. (!empty($block['el_class']) ? esc_attr($block['el_class']) : '') .'">';
            echo mgana_transfer_text_to_format($block['content'], true);
            echo '</div>';
        }
    }
}

if(!function_exists('mgana_add_custom_block_to_single_product_page')){
    function mgana_add_custom_block_to_single_product_page(){

        $position_detect = array(
            'pos1' => array(
                'hook_name' => 'woocommerce_single_product_summary',
                'priority'  => 30 /* After Cart */
            ),
            'pos2' => array(
                'hook_name' => 'woocommerce_single_product_summary',
                'priority'  => 40 /* After Meta */
            ),
            'pos3' => array(
                'hook_name' => 'woocommerce_single_product_summary',
                'priority'  => 10 /* After Price */
            ),
            'pos4' => array(
                'hook_name' => 'woocommerce_single_product_summary',
                'priority'  => 5 /* After Title */
            ),
            'pos5' => array(
                'hook_name' => 'woocommerce_single_product_summary',
                'priority'  => 20 /* After Description */
            ),
            'pos6' => array(
                'hook_name' => 'mgana/action/after_woocommerce_single_product_summary',
                'priority'  => 10 /* Beside Summary */
            ),
            'pos7' => array(
                'hook_name' => 'mgana/action/before_wc_tabs',
                'priority'  => 10 /* Before Tabs */
            ),
            'pos8' => array(
                'hook_name' => 'mgana/action/after_wc_tabs',
                'priority'  => 10 /* Before Tabs */
            ),
            'pos9' => array(
                'hook_name' => 'woocommerce_after_single_product_summary',
                'priority'  => 30 /* After Related */
            ),
            'pos10' => array(
                'hook_name' => 'woocommerce_after_single_product_summary',
                'priority'  => 15 /* After Up-sells */
            ),
            'pos11' => array(
                'hook_name' => 'mgana/action/before_main',
                'priority'  => 10 /* After Main Wrap */
            ),
            'pos12' => array(
                'hook_name' => 'mgana/action/after_main',
                'priority'  => 10 /* After Main Wrap */
            )
        );

        if(mgana_string_to_bool(mgana_get_option('woo_enable_custom_block_single_product'))){
            $blocks = mgana_get_option('woo_custom_block_single_product');
            if(!empty($blocks) && is_array($blocks)){
                foreach ($blocks as $k => $block){
                    $block_content = !empty($block['content']) ? $block['content'] : '';
                    $block_position = !empty($block['position']) ? $block['position'] : '';

                    if(!empty($block_content) && !empty($block_position) && is_array($position_detect[$block_position]) ){
                        $hooks = $position_detect[$block_position];
                        $hook_name = $hooks['hook_name'];
                        $priority = $hooks['priority'];

                        add_action( $hook_name, function() use( $block, $hook_name, $priority ) {  mgana_callback_func_to_show_custom_block($block, $hook_name, $priority); }, $priority );
                    }
                }
            }
        }
    }
    add_action('wp_head', 'mgana_add_custom_block_to_single_product_page');
}


if( !function_exists('mgana_calculator_free_shipping_thresholds')){
    function mgana_calculator_free_shipping_thresholds(){
        if( ! mgana_string_to_bool(mgana_get_option('freeshipping_thresholds', 'off')) ){
            return;
        }

        if ( WC()->cart->is_empty() ) {
            return;
        }
        // Get Free Shipping Methods for Rest of the World Zone & populate array $min_amounts
        $default_zone = new WC_Shipping_Zone( 0 );

        $default_methods = $default_zone->get_shipping_methods();
        foreach ( $default_methods as $key => $value ) {
            if ( $value->id === "free_shipping" ) {
                if ( $value->min_amount > 0 ) {
                    $min_amounts[] = $value->min_amount;
                }
            }
        }
        // Get Free Shipping Methods for all other ZONES & populate array $min_amounts
        $delivery_zones = WC_Shipping_Zones::get_zones();
        foreach ( $delivery_zones as $key => $delivery_zone ) {
            foreach ( $delivery_zone['shipping_methods'] as $key => $value ) {
                if ( $value->id === "free_shipping" ) {
                    if ( $value->min_amount > 0 ) {
                        $min_amounts[] = $value->min_amount;
                    }
                }
            }
        }
        // Find lowest min_amount
        if ( isset( $min_amounts ) ) {
            if ( is_array( $min_amounts ) && $min_amounts ) {
                $min_amount = min( $min_amounts );
                // Get Cart Subtotal inc. Tax excl. Shipping
                $current = WC()->cart->subtotal;
                // If Subtotal < Min Amount Echo Notice
                // and add "Continue Shopping" button
                if ( $current > 0 ) {
                    $percent = round( ( $current / $min_amount ) * 100, 2 );
                    $percent >= 100 ? $percent = '100' : '';
                    if ( $percent < 40 ) {
                        $parse_class = 'first-parse';
                    }
                    elseif ( $percent >= 40 && $percent < 80 ) {
                        $parse_class = 'second-parse';
                    }
                    else {
                        $parse_class = 'final-parse';
                    }
                    $parse_class .= ' free-shipping-required-notice';
                    $added_text='<svg xmlns="http://www.w3.org/2000/svg" width="62" height="45" viewBox="0 0 62 45"><g fill="currentColor" fill-rule="evenodd"><path d="M21 38a2 2 0 1 1-4 0 2 2 0 0 1 4 0m29 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0"></path><path d="M19 33.19A4.816 4.816 0 0 0 14.19 38 4.816 4.816 0 0 0 19 42.81 4.816 4.816 0 0 0 23.81 38 4.816 4.816 0 0 0 19 33.19M19 45c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7"></path><path d="M38 37H24.315v-2.145h11.544V2.145H2.14v32.71h11.544V37H0V0h38zm11-3.81A4.816 4.816 0 0 0 44.19 38 4.816 4.816 0 0 0 49 42.81 4.816 4.816 0 0 0 53.81 38 4.816 4.816 0 0 0 49 33.19M49 45c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7"></path><path d="M62 37h-7.607v-2.154h5.47V22.835l-8.578-12.681H38.137v24.692h5.465V37H36V8h16.415L62 22.17z"></path><path d="M42.147 19.932h10.792l-4.15-5.864h-6.642v5.864zM57 22H40V12h9.924L57 22z"></path></g></svg>';
                    if ( $current < $min_amount ) {
                        $added_text .= sprintf(__('Spend %s to get Free Shipping', 'mgana'), wc_price( $min_amount - $current ));
                    } else {
                        $added_text .= esc_html__( 'Congratulations! You\'ve got free shipping!', 'mgana' );
                    }
                    $html = '<div class="' . esc_attr( $parse_class ) . '">';
                    $html .= '<div class="la-loading-bar"><div class="load-percent" style="width:' . esc_attr( $percent ) . '%">';
                    $html .= '</div><span class="label-free-shipping">'.$added_text.'</span></div>';
                    $html .= '</div>';
                    echo ent2ncr( $html );
                }
            }
        }
    }
    add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'mgana_calculator_free_shipping_thresholds', 5 );
    add_action( 'woocommerce_before_cart_table', 'mgana_calculator_free_shipping_thresholds', 5 );
}

if(!function_exists('mgana_wc_add_qty_control_plus')){
    function mgana_wc_add_qty_control_plus(){
        echo '<span class="qty-plus"><i class="lastudioicon-i-add-2"></i></span>';
    }
}

if(!function_exists('mgana_wc_add_qty_control_minus')){
    function mgana_wc_add_qty_control_minus(){
        echo '<span class="qty-minus"><i class="lastudioicon-i-delete-2"></i></span>';
    }
}

add_action('woocommerce_after_quantity_input_field', 'mgana_wc_add_qty_control_plus');
add_action('woocommerce_before_quantity_input_field', 'mgana_wc_add_qty_control_minus');

if(!function_exists('mgana_override_dokan_main_query')){
    function mgana_override_dokan_main_query( $query ) {
        if(function_exists('dokan_is_store_page') && dokan_is_store_page() && isset($query->query['term_section'])){
            if(isset($_GET['per_page'])){
                $query->set('posts_per_page', 0);
            }
            WC()->query->product_query( $query );
        }
    }
    add_action('pre_get_posts', 'mgana_override_dokan_main_query', 11);
}
if(!function_exists('mgana_dokan_dashboard_wrap_before')){
    function mgana_dokan_dashboard_wrap_before(){
        echo '<div id="content-wrap" class="container"><div id="primary" class="content-area"><div id="content" class="site-content"><article class="single-content-article single-page-article"><div class="entry">';
    }
    add_filter('dokan_dashboard_wrap_before', 'mgana_dokan_dashboard_wrap_before');
}

if(!function_exists('mgana_dokan_dashboard_wrap_after')){
    function mgana_dokan_dashboard_wrap_after(){
        echo '</div></article></div></div></div>';
    }
    add_filter('dokan_dashboard_wrap_after', 'mgana_dokan_dashboard_wrap_after');
}