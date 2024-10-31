<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="panel-wrap recommended_products">

	<ul class="recommended_products_tabs wc-tabs">
		<?php foreach ( obselling_get_recommended_products_tabs_list() as $key => $tab ) : ?>
			<li class="<?php echo esc_attr( $key ); ?>_options <?php echo esc_attr( $key ); ?>_tab <?php echo esc_attr( isset( $tab['class'] ) ? implode( ' ', (array) $tab['class'] ) : '' ); ?>">
				<a href="#<?php echo esc_attr( $tab['target'] ); ?>"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
			</li>
		<?php endforeach; ?>
		<?php do_action( 'obselling_recommended_products_panel_tabs_list' ); ?>
	</ul>

	<?php
    obselling_recommended_products_panel_tabs_output();
		do_action( 'obselling_recommended_products_panel_tabs_view' );
  ?>
	<div class="clear"></div>
</div>
