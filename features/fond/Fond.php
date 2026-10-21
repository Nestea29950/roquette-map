<?php
namespace RoquetteMap\Features\Fond;

defined( 'ABSPATH' ) || exit;

/**
 * Fonds de carte (paramètre `fond` du shortcode). La liste est dans fonds.php.
 */
final class Fond {

	/**
	 * Tous les fonds définis dans fonds.php.
	 */
	public function all(): array {
		return require __DIR__ . '/fonds.php';
	}

	/**
	 * Fond demandé, ou celui marqué par défaut, au format attendu par carte.js.
	 */
	public function get( string $key ): array {
		$tiles = $this->all();

		if ( ! isset( $tiles[ $key ] ) ) {
			$defaults = wp_list_filter( $tiles, array( 'default' => true ) );
			$key      = $defaults ? array_key_first( $defaults ) : array_key_first( $tiles );
		}

		return array(
			'url'         => $tiles[ $key ]['url'],
			'attribution' => $tiles[ $key ]['attribution'],
			'maxZoom'     => $tiles[ $key ]['max_zoom'],
		);
	}
}
