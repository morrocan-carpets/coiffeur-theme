<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_blog_custom_style_03()
{
    return [
        [
            'key'       => 'layout_blog',
            'value'     => 'col-1c'
        ],
        [
            'key'       => 'blog_design',
            'value'     => 'grid-2'
        ],
        [
            'key'       => 'main_full_width_archive_post',
            'value'     => 'yes'
        ],
        [
            'key'       => 'blog_thumbnail_height_custom',
            'value'     => '70%'
        ],
	    [
		    'key'       => 'blog_pagination_type',
		    'value'     => 'load_more'
	    ],
	    [
		    'key'       => 'blog_item_space',
		    'value'     => [
			    'mobile' => [
				    'bottom' => '30'
			    ]
		    ]
	    ],
	    [
		    'key'       => 'blog_post_column',
		    'value'     => [
			    'mobile' => 1,
			    'mobile_landscape' => 2,
			    'tablet' => 2,
			    'laptop' => 3,
			    'desktop' => 3,
		    ]
	    ],
	    [
		    'key'       => 'main_space_archive_post',
		    'value'     => [
			    'laptop' => [
				    'top' => '80',
				    'bottom' => '80'
			    ]
		    ]
	    ],
    ];
}