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
	'osm-hot'   => array(
		'label'       => 'OpenStreetMap HOT',
		'description' => 'Style humanitaire, couleurs contrastées.',
		'url'         => 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>, Humanitarian OpenStreetMap Team, OpenStreetMap France',
		'max_zoom'    => 19,
	),
	'cyclosm'   => array(
		'label'       => 'CyclOSM',
		'description' => 'Pistes cyclables et itinéraires vélo.',
		'url'         => 'https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png',
		'attribution' => '<a href="https://www.cyclosm.org">CyclOSM</a> | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
		'max_zoom'    => 20,
	),
	'positron-nolabels' => array(
		'label'       => 'CARTO Positron sans noms',
		'description' => 'Positron sans aucun texte, très minimaliste.',
		'url'         => 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
		'max_zoom'    => 20,
	),
	'voyager-nolabels' => array(
		'label'       => 'CARTO Voyager sans noms',
		'description' => 'Voyager sans aucun texte.',
		'url'         => 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png',
		'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
		'max_zoom'    => 20,
	),
	'esri-rues' => array(
		'label'       => 'Esri Rues',
		'description' => 'Plan de rues Esri, style classique.',
		'url'         => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
		'attribution' => 'Tiles &copy; Esri',
		'max_zoom'    => 19,
	),
	'esri-topo' => array(
		'label'       => 'Esri Topographique',
		'description' => 'Relief et routes, rendu doux.',
		'url'         => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
		'attribution' => 'Tiles &copy; Esri',
		'max_zoom'    => 19,
	),
	'esri-gris' => array(
		'label'       => 'Esri Gris clair',
		'description' => 'Fond gris neutre (zoom max 16).',
		'url'         => 'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
		'attribution' => 'Tiles &copy; Esri',
		'max_zoom'    => 16,
	),
	'ign-plan'  => array(
		'label'       => 'IGN Plan',
		'description' => 'Plan IGN officiel (France uniquement).',
		'url'         => 'https://data.geopf.fr/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=GEOGRAPHICALGRIDSYSTEMS.PLANIGNV2&STYLE=normal&TILEMATRIXSET=PM&FORMAT=image/png&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}',
		'attribution' => '&copy; <a href="https://www.ign.fr">IGN</a>',
		'max_zoom'    => 19,
	),
	'ign-photo' => array(
		'label'       => 'IGN Photos aériennes',
		'description' => 'Orthophotos IGN haute résolution (France uniquement).',
		'url'         => 'https://data.geopf.fr/wmts?SERVICE=WMTS&REQUEST=GetTile&VERSION=1.0.0&LAYER=ORTHOIMAGERY.ORTHOPHOTOS&STYLE=normal&TILEMATRIXSET=PM&FORMAT=image/jpeg&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}',
		'attribution' => '&copy; <a href="https://www.ign.fr">IGN</a>',
		'max_zoom'    => 19,
	),
);
