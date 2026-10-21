/* Initialise chaque carte [data-roquette-map]. La popup est gérée par features/popup/popup.js. */
(function () {
	'use strict';

	function initMap(el) {
		var config = JSON.parse(el.dataset.roquetteMap);
		var map = L.map(el).setView([46.6, 2.4], 6);

		L.tileLayer(config.tile.url, {
			maxZoom: config.tile.maxZoom,
			attribution: config.tile.attribution
		}).addTo(map);

		// Une icône Leaflet par image de pin (features/pins/images/).
		var icons = {};
		Object.keys(config.icons).forEach(function (name) {
			icons[name] = L.icon(config.icons[name]);
		});

		var markers = config.pins.map(function (pin) {
			return L.marker([pin.lat, pin.lng], { icon: icons[pin.icon] }).bindPopup(pin.popup, RoquetteMapPopup.options).addTo(map);
		});

		RoquetteMapPopup.attach(map);

		if (markers.length) {
			map.fitBounds(L.featureGroup(markers).getBounds(), { padding: [40, 40] });
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-roquette-map]').forEach(initMap);
	});
})();
