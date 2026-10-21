<?php
namespace RoquetteMap\Features\Bloc;

use RoquetteMap\Core\Assets;
use RoquetteMap\Features\Carte\Carte;
use RoquetteMap\Features\Fond\Fond;
use RoquetteMap\Features\Pins\Pins;

defined( 'ABSPATH' ) || exit;

/**
 * Bloc Gutenberg « Roquette Map » : les réglages du shortcode dans la colonne de droite de l'éditeur.
 *   block.json  attributs (cpt, fond, pin, pins = image par CPT, termPins = image par terme de chaque CPT, posts = IDs choisis par CPT)
 *   editor.js   panneau de réglages (sans build, globals wp.*)
 * Rendu côté serveur par Carte::render(), comme le shortcode.
 */
final class Bloc {

	/** @var Carte */
	private $carte;

	/** @var Fond */
	private $fond;

	/** @var Pins */
	private $pins;

	public function __construct( Carte $carte, Fond $fond, Pins $pins ) {
		$this->carte = $carte;
		$this->fond  = $fond;
		$this->pins  = $pins;
	}

	public function register_hooks(): void {
		// Après l'enregistrement des CPT (init, 10).
		add_action( 'init', array( $this, 'register' ), 20 );
	}

	public function register(): void {
		wp_register_script(
			'roquette-map-bloc',
			Assets::url( 'features/bloc/editor.js' ),
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-data', 'wp-core-data', 'wp-i18n' ),
			ROQUETTE_MAP_VERSION,
			true
		);

		$types = array();
		foreach ( get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' ) as $slug => $type ) {
			$taxonomies = array();
			foreach ( get_object_taxonomies( $slug, 'objects' ) as $taxonomy ) {
				if ( $taxonomy->show_in_rest ) {
					$taxonomies[] = array(
						'slug'  => $taxonomy->name,
						'label' => $taxonomy->labels->name,
					);
				}
			}

			$types[] = array(
				'slug'       => $slug,
				'label'      => $type->labels->name,
				'taxonomies' => $taxonomies,
			);
		}

		$fonds = array();
		foreach ( $this->fond->all() as $key => $tile ) {
			$fonds[] = array(
				'value' => $key,
				'label' => $tile['label'],
			);
		}

		wp_add_inline_script(
			'roquette-map-bloc',
			'var RoquetteMapBloc = ' . wp_json_encode(
				array(
					'types'  => $types,
					'fonds'  => $fonds,
					'images' => $this->pins->images(),
				)
			) . ';',
			'before'
		);

		register_block_type( __DIR__, array( 'render_callback' => array( $this, 'render' ) ) );
	}

	public function render( array $attributes ): string {
		$types = array_filter( array_map( 'sanitize_key', (array) $attributes['cpt'] ) );
		$atts  = array(
			'cpt'  => implode( ',', $types ),
			'fond' => (string) $attributes['fond'],
			'pin'  => (string) $attributes['pin'],
		);

		// Image par CPT et par terme : même format que le paramètre du même nom dans le shortcode ("pin-rouge.png,concert:pin-bleu.png").
		foreach ( $types as $type ) {
			$setting = array();
			if ( ! empty( $attributes['pins'][ $type ] ) ) {
				$setting[] = (string) $attributes['pins'][ $type ];
			}
			foreach ( (array) ( $attributes['termPins'][ $type ] ?? array() ) as $slug => $image ) {
				if ( '' !== (string) $image ) {
					$setting[] = $slug . ':' . $image;
				}
			}
			if ( $setting ) {
				$atts[ $type ] = implode( ',', $setting );
			}
		}

		// Posts choisis par CPT. Un CPT sans choix garde tous ses posts.
		$chosen = array_filter( array_intersect_key( (array) $attributes['posts'], array_flip( $types ) ) );
		if ( $chosen ) {
			$ids = array();
			foreach ( $types as $type ) {
				$ids = array_merge(
					$ids,
					$chosen[ $type ] ?? get_posts(
						array(
							'post_type'      => $type,
							'post_status'    => 'publish',
							'posts_per_page' => -1,
							'fields'         => 'ids',
						)
					)
				);
			}
			$atts['posts'] = implode( ',', array_map( 'absint', $ids ) );
		}

		return $this->carte->render( $atts );
	}
}
