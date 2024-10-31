<?php

defined( 'ABSPATH' ) || exit;
?>

<div id="products_recommended_products" class="panel woocommerce_options_panel hidden">
  	<div class="options_group">
  		<p class="form-field">
  			<label for="recommended_ids"><?php _e('Recommended', 'obselling'); ?></label>
        <?php
          $product_ids = $product_object->get_meta( 'recommended_ids' );
          include __DIR__ . '/html-obsolete-products-products-search.php';
         ?>
		</p>

       <?php
       	include __DIR__ . '/html-get-pro-block.php';
       ?>
  	</div>
</div>

