<?php
namespace RoquetteMap\Features\Popup;

use RoquetteMap\Core\Assets;

defined( 'ABSPATH' ) || exit;

/**
 * Popup d'un pin, rendue avec Timber.
 *   popup.twig        structure (à modifier)
 *   popup.css         style (à modifier)
 *   popup.js          comportement (options Leaflet, fermeture)
 *   popup-reset.css   neutralise l'habillage Leaflet (ne pas toucher)
 */
final class Popup {

	const TEMPLATE = '@roquette-map/popup.twig';

	public function register_hooks(): void {
		add_filter( 'timber/locations', array( $this, 'add_location' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 5 );
	}

	/**
	 * Déclare ce dossier sous le namespace Twig @roquette-map.
	 */
	public function add_location( array $locations ): array {
		$locations['roquette-map'][] = __DIR__;
		return $locations;
	}

	/**
	 * Chargés automatiquement en dépendance de la carte.
	 */
	public function register_assets(): void {
		wp_register_style( 'roquette-map-popup-reset', Assets::url( 'features/popup/popup-reset.css' ), array( 'roquette-leaflet' ), ROQUETTE_MAP_VERSION );
		wp_register_style( 'roquette-map-popup', Assets::url( 'features/popup/popup.css' ), array( 'roquette-map-popup-reset' ), ROQUETTE_MAP_VERSION );
		wp_register_script( 'roquette-map-popup', Assets::url( 'features/popup/popup.js' ), array( 'roquette-leaflet' ), ROQUETTE_MAP_VERSION, true );
	}

	public function render( \WP_Post $post ): string {
		if ( ! class_exists( '\Timber\Timber' ) ) {
			return sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $post ) ), esc_html( get_the_title( $post ) ) );
		}

		return (string) \Timber\Timber::compile(
			self::TEMPLATE,
			array( 'post' => \Timber\Timber::get_post( $post ) )
		);
	}
}
