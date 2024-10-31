<?php

defined( 'ABSPATH' ) || exit;
global $product;
$product_orig = $product;

$args = array(
    'include' =>  $product->get_meta( 'recommended_ids', true ),
);
$products = wc_get_products( $args );
$products  = apply_filters( 'obselling_recommended_products_orderby', wc_products_array_orderby( $products, 'shuffle', ) );
// not sure why but 'loop' is at '1' after loading the page.
// so we have to set it to 'O' so wc_product_class can work properly.
wc_set_loop_prop('loop', 0);
wc_set_loop_prop('columns', 4);
?>
<div class="obselling-product-page">
  <div class="obselling-product-alert"><?php echo apply_filters( 'obselling_recommended_products_text', __( "This product is no longer available. We recommend this selection:", 'obselling' ) ) ?></div>
  <ul class="products columns-<?php esc_attr_e( wc_get_loop_prop( 'columns' ) )?>">
  <?php foreach( $products as $product ):?>
          <li <?php wc_product_class('recommended_product') ?> >
              <?php
              global $post;
              $post = WP_Post::get_instance( $product->get_id() );
              woocommerce_template_loop_product_link_open();
              woocommerce_template_loop_product_thumbnail();
              // we can't use woocommerce_template_loop_product_title() because it uses the title of the page
              // copied from include/wc-template-functions.php
              echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title( $product->get_id() ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
              woocommerce_template_loop_rating();

              if( !obselling_is_product_obsolete( $product ) ){
                woocommerce_template_loop_price();
              }

              woocommerce_template_loop_product_link_close();
              woocommerce_template_loop_add_to_cart();
              ?>
          </li>
  <?php
  endforeach;
  wp_reset_query();
  $product = $product_orig;
   ?>
  </ul>
</div>
