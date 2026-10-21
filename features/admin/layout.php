<?php
/**
 * Layout commun des pages admin (style iOS 17).
 *
 * @var \RoquetteMap\Core\View $view
 * @var string                 $title
 * @var string                 $page    Vue de la page à inclure.
 * @var array                  $context Variables passées à la page.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<div class="ios-app">
		<header class="ios-navbar">
			<h1 class="ios-large-title"><?php echo esc_html( $title ); ?></h1>
		</header>

		<main class="ios-content">
			<?php $view->display( $page, $context ?? array() ); ?>
		</main>
	</div>
</div>
