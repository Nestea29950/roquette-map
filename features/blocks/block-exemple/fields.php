<?php
defined( 'ABSPATH' ) || exit;

acf_add_local_field_group(
	array(
		'key'      => 'group_roquette_block_exemple',
		'title'    => 'Bloc exemple',
		'fields'   => array(
			array(
				'key'   => 'field_roquette_block_exemple_titre',
				'label' => 'Titre',
				'name'  => 'titre',
				'type'  => 'text',
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/block-exemple',
				),
			),
		),
	)
);
