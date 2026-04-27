<?php
/**
 * Load assets
 *
 * @package WooCommerce\Admin
 * @version 3.7.0
 */

namespace TinySolutions\cptwooint\Controllers\Admin;

use Automattic\Jetpack\Constants;
use TinySolutions\cptwooint\Helpers\Fns;
use TinySolutions\cptwooint\Traits\SingletonTrait;
use WC_Frontend_Scripts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Admin_Assets Class.
 */
class ProductAdminAssets {

	/**
	 * Singleton
	 */
	use SingletonTrait;

	/**
	 * Class Constructor
	 */
	private function __construct() {
		$wc_plugin_path = WC()->plugin_path();
		$file_path      = $wc_plugin_path . '/includes/admin/class-wc-admin-assets.php';
		add_filter( 'woocommerce_screen_ids', [ $this, 'cpt_screens' ], 99 );

		if ( file_exists( $file_path ) ) {
			include_once $file_path;
		}
		 $this->admin_styles();
		 $this->admin_scripts();
	}
	/**
	 * @param array $screen_ids post or page.
	 *
	 * @return array
	 */
	public function cpt_screens( $screen_ids ) {
		// Add your custom screen ID (e.g. for a custom admin page).
		$screen = get_current_screen();
		if ( ! Fns::is_supported( $screen->post_type ) ) {
			return $screen_ids;
		}
		$screen_id    = $screen ? $screen->id : '';
		$screen_ids[] = 'edit-' . $screen_id;
		$screen_ids[] = $screen_id;
		return $screen_ids;
	}
	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts;

