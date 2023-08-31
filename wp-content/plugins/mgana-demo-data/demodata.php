<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_mgana_get_demo_array($dir_url, $dir_path){

    $demo_items = array(
        'fashion-01' => array(
            'link'      => 'https://mgana.la-studioweb.com/fashion-01/',
            'title'     => 'Fashion 01',
            'data_sample'=> 'fashion-data.json',
            'data_product'=> 'fashion.csv',
            'data_widget'=> 'fashion.json',
            'data_slider'=> 'fashion-01.zip',
            'category'  => array(
                'Demo',
	            'Fashion'
            )
        ),
        'fashion-02' => array(
            'link'      => 'https://mgana.la-studioweb.com/fashion-02/',
            'title'     => 'Fashion 02',
            'data_sample'=> 'fashion-data.json',
            'data_product'=> 'fashion.csv',
            'data_widget'=> 'fashion.json',
            'data_slider'=> 'fashion-02.zip',
            'category'  => array(
                'Demo',
	            'Fashion'
            )
        ),
        'fashion-03' => array(
            'link'      => 'https://mgana.la-studioweb.com/fashion-03/',
            'title'     => 'Fashion 03',
            'data_sample'=> 'fashion-data.json',
            'data_product'=> 'fashion.csv',
            'data_widget'=> 'fashion.json',
            'data_slider'=> 'fashion-03.zip',
            'category'  => array(
                'Demo',
	            'Fashion'
            )
        ),
        'fashion-04' => array(
            'link'      => 'https://mgana.la-studioweb.com/fashion-04/',
            'title'     => 'Fashion 04',
            'data_sample'=> 'fashion-data.json',
            'data_product'=> 'fashion.csv',
            'data_widget'=> 'fashion.json',
            'data_slider'=> 'fashion-04.zip',
            'category'  => array(
                'Demo',
	            'Fashion'
            )
        ),
        'fashion-05' => array(
            'link'      => 'https://mgana.la-studioweb.com/fashion-05/',
            'title'     => 'Fashion 05',
            'data_sample'=> 'fashion-data.json',
            'data_product'=> 'fashion.csv',
            'data_widget'=> 'fashion.json',
            'category'  => array(
                'Demo',
	            'Fashion'
            )
        ),
        'furniture-01' => array(
            'link'      => 'https://mgana.la-studioweb.com/furniture/furniture-01/',
            'title'     => 'Furniture 01',
            'data_sample'=> 'furniture-data.json',
            'data_product'=> 'furniture.csv',
            'data_widget'=> 'furniture.json',
            'data_slider'=> 'furniture-01.zip',
            'category'  => array(
                'Demo',
	            'Furniture'
            )
        ),
        'furniture-02' => array(
            'link'      => 'https://mgana.la-studioweb.com/furniture/furniture-02/',
            'title'     => 'Furniture 02',
            'data_sample'=> 'furniture-data.json',
            'data_product'=> 'furniture.csv',
            'data_widget'=> 'furniture.json',
            'category'  => array(
                'Demo',
	            'Furniture'
            )
        ),
        'furniture-03' => array(
	        'link'      => 'https://mgana.la-studioweb.com/furniture/furniture-03/',
	        'title'     => 'Furniture 03',
	        'data_sample'=> 'furniture-data.json',
	        'data_product'=> 'furniture.csv',
	        'data_widget'=> 'furniture.json',
            'data_slider'=> 'furniture-03.zip',
	        'category'  => array(
		        'Demo',
		        'Furniture'
	        )
        ),
        'furniture-04' => array(
	        'link'      => 'https://mgana.la-studioweb.com/furniture/furniture-04/',
	        'title'     => 'Furniture 04',
	        'data_sample'=> 'furniture-data.json',
	        'data_product'=> 'furniture.csv',
	        'data_widget'=> 'furniture.json',
	        'category'  => array(
		        'Demo',
		        'Furniture'
	        )
        ),
        'pet-01' => array(
            'link'      => 'https://mgana.la-studioweb.com/pet/pet-01/',
            'title'     => 'Pet 01',
            'data_sample'=> 'pet-data.json',
            'data_product'=> 'pet.csv',
            'data_widget'=> 'pet.json',
            'data_slider'=> 'pet-01.zip',
            'category'  => array(
                'Demo',
	            'Pets'
            )
        ),
        'wine-01' => array(
            'link'      => 'https://mgana.la-studioweb.com/wine/wine-01/',
            'title'     => 'Wine 01',
            'data_sample'=> 'wine-data.json',
            'data_product'=> 'wine.csv',
            'data_widget'=> 'wine.json',
            'data_slider'=> 'wine-01.zip',
            'category'  => array(
                'Demo',
	            'Wine'
            )
        ),
        'beauty-01' => array(
            'link'      => 'https://mgana.la-studioweb.com/beauty/beauty-01/',
            'title'     => 'Beauty 01',
            'data_sample'=> 'beauty-data.json',
            'data_product'=> 'beauty.csv',
            'data_widget'=> 'beauty.json',
            'data_slider'=> 'beauty-01.zip',
            'category'  => array(
                'Demo',
	            'Beauty'
            )
        ),
        'jewelry-01' => array(
            'link'      => 'https://mgana.la-studioweb.com/jewelry/jewelry-01/',
            'title'     => 'Jewelry 01',
            'data_sample'=> 'jewelry-data.json',
            'data_product'=> 'jewelry.csv',
            'data_widget'=> 'jewelry.json',
            'data_slider'=> 'jewelry-01.zip',
            'category'  => array(
                'Demo',
	            'Jewelry'
            )
        ),
        'jewelry-02' => array(
	        'link'      => 'https://mgana.la-studioweb.com/jewelry/jewelry-02/',
	        'title'     => 'Jewelry 02',
	        'data_sample'=> 'jewelry-data.json',
	        'data_product'=> 'jewelry.csv',
	        'data_widget'=> 'jewelry.json',
            'data_slider'=> 'jewelry-02.zip',
	        'category'  => array(
		        'Demo',
		        'Jewelry'
	        )
        ),
    );

    $default_image_setting = array(
        'woocommerce_single_image_width' => 1000,
        'woocommerce_thumbnail_image_width' => 1000,
        'woocommerce_thumbnail_cropping' => 'custom',
        'woocommerce_thumbnail_cropping_custom_width' => 10,
        'woocommerce_thumbnail_cropping_custom_height' => 12,
        'thumbnail_size_w' => 520,
        'thumbnail_size_h' => 340,
        'medium_size_w' => 0,
        'medium_size_h' => 0,
        'medium_large_size_w' => 0,
        'medium_large_size_h' => 0,
        'large_size_w' => 0,
        'large_size_h' => 0
    );

    $default_menu = array(
        'main-nav'              => 'Primary Navigation'
    );

    $default_page = array(
        'page_for_posts' 	            => 'Blog',
        'woocommerce_shop_page_id'      => 'Shop Pages',
        'woocommerce_cart_page_id'      => 'Your Cart',
        'woocommerce_checkout_page_id'  => 'Checkout',
        'woocommerce_myaccount_page_id' => 'My Account'
    );

    $slider = $dir_path . 'Slider/';
    $content = $dir_path . 'Content/';
    $product = $dir_path . 'Product/';
    $widget = $dir_path . 'Widget/';
    $setting = $dir_path . 'Setting/';
    $preview = $dir_url;


    $data_return = array();

    foreach ($demo_items as $demo_key => $demo_detail){
	    $value = array();
	    $value['title']             = $demo_detail['title'];
	    $value['category']          = !empty($demo_detail['category']) ? $demo_detail['category'] : array('Demo');
	    $value['demo_preset']       = $demo_key;
	    $value['demo_url']          = $demo_detail['link'];
	    $value['preview']           = !empty($demo_detail['preview']) ? $demo_detail['preview'] : ($preview . $demo_key . '.jpg');
	    $value['option']            = $setting . $demo_key . '.json';
	    $value['content']           = !empty($demo_detail['data_sample']) ? $content . $demo_detail['data_sample'] : $content . 'sample-data.json';
	    $value['product']           = !empty($demo_detail['data_product']) ? $product . $demo_detail['data_product'] : $product . 'sample-product.json';
	    $value['widget']            = !empty($demo_detail['data_widget']) ? $widget . $demo_detail['data_widget'] : $widget . 'widget.json';
	    $value['pages']             = array_merge( $default_page, array( 'page_on_front' => $demo_detail['title'] ));
	    $value['menu-locations']    = array_merge( $default_menu, array( ));
	    $value['other_setting']     = array_merge( $default_image_setting, array( ));
	    if(!empty($demo_detail['data_slider'])){
		    $value['slider'] = $slider . $demo_detail['data_slider'];
	    }
	    $data_return[$demo_key] = $value;
    }


    if(class_exists('LAHB_Helper')){
        $header_presets = LAHB_Helper::getHeaderDefaultData();

        $header_01 = json_decode($header_presets['mgana-header-01']['data'], true);
        $header_02 = json_decode($header_presets['mgana-header-02']['data'], true);
        $header_03 = json_decode($header_presets['mgana-header-03']['data'], true);
        $header_04 = json_decode($header_presets['mgana-header-04']['data'], true);
        $header_05 = json_decode($header_presets['mgana-header-05']['data'], true);
        $header_06 = json_decode($header_presets['mgana-header-06']['data'], true);
        $header_vertical_01 = json_decode($header_presets['mgana-header-vertical-01']['data'], true);
        $header_vertical_02 = json_decode($header_presets['mgana-header-vertical-02']['data'], true);

        $data_return['fashion-01']['other_setting'] = $header_01;
        $data_return['fashion-02']['other_setting'] = $header_01;
        $data_return['fashion-03']['other_setting'] = $header_02;
        $data_return['fashion-04']['other_setting'] = $header_03;
        $data_return['fashion-05']['other_setting'] = $header_01;

        $data_return['furniture-01']['other_setting'] = $header_01;
        $data_return['furniture-02']['other_setting'] = $header_01;
        $data_return['furniture-03']['other_setting'] = $header_01;
        $data_return['furniture-04']['other_setting'] = $header_01;

        $data_return['wine-01']['other_setting'] = $header_01;
        $data_return['pet-01']['other_setting'] = $header_06;
        $data_return['beauty-01']['other_setting'] = $header_04;
        $data_return['beauty-01']['other_setting'] = $header_04;
        $data_return['jewelry-01']['other_setting'] = $header_vertical_01;
        $data_return['jewelry-02']['other_setting'] = $header_05;
    }

    return $data_return;
}