<?php
namespace RoquetteMap\Features\Pins;

use RoquetteMap\Core\Assets;
use RoquetteMap\Features\Popup\Popup;

defined( 'ABSPATH' ) || exit;

/**
 * Pins des CPT (paramètres `cpt` et `pin` du shortcode).
 * Un pin par publication ayant les métas `lat` et `lng`.
 * Images des pins : dossier images/ (default.png si rien n'est précisé).
 * Priorité : terme du post ("concert:pin-bleu.png" dans le paramètre du CPT) > image du CPT > `pin` > default.png.
 */
final class Pins {

	const DEFAULT_IMAGE = 'default.png';

	/** @var Popup */
	private $popup;

	public function __construct( Popup $popup ) {
		$this->popup = $popup;
	}

	/**
	 * @param string $cpt      Slugs de post types séparés par des virgules.
	 * @param string $pin      Image des pins de toute la carte, ex. "pin-vert.png".
	 * @param array  $settings Paramètre de chaque CPT, ex. array( 'evenements' => 'pin-rouge.png,concert:pin-bleu.png' ).
	 * @param string $posts    IDs séparés par des virgules : seuls ces posts sont affichés (vide = tous).
	 * @return array { pins: array, icons: array } au format attendu par carte.js.
	 */
	public function get( string $cpt, string $pin = '', array $settings = array(), string $posts = '' ): array {
		$types = array_filter( array_map( 'sanitize_key', explode( ',', $cpt ) ), 'post_type_exists' );

		if ( ! $types ) {
			return array(
				'pins'  => array(),
				'icons' => array(),
			);
		}

		$images = $this->images_by_type( $pin, $settings );
		$terms  = $this->images_by_term( $settings );

		$ids   = array_filter( array_map( 'absint', explode( ',', $posts ) ) );
		$posts = get_posts(
			array(
				'post_type'      => array_values( $types ),
				'post__in'       => $ids,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array( 'key' => 'lat', 'compare' => 'EXISTS' ),
					array( 'key' => 'lng', 'compare' => 'EXISTS' ),
				),
			)
		);

		$pins  = array();
		$icons = array();

		foreach ( $posts as $post ) {
			$image = $this->term_image( $post, $terms[ $post->post_type ] ?? array() )
				?? $images[ $post->post_type ]
				?? $images[''];

			if ( ! isset( $icons[ $image ] ) ) {
				$icons[ $image ] = $this->icon( $image );
			}

			$pins[] = array(
				'lat'   => (float) get_post_meta( $post->ID, 'lat', true ),
				'lng'   => (float) get_post_meta( $post->ID, 'lng', true ),
				'icon'  => $image,
				'popup' => $this->popup->render( $post ),
			);
		}

		return array(
			'pins'  => $pins,
			'icons' => $icons,
		);
	}

	/**
	 * Images disponibles dans images/ (pour la page admin).
	 *
	 * @return string[] Noms de fichiers.
	 */
	public function images(): array {
		$files = glob( __DIR__ . '/images/*.{png,jpg,jpeg,gif,webp,svg}', GLOB_BRACE );
		return array_map( 'basename', $files ? $files : array() );
	}

	/**
	 * Image de chaque CPT : celle de son paramètre, sinon `pin`, sinon default.png.
	 * La clé '' est l'image des CPT sans paramètre.
	 */
	private function images_by_type( string $pin, array $settings ): array {
		$images = array( '' => $this->find_image( $pin ) ?? self::DEFAULT_IMAGE );

		foreach ( $settings as $type => $value ) {
			$image = $this->find_image( $value );
			if ( null !== $image ) {
				$images[ $type ] = $image;
			}
		}

		return $images;
	}

	/**
	 * Images par terme de chaque CPT : éléments "slug-du-terme:image.png" de son paramètre.
	 *
	 * @return array array( 'evenements' => array( 'concert' => 'pin-bleu.png' ) ), dans l'ordre du paramètre.
	 */
	private function images_by_term( array $settings ): array {
		$terms = array();

		foreach ( $settings as $type => $value ) {
			foreach ( array_map( 'trim', explode( ',', $value ) ) as $item ) {
				if ( false === strpos( $item, ':' ) ) {
					continue;
				}
				list( $slug, $file ) = array_map( 'trim', explode( ':', $item, 2 ) );
				$image = $this->find_image( $file );
				if ( '' !== $slug && null !== $image ) {
					$terms[ $type ][ sanitize_title( $slug ) ] = $image;
				}
			}
		}

		return $terms;
	}

	/**
	 * Image du premier terme (ordre du paramètre) que porte le post, toutes taxonomies confondues.
	 */
	private function term_image( \WP_Post $post, array $images ): ?string {
		if ( ! $images ) {
			return null;
		}

		$slugs = array();
		foreach ( get_object_taxonomies( $post ) as $taxonomy ) {
			$post_terms = get_the_terms( $post, $taxonomy );
			if ( is_array( $post_terms ) ) {
				$slugs = array_merge( $slugs, wp_list_pluck( $post_terms, 'slug' ) );
			}
		}

		foreach ( $images as $slug => $image ) {
			if ( in_array( (string) $slug, $slugs, true ) ) {
				return $image;
			}
		}
		return null;
	}

	/**
	 * Premier élément de la liste "pin-rouge.png,autre,option" qui est une image présente dans images/.
	 */
	private function find_image( string $list ): ?string {
		foreach ( array_map( 'trim', explode( ',', $list ) ) as $item ) {
			if ( false !== strpos( $item, ':' ) ) {
				continue; // "terme:image.png" = image d'un terme, voir images_by_term().
			}
			$file = sanitize_file_name( $item );
			if ( preg_match( '/\.(png|jpe?g|gif|webp|svg)$/i', $file ) && is_file( __DIR__ . '/images/' . $file ) ) {
				return $file;
			}
		}
		return null;
	}

	/**
	 * Icône Leaflet : la pointe du pin (bas, centre) est sur la coordonnée.
	 */
	private function icon( string $image ): array {
		$size = @getimagesize( __DIR__ . '/images/' . $image ); // phpcs:ignore WordPress.PHP.NoSilencedErrors -- SVG : pas de taille lisible.
		$w    = $size ? $size[0] : 32;
		$h    = $size ? $size[1] : 32;

		return array(
			'iconUrl'     => Assets::url( 'features/pins/images/' . $image ),
			'iconSize'    => array( $w, $h ),
			'iconAnchor'  => array( (int) round( $w / 2 ), $h ),
			'popupAnchor' => array( 0, -$h ),
		);
	}
}
