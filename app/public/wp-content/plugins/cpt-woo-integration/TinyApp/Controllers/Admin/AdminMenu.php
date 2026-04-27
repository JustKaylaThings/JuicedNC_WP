<?php

namespace TinySolutions\cptwooint\Controllers\Admin;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

use TinySolutions\cptwooint\Helpers\Fns;
use TinySolutions\cptwooint\Loader;
use TinySolutions\cptwooint\Traits\SingletonTrait;

/**
 * Sub menu class
 *
 * @author Mostafa <mostafa.soufi@hotmail.com>
 */
class AdminMenu {

	/**
	 * Singleton
	 */
	use SingletonTrait;

	/**
	 * Parent Menu Page Slug
	 */
	const MENU_PAGE_SLUG = 'cptwooint-admin';

	/**
	 * Menu capability
	 */
	const MENU_CAPABILITY = 'manage_options';

	/**
	 * @var object
	 */
	protected $loader;

	/**
	 * Class Constructor
	 */
	private function __construct() {
		$this->loader = Loader::instance();
		$this->loader->add_action( 'admin_menu', $this, 'register_admin_menu' );
	}

	/**
	 * Register submenu
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		wp_enqueue_style( 'cptwooint-settings' );
		add_menu_page(
			esc_html__( 'WC Integration', 'cpt-woo-integration' ),
			esc_html__( 'WC Integration', 'cpt-woo-integration' ),
			self::MENU_CAPABILITY,
			self::MENU_PAGE_SLUG,
			[ $this, 'page_callback' ],
			'dashicons-cart',
			'55.6'
		);
		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__( 'WC Integration', 'cpt-woo-integration' ),
			'<span class="cptwooint-is-submenu" ><span class="dashicons dashicons-arrow-right-alt" ></span>' . esc_html__( 'WC Integration', 'cpt-woo-integration' ) . '</span>',
			self::MENU_CAPABILITY,
			self::MENU_PAGE_SLUG,
		);
		$menu_link_part = admin_url( 'admin.php?page=cptwooint-admin' );

		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__( 'Short Code', 'cpt-woo-integration' ),
			'<span class="cptwooint-is-submenu" ><span class="dashicons dashicons-arrow-right-alt" ></span>' . esc_html__( 'Short Code', 'cpt-woo-integration' ) . '</span>',
			self::MENU_CAPABILITY,
			$menu_link_part . '#/shortcode'
		);
		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__( 'Styles', 'cpt-woo-integration' ),
			'<span class="cptwooint-is-submenu" ><span class="dashicons dashicons-arrow-right-alt" ></span>' . esc_html__( 'Styles', 'cpt-woo-integration' ) . '</span>',
			self::MENU_CAPABILITY,
			$menu_link_part . '#/styles'
		);
		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__( 'Useful Plugins', 'cpt-woo-integration' ),
			'<span class="cptwooint-is-submenu" ><span class="dashicons dashicons-arrow-right-alt" ></span>' . esc_html__( 'Useful Plugins', 'cpt-woo-integration' ) . '</span>',
			self::MENU_CAPABILITY,
			$menu_link_part . '#/plugins'
		);
		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__( 'Contacts Support', 'cpt-woo-integration' ),
			'<span class="cptwooint-is-submenu" ><span class="dashicons dashicons-arrow-right-alt" ></span>' . esc_html__( 'Contacts Support', 'cpt-woo-integration' ) . '</span>',
			self::MENU_CAPABILITY,
			$menu_link_part . '#/support'
		);

		$tab_title = apply_filters( 'cptwooint/add/get-pro/submenu/label', esc_html__( 'Buy Pro', 'cpt-woo-integration' ) );

		$title = '<span class="cptwooint-submenu" style="color: #6BBE66;"> <span class="dashicons-icons" style="transform: rotateX(180deg) rotate(180deg);font-size: 18px;"></span> ' . $tab_title . '</span>';

		add_submenu_page(
			self::MENU_PAGE_SLUG,
			$tab_title,
			$title,
			self::MENU_CAPABILITY,
			'cptwooint-get-pro',
			[ $this, 'pro_pages' ]
		);

		do_action( 'cptwooint/add/more/submenu', self::MENU_PAGE_SLUG, self::MENU_CAPABILITY );
	}

	/**
	 * Render submenu
	 *
	 * @return void
	 */
	public function page_callback() {
		echo '<div class="wrap"><div id="cptwooint_root"></div></div>';
	}

