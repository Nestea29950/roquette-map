<?php
namespace RoquetteMap\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader minimal : les dossiers sont en minuscules, le fichier porte le nom de la classe.
 *   RoquetteMap\Core\View             => core/View.php
 *   RoquetteMap\Features\Popup\Popup  => features/popup/Popup.php
 */
final class Autoloader {

	const PREFIX = 'RoquetteMap\\';

	public static function register(): void {
		spl_autoload_register( array( __CLASS__, 'load' ) );
	}

	public static function load( string $class ): void {
		if ( strpos( $class, self::PREFIX ) !== 0 ) {
			return;
		}

		$parts = explode( '\\', substr( $class, strlen( self::PREFIX ) ) );
		$name  = array_pop( $parts );
		$dir   = $parts ? strtolower( implode( '/', $parts ) ) . '/' : '';
		$file  = ROQUETTE_MAP_PATH . $dir . $name . '.php';

		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
}
