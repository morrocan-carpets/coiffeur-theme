<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_blog_custom_style_02()
{
    return [
        [
            'key'       => 'layout_blog',
            'value'     => 'col-1c'
        ],
        [
            'key'       => 'blog_design',
            'value'     => 'list-2'
        ],
	    [
		    'key'       => 'main_full_width_archive_post',
		    'value'     => 'yes'
	    ],
	    [
		    'key'       => 'blog_thumbnail_height_custom',
		    'value'     => '80%'
	    ],
	    [
		    'key'       => 'blog_excerpt_length',
		    'value'     => '18'
	    ],
	    [
            'key'       => 'main_space_archive_post',
            'value'     => [
	            'mobile' => [
		            'top' => '0',
		            'bottom' => '0'
	            ]
            ]
        ],
	    [
            'key'       => 'blog_item_space',
            'value'     => [
	            'mobile' => [
		            'bottom' => '0'
	            ]
            ]
        ],
	    [
		    'filter_name'       => 'mgana/filter/get_option',
		    'filter_func'       => function( $value, $key ) {
			    if( $key == 'la_custom_css'){
				    $value .= '#main #content-wrap { max-width: 100%; } .site-content > .la-pagination { text-align: center; margin: 5em 0; }';
			    }
			    return $value;
		    },
		    'filter_priority'   => 10,
		    'filter_args'       => 2
	    ],
    ];
}