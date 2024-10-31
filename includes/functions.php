<?php

add_filter('woocommerce_is_purchasable', 'obselling_is_purchasable', 10, 2);
function obselling_is_purchasable( $is_purchasable, $product ) {
	if ( obselling_is_product_obsolete( $product ) ) {
		$is_purchasable = false;
	}

	return $is_purchasable;
}

add_filter('woocommerce_product_is_visible', 'obselling_product_is_visible', 10, 2);
function obselling_product_is_visible( $is_visible, $product_id ){
	$product = wc_get_product($product_id);

	if ( obselling_is_product_obsolete( $product ) ) {
		$is_visible = apply_filters( 'obselling_product_is_visible', true );
	}

	return $is_visible;
}

add_filter('woocommerce_loop_add_to_cart_args', 'obselling_loop_add_to_cart_args', 10, 2);
function obselling_loop_add_to_cart_args( $args, $product ){
  if ( obselling_is_product_obsolete( $product ) ) {
    $class = 'obsolete_product_button';
		$args['class'] = isset($args['class']) ? $args['class'] . ' ' . $class : $class;
	}

  return $args;
}

add_filter('woocommerce_product_add_to_cart_text', 'obselling_product_add_to_cart_text', 10, 2);
function obselling_product_add_to_cart_text( $text, $product ){
  if ( obselling_is_product_obsolete( $product ) ) {
		$text = __('Obsolete product', 'obselling');
	}

  return $text;
}

add_action( 'woocommerce_before_single_product_summary', 'obselling_before_single_product_summary' );
function obselling_before_single_product_summary(){
  global $product;
  $recommended_ids = $product->get_meta( 'recommended_ids', true );
  if( obselling_is_product_obsolete( $product ) && !empty($recommended_ids) && count($recommended_ids) ) {
    include OBSELLING_PLUGIN_DIR . '/includes/views/html-product-page-recommended-products-block.php';
  }

	do_action( 'obselling_before_single_product_summary', $product, $recommended_ids );
}

add_action( 'woocommerce_after_shop_loop_item_title', 'obselling_shop_loop_hide_price', 9 );

function obselling_shop_loop_hide_price(){
  if( has_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 ) ){
    global $product;
    //remove action to hide the price of the product list when the product is obsolete
    if( obselling_is_product_obsolete( $product ) ){
      remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
      add_action( 'woocommerce_after_shop_loop_item_title', 'obselling_shop_loop_restore_action', 11 );
    }
  }
}

function obselling_shop_loop_restore_action(){
  add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
}

add_action( 'woocommerce_single_product_summary', 'obselling_single_product_hide_price', 9 );
function obselling_single_product_hide_price(){
  global $product;
  //remove action to hide the price when the product is obsolete
  if( obselling_is_product_obsolete( $product ) ){
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
  }
}

add_action( 'woocommerce_before_shop_loop_item_title', 'obselling_hide_product_loop_sale_flash', 9 );
function obselling_hide_product_loop_sale_flash(){
  if( has_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10) ){

    global $product;
    if( obselling_is_product_obsolete( $product ) ){
      remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
      add_action( 'woocommerce_before_shop_loop_item_title', 'obselling_hide_product_loop_sale_flash_restore_action', 11 );
    }

  }
}

function obselling_hide_product_loop_sale_flash_restore_action(){
  add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
}

add_action( 'woocommerce_before_single_product_summary', 'obselling_hide_product_sale_flash', 9 );
function obselling_hide_product_sale_flash(){
  if( true == remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 ) ){

    global $product;
    if( 'yes' != $product->get_meta( 'is_obsolete' ) ){
      woocommerce_show_product_sale_flash();
    }

  }
}
/*
Priority should be 25 to be between the hooks:
    - woocommerce_template_single_excerpt - 20
    - woocommerce_template_single_add_to_cart - 30
*/
add_action( 'woocommerce_single_product_summary', 'obselling_single_product_summary', 25 );
function obselling_single_product_summary() {
  global $product;

  if( obselling_is_product_obsolete( $product ) ){
    include __DIR__ . '/views/html-product-page-product-summary-block.php';
  }
}

function obselling_is_product_obsolete( $product ) {
  return 'yes' === $product->get_meta( 'is_obsolete' );
}

function obselling_get_prop( $prop ) {
	$option = get_option( 'obselling' );

	if ( ! is_array( $option ) ) {
		$option = array();
	}

	return isset( $option[$prop] ) ? $option[$prop] : '';
}

function obselling_set_prop( $prop, $value ) {
	$option = get_option( 'obselling' );

	if ( ! is_array( $option ) ) {
		$option = array();
    $option[$prop] = $value;
  }

	update_option( 'obselling', $option );
}

function obselling_delete_prop( $prop ) {
	$option = get_option( 'obselling' );

	if ( ! is_array( $option ) ) {
		$option = array();
	}

	if ( isset( $option[$prop] ) ) {
		unset( $option[$prop] );

  	update_option( 'obselling', $option );
  }
}
?>
