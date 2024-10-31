<?php

defined( 'ABSPATH' ) || exit;
?>

<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="recommended_ids" name="recommended_ids[]" data-placeholder="<?php esc_attr_e( 'Search product', 'obselling' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>">
	<?php
	foreach ( $product_ids as $product_id ){
		$product = wc_get_product( $product_id );
		if ( is_object( $product ) ){
			echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
		}
	}
	?>
</select> <?php echo wc_help_tip( __( 'Products you recommend to replace the current one', 'obselling' ) );?>