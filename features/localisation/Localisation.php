<?php
namespace RoquetteMap\Features\Localisation;

defined( 'ABSPATH' ) || exit;

/**
 * Champs ACF « Localisation » (adresse, lat, lng) sur les CPT publics cochés dans l'admin (tous tant que rien n'est enregistré).
 * Les noms de champs = les métas lues par features/pins (lat, lng) : un post n'a un pin que s'il est localisé.
 */
final class Localisation {

	const OPTION = 'roquette_map_localisation';

	public function register_hooks(): void {
		// Après l'enregistrement des CPT (init, 10).
		add_action( 'init', array( $this, 'add_fields' ), 20 );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
	}

	public function register_setting(): void {
		register_setting(
			'roquette_map',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);
	}

	/**
	 * @param mixed $value
	 * @return string[]
	 */
	public function sanitize( $value ): array {
		return array_values( array_intersect( (array) $value, array_keys( $this->types() ) ) );
	}

	/**
	 * CPT publics non natifs pouvant recevoir les champs.
	 *
	 * @return \WP_Post_Type[]
	 */
	public function types(): array {
		return get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );
	}

	/**
	 * Slugs des CPT cochés.
	 *
	 * @return string[]
	 */
	public function enabled(): array {
		$saved = get_option( self::OPTION, null );
		return null === $saved ? array_keys( $this->types() ) : array_intersect( (array) $saved, array_keys( $this->types() ) );
	}

	public function add_fields(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$location = array();
		foreach ( $this->enabled() as $type ) {
			$location[] = array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $type,
				),
			);
		}

		if ( ! $location ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_roquette_map_localisation',
				'title'    => __( 'Localisation (carte)', 'roquette-map' ),
				'position' => 'side',
				'location' => $location,
				'fields'   => array(
					array(
						'key'   => 'field_roquette_map_adresse',
						'name'  => 'adresse',
						'label' => __( 'Adresse', 'roquette-map' ),
						'type'  => 'text',
					),
					array(
						'key'          => 'field_roquette_map_lat',
						'name'         => 'lat',
						'label'        => __( 'Latitude', 'roquette-map' ),
						'type'         => 'number',
						'min'          => -90,
						'max'          => 90,
						'step'         => 'any',
						'instructions' => __( 'Ex. 48.8566', 'roquette-map' ),
					),
					array(
						'key'          => 'field_roquette_map_lng',
						'name'         => 'lng',
						'label'        => __( 'Longitude', 'roquette-map' ),
						'type'         => 'number',
						'min'          => -180,
						'max'          => 180,
						'step'         => 'any',
						'instructions' => __( 'Ex. 2.3522', 'roquette-map' ),
					),
				),
			)
		);
	}
}
