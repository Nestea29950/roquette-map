<?php
/**
 * Plugin Name:       Roquette Map
 * Description:       Cartes Leaflet embarquées en local (sans CDN).
 * Version:           0.2
 * Author:            Roquette
 * Text Domain:       roquette-map
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Update URI:        https://github.com/Nestea29950/roquette-map
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ROQUETTE_MAP_VERSION', '0.2' );
define( 'ROQUETTE_MAP_FILE', __FILE__ );
define( 'ROQUETTE_MAP_PATH', plugin_dir_path( __FILE__ ) );
define( 'ROQUETTE_MAP_URL', plugin_dir_url( __FILE__ ) );

require_once ROQUETTE_MAP_PATH . 'core/Autoloader.php';
\RoquetteMap\Core\Autoloader::register();

add_action( 'plugins_loaded', array( \RoquetteMap\Core\Plugin::class, 'boot' ) );