	/**
	 * @return void
	 */
	public function pro_pages() {
		?>
		<div class="wrap cptwooint-license-wrap">
			<style>
				/* Base Styles */
				.cptwooint-pro-page-wrapper {
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
					max-width: 1200px;
					margin: 0 auto;
					padding: 20px;
				}
				.cptwooint-pro-page-wrapper * {
					box-sizing: border-box;
				}
				.cptwooint-pro-page-wrapper a {
					text-decoration: none;
				}
				.current .cptwooint-submenu,
				.current .dashicons {
					color: #2563eb !important;
				}
				.media_page_cptwooint-get-pro #wpwrap {
					background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
				}

				/* Page Header */
				.cptwooint-pro-header {
					text-align: center;
					margin-bottom: 40px;
				}
				.cptwooint-pro-header h1 {
					font-size: 36px;
					font-weight: 700;
					color: #1e293b;
					margin: 0 0 12px 0;
				}
				.cptwooint-pro-header p {
					font-size: 18px;
					color: #64748b;
					margin: 0;
				}

				/* Cards Container */
				#cptwooint-pro-page-wrapper {
					display: grid;
					grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
					gap: 24px;
					margin-bottom: 40px;
				}

				/* Pro Features Card */
				.cptwooint-pro-card {
					background: #fff;
					border-radius: 16px;
					box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
					overflow: hidden;
					transition: transform 0.3s ease, box-shadow 0.3s ease;
				}
				.cptwooint-pro-card:hover {
					transform: translateY(-4px);
					box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
				}

				/* Card Header */
				.cptwooint-card-header {
					background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
					padding: 32px;
					text-align: center;
				}
				.cptwooint-card-header .badge {
					display: inline-flex;
					align-items: center;
					gap: 8px;
					background: rgba(255, 255, 255, 0.2);
					padding: 8px 16px;
					border-radius: 50px;
					margin-bottom: 16px;
				}
				.cptwooint-card-header .badge svg {
					width: 20px;
					height: 20px;
					fill: #fbbf24;
				}
				.cptwooint-card-header .badge span {
					color: #fff;
					font-size: 14px;
					font-weight: 600;
				}
				.cptwooint-card-header h2 {
					color: #fff;
					font-size: 28px;
					font-weight: 700;
					margin: 0;
				}
				.cptwooint-card-header p {
					color: rgba(255, 255, 255, 0.8);
					font-size: 14px;
					margin: 8px 0 0 0;
				}

				/* Feature List */
				.cptwooint-features-list {
					padding: 24px;
					margin: 0;
					list-style: none;
				}
				.cptwooint-features-list li {
					display: flex;
					align-items: flex-start;
					gap: 12px;
					padding: 12px 0;
					border-bottom: 1px solid #f1f5f9;
					font-size: 15px;
					color: #334155;
				}
				.cptwooint-features-list li:last-child {
					border-bottom: none;
				}
				.cptwooint-features-list .check-icon {
					flex-shrink: 0;
					width: 24px;
					height: 24px;
					background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
					border-radius: 50%;
					display: flex;
					align-items: center;
					justify-content: center;
				}
				.cptwooint-features-list .check-icon svg {
					width: 14px;
					height: 14px;
					fill: none;
					stroke: #fff;
					stroke-width: 3;
				}

				/* Card Footer */
				.cptwooint-card-footer {
					padding: 24px;
					background: #f8fafc;
					display: flex;
					gap: 12px;
				}
				.cptwooint-btn {
					flex: 1;
					display: inline-flex;
					align-items: center;
					justify-content: center;
					gap: 8px;
					padding: 14px 24px;
					border-radius: 10px;
					font-size: 15px;
					font-weight: 600;
					cursor: pointer;
					transition: all 0.2s ease;
					border: none;
				}
				.cptwooint-btn-primary {
					background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
					color: #fff !important;
					box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.4);
				}
				.cptwooint-btn-primary:hover {
					background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
					transform: translateY(-2px);
					box-shadow: 0 6px 20px 0 rgba(37, 99, 235, 0.5);
				}
				.cptwooint-btn-secondary {
					background: #fff;
					color: #2563eb !important;
					border: 2px solid #e2e8f0;
				}
				.cptwooint-btn-secondary:hover {
					border-color: #2563eb;
					background: #eff6ff;
				}

				/* Guarantee Card */
				.cptwooint-guarantee-card {
					background: #fff;
					border-radius: 16px;
					box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
					padding: 40px;
					text-align: center;
				}
				.cptwooint-guarantee-badge {
					display: inline-flex;
					align-items: center;
					justify-content: center;
					width: 80px;
					height: 80px;
					background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
					border-radius: 50%;
					margin-bottom: 24px;
				}
				.cptwooint-guarantee-badge svg {
					width: 40px;
					height: 40px;
					fill: #fff;
				}
				.cptwooint-guarantee-card h3 {
					font-size: 24px;
					font-weight: 700;
					color: #1e293b;
					margin: 0 0 16px 0;
				}
				.cptwooint-guarantee-card > p {
					font-size: 15px;
					color: #64748b;
					line-height: 1.7;
					margin: 0 0 24px 0;
				}
				.cptwooint-info-boxes {
					display: flex;
					flex-direction: column;
					gap: 12px;
				}
				.cptwooint-info-box {
					background: #f0fdf4;
					border: 1px solid #bbf7d0;
					border-radius: 10px;
					padding: 16px;
					text-align: left;
				}
				.cptwooint-info-box p {
					margin: 0;
					font-size: 14px;
					color: #166534;
					line-height: 1.6;
				}

				/* Discount Banner */
				.cptwooint-discount-banner {
					background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
					border: 2px solid #bbf7d0;
					border-radius: 16px;
					padding: 32px;
					text-align: center;
					margin-bottom: 40px;
				}
				.cptwooint-discount-banner p {
					font-size: 18px;
					color: #166534;
					line-height: 1.7;
					margin: 0;
				}
				.cptwooint-discount-banner .highlight {
					color: #dc2626;
					font-weight: 700;
				}
				.cptwooint-discount-banner a {
					color: #2563eb;
					font-weight: 600;
				}
				.cptwooint-discount-banner a:hover {
					text-decoration: underline;
				}

				/* FAQ Section */
				.cptwooint-faq-section {
					background: #fff;
					border-radius: 16px;
					box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
					padding: 40px;
				}
				.cptwooint-faq-section h2 {
					font-size: 28px;
					font-weight: 700;
					color: #1e293b;
					margin: 0 0 32px 0;
					text-align: center;
				}
				.cptwooint-faq-list {
					list-style: none;
					padding: 0;
					margin: 0;
					display: grid;
					grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
					gap: 24px;
				}
				.cptwooint-faq-item {
					padding: 24px;
					background: #f8fafc;
					border-radius: 12px;
					transition: background 0.2s ease;
				}
				.cptwooint-faq-item:hover {
					background: #f1f5f9;
				}
				.cptwooint-faq-item h3 {
					font-size: 16px;
					font-weight: 600;
					color: #1e293b;
					margin: 0 0 8px 0;
				}
				.cptwooint-faq-item p {
					font-size: 14px;
					color: #64748b;
					line-height: 1.6;
					margin: 0;
				}
				.cptwooint-faq-item a {
					color: #2563eb;
					font-weight: 500;
				}
				.cptwooint-faq-item a:hover {
					text-decoration: underline;
				}

				/* Responsive */
				@media only screen and (max-width: 768px) {
					#cptwooint-pro-page-wrapper {
						grid-template-columns: 1fr;
					}
					.cptwooint-faq-list {
						grid-template-columns: 1fr;
					}
					.cptwooint-card-footer {
						flex-direction: column;
					}
					.cptwooint-pro-header h1 {
						font-size: 28px;
					}
				}
			</style>

			<div class="cptwooint-pro-page-wrapper">
				<!-- Page Header -->
				<div class="cptwooint-pro-header">
					<h1>Upgrade to Pro</h1>
					<p>Unlock powerful features to supercharge your WooCommerce integration</p>
				</div>

				<!-- Cards Grid -->
				<div id="cptwooint-pro-page-wrapper">
					<!-- Pro Features Card -->
					<div class="cptwooint-pro-card">
						<div class="cptwooint-card-header">
							<div class="badge">
								<svg viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
								<span>Premium</span>
							</div>
							<h2>Pro Features</h2>
							<p>Everything you need for advanced integration</p>
						</div>

						<ul class="cptwooint-features-list">
							<?php foreach ( Fns::pro_feature_list() as $item ) { ?>
								<li>
									<span class="check-icon">
										<svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
									</span>
									<span><?php echo esc_attr( $item['title'] ); ?></span>
								</li>
							<?php } ?>
						</ul>

						<div class="cptwooint-card-footer">
							<a href="https://checkout.freemius.com/plugin/13672/plan/22848/licenses/1/" target="_blank" class="cptwooint-btn cptwooint-btn-primary">
								<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
								Buy Now
							</a>
							<a href="https://www.wptinysolutions.com/tiny-products/cpt-woo-integration/" target="_blank" class="cptwooint-btn cptwooint-btn-secondary">
								<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
								Website
							</a>
						</div>
					</div>

					<!-- Money Back Guarantee Card -->
					<div class="cptwooint-guarantee-card">
						<div class="cptwooint-guarantee-badge">
							<svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
						</div>
						<h3>30-Day Money Back Guarantee</h3>
						<p>
							You are fully protected by our 100% Money Back Guarantee. If during the next 30 days you experience an issue that makes the plugin unusable and we are unable to resolve it, we'll happily offer a full refund.
						</p>

						<div class="cptwooint-info-boxes">
							<div class="cptwooint-info-box">
								<p><strong>Yearly License:</strong> Entitles you to 1 year of updates and support. Your subscription will auto-renew each year until cancelled.</p>
							</div>
							<div class="cptwooint-info-box">
								<p><strong>Lifetime License:</strong> Entitles you to updates and support forever. It's a one-time payment, not a subscription.</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Discount Banner -->
				<div class="cptwooint-discount-banner">
					<p>
						Are you enjoying the free version? Have valuable feedback to share?
						We might have a special <span class="highlight">discount</span> waiting for you!
						<br>
						Contact us at <a href="https://help.wptinysolutions.com/" target="_blank">help.wptinysolutions.com</a> to learn more about our current promotions.
					</p>
				</div>

				<!-- FAQ Section -->
				<div class="cptwooint-faq-section">
					<h2>Frequently Asked Questions</h2>
					<ul class="cptwooint-faq-list">
						<li class="cptwooint-faq-item">
							<h3>Is there a setup fee?</h3>
							<p>No. There are no setup fees on any of our plans.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>Can I cancel my account at any time?</h3>
							<p>Yes, simply cancel your account from your Account panel anytime.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>What's the time span for contracts?</h3>
							<p>All plans are year-to-year unless you purchase a lifetime plan.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>Do you offer a renewals discount?</h3>
							<p>Yes, you get 10% discount for all annual plan automatic renewals.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>What payment methods are accepted?</h3>
							<p>We accept Visa, Mastercard, American Express, and PayPal.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>Do you offer refunds?</h3>
							<p>Yes! We'll refund 100% if we can't resolve an issue that makes the plugin unusable.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>Do I get updates for the premium plugin?</h3>
							<p>Yes! Automatic updates are available free as long as you're a paying customer.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>Do you offer support if I need help?</h3>
							<p>Yes! We provide top-notch customer support via our support page.</p>
						</li>
						<li class="cptwooint-faq-item">
							<h3>I have other pre-sale questions?</h3>
							<p>Ask us anything through our <a href="https://help.wptinysolutions.com/" target="_blank">Contact Us</a> page.</p>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
}
