<?php
/**
 * Page d'accueil : documentation du shortcode.
 *
 * @var \WP_Post_Type[] $post_types
 * @var array           $tiles
 * @var string[]        $pin_images
 */

defined( 'ABSPATH' ) || exit;

$first_cpt = $post_types ? array_key_first( $post_types ) : 'evenements';
?>

<section class="ios-section">
	<h2 class="ios-section__header"><?php esc_html_e( 'Shortcode', 'roquette-map' ); ?></h2>
	<div class="ios-group">
		<div class="ios-cell">
			<code class="ios-code">[roquette_map cpt="<?php echo esc_html( $first_cpt ); ?>" fond="positron" <?php echo esc_html( $first_cpt ); ?>="pin-rouge.png"]</code>
		</div>
	</div>
	<p class="ios-section__footer"><?php esc_html_e( 'Ajoutez le bloc « Roquette Map » dans l’éditeur (réglages dans la colonne de droite), ou utilisez le shortcode. Les pins utilisent les champs ACF « Localisation » (lat, lng) de chaque publication.', 'roquette-map' ); ?></p>
</section>

<section class="ios-section">
	<h2 class="ios-section__header"><?php esc_html_e( 'Paramètres', 'roquette-map' ); ?></h2>
	<ul class="ios-group">
		<li class="ios-cell ios-cell--icon">
			<span class="ios-cell__icon"><span class="dashicons dashicons-location"></span></span>
			<span class="ios-cell__body">
				<span class="ios-cell__title"><code class="ios-code">cpt</code></span>
				<span class="ios-cell__subtitle"><?php esc_html_e( 'Un ou plusieurs types de contenu, séparés par des virgules. Affiche un pin par publication.', 'roquette-map' ); ?></span>
			</span>
		</li>
		<li class="ios-cell ios-cell--icon">
			<span class="ios-cell__icon" style="background: var(--ios-indigo)"><span class="dashicons dashicons-admin-site-alt3"></span></span>
			<span class="ios-cell__body">
				<span class="ios-cell__title"><code class="ios-code">fond</code></span>
				<span class="ios-cell__subtitle"><?php esc_html_e( 'Fond de carte, parmi la liste ci-dessous.', 'roquette-map' ); ?></span>
			</span>
		</li>
		<li class="ios-cell ios-cell--icon">
			<span class="ios-cell__icon" style="background: var(--ios-red)"><span class="dashicons dashicons-sticky"></span></span>
			<span class="ios-cell__body">
				<span class="ios-cell__title"><code class="ios-code">pin</code></span>
				<span class="ios-cell__subtitle"><?php esc_html_e( 'Image des pins de toute la carte, parmi la liste ci-dessous. Sans ce paramètre : default.png.', 'roquette-map' ); ?></span>
			</span>
		</li>
		<li class="ios-cell ios-cell--icon">
			<span class="ios-cell__icon" style="background: var(--ios-orange)"><span class="dashicons dashicons-admin-generic"></span></span>
			<span class="ios-cell__body">
				<span class="ios-cell__title"><code class="ios-code"><?php esc_html_e( 'nom-du-cpt', 'roquette-map' ); ?></code></span>
				<span class="ios-cell__subtitle">
					<?php esc_html_e( 'Réglages d’un CPT listé dans cpt, séparés par des virgules : son image de pin (remplace pin) et, pour ses taxonomies, une image par terme (terme:image, remplace l’image du CPT).', 'roquette-map' ); ?><br>
					<code class="ios-code">pin="pin-vert.png" evenements="pin-rouge.png"</code> <?php esc_html_e( 'evenements en rouge, le reste en vert', 'roquette-map' ); ?><br>
					<code class="ios-code">evenements="pin-rouge.png,concert:pin-bleu.png"</code> <?php esc_html_e( 'les événements du terme « concert » en bleu, les autres en rouge', 'roquette-map' ); ?>
				</span>
			</span>
		</li>
		<li class="ios-cell ios-cell--icon">
			<span class="ios-cell__icon" style="background: var(--ios-indigo)"><span class="dashicons dashicons-filter"></span></span>
			<span class="ios-cell__body">
				<span class="ios-cell__title"><code class="ios-code">posts</code></span>
				<span class="ios-cell__subtitle"><?php esc_html_e( 'IDs de publications séparés par des virgules : seules celles-ci sont affichées. Sans ce paramètre : toutes les publications localisées des CPT.', 'roquette-map' ); ?></span>
			</span>
		</li>
	</ul>
