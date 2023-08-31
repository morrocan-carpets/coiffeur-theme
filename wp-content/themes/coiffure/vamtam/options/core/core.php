<?php

/**
 * Controls attached to core sections
 *
 * @package vamtam/coiffure
 */


return array(
	array(
		'label'     => esc_html__( 'Header Logo Type', 'coiffure' ),
		'id'        => 'header-logo-type',
		'type'      => 'switch',
		'transport' => 'postMessage',
		'section'   => 'title_tagline',
		'choices'   => array(
			'image'      => esc_html__( 'Image', 'coiffure' ),
			'site-title' => esc_html__( 'Site Title', 'coiffure' ),
		),
		'priority' => 8,
	),

	array(
		'label'     => esc_html__( 'Single Product Image Zoom', 'coiffure' ),
		'id'        => 'wc-product-gallery-zoom',
		'type'      => 'switch',
		'transport' => 'postMessage',
		'section'   => 'woocommerce_product_images',
		'choices'   => array(
			'enabled'  => esc_html__( 'Enabled', 'coiffure' ),
			'disabled' => esc_html__( 'Disabled', 'coiffure' ),
		),
		// 'active_callback' => 'vamtam_extra_features',
	),
);


