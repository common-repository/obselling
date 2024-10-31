<?php

defined( 'ABSPATH' ) || exit;
?>

<div id="general_recommended_products" class="panel woocommerce_options_panel">
  <?php
  woocommerce_wp_checkbox(
		array(
			'id'            => 'is_obsolete',
			'wrapper_class' => 'show_if_simple show_if_variable',
			'label'         => __( 'Obsolete product?', 'obselling' ),
			'description'   => __( 'Make this product obsolete', 'obselling' )
		)
	);
  ?>
  <?php  include __DIR__ . '/html-get-pro-block.php';
   ?>
</div>
