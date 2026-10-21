<?php
namespace RoquetteMap\Features\Carte;

use RoquetteMap\Core\Assets;
use RoquetteMap\Core\View;
use RoquetteMap\Features\Fond\Fond;
use RoquetteMap\Features\Pins\Pins;

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode [roquette_map cpt="evenements,restaurants" fond="positron" pin="pin-vert.png" evenements="pin-rouge.png" posts="12,34"]
 * Chaque CPT listé peut avoir son propre paramètre (même nom que le CPT) : liste de réglages séparés par des virgules.
 * Assemble la carte : fond (features/fond) + pins (features/pins).
 */
final class Carte {

	const TAG = 'roquette_map';

	/** @var View */
	private $view;

	/** @var Fond */
	private $fond;

	/** @var Pins */
	private $pins;

	/** @var int */
	private $count = 0;

	public function __construct( View $view, Fond $fond, Pins $pins ) {
		$this->view = $view;
		$this->fond = $fond;
		$this->pins = $pins;
	}

	public function register_hooks(): void {
		add_shortcode( self::TAG, array( $this, 'render' ) );
	}

	/**
	 * @param array|string $atts
	 */
	public function render( $atts ): string {
		$raw  = (array) $atts;
		$atts = shortcode_atts(
			array(
				'cpt'   => '',
				'fond'  => '',
				'pin'   => '',
				'posts' => '',
			),
			$atts,
			self::TAG
		);

		wp_enqueue_style( 'roquette-map', Assets::url( 'features/carte/carte.css' ), array( 'roquette-leaflet', 'roquette-map-popup' ), ROQUETTE_MAP_VERSION );
		wp_enqueue_script( 'roquette-map', Assets::url( 'features/carte/carte.js' ), array( 'roquette-leaflet', 'roquette-map-popup' ), ROQUETTE_MAP_VERSION, true );

		// Paramètre propre à chaque CPT, ex. evenements="pin-rouge.png".
		$settings = array();
		foreach ( array_map( 'sanitize_key', explode( ',', $atts['cpt'] ) ) as $type ) {
			if ( isset( $raw[ $type ] ) ) {
				$settings[ $type ] = (string) $raw[ $type ];
			}
		}

		$pins = $this->pins->get( $atts['cpt'], $atts['pin'], $settings, $atts['posts'] );

		return $this->view->render(
			'carte/conteneur',
			array(
				'id'     => 'roquette-map-' . ( ++$this->count ),
				'config' => array(
					'tile'  => $this->fond->get( $atts['fond'] ),
					'icons' => $pins['icons'],
					'pins'  => $pins['pins'],
				),
			)
		);
	}
}
