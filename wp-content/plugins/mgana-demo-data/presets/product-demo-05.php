<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_product_demo_05()
{
    return [
        [
            'key' => 'woocommerce_product_page_design',
            'value' => '5'
        ],
	    [
            'key' => 'move_woo_tabs_to_bottom',
            'value' => 'no'
        ],
	    [
		    'filter_name'       => 'mgana/filter/get_option',
		    'filter_func'       => function( $value, $key ) {
			    if( $key == 'la_custom_css'){
				    $value .= '
@media(min-width: 1300px){
    .la-p-single-5.la-p-single-wrap .s_product_content_top>.product-main-image {
        margin-left: calc( -1 * ((100vw - 1200px)/2) );
        width: 100vw;
        padding-left: 30px;
        padding-right: 30px;
    }   
}

@media(min-width: 1600px){
    .la-p-single-5.la-p-single-wrap .s_product_content_top>.product-main-image {
        margin-left: calc( -1 * ((100vw - 1220px)/2) );
        padding-left: 60px;
        padding-right: 60px;
    }   
}

@media(min-width: 1800px){
    .la-p-single-5.la-p-single-wrap .s_product_content_top>.product-main-image {
        padding-left: 140px;
        padding-right: 140px;
    }   
}
				    ';
			    }
			    return $value;
		    },
		    'filter_priority'   => 10,
		    'filter_args'       => 2
	    ],
    ];
}