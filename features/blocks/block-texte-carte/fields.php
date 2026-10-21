<?php
defined( 'ABSPATH' ) || exit;

acf_add_local_field_group(
	array(
		'key'      => 'group_roquette_block_texte_carte',
		'title'    => 'Texte + carte',
		'fields'   => array(
			array(
				'key'   => 'field_roquette_block_texte_carte_titre',
				'label' => 'Titre',
				'name'  => 'titre',
				'type'  => 'text',
			),
			array(
				'key'       => 'field_roquette_block_texte_carte_description',
				'label'     => 'Description',
				'name'      => 'description',
				'type'      => 'textarea',
				'rows'      => 4,
				'new_lines' => '',
			),
			array(
				'key'          => 'field_roquette_block_texte_carte_liens',
				'label'        => 'Liens',
				'name'         => 'liens',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => 'Ajouter un lien',
				'sub_fields'   => array(
					array(
						'key'           => 'field_roquette_block_texte_carte_lien',
						'label'         => 'Lien',
						'name'          => 'lien',
						'type'          => 'link',
						'return_format' => 'array',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/block-texte-carte',
				),
			),
		),
	)
);
