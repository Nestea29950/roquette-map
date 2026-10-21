/* Comportement de la popup d'un pin. Utilisé par features/carte/carte.js. */
window.RoquetteMapPopup = (function () {
	'use strict';

	return {
		// Popup sans l'habillage Leaflet : tout vient de popup.twig + popup.css.
		options: {
			className: 'roquette-popup',
			closeButton: false,
			minWidth: 0,
			maxWidth: 10000,
			offset: [0, 10] // compense la marge de 20px que Leaflet garde pour sa pointe
		},

		// Tout élément [data-roquette-popup-close] du Twig ferme la popup.
		attach: function (map) {
			map.on('popupopen', function (e) {
				e.popup.getElement().querySelectorAll('[data-roquette-popup-close]').forEach(function (btn) {
					btn.addEventListener('click', function (event) {
						event.preventDefault();
						map.closePopup(e.popup);
					});
				});
			});
		}
	};
})();
