<?php
/**
 * Main FilterHooks class.
 *
 * @package TinySolutions\WM
 */

namespace TinySolutions\cptwooint\Hooks;

defined( 'ABSPATH' ) || exit();

use TinySolutions\cptwooint\Helpers\Fns;
use TinySolutions\cptwooint\Loader;
use TinySolutions\cptwooint\Models\CPTOrderItemProduct;
use TinySolutions\cptwooint\Models\CPTProductDataStore;
use TinySolutions\cptwooint\Traits\SingletonTrait;
use WC_Product;

/**
 * Main FilterHooks class.
 */
class FilterHooks {
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
		// Search Support.
		$this->loader->add_filter( 'woocommerce_product_data_store_cpt_get_products_query', $this, 'get_products_query_with_cpt', 8 );
		// Plugins Setting Page.
		$this->loader->add_filter( 'body_class', $this, 'add_body_class' );
		$this->loader->add_filter( 'plugin_row_meta', $this, 'plugin_row_meta', 10, 2 );
		$this->loader->add_filter( 'plugin_action_links_' . CPTWI_BASENAME, $this, 'plugins_setting_links' );
		$this->loader->add_filter( 'woocommerce_data_stores', $this, 'cptwoo_data_stores', 99 );
		// Show meta value after post content THis will be shortcode.
		$this->loader->add_filter( 'the_content', $this, 'display_price_and_cart_button' );
		// Order Product Class.
		$this->loader->add_filter( 'woocommerce_get_order_item_classname', $this, 'get_order_item_classname', 12, 3 );
		// Checkout Page issue. Plugin Support.
		$this->loader->add_filter( 'woocommerce_checkout_create_order_line_item_object', $this, 'checkout_create_order_line_item_object', 12, 4 );
		// Add suggestions to the product tabs.
		$this->loader->add_filter( 'woocommerce_product_data_tabs', $this, 'product_data_tabs', 20 );
		$this->loader->add_filter( 'woocommerce_format_sale_price', $this, 'format_sale_price', 20 );
		$this->loader->add_filter( 'is_woocommerce', $this, 'is_woocommerce', 20 );
		// Generating dynamically the product "sale price".
		$this->loader->add_filter( 'woocommerce_product_get_sale_price', $this, 'custom_dynamic_sale_price', 10, 2 );
		$this->loader->add_filter( 'woocommerce_product_reviews_tab_title', $this, 'product_reviews_tab_title', 12, 2 );
		// Generating dynamically the product "regular price".
		$this->loader->add_filter( 'woocommerce_product_get_regular_price', $this, 'custom_dynamic_regular_price', 10, 2 );
		$this->loader->add_filter( 'woocommerce_account_downloads_columns', $this, 'account_downloads_columns', 15 );
		// Custom Page Template.
		$this->loader->add_filter( 'template_include', $this, 'woo_post_template', 99 );
		$this->loader->add_filter( 'comments_template', $this, 'comments_template_loader', 50 );
		// Taxonomy Support.
		$this->loader->add_filter( 'woocommerce_taxonomy_objects_product_cat', $this, 'product_cat', 15 );
		$this->loader->add_filter( 'woocommerce_taxonomy_objects_product_tag', $this, 'product_tag', 15 );
		// Get Edit Post.
		$this->loader->add_filter( 'get_edit_post_link', $this, 'get_edit_post_link', 15, 3 );
		$this->loader->add_filter( 'woocommerce_is_purchasable', $this, 'allow_add_to_cart_without_price', 10, 2 );
		$this->loader->add_filter( 'woocommerce_get_price_html', $this, 'custom_price_html_display', 99, 2 );
	}
	/**
	 * Modify WooCommerce price HTML for display (converted pricing).
	 *
	 * @param string     $price_html The original price HTML.
	 * @param WC_Product $product    The product object.
	 * @return string
	 */
	public function custom_price_html_display( $price_html, $product ) {
		if ( ! $product instanceof \WC_Product ) {
			return $price_html;
		}
		$post_type = get_post_type( $product->get_id() );
		if ( ! Fns::is_supported( $post_type ) ) {
			return $price_html;
		}
		if ( $product->is_type( 'variable' ) ) {
			$min_regular = $product->get_variation_regular_price( 'min', true );
			$max_regular = $product->get_variation_regular_price( 'max', true );
			if ( '' === $min_regular && '' === $max_regular ) {
				return '';
			}
			return wc_price( $min_regular ) . ' – ' . wc_price( $max_regular );
		}
		// Simple product (regular + sale).
		$regular_price = $product->get_regular_price();
		$sale_price    = $product->get_sale_price();

		// If regular price is empty (not set), hide price entirely.
		if ( '' === $regular_price || is_null( $regular_price ) ) {
			return '';
		}

		if ( $sale_price && $sale_price < $regular_price ) {
			return wc_format_sale_price( $regular_price, $sale_price );
		}
		return wc_price( $regular_price );
	}
	/**
	 * Make products without price purchasable.
	 *
	 * @param bool   $purchasable is bool.
	 * @param object $product product.
	 *
	 * @return mixed|true
	 */
	public function allow_add_to_cart_without_price( $purchasable, $product ) {
		if ( ! $product instanceof \WC_Product ) {
			return $purchasable;
		}
		$post_type = get_post_type( $product->get_id() );
		if ( ! Fns::is_supported( $post_type ) ) {
			return $purchasable;
		}
		$price = Fns::get_price( $post_type, $product->get_id() );
		if ( '' === $price || null === $price ) {
			return $purchasable;
		}
		return true;
	}

	/**
	 * Filters and generates the edit post link for supported post types.
	 *
	 * This method checks if the given post type is supported and the user has permission to edit the post.
	 * It then builds a custom admin edit link based on the post type and context.
	 *
	 * Special handling is included for core WordPress block-based post types such as:
	 * - `wp_template`
	 * - `wp_template_part`
	 * - `wp_navigation`
	 *
	 * @param string $link     The original edit link.
	 * @param int    $post_ID  The ID of the post.
	 * @param string $context  The link context. If set to 'display', ampersands are HTML-encoded.
	 *
	 * @return string|null The customized edit link if applicable, otherwise the original link or null.
	 */
	public function get_edit_post_link( $link, $post_ID, $context ) {
		$post = get_post( $post_ID );
		if ( ! Fns::is_supported( $post->post_type ) ) {
			return $link;
		}
		if ( ! $post ) {
			return $link;
		}
		if ( 'revision' === $post->post_type ) {
			$action = '';
		} elseif ( 'display' === $context ) {
			$action = '&amp;action=edit';
		} else {
			$action = '&action=edit';
		}
		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}
		$link = '';
		if ( 'wp_template' === $post->post_type || 'wp_template_part' === $post->post_type ) {
			$slug = urlencode( get_stylesheet() . '//' . $post->post_name );
			$link = admin_url( sprintf( $post_type_object->_edit_link, $post->post_type, $slug ) );
		} elseif ( 'wp_navigation' === $post->post_type ) {
			$link = admin_url( sprintf( $post_type_object->_edit_link, (string) $post->ID ) );
		} elseif ( $post_type_object->_edit_link ) {
			$link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
		}
		/**
		 * Filters the post edit link.
		 *
		 * @since 2.3.0
		 *
		 * @param string $link    The edit link.
		 * @param int    $post_id Post ID.
		 * @param string $context The link context. If set to 'display' then ampersands
		 *                        are encoded.
		 */
		return $link;
	}

	/**
	 * @param $cpt
	 *
	 * @return array
	 */
	public function product_tag( $cpt ) {
		$supported_types = Fns::supported_post_types();
		if ( empty( $supported_types ) ) {
			return $cpt;
		}
		foreach ( $supported_types as $type ) {
			if ( ! Fns::is_tag_enabled( $type ) ) {
				continue;
			}
			$cpt[] = $type;
		}
		return $cpt;
	}

	/**
	 * @param $cpt
	 *
	 * @return array
	 */
	public function product_cat( $cpt ) {
		$supported_types = Fns::supported_post_types();
		if ( empty( $supported_types ) ) {
			return $cpt;
		}
		foreach ( $supported_types as $type ) {
			if ( ! Fns::is_cat_enabled( $type ) ) {
				continue;
			}
			$cpt[] = $type;
		}
		return $cpt;
	}

	/**
	 * Modifies the WP_Query arguments to include custom post types.
	 *
	 * This function adds supported custom post types to the `post_type` parameter
	 * of the WordPress query arguments. It retrieves the supported post types
	 * using the `Fns::supported_post_types()` function and merges them into the
	 * existing `post_type` argument.
	 *
	 * @param array $wp_query_args The original WordPress query arguments. - 'post_type' (string|array): The post types to query.
	 * @return array Modified WordPress query arguments including supported custom post types.
	 */
	public function get_products_query_with_cpt( $wp_query_args ) {
		if ( ! apply_filters( 'cptwoo_get_products_query_with_cpt', is_admin() && wp_doing_ajax(), $wp_query_args ) ) {
			return $wp_query_args;
		}
		$supported = Fns::supported_post_types();
		if ( empty( $supported ) ) {
			return $wp_query_args;
		}
		if ( is_array( $wp_query_args['post_type'] ) ) {
			$wp_query_args['post_type'] = array_merge( $wp_query_args['post_type'], $supported );
		} else {
			$wp_query_args['post_type'] = array_merge( [ $wp_query_args['post_type'] ], $supported );
		}
		return $wp_query_args;
	}
	/**
	 * Add specific body class when the Wishlist page is opened
	 *
	 * @param array $classes Existing boy classes.
	 *
	 * @return array
	 */
	public function add_body_class( $classes ) {
		$classes[] = 'cpt-woo-integration cptwooint-v--' . CPTWI_VERSION;
		if ( defined( 'CPTWIP_VERSION' ) ) {
			$classes[] = 'cpt-woo-integration-pro cptwoointpro-v--' . CPTWIP_VERSION;
		}
		if ( ! cptwooint()->has_pro() ) {
			$classes[] = 'cptwoointpro-expired';
		}
		$supported_types = Fns::supported_post_types();
		if ( empty( $supported_types ) ) {
			return $classes;
		}
		foreach ( $supported_types as $type ) {
			$classes[] = 'cpt-woo-int-type--' . $type;
		}
		return $classes;
	}


	/**
	 * Load comments template.
	 *
	 * @param string $template template to load.
	 * @return string
	 */
	public static function comments_template_loader( $template ) {
		$type         = get_post_type();
		$is_supported = Fns::is_review_enabled( $type );
		$is_single    = Fns::is_single_page_like_product_page( $type );
		if ( $is_supported ) {
			return $template;
		}
		$check_dirs = [
			trailingslashit( get_stylesheet_directory() ) ,
			trailingslashit( get_template_directory() ),
		];
		foreach ( $check_dirs as $dir ) {
			if ( file_exists( trailingslashit( $dir ) . 'comments.php' ) ) {
				return trailingslashit( $dir ) . 'comments.php';
			}
		}
		return $template;
	}

	/**
	 * Change Archive Page Template.
	 *
	 * @param string $template file path.
	 * @return string
	 */
	public function woo_post_template( $template ) {
		$post_type = get_query_var( 'post_type' );
		if ( empty( $post_type ) ) {
			return $template;
		}
		$tmp = '';
		if ( is_post_type_archive() && Fns::is_archive_page_like_shop_page( $post_type ) ) {
			$tmp = wc_locate_template( 'archive-product.php' );
		} elseif ( is_singular( $post_type ) && Fns::is_single_page_like_product_page( $post_type ) ) {
			$tmp = wc_locate_template( 'single-product.php' );
		}
		if ( ! empty( $tmp ) && file_exists( $tmp ) ) {
			return $tmp;
		}
		return $template;
	}

	/**
	 * @param string $title Tab Title.
	 * @return string
	 */
	public function product_reviews_tab_title( $title ) {
		global $product;
		if ( ! $product instanceof \WC_Product ) {
			return $title;
		}
		$post_type = get_post_type( $product->get_id() );
		if ( Fns::is_supported( $post_type ) && ! ( cptwooint()->has_pro() && Fns::is_review_enabled( $post_type ) ) ) {
			$title = esc_html__( 'Comments', 'cpt-woo-integration' );
		}
		return $title;
	}

	/**
	 * Column list
	 *
	 * @param array $column columns.
	 * @return mixed
	 */
	public function account_downloads_columns( $column ) {
		$column['post-type'] = apply_filters( 'cptwooint_account_downloads_columns_type', esc_html__( 'Type', 'cpt-woo-integration' ) );
		return $column;
	}

	/**
	 * Dunamic Reguler Price
	 *
	 * @param float  $regular_price price.
	 * @param object $product Product object.
	 * @return float|int|mixed|string
	 */
	public function custom_dynamic_regular_price( $regular_price, $product ) {
		$post_type = get_post_type( $product->get_id() );
		if ( ! Fns::is_supported( $post_type ) ) {
			return $regular_price;
		}
		$is_add_price_meta = Fns::is_add_cpt_meta( $post_type, 'default_price_meta_field' );
		if ( $is_add_price_meta ) {
			$regular_price = get_post_meta( $product->get_id(), '_regular_price', true );
			if ( is_null( $regular_price ) || '' === $regular_price ) {
				$regular_price = get_post_meta( $product->get_id(), '_price', true );
			}
		} else {
			$regular_price = Fns::cptwoo_get_price( $product->get_id() );
		}
		if ( is_null( $regular_price ) || '' === $regular_price ) {
			 $regular_price = Fns::cptwoo_get_price( $product->get_id() );
		}
		return $regular_price;
	}
	/**
	 * Sale Price
	 *
	 * @param float  $sale_price Price.
	 * @param object $product Product object.
	 * @return float|int|mixed|string
	 */
	public function custom_dynamic_sale_price( $sale_price, $product ) {
		$post_type = get_post_type( $product->get_id() );
		if ( ! Fns::is_supported( $post_type ) ) {
			return $sale_price;
		}
		$is_add_price_meta = Fns::is_add_cpt_meta( $post_type, 'default_price_meta_field' );
		if ( $is_add_price_meta ) {
			$sale_price = get_post_meta( $product->get_id(), '_price', true );
			if ( is_null( $sale_price ) || '' === $sale_price ) {
				$sale_price = get_post_meta( $product->get_id(), '_regular_price', true );
			}
		} else {
			$sale_price = Fns::cptwoo_get_price( $product->get_id(), 'sale_price' );
		}
		if ( is_null( $sale_price ) || '' === $sale_price ) {
			$sale_price = Fns::cptwoo_get_price( $product->get_id(), 'sale_price' );
		}
		return $sale_price;
	}

	/**
	 * @param $is_woocommerce
	 *
	 * @return bool
	 */
	public function is_woocommerce( $is_woocommerce ) {
		if ( $is_woocommerce ) {
			return $is_woocommerce;
		}
		$post_type = get_post_type( get_queried_object_id() );
		$_is       = ( is_post_type_archive( $post_type ) && Fns::is_archive_page_like_shop_page( $post_type ) ) || ( is_singular( $post_type ) );
		if ( 'page' !== $post_type && $_is ) {
			return true;
		}
		return $is_woocommerce;
	}

	/**
	 * @param $links
	 * @param $file
	 *
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $file == CPTWI_BASENAME ) {
			$report_url         = 'https://help.wptinysolutions.com/';
			$row_meta['issues'] = sprintf( '%2$s <a target="_blank" href="%1$s">%3$s</a>', esc_url( $report_url ), esc_html__( 'Facing issue?', 'cpt-woo-integration' ), '<span style="color: red">' . esc_html__( 'Please open a support ticket.', 'cpt-woo-integration' ) . '</span>' );

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}

	/**
	 * @param $types
	 *
	 * @return mixed
	 */
	public function format_sale_price( $price ) {
		return '<span class="cpt-price-wrapper">' . $price . '</span>';
	}

	/**
	 * Product data tabs filter
	 *
	 * Adds a new Extensions tab to the product data meta box.
	 *
	 * @param array $tabs Existing tabs.
	 *
	 * @return array
	 */
	public function product_data_tabs( $tabs ) {
		if ( ! Fns::is_supported( get_post_type( get_the_ID() ) ) ) {
			return $tabs;
		}
		unset(
			$tabs['marketplace-suggestions']
		);

		return $tabs;
	}

	/**
	 * @param $obj_WC_Order_Item_Product
	 * @param $cart_item_key
	 * @param $values
	 * @param $order
	 *
	 * @return mixed
	 */
	public function checkout_create_order_line_item_object( $obj_WC_Order_Item_Product, $cart_item_key, $values, $order ) {
		$product_id = isset( $values['product_id'] ) ? absint( $values['product_id'] ) : 0;
		if ( $product_id && Fns::is_supported( get_post_type( $product_id ) ) ) {
			return new CPTOrderItemProduct();
		}

		return $obj_WC_Order_Item_Product;
	}

	/***
	 * @param $content
	 *
	 * @return mixed|string
	 */
	public function display_price_and_cart_button( $content ) {
		$button_content    = '';
		$current_post_type = get_post_type( get_the_ID() );
		$options           = Fns::get_options();

		if ( ! empty( $options['price_after_content_post_types'] ) &&
			 is_array( $options['price_after_content_post_types'] ) &&
			 in_array( $current_post_type, $options['price_after_content_post_types'], true )
		) {
			$button_content .= do_shortcode( '[cptwooint_price/]' );
		}

		if (
			! empty( $options['cart_button_after_content_post_types'] ) &&
			is_array( $options['cart_button_after_content_post_types'] ) &&
			in_array( $current_post_type, $options['cart_button_after_content_post_types'], true )
		) {
			$button_content .= do_shortcode( '[cptwooint_cart_button/]' );
		}

		if ( ! empty( $button_content ) ) {
			$content .= '<div class="cpt-price-and-cart-button">';
			$content .= $button_content;
			$content .= '</div>';
		}
		return $content;
	}

	/**
	 * @param $stores
	 *
	 * @return mixed
	 */
	public function cptwoo_data_stores( $stores ) {
		$stores['product'] = CPTProductDataStore::class;

		return $stores;
	}

	/**
	 * @param $stores
	 *
	 * @return mixed
	 */
	public function get_order_item_classname( $classname, $item_type, $id ) {
		if ( 'WC_Order_Item_Product' === $classname ) {
			$classname = '\TinySolutions\cptwooint\Models\CPTOrderItemProduct';
		}

		return $classname;
	}

	/**
	 * @param array $links default plugin action link
	 *
	 * @return array [array] plugin action link
	 */
	public function plugins_setting_links( $links ) {
		$new_links                       = [];
		$new_links['cptwooint_settings'] = '<a href="' . admin_url( 'admin.php?page=cptwooint-admin' ) . '">' . esc_html__( 'Settings', 'cpt-woo-integration' ) . '</a>';
		if ( ! Fns::is_plugins_installed( 'cpt-woo-integration-pro/cpt-woo-integration-pro.php' ) ) {
			$links['cptwooint_pro'] = sprintf( '<a href="https://www.wptinysolutions.com/tiny-products/cpt-woo-integration/" target="_blank" style="color: #39b54a; font-weight: bold;">' . esc_html__( 'Go Pro', 'cpt-woo-integration' ) . '</a>' );
		}

		return array_merge( $new_links, $links );
	}
}
