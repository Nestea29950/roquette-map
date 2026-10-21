<?php
namespace RoquetteMap\Features\Maj;

defined( 'ABSPATH' ) || exit;

/**
 * Mises à jour depuis les releases GitHub (en-tête « Update URI » de roquette-map.php).
 * La release doit avoir un zip joint contenant le dossier roquette-map/.
 */
final class Maj {

	const REPO = 'Nestea29950/roquette-map';

	public function register_hooks(): void {
		add_filter( 'update_plugins_github.com', array( $this, 'check' ), 10, 3 );
	}

	/**
	 * @param array|false $update
	 * @return array|false
	 */
	public function check( $update, array $plugin_data, string $plugin_file ) {
		if ( plugin_basename( ROQUETTE_MAP_FILE ) !== $plugin_file ) {
			return $update;
		}

		$res = wp_remote_get( 'https://api.github.com/repos/' . self::REPO . '/releases/latest' );
		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return $update;
		}

		$release = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $release['tag_name'] ) || empty( $release['assets'][0]['browser_download_url'] ) ) {
			return $update;
		}

		return array(
			'slug'    => 'roquette-map',
			'version' => ltrim( $release['tag_name'], 'v' ),
			'url'     => $release['html_url'],
			'package' => $release['assets'][0]['browser_download_url'],
		);
	}
}
