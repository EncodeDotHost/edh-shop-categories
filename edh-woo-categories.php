<?php
/**
 * Plugin Name:     Woo Categories
 * Description:     A simple plugin to separate products from categories on the shop page
 * Author:          EncodeDotHost
 * Author URI:      https://encode.host
 * Text Domain:     edh-woo-categories
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Edh_Woo_Categories
*/

defined( 'ABSPATH' ) or die( 'No direct access please!' );

register_activation_hook( __FILE__, 'edh_plugin_activation_hook' );

function edh_plugin_activation_hook() {
	// Check if the other plugin is active.
	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		// If the other plugin is not active, deactivate and stop the activation of your plugin.
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'Please activate Woocommerce before activating this plugin.' );
	}
}

function edh_scripts() {
      /* register the stylesheet */
      wp_register_style( 'edh-styles', plugins_url( 'assets/css/style.css', __FILE__ ) );
      wp_enqueue_style('edh-styles');
}
add_action( 'wp_enqueue_scripts', 'edh_scripts' );

/**
 * Display subcategories for the current category.
 *
 * @param array $args {
 *     Optional. Arguments to customize the subcategories output.
 *
 *     @type int    $parent  Category ID to get the subcategories for. Default is the current category ID.
 *     @type string $before  HTML to output before the subcategories. Default is '<ul class="product-cats">'.
 *     @type string $after   HTML to output after the subcategories. Default is '</ul>'.
 * }
*/
function edh_custom_subcategories( $args = array() ) {
	// Check if WooCommerce is active.
	if ( ! function_exists( 'woocommerce_subcategory_thumbnail' ) ) {
		return;
	}

	$defaults = array(
		'parent' => get_queried_object_id(),
		'before' => '<ul class="edh-product-cats">',
		'after'  => '</ul>',
	);
	$args     = wp_parse_args( $args, $defaults );

	$terms = get_terms( 'product_cat', $args );
	if ( $terms ) {
		echo $args['before'];
		foreach ( $terms as $term ) {
			echo '<li class="category">';
			woocommerce_subcategory_thumbnail( $term );
			echo '<h2><a href="' . esc_url( get_term_link( $term ) ) . '" class="' . $term->slug . '">' . $term->name . '</a></h2>';
			echo '</li>';
		}
		echo $args['after'];
	} 
}
add_action( 'woocommerce_before_shop_loop', 'edh_custom_subcategories', 50 );