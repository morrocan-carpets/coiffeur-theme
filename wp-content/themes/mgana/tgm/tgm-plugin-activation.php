<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'tgmpa_register', 'mgana_register_required_plugins' );

if(!function_exists('lasf_get_plugin_source')){
    function lasf_get_plugin_source( $new, $initial, $plugin_name, $type = 'source'){
        if(isset($new[$plugin_name], $new[$plugin_name][$type]) && version_compare($initial[$plugin_name]['version'], $new[$plugin_name]['version']) < 0 ){
            return $new[$plugin_name][$type];
        }
        else{
            return $initial[$plugin_name][$type];
        }
    }
}

if(!function_exists('mgana_register_required_plugins')){

	function mgana_register_required_plugins() {

        $initial_required = array(
            'lastudio' => array(
                'source'    => 'https://la-studioweb.com/file-resouces/shared/plugins/lastudio_v2.2.0.zip',
                'version'   => '2.2.0'
            ),
            'lastudio-header-builders' => array(
                'source'    => 'https://la-studioweb.com/file-resouces/shared/plugins/lastudio-header-builders_v1.2.2.1.zip',
                'version'   => '1.2.2.1'
            ),
            'revslider' => array(
                'source'    => 'https://la-studioweb.com/shared/plugins/revslider_v6.6.14.zip',
                'version'   => '6.6.14'
            ),
            'mgana-demo-data' => array(
                'source'    => 'https://la-studioweb.com/file-resouces/mgana/plugins/mgana-demo-data/1.0.0/mgana-demo-data.zip',
                'version'   => '1.0.0'
            )
        );

        $from_option = get_option('mgana_required_plugins_list', $initial_required);

		$plugins = array();

		$plugins[] = array(
			'name'					=> esc_html_x('LA-Studio Core', 'admin-view', 'mgana'),
			'slug'					=> 'lastudio',
            'source'				=> lasf_get_plugin_source($from_option, $initial_required, 'lastudio'),
            'required'				=> true,
            'version'				=> lasf_get_plugin_source($from_option, $initial_required, 'lastudio', 'version')
		);

		$plugins[] = array(
			'name'					=> esc_html_x('LA-Studio Header Builder', 'admin-view', 'mgana'),
			'slug'					=> 'lastudio-header-builders',
            'source'				=> lasf_get_plugin_source($from_option, $initial_required, 'lastudio-header-builders'),
            'required'				=> true,
            'version'				=> lasf_get_plugin_source($from_option, $initial_required, 'lastudio-header-builders', 'version')
		);

        $plugins[] = array(
            'name' 					=> esc_html_x('Elementor', 'admin-view', 'mgana'),
            'slug' 					=> 'elementor',
            'required' 				=> true,
            'version'				=> '3.11.5'
        );

		$plugins[] = array(
			'name'     				=> esc_html_x('WooCommerce', 'admin-view', 'mgana'),
			'slug'     				=> 'woocommerce',
			'version'				=> '7.5.0',
			'required' 				=> false
		);
        
        $plugins[] = array(
			'name'     				=> esc_html_x('Mgana Package Demo Data', 'admin-view', 'mgana'),
			'slug'					=> 'mgana-demo-data',
            'source'				=> lasf_get_plugin_source($from_option, $initial_required, 'mgana-demo-data'),
            'required'				=> false,
            'version'				=> lasf_get_plugin_source($from_option, $initial_required, 'mgana-demo-data', 'version')
		);

		$plugins[] = array(
			'name'     				=> esc_html_x('Envato Market', 'admin-view', 'mgana'),
			'slug'     				=> 'envato-market',
			'source'   				=> 'https://envato.github.io/wp-envato-market/dist/envato-market.zip',
			'required' 				=> false,
			'version' 				=> '2.0.7'
		);

		$plugins[] = array(
			'name' 					=> esc_html_x('Contact Form 7', 'admin-view', 'mgana'),
			'slug' 					=> 'contact-form-7',
			'required' 				=> false
		);

		$plugins[] = array(
			'name'					=> esc_html_x('Slider Revolution', 'admin-view', 'mgana'),
			'slug'					=> 'revslider',
            'source'				=> lasf_get_plugin_source($from_option, $initial_required, 'revslider'),
            'required'				=> false,
            'version'				=> lasf_get_plugin_source($from_option, $initial_required, 'revslider', 'version')
		);

		$config = array(
			'id'           				=> 'mgana',
			'default_path' 				=> '',
			'menu'         				=> 'tgmpa-install-plugins',
			'has_notices'  				=> true,
			'dismissable'  				=> true,
			'dismiss_msg'  				=> '',
			'is_automatic' 				=> false,
			'message'      				=> ''
		);

		tgmpa( $plugins, $config );

	}

}