		$version   = Constants::get_constant( 'WC_VERSION' );
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( ! Fns::is_supported( $screen_id ) ) {
			return;
		}
		wp_enqueue_style( 'cptwooint-settings' );
		wp_enqueue_style( 'wp-components' );
		// Register admin styles.
		if ( $screen && $screen->is_block_editor() ) {
			$styles = WC_Frontend_Scripts::get_styles();
			if ( $styles ) {
				foreach ( $styles as $handle => $args ) {
					wp_register_style(
						$handle,
						$args['src'],
						$args['deps'],
						$args['version'],
						$args['media']
					);

					if ( ! isset( $args['has_rtl'] ) ) {
						wp_style_add_data( $handle, 'rtl', 'replace' );
					}

					wp_enqueue_style( $handle );
				}
			}
		}
		// Sitewide menu CSS.
	}
	
	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $wp_query, $post, $theorder;

		$screen       = get_current_screen();
		$screen_id    = $screen ? $screen->id : '';
		$wc_screen_id = 'woocommerce';
		$suffix       = Constants::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';
		$version      = Constants::get_constant( 'WC_VERSION' );

		if ( ! Fns::is_supported( $screen->post_type ) ) {
			return;
		}
		// Register scripts.
		wp_register_script( 'wc-admin-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', [ 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'wc-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ], $version, true );
		// Edit product category pages.
		wp_enqueue_script( 'wc-admin-product-editor', WC()->plugin_url() . '/assets/js/admin/product-editor' . $suffix . '.js', [ 'jquery' ], $version, false );
		wp_localize_script(
			'wc-admin-product-editor',
			'woocommerce_admin_product_editor',
			[
				'i18n_description' => esc_js( __( 'Product description', 'cpt-woo-integration' ) ),
			]
		);
		// Meta boxes.
		$post_id                = isset( $post->ID ) ? $post->ID : '';
		$currency               = '';
		$remove_item_notice     = __( 'Are you sure you want to remove the selected items?', 'cpt-woo-integration' );
		$remove_fee_notice      = __( 'Are you sure you want to remove the selected fees?', 'cpt-woo-integration' );
		$remove_shipping_notice = __( 'Are you sure you want to remove the selected shipping?', 'cpt-woo-integration' );
		$product                = wc_get_product( $post_id );
		// Eventually this will become wc_data_or_post object as we implement more custom tables.
		$order_or_post_object = $post;
		$params               = [
			'remove_item_notice'                 => $remove_item_notice,
			'remove_fee_notice'                  => $remove_fee_notice,
			'remove_shipping_notice'             => $remove_shipping_notice,
			'i18n_select_items'                  => __( 'Please select some items.', 'cpt-woo-integration' ),
			'i18n_do_refund'                     => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'cpt-woo-integration' ),
			'i18n_delete_refund'                 => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'cpt-woo-integration' ),
			'i18n_delete_tax'                    => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'cpt-woo-integration' ),
			'remove_item_meta'                   => __( 'Remove this item meta?', 'cpt-woo-integration' ),
			'name_label'                         => __( 'Name', 'cpt-woo-integration' ),
			'remove_label'                       => __( 'Remove', 'cpt-woo-integration' ),
			'click_to_toggle'                    => __( 'Click to toggle', 'cpt-woo-integration' ),
			'values_label'                       => __( 'Value(s)', 'cpt-woo-integration' ),
			'text_attribute_tip'                 => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'cpt-woo-integration' ),
			'visible_label'                      => __( 'Visible on the product page', 'cpt-woo-integration' ),
			'used_for_variations_label'          => __( 'Used for variations', 'cpt-woo-integration' ),
			'new_attribute_prompt'               => __( 'Enter a name for the new attribute term:', 'cpt-woo-integration' ),
			'calc_totals'                        => __( 'Recalculate totals? This will calculate taxes based on the customers country (or the store base country) and update totals.', 'cpt-woo-integration' ),
			'copy_billing'                       => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'cpt-woo-integration' ),
			'load_billing'                       => __( "Load the customer's billing information? This will remove any currently entered billing information.", 'cpt-woo-integration' ),
			'load_shipping'                      => __( "Load the customer's shipping information? This will remove any currently entered shipping information.", 'cpt-woo-integration' ),
			'featured_label'                     => __( 'Featured', 'cpt-woo-integration' ),
			'prices_include_tax'                 => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
			'tax_based_on'                       => esc_attr( get_option( 'woocommerce_tax_based_on' ) ),
			'round_at_subtotal'                  => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
			'no_customer_selected'               => __( 'No customer selected', 'cpt-woo-integration' ),
			'plugin_url'                         => WC()->plugin_url(),
			'ajax_url'                           => admin_url( 'admin-ajax.php' ),
			'order_item_nonce'                   => wp_create_nonce( 'order-item' ),
			'add_attribute_nonce'                => wp_create_nonce( 'add-attribute' ),
			'save_attributes_nonce'              => wp_create_nonce( 'save-attributes' ),
			'add_attributes_and_variations'      => wp_create_nonce( 'add-attributes-and-variations' ),
			'calc_totals_nonce'                  => wp_create_nonce( 'calc-totals' ),
			'get_customer_details_nonce'         => wp_create_nonce( 'get-customer-details' ),
			'search_products_nonce'              => wp_create_nonce( 'search-products' ),
			'grant_access_nonce'                 => wp_create_nonce( 'grant-access' ),
			'revoke_access_nonce'                => wp_create_nonce( 'revoke-access' ),
			'add_order_note_nonce'               => wp_create_nonce( 'add-order-note' ),
			'delete_order_note_nonce'            => wp_create_nonce( 'delete-order-note' ),
			'calendar_image'                     => WC()->plugin_url() . '/assets/images/calendar.png',
			'post_id'                            => $this->is_order_meta_box_screen( $screen_id ) && isset( $order_or_post_object ) ? \Automattic\WooCommerce\Utilities\OrderUtil::get_post_or_order_id( $order_or_post_object ) : $post_id,
			'base_country'                       => WC()->countries->get_base_country(),
			'currency_format_num_decimals'       => wc_get_price_decimals(),
			'currency_format_symbol'             => get_woocommerce_currency_symbol( $currency ),
			'currency_format_decimal_sep'        => esc_attr( wc_get_price_decimal_separator() ),
			'currency_format_thousand_sep'       => esc_attr( wc_get_price_thousand_separator() ),
			'currency_format'                    => esc_attr( str_replace( [ '%1$s', '%2$s' ], [ '%s', '%v' ], get_woocommerce_price_format() ) ), // For accounting JS.
			'rounding_precision'                 => wc_get_rounding_precision(),
			'tax_rounding_mode'                  => wc_get_tax_rounding_mode(),
			'product_types'                      => array_unique( array_merge( [ 'simple', 'grouped', 'variable', 'external' ], array_keys( wc_get_product_types() ) ) ),
			'i18n_download_permission_fail'      => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'cpt-woo-integration' ),
			'i18n_permission_revoke'             => __( 'Are you sure you want to revoke access to this download?', 'cpt-woo-integration' ),
			'i18n_tax_rate_already_exists'       => __( 'You cannot add the same tax rate twice!', 'cpt-woo-integration' ),
			'i18n_delete_note'                   => __( 'Are you sure you wish to delete this note? This action cannot be undone.', 'cpt-woo-integration' ),
			'i18n_apply_coupon'                  => __( 'Enter a coupon code to apply. Discounts are applied to line totals, before taxes.', 'cpt-woo-integration' ),
			'i18n_add_fee'                       => __( 'Enter a fixed amount or percentage to apply as a fee.', 'cpt-woo-integration' ),
			'i18n_attribute_name_placeholder'    => __( 'New attribute', 'cpt-woo-integration' ),
			'i18n_product_simple_tip'            => __( '<b>Simple –</b> covers the vast majority of any products you may sell. Simple products are shipped and have no options. For example, a book.', 'cpt-woo-integration' ),
			'i18n_product_grouped_tip'           => __( '<b>Grouped –</b> a collection of related products that can be purchased individually and only consist of simple products. For example, a set of six drinking glasses.', 'cpt-woo-integration' ),
			'i18n_product_external_tip'          => __( '<b>External or Affiliate –</b> one that you list and describe on your website but is sold elsewhere.', 'cpt-woo-integration' ),
			'i18n_product_variable_tip'          => __( '<b>Variable –</b> a product with variations, each of which may have a different SKU, price, stock option, etc. For example, a t-shirt available in different colors and/or sizes.', 'cpt-woo-integration' ),
			'i18n_product_other_tip'             => __( 'Product types define available product details and attributes, such as downloadable files and variations. They’re also used for analytics and inventory management.', 'cpt-woo-integration' ),
			'i18n_product_description_tip'       => __( 'Describe this product. What makes it unique? What are its most important features?', 'cpt-woo-integration' ),
			'i18n_product_short_description_tip' => __( 'Summarize this product in 1-2 short sentences. We’ll show it at the top of the page.', 'cpt-woo-integration' ),
			'i18n_save_attribute_variation_tip'  => __( 'Make sure you enter the name and values for each attribute.', 'cpt-woo-integration' ),
			/* translators: %1$s: maximum file size */
			'i18n_product_image_tip'             => sprintf( __( 'For best results, upload JPEG or PNG files that are 1000 by 1000 pixels or larger. Maximum upload file size: %1$s.', 'cpt-woo-integration' ), size_format( wp_max_upload_size() ) ),
			'i18n_remove_used_attribute_confirmation_message' => __( 'If you remove this attribute, customers will no longer be able to purchase some variations of this product.', 'cpt-woo-integration' ),
			'i18n_add_attribute_error_notice'    => __( 'Adding new attribute failed.', 'cpt-woo-integration' ),
		];

		wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $params );

		/* phpcs:disable */
		wp_enqueue_media();
		wp_register_script( 'wc-admin-product-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'media-models' ), $version );
		wp_register_script( 'wc-admin-variation-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product-variation' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'serializejson', 'media-models', 'backbone', 'jquery-ui-sortable', 'wc-backbone-modal' ), $version );

		wp_enqueue_script( 'wp-data' );
		wp_enqueue_script( 'wp-edit-post' );

		wp_enqueue_script( 'wc-admin-product-meta-boxes' );
		wp_enqueue_script( 'wc-admin-variation-meta-boxes' );
		$params = array(
			'post_id'                             => isset( $post->ID ) ? $post->ID : '',
			'plugin_url'                          => WC()->plugin_url(),
			'ajax_url'                            => admin_url( 'admin-ajax.php' ),
			'woocommerce_placeholder_img_src'     => wc_placeholder_img_src(),
			'add_variation_nonce'                 => wp_create_nonce( 'add-variation' ),
			'link_variation_nonce'                => wp_create_nonce( 'link-variations' ),
			'delete_variations_nonce'             => wp_create_nonce( 'delete-variations' ),
			'load_variations_nonce'               => wp_create_nonce( 'load-variations' ),
			'save_variations_nonce'               => wp_create_nonce( 'save-variations' ),
			'bulk_edit_variations_nonce'          => wp_create_nonce( 'bulk-edit-variations' ),
			/* translators: %d: Number of variations */
			'i18n_link_all_variations'            => esc_js( sprintf( __( 'Do you want to generate all variations? This will create a new variation for each and every possible combination of variation attributes (max %d per run).', 'cpt-woo-integration' ), Constants::is_defined( 'WC_MAX_LINKED_VARIATIONS' ) ? Constants::get_constant( 'WC_MAX_LINKED_VARIATIONS' ) : 50 ) ),
			'i18n_enter_a_value'                  => esc_js( __( 'Enter a value', 'cpt-woo-integration' ) ),
			'i18n_enter_menu_order'               => esc_js( __( 'Variation menu order (determines position in the list of variations)', 'cpt-woo-integration' ) ),
			'i18n_enter_a_value_fixed_or_percent' => esc_js( __( 'Enter a value (fixed or %)', 'cpt-woo-integration' ) ),
			'i18n_delete_all_variations'          => esc_js( __( 'Are you sure you want to delete all variations? This cannot be undone.', 'cpt-woo-integration' ) ),
			'i18n_last_warning'                   => esc_js( __( 'Last warning, are you sure?', 'cpt-woo-integration' ) ),
			'i18n_choose_image'                   => esc_js( __( 'Choose an image', 'cpt-woo-integration' ) ),
			'i18n_set_image'                      => esc_js( __( 'Set variation image', 'cpt-woo-integration' ) ),
			'i18n_variation_added'                => esc_js( __( '1 variation added', 'cpt-woo-integration' ) ),
			'i18n_variations_added'               => esc_js( __( '%qty% variations added', 'cpt-woo-integration' ) ),
			'i18n_remove_variation'               => esc_js( __( 'Are you sure you want to remove this variation?', 'cpt-woo-integration' ) ),
			'i18n_scheduled_sale_start'           => esc_js( __( 'Sale start date (YYYY-MM-DD format or leave blank)', 'cpt-woo-integration' ) ),
			'i18n_scheduled_sale_end'             => esc_js( __( 'Sale end date (YYYY-MM-DD format or leave blank)', 'cpt-woo-integration' ) ),
			'i18n_edited_variations'              => esc_js( __( 'Save changes before changing page?', 'cpt-woo-integration' ) ),
			'i18n_variation_count_single'         => esc_js( __( '1 variation', 'cpt-woo-integration' ) ),
			'i18n_variation_count_plural'         => esc_js( __( '%qty% variations', 'cpt-woo-integration' ) ),
			'variations_per_page'                 => absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) ),
		);
		wp_localize_script( 'wc-admin-variation-meta-boxes', 'woocommerce_admin_meta_boxes_variations', $params );
		
		wp_enqueue_script( 'woocommerce_quick-edit', WC()->plugin_url() . '/assets/js/admin/quick-edit' . $suffix . '.js', array( 'jquery', 'woocommerce_admin' ), $version );
		$params = array(
			'strings' => array(
				'allow_reviews' => esc_js( __( 'Enable reviews', 'cpt-woo-integration' ) ),
			),
		);
		wp_localize_script( 'woocommerce_quick-edit', 'woocommerce_quick_edit', $params );

	}

	/**
	 * Helper function to determine whether the current screen is an order edit screen.
	 *
	 * @param string $screen_id Screen ID.
	 *
	 * @return bool Whether the current screen is an order edit screen.
	 */
	private function is_order_meta_box_screen( $screen_id ) {
		$screen_id = str_replace( 'edit-', '', $screen_id );

		$types_with_metaboxes_screen_ids = array_filter(
			array_map(
				'wc_get_page_screen_id',
				wc_get_order_types( 'order-meta-boxes' )
			)
		);

		return in_array( $screen_id, $types_with_metaboxes_screen_ids, true );
	}

}

