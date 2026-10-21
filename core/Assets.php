<?php
namespace RoquetteMap\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Leaflet, servi depuis lib/leaflet (aucun CDN).
 * Les assets propres à une fonctionnalité sont enregistrés dans son dossier features/.
 */
final class Assets {

	const LEAFLET_VERSION = '1.9.4';

	public function register_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register' ), 5 );
	}

	public function register(): void {
		wp_register_style( 'roquette-leaflet', self::url( 'lib/leaflet/leaflet.css' ), array(), self::LEAFLET_VERSION );
		wp_register_script( 'roquette-leaflet', self::url( 'lib/leaflet/leaflet.js' ), array(), self::LEAFLET_VERSION, true );
	}

	/**
	 * URL d'un fichier du plugin, ex. Assets::url( 'features/popup/popup.css' ).
	 */
	public static function url( string $path ): string {
		return ROQUETTE_MAP_URL . ltrim( $path, '/' );
	}
}
