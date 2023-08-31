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
        <div id="dokan-content" class="store-review-wrap woocommerce" role="main">

            <?php
            $dokan_template_reviews = dokan_pro()->review;
            $id                     = $store_user->ID;
            $post_type              = 'product';
            $limit                  = 20;
            $status                 = '1';
            $comments               = $dokan_template_reviews->comment_query( $id, $post_type, $limit, $status );
            ?>

            <div id="reviews">
                <div id="comments">

                    <?php do_action( 'dokan_review_tab_before_comments' ); ?>

                    <h2 class="headline"><?php _e( 'Vendor Review', 'dokan' ); ?></h2>

                    <ol class="commentlist">
                        <?php echo $dokan_template_reviews->render_store_tab_comment_list( $comments , $store_user->ID); ?>
                    </ol>

                </div>
            </div>

            <?php
            if ( dokan_pro()->module->is_active( 'store_reviews' ) ) {
                echo $dokan_template_reviews->review_pagination( $store_user->ID, $post_type, $limit, $status );
            } else {
                $pagenum = isset( $_REQUEST['pagenum'] ) ? absint( $_REQUEST['pagenum'] ) : 1; // phpcs:ignore
                echo $dokan_template_reviews->review_pagination_with_query( $store_user->ID, $post_type, $limit, $status, $pagenum );
            }
            ?>

        </div><!-- #content .site-content -->

</div><!-- #primary .content-area -->


<?php

do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );