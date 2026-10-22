<?php
namespace RoquetteMap\Core;

use RoquetteMap\Features\Admin\Admin;
use RoquetteMap\Features\Bloc\Bloc;
use RoquetteMap\Features\Carte\Carte;
use RoquetteMap\Features\Fond\Fond;
use RoquetteMap\Features\Localisation\Localisation;
use RoquetteMap\Features\Maj\Maj;
use RoquetteMap\Features\Pins\Pins;
use RoquetteMap\Features\Popup\Popup;

defined( 'ABSPATH' ) || exit;

/**
 * Point d'entrée : instancie chaque fonctionnalité (features/) et branche ses hooks.
 */
final class Plugin {

	public static function boot(): void {
		load_plugin_textdomain( 'roquette-map', false, dirname( plugin_basename( ROQUETTE_MAP_FILE ) ) . '/languages' );

		$view         = new View( ROQUETTE_MAP_PATH . 'features' );
		$fond         = new Fond();
		$popup        = new Popup();
		$pins         = new Pins( $popup );
		$localisation = new Localisation();

		( new Assets() )->register_hooks();
		$popup->register_hooks();
		$localisation->register_hooks();
		( new Maj() )->register_hooks();
		$carte = new Carte( $view, $fond, $pins );
		$carte->register_hooks();
		( new Bloc( $carte, $fond, $pins ) )->register_hooks();

		if ( is_admin() ) {
			( new Admin( $view, $fond, $pins, $localisation ) )->register_hooks();
		}
	}
}
