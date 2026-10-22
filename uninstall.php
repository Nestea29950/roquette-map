<?php
/**
 * Nettoyage à la suppression du plugin.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'roquette_map_localisation' );
