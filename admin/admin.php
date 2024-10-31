<?php
defined( 'ABSPATH' ) || exit;

require_once OBSELLING_PLUGIN_DIR . '/admin/includes/validate.php';

add_action( 'admin_enqueue_scripts', 'obselling_admin_enqueue_scripts', 10, 1 );

function obselling_admin_enqueue_scripts( $hook_suffix ) {

	$admin_pages = array('post.php', 'edit.php', 'woocommerce_page_obselling-pro');

	if( ! in_array( $hook_suffix, $admin_pages) ){
		return;
	}

	wp_enqueue_style( 'obselling-admin',
		plugins_url( 'admin/css/admin.css', OBSELLING_PLUGIN_BASENAME ),
		array(), OBSELLING_VERSION, 'all'
	);

	if ( is_rtl() ) {
		wp_enqueue_style( 'obselling-admin-rtl',
			plugins_url( 'admin/css/admin-rtl.css', OBSELLING_PLUGIN_BASENAME ),
			array(), OBSELLING_VERSION, 'all'
		);
	}

	wp_enqueue_script( 'obselling-admin-js',
		plugins_url( 'admin/js/admin.js', OBSELLING_PLUGIN_BASENAME ),
		array('jquery'), OBSELLING_VERSION, true
	);
}

add_filter( 'plugin_action_links_obselling/obselling.php', 'obselling_admin_plugin_url_link' );

function obselling_admin_plugin_url_link( $links ) {

		if( ! file_exists( WP_PLUGIN_DIR . '/obselling-pro/obselling-pro.php' ) ) {
			$links['obselling_url'] = '<a href="' . esc_url( OBSELLING_WEBSITE_URL ) . '" target="_blank" style="color: dodgerblue; font-weight: 500;">'.__( 'Get PRO version', 'obselling' ) . '</a>';
		}

		return apply_filters( 'obselling_admin_plugin_action_links', $links);
}

add_action( 'add_meta_boxes', 'obselling_add_meta_boxes', 40 );

function obselling_add_meta_boxes() {
  add_meta_box( 'obselling-recommended-products', __( 'Obselling', 'obselling' ), 'obselling_recommended_products_panel_output', 'product', 'normal' );
}

function obselling_recommended_products_panel_output() {
  include __DIR__ . '/views/html-obsolete-products-panel.php';
}

function obselling_recommended_products_panel_tabs_output() {
  global $post, $thepostid, $product_object;

  include __DIR__ . '/views/html-obsolete-products-general.php';
  include __DIR__ . '/views/html-obsolete-products-products.php';
}

function obselling_get_recommended_products_tabs_list() {
  $tabs = [
    'general' => [
      'target' => 'general_recommended_products',
      'label' => __('General', 'obselling')
    ],
    'products' => [
      'target' => 'products_recommended_products',
      'label' => __('Products', 'obselling')
    ]
  ];

  return $tabs;
}

add_filter( 'views_edit-product', 'obselling_add_obsolete_recommended_products_quicklink', 20);

