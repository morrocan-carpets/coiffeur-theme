<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_product_demo_02()
{
    return [
        [
            'key' => 'woocommerce_product_page_design',
            'value' => '1'
        ],
        [
            'key' => 'main_full_width_single_product',
            'value' => 'no'
        ],
        [
            'key'       => 'product_gallery_column',
            'value'     => [
            	'mobile' => 3,
            	'mobile_landscape' => 4,
            	'tablet' => 3,
            	'laptop' => 4,
            	'desktop' => 4
            ]
        ],
    ];
}