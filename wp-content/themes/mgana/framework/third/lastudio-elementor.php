<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!function_exists('mgana_override_elementor_resource')){
    function mgana_override_elementor_resource( $path ){
        $path = get_theme_file_uri('assets/addon');
        return $path;
    }
}
add_filter('LaStudioElement/resource-base-url', 'mgana_override_elementor_resource');

if(!function_exists('mgana_add_icon_library_into_elementor')){
    function mgana_add_icon_library_into_elementor( $tabs ) {
        $tabs['lastudioicon'] = [
            'name' => 'lastudioicon',
            'label' => esc_html__( 'LA-Studio Icons', 'mgana' ),
            'prefix' => 'lastudioicon-',
            'displayPrefix' => '',
            'labelIcon' => 'fas fa-star',
            'ver' => '1.0.0',
            'fetchJson' => get_theme_file_uri('assets/fonts/LaStudioIcons.json'),
            'native' => false
        ];
        return $tabs;
    }
}
add_filter('elementor/icons_manager/additional_tabs', 'mgana_add_icon_library_into_elementor');

if(!function_exists('mgana_add_banner_hover_effect')){
    function mgana_add_banner_hover_effect( $effects ){
        return array_merge(array(
            'none'   => esc_html__( 'None', 'mgana' ),
            'type-1' => esc_html__( 'Shadow', 'mgana' )
        ), $effects);
    }
}
add_filter('LaStudioElement/banner/hover_effect', 'mgana_add_banner_hover_effect');

if(!function_exists('mgana_add_portfolio_preset')){
    function mgana_add_portfolio_preset( ){
        return array(
            'type-1' => esc_html__( 'Type 1', 'mgana' ),
            'type-2' => esc_html__( 'Type 2', 'mgana' ),
            'type-3' => esc_html__( 'Type 3', 'mgana' ),
            'type-4' => esc_html__( 'Type 4', 'mgana' ),
            'type-5' => esc_html__( 'Type 5', 'mgana' ),
            'type-6' => esc_html__( 'Type 6', 'mgana' ),
            'type-7' => esc_html__( 'Type 7', 'mgana' ),
        );
    }
}
add_filter('LaStudioElement/portfolio/control/preset', 'mgana_add_portfolio_preset');

if(!function_exists('mgana_add_portfolio_list_preset')){
    function mgana_add_portfolio_list_preset( ){
        return array(
            'list-type-1' => esc_html__( 'Type 1', 'mgana' ),
            'list-type-2' => esc_html__( 'Type 2', 'mgana' ),
            'list-type-3' => esc_html__( 'Type 3', 'mgana' ),
            'list-type-4' => esc_html__( 'Type 4', 'mgana' )
        );
    }
}
add_filter('LaStudioElement/portfolio/control/preset_list', 'mgana_add_portfolio_list_preset');

if(!function_exists('mgana_add_team_member_preset')){
    function mgana_add_team_member_preset( ){
        return array(
            'type-1' => esc_html__( 'Type 1', 'mgana' ),
            'type-2' => esc_html__( 'Type 2', 'mgana' ),
            'type-3' => esc_html__( 'Type 3', 'mgana' ),
            'type-4' => esc_html__( 'Type 4', 'mgana' ),
            'type-5' => esc_html__( 'Type 5', 'mgana' ),
            'type-6' => esc_html__( 'Type 6', 'mgana' ),
            'type-7' => esc_html__( 'Type 7', 'mgana' ),
            'type-8' => esc_html__( 'Type 8', 'mgana' )
        );
    }
}
add_filter('LaStudioElement/team-member/control/preset', 'mgana_add_team_member_preset');