function obselling_add_obsolete_recommended_products_quicklink( $views ){
	global $wp_query;
	if ( current_user_can( 'edit_others_products' ) ) {
		$class            = ( isset( $wp_query->query['is_obsolete'] ) && 'yes' === $wp_query->query['is_obsolete'] ) ? 'current' : '';
		$query_string     = add_query_arg( 'is_obsolete', 'yes' );
		$query_string     = add_query_arg( 'filter_action', 'Filter', $query_string );
		$count = obselling_get_obsoletes_products_count();
		$views['obsolete'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">'
				. _n('Obsolete', 'Obsoletes', $count, 'obselling' )
				. '<span class="count"> (' . $count . ')</span>'
				. '</a>';
	}

	return $views;
}

add_action( 'pre_get_posts', 'obselling_pre_get_posts' );

function obselling_pre_get_posts( $query ){
	if( is_admin() && isset( $_GET['is_obsolete'] ) ) {
		$query->set( 'meta_key', 'is_obsolete' );
		$query->set( 'meta_value', sanitize_text_field( $_GET['is_obsolete'] ) );
	}
}

function obselling_get_obsoletes_products_count(){
    global $wpdb;
		static $count = null;

		if( !isset( $count ) ) {
	    // The SQL query
	    $result = $wpdb->get_var( "
	        SELECT COUNT(p.ID)
	        FROM {$wpdb->prefix}posts as p
	        INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
	        WHERE p.post_type LIKE 'product'
	        AND p.post_status = 'publish'
	        AND pm.meta_key = 'is_obsolete'
	        AND pm.meta_value = 'yes'
	    " );

			$count = (int) $result;
		}

    return $count;
}

add_filter( 'query_vars', 'obselling_add_query_vars', 100 );
function obselling_add_query_vars( $query_vars ) {
    $query_vars[] = 'is_obsolete';
    return $query_vars;
}

add_action( 'woocommerce_product_bulk_and_quick_edit', 'obselling_quick_edit_validate_is_obsolete', 10, 2);
function obselling_quick_edit_validate_is_obsolete($post_id, $post){
	$post_is_obsolete = isset($_REQUEST['is_obsolete']) ? sanitize_text_field( $_REQUEST['is_obsolete'] ) : '';

	$product = wc_get_product( $post );
	//check that the user try to change the obsolete status of the product to 'yes'
	if( 'yes' === $post_is_obsolete && 'yes' != $product->get_meta( 'is_obsolete' ) ) {
			do_action('obselling_quick_edit_validate_is_obsolete', $product, $post_is_obsolete);
	}
}

//it is important to have a high priority on the action to check the validity of the obsolete status before it get changed
add_action('woocommerce_process_product_meta', 'obselling_validate_is_obsolete', 5, 2);
function obselling_validate_is_obsolete( $post_id, $post ){
	$product_type = empty( $_POST['product-type'] ) ? WC_Product_Factory::get_product_type( $post_id ) : sanitize_title( wp_unslash( $_POST['product-type'] ) );
	$classname    = WC_Product_Factory::get_product_classname( $post_id, $product_type ? $product_type : 'simple' );

	if( ! class_exists( $classname ) ){
		return;
	}

	$product      = new $classname( $post_id );

	$post_is_obsolete = isset($_POST['is_obsolete']) ? sanitize_text_field( $_POST['is_obsolete'] ) : '';

	if( 'yes' === $post_is_obsolete && 'yes' != $product->get_meta( 'is_obsolete' ) ) {
			do_action('obselling_validate_is_obsolete', $product, $post_is_obsolete);
	}
}

add_action( 'woocommerce_admin_process_product_object', 'obselling_save_products' );
function obselling_save_products( $product ){
		$recommended_ids = isset($_POST['recommended_ids']) ? $_POST['recommended_ids'] : array();
		foreach( $recommended_ids as $key => $id ){
			$recommended_ids[$key] = absint( $id );
		}

		$is_obsolete = isset($_POST['is_obsolete']) ? sanitize_text_field( $_POST['is_obsolete'] ): false;

		do_action('obselling_save_products', $product, $recommended_ids, $is_obsolete);
}

add_filter('woocommerce_admin_stock_html', 'obselling_render_is_in_stock_column', 10, 2);
function obselling_render_is_in_stock_column($html, $product) {
		return obselling_is_product_obsolete( $product ) ? '<mark class="obsolete">' . _n( 'Obsolete', 'Obsoletes', 1, 'obselling' ) . '</mark>' : $html;//use _n because of ambiguity in poedit
}

add_action( 'woocommerce_product_quick_edit_end', 'obselling_product_quick_edit_is_obsolete' );
function obselling_product_quick_edit_is_obsolete() {
	?>
	<div class="inline-edit-group is_obsolete_field">
		<label class="manage_stock">
			<input type="checkbox" name="is_obsolete" value="yes">
			<span class="checkbox-title"><?php esc_html_e( 'Obsolete product?', 'obselling' ); ?></span>
		</label>
	</div>
	<?php
}

add_action( 'manage_product_posts_custom_column', 'obselling_product_quick_edit_inline_metadata', 100, 2);
function obselling_product_quick_edit_inline_metadata( $column, $post_id ){
	switch ( $column ) {
	    case 'name' :

	        ?>
	        <div class="hidden is_obsolete_inline" id="is_obsolete_inline_<?php esc_attr_e( $post_id ); ?>">
	            <div id="is_obsolete"><?php echo esc_html( get_post_meta( $post_id, 'is_obsolete', true ) ); ?></div>
	        </div>
	        <?php

	        break;

	    default :
	        break;
	}
}

?>
