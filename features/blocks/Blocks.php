<?php
namespace RoquetteMap\Features\Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Blocs ACF (ACF PRO) communs aux sites qui utilisent le plugin.
 * Un dossier par bloc : features/blocks/{slug}/
 *   block.json     name "acf/{slug}", acf.renderCallback "RoquetteMap\\Features\\Blocks\\Blocks::render"
 *   fields.php     champs du bloc (acf_add_local_field_group, location block == acf/{slug})
 *   {slug}.twig    rendu Timber : attributes, fields, is_preview (comme dans le thème roquette-twig)
 */
final class Blocks {

	public function register_hooks(): void {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'acf/include_fields', array( $this, 'include_fields' ) );
		add_filter( 'timber/locations', array( $this, 'add_location' ) );
	}

	public function register(): void {
		// Blocs ACF = ACF PRO uniquement.
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}

		foreach ( glob( __DIR__ . '/*/block.json' ) as $file ) {
			register_block_type( dirname( $file ) );
		}
	}

	public function include_fields(): void {
		foreach ( glob( __DIR__ . '/*/fields.php' ) as $file ) {
			require $file;
		}
	}

	/**
	 * Déclare ce dossier sous le namespace Twig @roquette-blocks.
	 */
	public function add_location( array $locations ): array {
		$locations['roquette-blocks'][] = __DIR__;
		return $locations;
	}

	/**
	 * Rendu d'un bloc : {slug}/{slug}.twig.
	 */
	public static function render( array $block, $content = '', $is_preview = false ): void {
		if ( ! class_exists( '\Timber\Timber' ) ) {
			return;
		}

		$slug = str_replace( 'acf/', '', $block['name'] );

		$context               = \Timber\Timber::context();
		$context['attributes'] = $block;
		$context['fields']     = get_fields();
		$context['is_preview'] = $is_preview;

		\Timber\Timber::render( '@roquette-blocks/' . $slug . '/' . $slug . '.twig', $context );
	}
}
