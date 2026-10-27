<?php
/**
 * @wordpress-plugin
 * Plugin Name: Custom Post Type WooCommerce Integration
 * Plugin URI: https://www.wptinysolutions.com/tiny-products/cpt-woo-integration
 * Description: Integrate custom post-type with woocommerce. Sell Any Kind Of Custom Post
 * Version: 2.2.8
 * Author: Tiny Solutions
 * Author URI: https://www.wptinysolutions.com/
 * Requires at least: 5.2
 * Tested up to: 7.0
 * WC tested up to: 10.3.5
 * Text Domain: cpt-woo-integration
 * Domain Path: /languages
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @package TinySolutions\WM
 */

// Do not allow directly accessing this file.
use TinySolutions\cptwooint\Controllers\Installation;
use TinySolutions\cptwooint\CptWooIntegration;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Define cptwooint Constant.
 */

define( 'CPTWI_VERSION', '2.2.8' );

define( 'CPTWI_FILE', __FILE__ );

define( 'CPTWI_BASENAME', plugin_basename( CPTWI_FILE ) );

define( 'CPTWI_URL', plugins_url( '', CPTWI_FILE ) );

define( 'CPTWI_ABSPATH', dirname( CPTWI_FILE ) );

define( 'CPTWI_PATH', plugin_dir_path( __FILE__ ) );

/**
 * App Init.
 */

require_once CPTWI_PATH . 'vendor/autoload.php';

// Register Plugin Active Hook.
register_activation_hook(
	CPTWI_FILE,
	function () {
		Installation::activate();
		set_transient( 'cptwi_activation_redirect', 1, 30 );
	}
);
// Register Plugin Deactivate Hook.
register_deactivation_hook( CPTWI_FILE, [ Installation::class, 'deactivation' ] );

// Redirect to settings page after activation.
add_action(
	'admin_init',
	function () {
		if ( ! get_transient( 'cptwi_activation_redirect' ) ) {
			return;
		}
		delete_transient( 'cptwi_activation_redirect' );
		if ( wp_doing_ajax() || is_network_admin() || isset( $_GET['activate-multi'] ) ) { // phpcs:ignore
			return;
		}
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		wp_safe_redirect( admin_url( 'admin.php?page=cptwooint-admin' ) );
		exit;
	}
);
/**
 * @return CptWooIntegration
 */
function cptwooint() {
	return CptWooIntegration::instance();
}
add_action( 'plugins_loaded', 'cptwooint' );