if(!function_exists('mgana_add_posts_preset')){
    function mgana_add_posts_preset( ){
        return array(
            'grid-1' => esc_html__( 'Grid 1', 'mgana' ),
            'grid-2' => esc_html__( 'Grid 2', 'mgana' ),
            'grid-3' => esc_html__( 'Grid 3', 'mgana' ),
            'grid-4' => esc_html__( 'Grid 4', 'mgana' ),
            'grid-5' => esc_html__( 'Grid 5', 'mgana' ),
            'grid-6' => esc_html__( 'Grid 6', 'mgana' ),
            'list-1' => esc_html__( 'List 1', 'mgana' ),
            'list-2' => esc_html__( 'List 2', 'mgana' ),
            'list-3' => esc_html__( 'List 3', 'mgana' )
        );
    }
}
add_filter('LaStudioElement/posts/control/preset', 'mgana_add_posts_preset');

if(!function_exists('mgana_add_testimonials_preset')){
    function mgana_add_testimonials_preset( ){
        return array(
	        'type-1' => esc_html__( 'Type 1', 'mgana' ),
	        'type-2' => esc_html__( 'Type 2', 'mgana' ),
	        'type-3' => esc_html__( 'Type 3', 'mgana' ),
	        'type-4' => esc_html__( 'Type 4', 'mgana' )
        );
    }
}
add_filter('LaStudioElement/testimonials/control/preset', 'mgana_add_testimonials_preset');

if(!function_exists('mgana_add_google_maps_api')){
    function mgana_add_google_maps_api( $key ){
        return mgana_get_option('google_key', $key);
    }
}
add_filter('LaStudioElement/advanced-map/api', 'mgana_add_google_maps_api');

if(!function_exists('mgana_add_instagram_access_token_api')){
    function mgana_add_instagram_access_token_api( $key ){
        return mgana_get_option('instagram_token', $key);
    }
}
add_filter('LaStudioElement/instagram-gallery/api', 'mgana_add_instagram_access_token_api');

if(!function_exists('mgana_add_mailchimp_access_token_api')){
    function mgana_add_mailchimp_access_token_api( $key ){
        return mgana_get_option('mailchimp_api_key', $key);
    }
}
add_filter('LaStudioElement/mailchimp/api', 'mgana_add_mailchimp_access_token_api');

if(!function_exists('mgana_add_mailchimp_list_id')){
    function mgana_add_mailchimp_list_id( $key ){
        return mgana_get_option('mailchimp_list_id', $key);
    }
}
add_filter('LaStudioElement/mailchimp/list_id', 'mgana_add_mailchimp_list_id');

if(!function_exists('mgana_add_mailchimp_double_opt_in')){
    function mgana_add_mailchimp_double_opt_in( $key ){
        return mgana_get_option('mailchimp_double_opt_in', $key);
    }
}
add_filter('LaStudioElement/mailchimp/double_opt_in', 'mgana_add_mailchimp_double_opt_in');

if(!function_exists('mgana_render_breadcrumbs_in_widget')){
    function mgana_render_breadcrumbs_in_widget( $args ) {

        $html_tag = 'nav';
        if(!empty($args['container'])){
            $html_tag = esc_attr($args['container']);
        }

        if ( function_exists( 'yoast_breadcrumb' ) ) {
            $classes = 'site-breadcrumbs';
            return yoast_breadcrumb( '<'.$html_tag.' class="'. esc_attr($classes) .'">', '</'.$html_tag.'>' );
        }

        $breadcrumb = apply_filters( 'breadcrumb_trail_object', null, $args );

        if ( !is_object( $breadcrumb ) ){
            $breadcrumb = new Mgana_Breadcrumb_Trail( $args );
        }

        return $breadcrumb->trail();

    }
}
add_action('LaStudioElement/render_breadcrumbs_output', 'mgana_render_breadcrumbs_in_widget');

if(!function_exists('mgana_turnoff_default_style_of_gallery')){
    function mgana_turnoff_default_style_of_gallery( $base ){
        if( 'image-gallery' === $base->get_name() ) {
            add_filter('use_default_gallery_style', '__return_false');
        }
    }
}
add_action('elementor/widget/before_render_content', 'mgana_turnoff_default_style_of_gallery');