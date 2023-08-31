<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_shop_fullwidth()
{
    return [
    	[
    		'key'               => 'layout_archive_product',
		    'value'             => 'col-1c'
	    ],
	    [
    		'key'               => 'product_per_row_allow',
		    'value'             => '2,3,4,5'
	    ],
	    [
		    'key'               => 'woocommerce_shop_page_columns',
		    'value'             => [
			    'desktop' => 4,
			    'laptop' => 4,
			    'tablet' => 3,
			    'mobile_landscape' => 2,
			    'mobile' => 2
		    ]
	    ],
        [
            'filter_name'       => 'mgana/filter/current_title',
            'filter_func'       => function( $title ) {
                $title = 'Shop fullwidth';
                return $title;
            },
            'filter_priority'   => 10,
            'filter_args'       => 1
        ]
    ];
}