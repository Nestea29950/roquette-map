<?php
namespace RoquetteMap\Features\Admin;

use RoquetteMap\Core\Assets;
use RoquetteMap\Core\View;
use RoquetteMap\Features\Fond\Fond;
use RoquetteMap\Features\Pins\Pins;

defined( 'ABSPATH' ) || exit;

/**
 * Page admin « Roquette Map » : documentation du shortcode.
 *   layout.php     squelette (style iOS 17)
 *   dashboard.php  contenu de la page
 *   admin.css      style
 *   admin.js       menu déroulant des pins (aperçu + code)
 */
final class Admin {

	const SLUG = 'roquette-map';

	/** @var View */
	private $view;

	/** @var Fond */
	private $fond;

	/** @var Pins */
	private $pins;

	/** @var string */
	private $hook_suffix = '';

	public function __construct( View $view, Fond $fond, Pins $pins ) {
		$this->view = $view;
		$this->fond = $fond;
		$this->pins = $pins;
	}

	public function register_hooks(): void {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
	}

	public function add_menu(): void {
		$this->hook_suffix = (string) add_menu_page(
			__( 'Roquette Map', 'roquette-map' ),
			__( 'Roquette Map', 'roquette-map' ),
			'manage_options',
			self::SLUG,
			array( $this, 'render_page' ),
			'dashicons-location-alt',
			80
		);
	}

	public function enqueue( string $hook ): void {
		if ( $hook === $this->hook_suffix ) {
			wp_enqueue_style( 'roquette-map-admin', Assets::url( 'features/admin/admin.css' ), array(), ROQUETTE_MAP_VERSION );
			wp_enqueue_script( 'roquette-map-admin', Assets::url( 'features/admin/admin.js' ), array(), ROQUETTE_MAP_VERSION, true );
		}
	}

	public function body_class( string $classes ): string {
		$screen = get_current_screen();
		if ( $screen && $screen->id === $this->hook_suffix ) {
			$classes .= ' ios-admin';
		}
		return $classes;
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->view->display(
			'admin/layout',
			array(
				'title'   => __( 'Roquette Map', 'roquette-map' ),
				'page'    => 'admin/dashboard',
				'context' => array(
					'post_types' => $this->post_types(),
					'tiles'      => $this->fond->all(),
					'pin_images' => $this->pins->images(),
				),
			)
		);
	}

	/**
	 * Post types publics utilisables dans le paramètre `cpt`.
	 *
	 * @return \WP_Post_Type[]
	 */
	private function post_types(): array {
		$types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $types['attachment'] );
		return $types;
	}
}
