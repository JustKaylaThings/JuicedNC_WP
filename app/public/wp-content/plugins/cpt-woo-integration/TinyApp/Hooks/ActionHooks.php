<?php
/**
 * Main ActionHooks class.
 *
 * @package TinySolutions\cptwooint
 */

namespace TinySolutions\cptwooint\Hooks;

use TinySolutions\cptwooint\Helpers\Fns;
use TinySolutions\cptwooint\Loader;
use TinySolutions\cptwooint\Traits\SingletonTrait;

defined( 'ABSPATH' ) || exit();

/**
 * Main ActionHooks class.
 */
class ActionHooks {
	/**
	 * Singleton
	 */
	use SingletonTrait;

	/**
	 * @var object
	 */
	protected $loader;

	/**
	 * Class Constructor
	 */
	private function __construct() {
		$this->loader = Loader::instance();
		$this->loader->add_action( 'in_admin_header', $this, 'remove_all_notices', 99 );
		$this->loader->add_action( 'save_post', $this, 'update_product_price', 15 );
		$this->loader->add_action( 'cptwooint_display_add_to_cart_button', $this, 'woocommerce_template_loop_add_to_cart', 20 );
		$this->loader->add_action( 'cptwooint_display_single_add_to_cart_button', $this, 'woocommerce_template_single_add_to_cart' );
		$this->loader->add_action( 'woocommerce_account_downloads_column_post-type', $this, 'account_downloads_column', 15 );
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $this, 'set_price_in_cart', 20 );
		$this->loader->add_action( 'the_post', $this, 'setup_product_data', 15 );
		$this->loader->add_action( 'pre_get_posts', $this, 'wc_setup_loop', 5 );
		$this->loader->add_action( 'wp_body_open', $this, 'wp_body_open', 5 );
		$this->loader->add_action( 'wp_footer', $this, 'wp_footer', 99 );
	}

	/**
	 * Post Type.
	 *
	 * @return void
	 */
	public function woocommerce_template_loop_add_to_cart() {
		woocommerce_template_loop_add_to_cart();
	}

	/**
	 * Post Type.
	 *
	 * @param array $download download.
	 * @return void
	 */
	public function woocommerce_template_single_add_to_cart() {
		woocommerce_template_single_add_to_cart();
	}

	/**
	 * Post Type.
	 *
	 * @param array $download download.
	 * @return void
	 */
	public function account_downloads_column( $download ) {
		$post_type = get_post_type( $download['product_id'] );
		echo esc_html( $post_type );
	}

	/**
	 *
	 * @return void
	 */
	public function wp_body_open() {
		// Check if it's the main query and not in the admin area.
		if ( ! Fns::is_supported( $GLOBALS['wp_query']->get( 'post_type' ) ) ) {
			return;
		}
		?>
		<div class="product">
		<?php
	}

	/**
	 * @return void
	 */
	public function wp_footer() {
		// Check if it's the main query and not in the admin area.
		if ( ! Fns::is_supported( $GLOBALS['wp_query']->get( 'post_type' ) ) ) {
			return;
		}
		?>
		</div>
		<!-- End Product Class -->
		<?php
	}

	/**
	 * @param $query
	 *
	 * @return void
	 */
	public function wc_setup_loop( $query ) {
		// Check if it's the main query and not in the admin area.
		if ( $query->is_main_query() && ! is_admin() && Fns::is_supported( $GLOBALS['wp_query']->get( 'post_type' ) ) ) {
			 $query->set( 'wc_query', 'product_query' );
		}
	}

	/**
	 * When the_post is called, put product data into a global.
	 *
	 * @param mixed $post Post Object.
	 * @return WC_Product
	 */
	public function setup_product_data( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}
		if ( ! Fns::is_supported( $post->post_type ) ) {
			return;
		}
		$GLOBALS['product'] = wc_get_product( $post );
		return $GLOBALS['product'];
	}

	/**
	 * @return void
	 */
	public function remove_all_notices() {
		$screen = get_current_screen();

		if ( in_array( $screen->base, [ 'toplevel_page_cptwooint-admin', 'wc-integration_page_cptwooint-get-pro', 'wc-integration_page_cptwooint-pricing-pro' ], true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}
	/**
	 * @param $post_id
	 *
	 * @return void
	 */
	public function update_product_price( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( ! Fns::is_supported( $post_type ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$is_add_price_meta = Fns::is_add_cpt_meta( $post_type, 'default_price_meta_field' );

		if ( cptwooint()->has_pro() && $is_add_price_meta ) {
			return;
		}

		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		$nonce_action = 'update-post_' . $post_id;
		if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			return;
		}

		if ( $is_add_price_meta && isset( $_POST['_regular_price'] ) ) {
			$regular_price = sanitize_text_field( wp_unslash( $_POST['_regular_price'] ?? '' ) );
		} else {
			$regular_price = Fns::cptwoo_get_price( $post_id );
		}

		update_post_meta( $post_id, '_regular_price', $regular_price );

		if ( $is_add_price_meta && isset( $_POST['_sale_price'] ) ) {
			$sale_price = sanitize_text_field( wp_unslash( $_POST['_sale_price'] ?? '' ) );
		} else {
			$sale_price = Fns::cptwoo_get_price( $post_id, 'sale_price' );
		}
		update_post_meta( $post_id, '_price', ( ! empty( $sale_price ) ? $sale_price : $regular_price ) );
		update_post_meta( $post_id, '_sale_price', $sale_price );
	}
	/**
	 * Sets custom sale and regular prices for supported products in the cart.
	 *
	 * This function hooks into WooCommerce before totals are calculated and modifies the
	 * product prices in the cart based on custom logic defined in `Fns::cptwoo_get_price()`.
	 * It only applies to supported post types as determined by `Fns::is_supported()`.
	 *
	 * This ensures the correct custom prices are used in cart totals, display, and checkout.
	 *
	 * @param \WC_Cart $cart The WooCommerce cart object.
	 */
	public function set_price_in_cart( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		if ( did_action( 'woocommerce_before_calculate_totals' ) > 1 ) {
			return; // Skip repeated calls.
		}
		foreach ( $cart->get_cart() as $cart_item ) {
			$product = $cart_item['data'];
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$post_type = get_post_type( $product->get_id() );
			if ( ! Fns::is_supported( $post_type ) ) {
				continue;
			}
			$price = Fns::get_price( $post_type, $product->get_id() );
			if ( '' === $price || null === $price ) {
				continue;
			}
			$regularPrice = Fns::cptwoo_get_price( $product->get_id() );
			$product->set_price( $price );
			$product->set_regular_price( $regularPrice );
			$product->set_sale_price( $price );
		}
	}
}
