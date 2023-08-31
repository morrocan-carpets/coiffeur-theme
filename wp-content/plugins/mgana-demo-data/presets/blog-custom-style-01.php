<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_blog_custom_style_01()
{
    return [
        [
            'key'       => 'layout_blog',
            'value'     => 'col-1c'
        ],
        [
            'key'       => 'blog_design',
            'value'     => 'list-3'
        ],
        [
            'key'       => 'blog_thumbnail_height_custom',
            'value'     => '56%'
        ],
	    [
            'key'       => 'blog_pagination_type',
            'value'     => 'load_more'
        ],
	    [
            'key'       => 'main_space_archive_post',
            'value'     => [
	            'laptop' => [
		            'top' => '160',
		            'bottom' => '150'
	            ]
            ]
        ],
	    [
            'key'       => 'blog_item_space',
            'value'     => [
	            'laptop' => [
		            'bottom' => '100'
	            ]
            ]
        ],
    ];
}