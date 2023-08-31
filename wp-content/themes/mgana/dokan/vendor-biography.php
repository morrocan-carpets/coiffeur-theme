<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$enable_header_banner = dokan_get_option('enable_theme_store_header_tpl', 'dokan_general');

$store_user = get_userdata( get_query_var( 'author' ) );
$store_info   = dokan_get_store_info( $store_user->ID );
$map_location = $store_user->get_location();
$layout       = get_theme_mod( 'store_layout', 'left' );

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

$catalog_class = array('catalog-grid-1 products grid-items');
$catalog_class[] = mgana_get_responsive_column_classes('woocommerce_catalog_columns');

?>
    <div id="dokan-primary" class="dokan-single-store">
<?php
if($enable_header_banner != 'on'){
    dokan_get_template_part( 'store-header' );
}

?>
<div id="vendor-biography">
    <div id="comments">
    <?php do_action( 'dokan_vendor_biography_tab_before', $store_user, $store_info ); ?>

    <h2 class="headline"><?php echo apply_filters( 'dokan_vendor_biography_title', __( 'Vendor Biography', 'dokan' ) ); ?></h2>

    <?php
        if ( ! empty( $store_info['vendor_biography'] ) ) {
            printf( '%s', apply_filters( 'the_content', $store_info['vendor_biography'] ) );
        }
    ?>

    <?php do_action( 'dokan_vendor_biography_tab_after', $store_user, $store_info ); ?>
    </div>
</div>
<?php

do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );

