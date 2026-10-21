<?php
/**
 * Conteneur d'une carte.
 *
 * @var string $id
 * @var array  $config
 */

defined( 'ABSPATH' ) || exit;
?>
<div
	id="<?php echo esc_attr( $id ); ?>"
	class="roquette-map"
	data-roquette-map="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
></div>
