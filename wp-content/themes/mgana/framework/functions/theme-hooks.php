<?php
/**
 * This file includes helper functions used throughout the theme.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_filter( 'body_class', 'mgana_body_classes' );

/**
 * Head
 */
add_action('wp_head', 'mgana_add_meta_into_head_tag', 100 );
add_action('mgana/action/head', 'mgana_add_extra_data_into_head');

add_action('mgana/action/before_outer_wrap', 'mgana_add_pageloader_icon', 1);

/**
 * Header
 */
add_action( 'mgana/action/header', 'mgana_render_header', 10 );


/**
 * Page Header
 */
add_action( 'mgana/action/page_header', 'mgana_render_page_header', 10 );


/**
 * Sidebar
 */


$site_layout = mgana_get_site_layout();

if($site_layout == 'col-2cr' || $site_layout == 'col-2cr-l'){
    add_action( 'mgana/action/after_primary', 'mgana_render_sidebar', 10 );
}
else{
    add_action( 'mgana/action/before_primary', 'mgana_render_sidebar', 10 );
}


/**
 * Footer
 */
add_action( 'mgana/action/footer', 'mgana_render_footer', 10 );

add_action( 'mgana/action/after_outer_wrap', 'mgana_render_footer_searchform_overlay', 10 );
add_action( 'mgana/action/after_outer_wrap', 'mgana_render_footer_cartwidget_overlay', 15 );
add_action( 'mgana/action/after_outer_wrap', 'mgana_render_footer_newsletter_popup', 20 );
add_action( 'mgana/action/after_outer_wrap', 'mgana_render_footer_handheld', 25 );
add_action( 'wp_footer', 'mgana_render_footer_custom_js', 100 );


add_action( 'mgana/action/after_page_entry', 'mgana_render_comment_for_page', 0);

/**
 * Related Posts
 */
add_action( 'mgana/action/after_main', 'mgana_render_related_posts' );
/**
 * FILTERS
 */

add_filter('mgana/filter/get_theme_option_by_context', 'mgana_override_page_title_bar_from_context', 10, 2);
add_filter('previous_post_link', 'mgana_override_post_navigation_template', 10, 5);
add_filter('next_post_link', 'mgana_override_post_navigation_template', 10, 5);

add_filter('mgana/filter/sidebar_primary_name', 'mgana_override_sidebar_name_from_context');

add_filter('wp_get_attachment_image_attributes', 'mgana_add_lazyload_to_image_tag');

add_filter('excerpt_length', 'mgana_change_excerpt_length');

add_filter('mgana/filter/show_page_title', 'mgana_filter_page_title', 10, 1);
add_filter('mgana/filter/show_breadcrumbs', 'mgana_filter_show_breadcrumbs', 10, 1);

add_filter('register_taxonomy_args', 'mgana_override_portfolio_tax_type_args', 99, 2);
add_filter('register_post_type_args', 'mgana_override_portfolio_content_type_args', 99, 2);


add_filter( 'pre_get_posts', 'mgana_setup_post_per_page_for_portfolio');
add_action('wp_head', 'mgana_render_custom_block');