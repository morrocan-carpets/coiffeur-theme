<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_shop_03_columns()
{
    return [
        [
            'filter_name'       => 'mgana/filter/current_title',
            'filter_func'       => function( $title ) {
                $title = 'Shop 03 Columns';
                return $title;
            },
            'filter_priority'   => 10,
            'filter_args'       => 1
        ],
        [
            'key'               => 'main_full_width_archive_product',
            'value'             => 'no'
        ],
	    [
		    'key'               => 'layout_archive_product',
		    'value'             => 'col-1c'
	    ],
        [
            'key'               => 'shop_item_space',
            'value'             => [
                'laptop' => [
                    'left' => '15',
                    'right' => '15',
                    'bottom' => '30'
                ],
                'mobile' => [
                    'left' => '5',
                    'right' => '5',
                    'bottom' => '10'
                ]
            ]
        ],
        [
            'key'               => 'woocommerce_shop_page_columns',
            'value'             => [
                'desktop' => 3,
                'laptop' => 3,
                'tablet' => 3,
                'mobile_landscape' => 2,
                'mobile' => 2
            ]
        ]
    ];
}