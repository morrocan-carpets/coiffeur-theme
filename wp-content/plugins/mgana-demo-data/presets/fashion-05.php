<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_preset_fashion_05()
{
    return [
    	[
    		'filter_name' => 'LaStudio_Builder/logo_transparency_id',
		    'value'       => 611
	    ],
	    [
		    'filter_name'       => 'mgana/filter/get_option',
		    'filter_func'       => function( $value, $key ) {
			    if( $key == 'la_custom_css'){
				    $value .= '
.lahb-wrap:not(.is-sticky) .lahb-screen-view .lahb-row1-area {
    color: #fff;
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