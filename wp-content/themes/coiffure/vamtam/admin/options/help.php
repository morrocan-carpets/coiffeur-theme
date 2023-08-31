<?php
return array(
	'name' => esc_html__( 'Help', 'coiffure' ),
	'auto' => true,
	'config' => array(

		array(
			'name' => esc_html__( 'Help', 'coiffure' ),
			'type' => 'title',
			'desc' => '',
		),

		array(
			'name' => esc_html__( 'Help', 'coiffure' ),
			'type' => 'start',
			'nosave' => true,
		),
//----
		array(
			'type' => 'docs',
		),

			array(
				'type' => 'end',
			),
	),
);
