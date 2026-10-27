<?php

namespace TinySolutions\cptwooint\Controllers;

// Do not allow directly accessing this file.
use TinySolutions\cptwooint\Helpers\Fns;
use TinySolutions\cptwooint\Loader;
use TinySolutions\cptwooint\Traits\SingletonTrait;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * ShortCodes
 *
 * This class handles the registration and functionality of WooCommerce related shortcodes.
 * These shortcodes are designed to display various product-related information on the site.
 */
class ShortCodes {
	/**
	 * Singleton Trait
	 *
	 * The Singleton pattern ensures only one instance of this class is created.
	 */
	use SingletonTrait;

	/**
	 * @var object $loader Instance of the Loader class
	 */
	protected $loader;

	/**
	 * Class Constructor
	 *
	 * Initializes the class, registers the `cptwooint_shortcodes` method to the `init` action hook,
	 * and sets up the loader to handle shortcode registrations.
	 */
	private function __construct() {
		$this->loader = Loader::instance();
		$this->loader->add_action( 'init', $this, 'cptwooint_shortcodes' );
	}

	/**
	 * Registers shortcodes for use in WordPress.
	 *
	 * This method maps each shortcode to its respective callback function.
	 * It registers four shortcodes: `wc_all_notices`, `short_description`, `cart_button`, and `price`.
	 *
	 * @return void
	 */
	public function cptwooint_shortcodes() {
		$shortcodes = [
			'wc_all_notices',
			'short_description',
			'cart_button',
			'price',
		];
		foreach ( $shortcodes as $shortcode ) :
			add_shortcode( 'cptwooint_' . $shortcode, [ $this, $shortcode . '_shortcode' ] );
		endforeach;
	}

	/**
	 * Shortcode for displaying all WooCommerce notices on a product page.
	 *
	 * This shortcode outputs all the notices related to the product, such as errors, warnings, and success messages.
	 * It is executed inside the product page template.
	 *
	 * @param array $atts Shortcode attributes (not used in this function).
	 *
	 * @return string|null The output of the notices or nothing if the product is not supported.
	 */
	public function wc_all_notices_shortcode( $atts ) {
		if ( ! Fns::is_supported( get_the_ID() ) ) {
			return;
		}
		global $product;
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		ob_start();
		woocommerce_output_all_notices();
		return ob_get_clean();
	}

	/**
	 * Shortcode for displaying the short description of a WooCommerce product.
	 *
	 * This shortcode outputs the short description of a product. It also triggers actions before and after
	 * the description is displayed, allowing for easy customization via hooks.
	 *
	 * @param array $atts Shortcode attributes (not used in this function).
	 *
	 * @return string The short description HTML output, or nothing if the product is not supported.
	 */
	public function short_description_shortcode( $atts ) {
		if ( ! Fns::is_supported( get_the_ID() ) ) {
			return;
		}

		ob_start();
		do_action( 'cptwooint_before_display_short_description' );

		global $product;
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		?>
		<div class="cptwooint-cart-btn-wrapper">
			<?php
			$description = $product->get_short_description();
			Fns::print_html( apply_filters( 'cptwooint_display_short_description', $description, $product ) );
			?>
		</div>
		<?php
		do_action( 'cptwooint_after_display_short_description' );
		return ob_get_clean();
	}

	/**
	 * Shortcode for displaying the price of a WooCommerce product.
	 *
	 * This shortcode outputs the price of the product on the product page. It also triggers actions
	 * before and after displaying the price, allowing for easy customization via hooks.
	 *
	 * @param array $atts Shortcode attributes (not used in this function).
	 *
	 * @return string The price HTML output, or nothing if the product is not supported.
	 */
	public function price_shortcode( $atts ) {
		if ( ! Fns::is_supported( get_the_ID() ) ) {
			return;
		}
		global $product;
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		ob_start();
		?>
		<div class=" cptwooint-product-price">
			<?php
			do_action( 'cptwooint_before_display_price' );
			echo $product->get_price_html();
			do_action( 'cptwooint_after_display_price' );
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Shortcode for displaying the add to cart button for a WooCommerce product.
	 *
	 * This shortcode outputs the add to cart button for a product. It checks whether the page is a single product page
	 * and outputs the corresponding to add to cart button accordingly. It triggers hooks before and after the button
	 * display, allowing for customization.
	 *
	 * @param array $atts Shortcode attributes (not used in this function).
	 *
	 * @return string The HTML output for the add to cart button, or nothing if the product is not supported.
	 */
	public function cart_button_shortcode( $atts ) {
		$current_post_type = get_post_type( get_the_ID() );
		if ( ! Fns::is_supported( $current_post_type ) ) {
			return;
		}
		global $product;
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		$options = Fns::get_options();
		ob_start();
		?>
		<div class="cptwooint-cart-btn-wrapper <?php echo esc_attr( $product->is_sold_individually() ? 'cptwooint-sold-individually' : '' ); ?> ">
			<?php
			if ( is_single( get_the_ID() ) ) {
				do_action( 'cptwooint_display_single_add_to_cart_button', $product, $options, $current_post_type );
			} else {
				do_action( 'cptwooint_display_add_to_cart_button', $product, $options, $current_post_type );
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}
}
