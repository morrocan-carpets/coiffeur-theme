<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_product_demo_04()
{
    return [
        [
            'key' => 'woocommerce_product_page_design',
            'value' => '4'
        ],
	    [
		    'filter_name'       => 'mgana/filter/get_option',
		    'filter_func'       => function( $value, $key ) {
			    if( $key == 'la_custom_css'){
				    $value .= '
@media(min-width: 1200px){
    .row.s_product_content_top>.p-left {
	    width: 50vw;
	    margin-left: calc( -1 * ( 50vw - 585px) );
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