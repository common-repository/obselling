<?php
defined( 'ABSPATH' ) || exit;

add_action( 'obselling_quick_edit_validate_is_obsolete', 'obselling_free_quick_edit_validate_is_obsolete', 10, 2 );

function obselling_free_quick_edit_validate_is_obsolete( $product, $post_is_obsolete ){
  $obsolete_count = obselling_get_obsoletes_products_count();

  if( $obsolete_count >= OBSELLING_OBSOLETE_PRODUCTS_FREEMIUM_LIMIT ){
      $_REQUEST['is_obsolete'] = ''; //keep the product to its old obsolete status
  }
}

add_action( 'obselling_validate_is_obsolete', 'obselling_free_validate_is_obsolete', 10, 2 );

function obselling_free_validate_is_obsolete( $product, $post_is_obsolete ){
  $obsolete_count = obselling_get_obsoletes_products_count();

  if( $obsolete_count >= OBSELLING_OBSOLETE_PRODUCTS_FREEMIUM_LIMIT ){
      $_POST['is_obsolete'] = $product->get_meta( 'is_obsolete' ); //keep the product to its old obsolete status
      WC_Admin_Meta_Boxes::add_error(
		sprintf(__("The product wasn't made obsolete because you reached the limit of %s obsoletes products allowed in the Free version. Get PRO Version !", 'obselling'), OBSELLING_OBSOLETE_PRODUCTS_FREEMIUM_LIMIT)
	); //display a error notice
  }
}

add_action( 'obselling_save_products', 'obselling_free_save_products', 10, 3);
function obselling_free_save_products( $product, $recommended_ids, $is_obsolete ){
  if( count($recommended_ids) <= OBSELLING_RECOMMENDED_PRODUCTS_FREEMIUM_LIMIT ){
    $product->update_meta_data( 'recommended_ids', $recommended_ids );
    $product->update_meta_data( 'is_obsolete', $is_obsolete );
  } else {
    WC_Admin_Meta_Boxes::add_error(
       sprintf(__("The recommended products weren't updated because you reached the limit of %s product allowed in the Free version. Get PRO Version !", 'obselling'), OBSELLING_RECOMMENDED_PRODUCTS_FREEMIUM_LIMIT)
	); //display a error notice
  }
}

add_action( 'woocommerce_product_quick_edit_save', 'obselling_free_quick_edit_save_product');
function obselling_free_quick_edit_save_product( $product ){
  $product->update_meta_data( 'is_obsolete', wc_clean( $_REQUEST['is_obsolete'] ));
  $product->save();
}
?>
