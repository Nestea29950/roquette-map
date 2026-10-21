<?php
namespace RoquetteMap\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Moteur de vues : rend les vues PHP de features/.
 * Dans une vue, $view permet d'inclure une autre vue.
 */
final class View {

	/** @var string */
	private $base_path;

	public function __construct( string $base_path ) {
		$this->base_path = rtrim( $base_path, '/\\' );
	}

	/**
	 * Retourne le HTML d'une vue.
	 *
	 * @param string $name    Chemin relatif sans extension, ex. "admin/layout" => features/admin/layout.php.
	 * @param array  $context Variables exposées à la vue.
	 */
	public function render( string $name, array $context = array() ): string {
		$file = $this->base_path . '/' . trim( $name, '/' ) . '.php';

		if ( ! is_readable( $file ) ) {
			return '';
		}

		$context['view'] = $this;

		ob_start();
		( static function ( $__file, $__context ) {
			extract( $__context, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract
			include $__file;
		} )( $file, $context );

		return (string) ob_get_clean();
	}

	/**
	 * Affiche directement une vue.
	 */
	public function display( string $name, array $context = array() ): void {
		echo $this->render( $name, $context ); // phpcs:ignore WordPress.Security.EscapeOutput -- les vues échappent elles-mêmes.
	}
}
