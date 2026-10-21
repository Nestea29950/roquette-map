<?php
/**
 * Fonds de carte disponibles (paramètre `fond` du shortcode).
 * Pour ajouter un fond : ajouter une entrée ici, il apparaît automatiquement dans l'admin.
 * Tous gratuits et sans clé API.
 *
 * Clé = valeur à mettre dans le shortcode.
 */

defined( 'ABSPATH' ) || exit;

return array(
	'osm'       => array(
		'label'       => 'OpenStreetMap',
		'description' => 'Carte standard, très détaillée.',
		'url'         => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
		'max_zoom'    => 19,
		'default'     => true,
	),
	'osm-fr'    => array(
		'label'       => 'OpenStreetMap France',
		'description' => 'Rendu français, noms de lieux en français.',
		'url'         => 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',
		'attribution' => '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
		'max_zoom'    => 20,
	),
	'positron'  => array(
		'label'       => 'CARTO Positron',
		'description' => 'Clair et épuré, idéal pour faire ressortir les pins.',
		'url'         => 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
		'max_zoom'    => 20,
	),
	'voyager'   => array(
		'label'       => 'CARTO Voyager',
		'description' => 'Coloré et lisible, bon compromis.',
		'url'         => 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
		'max_zoom'    => 20,
	),
	'dark'      => array(
		'label'       => 'CARTO Dark Matter',
		'description' => 'Thème sombre.',
		'url'         => 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
		'max_zoom'    => 20,
	),
	'topo'      => array(
		'label'       => 'OpenTopoMap',
		'description' => 'Topographique, courbes de niveau et relief.',
		'url'         => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="https://opentopomap.org">OpenTopoMap</a>',
		'max_zoom'    => 17,
	),
	'satellite' => array(
		'label'       => 'Esri Satellite',
		'description' => 'Vue satellite.',
		'url'         => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
		'attribution' => 'Tiles &copy; Esri',
		'max_zoom'    => 19,
	),
);