</section>

<section class="ios-section">
	<span class="ios-section__icon"><span class="dashicons dashicons-location"></span></span>
	<h2 class="ios-section__header"><?php esc_html_e( 'Types de contenu disponibles', 'roquette-map' ); ?></h2>
	<ul class="ios-group">
		<?php foreach ( $post_types as $slug => $type ) : ?>
			<li class="ios-cell">
				<span class="ios-cell__body"><?php echo esc_html( $type->labels->name ); ?></span>
				<code class="ios-code"><?php echo esc_html( $slug ); ?></code>
			</li>
		<?php endforeach; ?>
	</ul>
	<p class="ios-section__footer"><?php esc_html_e( 'Valeurs à utiliser dans le paramètre cpt.', 'roquette-map' ); ?></p>
</section>

<section class="ios-section">
	<span class="ios-section__icon" style="background: var(--ios-indigo)"><span class="dashicons dashicons-admin-site-alt3"></span></span>
	<h2 class="ios-section__header"><?php esc_html_e( 'Fonds de carte', 'roquette-map' ); ?></h2>
	<ul class="ios-group">
		<?php foreach ( $tiles as $key => $tile ) : ?>
			<li class="ios-cell">
				<span class="ios-cell__body">
					<span class="ios-cell__title">
						<?php echo esc_html( $tile['label'] ); ?>
						<?php if ( ! empty( $tile['default'] ) ) : ?>
							<span class="ios-tag"><?php esc_html_e( 'Par défaut', 'roquette-map' ); ?></span>
						<?php endif; ?>
					</span>
					<span class="ios-cell__subtitle"><?php echo esc_html( $tile['description'] ); ?></span>
				</span>
				<code class="ios-code"><?php echo esc_html( $key ); ?></code>
			</li>
		<?php endforeach; ?>
	</ul>
	<p class="ios-section__footer"><?php esc_html_e( 'Valeurs à utiliser dans le paramètre fond. Liste définie dans features/fond/fonds.php.', 'roquette-map' ); ?></p>
</section>

<section class="ios-section">
	<span class="ios-section__icon" style="background: var(--ios-red)"><span class="dashicons dashicons-sticky"></span></span>
	<h2 class="ios-section__header"><?php esc_html_e( 'Images de pins', 'roquette-map' ); ?></h2>
	<div class="ios-group" data-roquette-pin-picker>
		<label class="ios-cell">
			<span class="ios-cell__body"><?php esc_html_e( 'Image', 'roquette-map' ); ?></span>
			<select class="ios-select" data-roquette-pin-select>
				<?php foreach ( $pin_images as $image ) : ?>
					<option value="<?php echo esc_attr( $image ); ?>" data-url="<?php echo esc_url( \RoquetteMap\Core\Assets::url( 'features/pins/images/' . $image ) ); ?>" <?php selected( $image, 'default.png' ); ?>><?php echo esc_html( $image ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<div class="ios-cell">
			<span class="ios-cell__body">
				<img class="ios-pin-preview" src="<?php echo esc_url( \RoquetteMap\Core\Assets::url( 'features/pins/images/default.png' ) ); ?>" alt="" data-roquette-pin-preview>
			</span>
			<code class="ios-code" data-roquette-pin-code>pin="default.png"</code>
		</div>
	</div>
	<p class="ios-section__footer"><?php esc_html_e( 'Valeurs à utiliser dans pin ou dans le paramètre d’un CPT. Pour en ajouter : déposer l’image dans features/pins/images/ (la pointe du pin = bas, centre de l’image).', 'roquette-map' ); ?></p>
</section>
