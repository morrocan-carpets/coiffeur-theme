<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_shop_masonry()
{
    return [
        [
            'filter_name'       => 'mgana/filter/current_title',
            'filter_func'       => function( $title ) {
                $title = 'Shop Masonry';
                return $title;
            },
            'filter_priority'   => 10,
            'filter_args'       => 1
        ],
	    [
		    'key'               => 'layout_archive_product',
		    'value'             => 'col-1c'
	    ],
	    [
		    'key'               => 'product_per_row_allow',
		    'value'             => ''
	    ],
	    [
		    'key'               => 'hide_shop_toolbar',
		    'value'             => 'on'
	    ],
        [
            'key' => 'main_space_archive_product',
            'value' => [
                'mobile' => [
                    'top' => 0,
                    'bottom' => 60,
                ]
            ]
        ],

        [
            'key' => 'shop_catalog_grid_style',
            'value' => '6'
        ],
        [
            'key' => 'woocommerce_pagination_type',
            'value' => 'load_more'
        ],
        [
            'key' => 'product_masonry_image_size',
            'value' => 'full'
        ],
        [
            'key' => 'woocommerce_toggle_grid_list',
            'value' => 'off'
        ],
        [
            'key' => 'active_shop_masonry',
            'value' => 'on'
        ],
        [
            'key' => 'shop_masonry_column_type',
            'value' => 'custom'
        ],
        [
            'key' => 'product_masonry_container_width',
            'value' => 1920
        ],
        [
            'key' => 'product_masonry_item_width',
            'value' => 480
        ],
        [
            'key' => 'product_masonry_item_height',
            'value' => 520
        ],
        [
            'key' => 'woocommerce_shop_masonry_custom_columns',
            'value' => [
                'mobile' => 1,
                'mobile_landscape' => 2,
                'tablet' => 3,
                'laptop' => 3
            ]
        ],
        [
            'key' => 'enable_shop_masonry_custom_setting',
            'value' => 'on'
        ],
        [
            'key' => 'shop_masonry_item_setting',
            'value' => [
                0 => [
                    'size_name' => '1w x 1h',
                    'w'         => 1,
                    'h'         => 1,
                ],
                1 => [
	                'size_name' => '1w x 1h',
                    'w'         => 1,
                    'h'         => 1,
                ],
                2 => [
                    'size_name' => '1w x 1h',
                    'w'         => 1,
                    'h'         => 1,
                ],
                3 => [
                    'size_name' => '1w x 1h',
                    'w'         => 1,
                    'h'         => 1,
                ],
                4 => [
                    'size_name' => '2w x 1h',
                    'w'         => 2,
                    'h'         => 1,
                ],
                5 => [
	                'size_name' => '1w x 1h',
	                'w'         => 1,
	                'h'         => 1,
                ],
                6 => [
	                'size_name' => '1w x 1h',
	                'w'         => 1,
	                'h'         => 1,
                ],
                7 => [
	                'size_name' => '2w x 1h',
	                'w'         => 2,
	                'h'         => 1,
                ],
                8 => [
	                'size_name' => '2w x 1h',
	                'w'         => 2,
	                'h'         => 1,
                ],
                9 => [
	                'size_name' => '2w x 2h',
	                'w'         => 2,
	                'h'         => 2,
                ],
                10 => [
	                'size_name' => '1w x 1h',
	                'w'         => 1,
	                'h'         => 1,
                ],
                11 => [
	                'size_name' => '1w x 1h',
	                'w'         => 1,
	                'h'         => 1,
                ],
                12 => [
	                'size_name' => '1w x 1h',
	                'w'         => 1,
	                'h'         => 1,
                ],
                13 => [
	                'size_name' => '1w x 1h',
	                'w'         => 1,
	                'h'         => 1,
                ],
            ]
        ],
        [
            'key' => 'shop_item_space',
            'value' => [
                'mobile' => [
                    'left' => '0',
                    'right' => '0',
                    'bottom' => '0'
                ]
            ]
        ],
        [
            'filter_name'       => 'mgana/filter/get_option',
            'filter_func'       => function( $value, $key ) {
                if( $key == 'la_custom_css'){
                    $value .= '.la-pagination.active-loadmore {margin-top: 2em;}#main #content-wrap{max-width: 100%}';
                }
                return $value;
            },
            'filter_priority'   => 10,
            'filter_args'       => 2
        ]
    ];
}