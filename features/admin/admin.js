/* Menu déroulant des pins : met à jour l'aperçu et le code à copier. */
(function () {
	'use strict';

	document.querySelectorAll('[data-roquette-pin-picker]').forEach(function (picker) {
		var select = picker.querySelector('[data-roquette-pin-select]');
		var preview = picker.querySelector('[data-roquette-pin-preview]');
		var code = picker.querySelector('[data-roquette-pin-code]');

		select.addEventListener('change', function () {
			preview.src = select.selectedOptions[0].dataset.url;
			code.textContent = 'pin="' + select.value + '"';
		});
	});
})();
