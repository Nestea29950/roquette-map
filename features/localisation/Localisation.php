<?php
namespace RoquetteMap\Features\Localisation;

defined( 'ABSPATH' ) || exit;

/**
 * Champs ACF « Localisation » (adresse, code postal, ville, lat, lng) sur les CPT publics cochés dans l'admin (tous tant que rien n'est enregistré).
 * Les noms de champs = les métas lues par features/pins (lat, lng) : un post n'a un pin que s'il est localisé.
 * À l'enregistrement, si lat/lng sont vides, l'adresse est géocodée (Nominatim / OpenStreetMap) :
 * adresse complète, sinon code postal + ville, sinon ville seule.
 */
final class Localisation {

	const OPTION = 'roquette_map_localisation';

	public function register_hooks(): void {
		// Après l'enregistrement des CPT (init, 10).
		add_action( 'init', array( $this, 'add_fields' ), 20 );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'acf/save_post', array( $this, 'geocode' ), 20 );
	}

	/**
	 * Remplit lat/lng depuis l'adresse quand ils sont vides.
	 *
	 * @param int|string $post_id
	 */
	public function geocode( $post_id ): void {
		if ( ! is_numeric( $post_id ) || ! in_array( get_post_type( (int) $post_id ), $this->enabled(), true ) ) {
			return;
		}
		if ( '' !== (string) get_field( 'lat', $post_id ) && '' !== (string) get_field( 'lng', $post_id ) ) {
			return;
		}

		$adresse = trim( (string) get_field( 'adresse', $post_id ) );
		$cp      = trim( (string) get_field( 'code_postal', $post_id ) );
		$ville   = trim( (string) get_field( 'ville', $post_id ) );

		$queries = array_unique( array_filter( array( trim( "$adresse $cp $ville" ), trim( "$cp $ville" ), $ville ) ) );

		foreach ( $queries as $query ) {
			$point = $this->search( $query );
			if ( $point ) {
				update_field( 'lat', $point['lat'], $post_id );
				update_field( 'lng', $point['lon'], $post_id );
				return;
			}
		}
	}

	/**
	 * @return array|null { lat, lon } du premier résultat Nominatim.
	 */
	private function search( string $query ): ?array {
		$response = wp_remote_get(
			add_query_arg(
				array(
					'q'      => $query,
					'format' => 'json',
					'limit'  => 1,
				),
				'https://nominatim.openstreetmap.org/search'
			),
			array(
				'timeout' => 8,
				// Nominatim refuse les requêtes sans User-Agent identifiable.
				'headers' => array( 'User-Agent' => 'roquette-map (' . home_url() . ')' ),
			)
		);

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		return isset( $data[0]['lat'], $data[0]['lon'] ) ? $data[0] : null;
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
						'key'          => 'field_roquette_map_adresse',
						'name'         => 'adresse',
						'label'        => __( 'Adresse', 'roquette-map' ),
						'type'         => 'text',
						'instructions' => __( 'Ex. 12 rue de la Roquette. Lat/lng vides = remplis automatiquement à l\'enregistrement.', 'roquette-map' ),
					),
					array(
						'key'          => 'field_roquette_map_code_postal',
						'name'         => 'code_postal',
						'label'        => __( 'Code postal', 'roquette-map' ),
						'type'         => 'text',
						'instructions' => __( 'Ex. 75011', 'roquette-map' ),
					),
					array(
						'key'          => 'field_roquette_map_ville',
						'name'         => 'ville',
						'label'        => __( 'Ville', 'roquette-map' ),
						'type'         => 'text',
						'instructions' => __( 'Ex. Paris', 'roquette-map' ),
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
