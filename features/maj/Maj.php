<?php
namespace RoquetteMap\Features\Maj;

defined( 'ABSPATH' ) || exit;

/**
 * Mises à jour depuis les releases GitHub (en-tête « Update URI » de roquette-map.php).
 * La release doit avoir un zip joint contenant le dossier roquette-map/.
 */
final class Maj {

	const REPO = 'Nestea29950/roquette-map';

	/**
	 * Fichiers propres à chaque site : la mise à jour ne les écrase pas (ajoutés ou modifiés).
	 */
	const KEEP = array(
		'features/pins/images/*',
		'features/popup/*.twig',
		'features/popup/popup.css',
	);

	public function register_hooks(): void {
		add_filter( 'update_plugins_github.com', array( $this, 'check' ), 10, 3 );
		add_filter( 'plugins_api', array( $this, 'info' ), 10, 3 );
		add_filter( 'upgrader_pre_install', array( $this, 'backup' ), 10, 2 );
		add_filter( 'upgrader_post_install', array( $this, 'restore' ), 10, 3 );
	}

	/**
	 * Avant la mise à jour : copie les fichiers KEEP dans un dossier temporaire.
	 *
	 * @param bool|\WP_Error $response
	 * @return bool|\WP_Error
	 */
	public function backup( $response, array $hook_extra ) {
		if ( ! $this->is_this_plugin( $hook_extra ) || is_wp_error( $response ) ) {
			return $response;
		}

		if ( is_dir( $this->backup_dir() ) ) {
			$this->delete_dir( $this->backup_dir() ); // Reste d'une mise à jour interrompue.
		}
		$this->copy_kept( ROQUETTE_MAP_PATH, $this->backup_dir() );
		return $response;
	}

	/**
	 * Après la mise à jour : remet les fichiers sauvegardés par-dessus la nouvelle version.
	 *
	 * @param bool|\WP_Error $response
	 * @return bool|\WP_Error
	 */
	public function restore( $response, array $hook_extra, array $result ) {
		if ( ! $this->is_this_plugin( $hook_extra ) || ! is_dir( $this->backup_dir() ) ) {
			return $response;
		}

		// Mise à jour ratée : WordPress remet l'ancienne version, la sauvegarde ne sert plus.
		if ( ! is_wp_error( $response ) && ! empty( $result['destination'] ) ) {
			$this->copy_kept( $this->backup_dir(), trailingslashit( $result['destination'] ) );
		}

		$this->delete_dir( $this->backup_dir() );
		return $response;
	}

	private function is_this_plugin( array $hook_extra ): bool {
		return ( $hook_extra['plugin'] ?? '' ) === plugin_basename( ROQUETTE_MAP_FILE );
	}

	private function backup_dir(): string {
		return WP_CONTENT_DIR . '/upgrade/roquette-map-keep/'; // Dossier de travail des mises à jour de WordPress.
	}

	private function copy_kept( string $from, string $to ): void {
		foreach ( self::KEEP as $pattern ) {
			foreach ( glob( $from . $pattern ) ?: array() as $file ) {
				if ( is_file( $file ) ) {
					$target = $to . substr( $file, strlen( $from ) );
					wp_mkdir_p( dirname( $target ) );
					copy( $file, $target );
				}
			}
		}
	}

	private function delete_dir( string $dir ): void {
		$it = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $dir, \FilesystemIterator::SKIP_DOTS ), \RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $it as $file ) {
			$file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
		}
		rmdir( $dir );
	}

	/**
	 * @param array|false $update
	 * @return array|false
	 */
	public function check( $update, array $plugin_data, string $plugin_file ) {
		if ( plugin_basename( ROQUETTE_MAP_FILE ) !== $plugin_file ) {
			return $update;
		}

		$release = $this->release();
		if ( ! $release ) {
			return $update;
		}

		return array(
			'slug'    => 'roquette-map',
			'version' => ltrim( $release['tag_name'], 'v' ),
			'url'     => $release['html_url'],
			'package' => $release['assets'][0]['browser_download_url'],
		);
	}

	/**
	 * Fenêtre « Voir les détails » : sans ce filtre, WordPress interroge wordpress.org, où le plugin n'existe pas.
	 *
	 * @param false|object|array $result
	 * @param object             $args
	 * @return false|object|\WP_Error
	 */
	public function info( $result, string $action, $args ) {
		if ( 'plugin_information' !== $action || 'roquette-map' !== ( $args->slug ?? '' ) ) {
			return $result;
		}

		$release = $this->release();
		if ( ! $release ) {
			return new \WP_Error( 'roquette_map_github', __( 'Impossible de récupérer la dernière version sur GitHub.', 'roquette-map' ) );
		}

		return (object) array(
			'name'          => __( 'Roquette Map', 'roquette-map' ),
			'slug'          => 'roquette-map',
			'version'       => ltrim( $release['tag_name'], 'v' ),
			'homepage'      => $release['html_url'],
			'download_link' => $release['assets'][0]['browser_download_url'],
			'last_updated'  => $release['published_at'] ?? '',
			'sections'      => array(
				'changelog' => $release['body'] ? nl2br( esc_html( $release['body'] ) ) : esc_html__( 'Pas de notes pour cette version.', 'roquette-map' ),
			),
		);
	}

	/**
	 * Dernière release GitHub ayant un zip joint, ou null.
	 */
	private function release(): ?array {
		$res = wp_remote_get( 'https://api.github.com/repos/' . self::REPO . '/releases/latest' );
		if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
			return null;
		}

		$release = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( empty( $release['tag_name'] ) || empty( $release['assets'][0]['browser_download_url'] ) ) {
			return null;
		}

		return $release;
	}
}
