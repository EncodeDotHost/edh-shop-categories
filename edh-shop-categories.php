<?php
/**
 * Plugin Name:     EDH Shop Categories
 * Description:     A simple plugin to separate products from categories on the WooCommerce shop page
 * Author:          EncodeDotHost
 * Author URI:      https://encode.host
 * Text Domain:     edh-shop-categories
 * Domain Path:     /languages
 * Version:         1.1.1
 * Requires Plugins: woocommerce
 *
 * @package         EDH_Shop_Categories
*/

defined( 'ABSPATH' ) or die( 'No direct access please!' );

function edh_scripts() {
	if ( is_woocommerce() ) {
		wp_enqueue_style( 'edh-styles', plugins_url( 'assets/css/style.css', __FILE__ ) );
	}
}
add_action( 'wp_enqueue_scripts', 'edh_scripts' );

/**
 * Display subcategories for the current category.
 *
 * @param array $args {
 *     Optional. Arguments to customize the subcategories output.
 *
 *     @type int    $parent  Category ID to get the subcategories for. Default is the current category ID (0 on the main shop page).
 *     @type string $before  HTML to output before the subcategories. Default is '<ul class="edh-product-cats">'.
 *     @type string $after   HTML to output after the subcategories. Default is '</ul>'.
 * }
*/
function edh_custom_subcategories( $args = array() ) {
	if ( ! function_exists( 'woocommerce_subcategory_thumbnail' ) ) {
		return;
	}

	$defaults = array(
		'parent' => is_shop() ? 0 : get_queried_object_id(),
		'before' => '<ul class="edh-product-cats">',
		'after'  => '</ul>',
	);
	$args = wp_parse_args( $args, $defaults );

	$terms = get_terms( 'product_cat', array( 'parent' => $args['parent'] ) );
	if ( $terms ) {
		echo $args['before'];
		foreach ( $terms as $term ) {
			echo '<li class="category">';
			woocommerce_subcategory_thumbnail( $term );
			echo '<h2><a href="' . esc_url( get_term_link( $term ) ) . '" class="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</a></h2>';
			echo '</li>';
		}
		echo $args['after'];
	}
}
add_action( 'woocommerce_before_shop_loop', 'edh_custom_subcategories', 50 );
