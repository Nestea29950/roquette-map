/* Bloc « Roquette Map » : réglages dans la colonne de droite. Données PHP : RoquetteMapBloc (Bloc.php). */
(function (wp, data) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var c = wp.components;
	var be = wp.blockEditor;

	var images = [{ value: '', label: __('Par défaut', 'roquette-map') }].concat(
		data.images.map(function (img) { return { value: img, label: img }; })
	);

	function getType(slug) {
		return data.types.filter(function (t) { return t.slug === slug; })[0];
	}

	function typeLabel(slug) {
		var type = getType(slug);
		return type ? type.label : slug;
	}

	// Une image par terme d'une taxonomie. value = { slugDuTerme: image } (tous les termes du CPT).
	function TermPins(props) {
		var terms = wp.data.useSelect(function (select) {
			return select('core').getEntityRecords('taxonomy', props.taxonomy.slug, { per_page: 100, _fields: 'id,name,slug' });
		}, [props.taxonomy.slug]);

		if (!terms) {
			return el(c.Spinner);
		}

		return el('div', null,
			el('p', null, el('strong', null, props.taxonomy.label)),
			terms.length ? terms.map(function (term) {
				return el(c.SelectControl, {
					key: term.id,
					label: term.name,
					value: props.value[term.slug] || '',
					options: images,
					onChange: function (v) {
						var obj = Object.assign({}, props.value);
						obj[term.slug] = v;
						props.onChange(obj);
					}
				});
			}) : el('p', null, __('Aucun terme.', 'roquette-map'))
		);
	}

	// Cases à cocher des posts d'un CPT. Aucune cochée = tous.
	function PostsPicker(props) {
		var posts = wp.data.useSelect(function (select) {
			return select('core').getEntityRecords('postType', props.type, { per_page: 100, status: 'publish', _fields: 'id,title' });
		}, [props.type]);

		if (!posts) {
			return el(c.Spinner);
		}
		if (!posts.length) {
			return el('p', null, __('Aucun post.', 'roquette-map'));
		}

		return posts.map(function (post) {
			return el(c.CheckboxControl, {
				key: post.id,
				label: post.title.rendered || '#' + post.id,
				checked: props.value.indexOf(post.id) !== -1,
				onChange: function (checked) {
					props.onChange(checked
						? props.value.concat(post.id)
						: props.value.filter(function (id) { return id !== post.id; }));
				}
			});
		});
	}

	function Edit(props) {
		var a = props.attributes;
		var set = props.setAttributes;

		function setKey(name, key, value) {
			var obj = Object.assign({}, a[name]);
			obj[key] = value;
			set({ [name]: obj });
		}

		var inspector = el(be.InspectorControls, null,
			el(c.PanelBody, { title: __('Contenus', 'roquette-map') },
				data.types.map(function (type) {
					return el(c.CheckboxControl, {
						key: type.slug,
						label: type.label,
						checked: a.cpt.indexOf(type.slug) !== -1,
						onChange: function (checked) {
							set({ cpt: checked ? a.cpt.concat(type.slug) : a.cpt.filter(function (s) { return s !== type.slug; }) });
						}
					});
				})
			),
			el(c.PanelBody, { title: __('Apparence', 'roquette-map') },
				el(c.SelectControl, {
					label: __('Fond de carte', 'roquette-map'),
					value: a.fond,
					options: [{ value: '', label: __('Par défaut', 'roquette-map') }].concat(data.fonds),
					onChange: function (v) { set({ fond: v }); }
				}),
				el(c.SelectControl, {
					label: __('Pin de la carte', 'roquette-map'),
					value: a.pin,
					options: images,
					onChange: function (v) { set({ pin: v }); }
				}),
				a.cpt.map(function (slug) {
					return el(c.SelectControl, {
						key: slug,
						label: __('Pin :', 'roquette-map') + ' ' + typeLabel(slug),
						value: a.pins[slug] || '',
						options: images,
						onChange: function (v) { setKey('pins', slug, v); }
					});
				})
			),
			a.cpt.map(function (slug) {
				var type = getType(slug);
				if (!type || !type.taxonomies.length) {
					return null;
				}
				return el(c.PanelBody, { key: 'terms-' + slug, title: __('Pins par terme :', 'roquette-map') + ' ' + type.label, initialOpen: false },
					el('p', null, __('Remplace le pin du CPT pour les posts ayant ce terme (le premier trouvé l’emporte).', 'roquette-map')),
					type.taxonomies.map(function (taxonomy) {
						return el(TermPins, {
							key: taxonomy.slug,
							taxonomy: taxonomy,
							value: a.termPins[slug] || {},
							onChange: function (obj) { setKey('termPins', slug, obj); }
						});
					})
				);
			}),
			a.cpt.map(function (slug) {
				return el(c.PanelBody, { key: slug, title: __('Posts :', 'roquette-map') + ' ' + typeLabel(slug), initialOpen: false },
					el('p', null, __('Aucun coché = tous les posts localisés.', 'roquette-map')),
					el(PostsPicker, {
						type: slug,
						value: a.posts[slug] || [],
						onChange: function (ids) { setKey('posts', slug, ids); }
					})
				);
			})
		);

		var summary = a.cpt.length
			? a.cpt.map(function (slug) {
				var n = (a.posts[slug] || []).length;
				return typeLabel(slug) + ' (' + (n ? n + ' ' + __('choisis', 'roquette-map') : __('tous', 'roquette-map')) + ')';
			}).join(', ')
			: __('Choisissez les contenus dans la colonne de droite.', 'roquette-map');

		return el('div', be.useBlockProps(),
			inspector,
			el(c.Placeholder, { icon: 'location-alt', label: 'Roquette Map', instructions: summary })
		);
	}

	wp.blocks.registerBlockType('roquette-map/carte', {
		edit: Edit,
		save: function () { return null; }
	});
})(window.wp, window.RoquetteMapBloc);
