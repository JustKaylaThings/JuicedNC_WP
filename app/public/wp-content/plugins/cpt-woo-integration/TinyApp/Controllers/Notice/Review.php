<?php

namespace TinySolutions\cptwooint\Controllers\Notice;

use TinySolutions\cptwooint\Loader;
use TinySolutions\cptwooint\Traits\SingletonTrait;

/**
 * Review class
 */
class Review {
	/**
	 * Singleton
	 */
	use SingletonTrait;

	/**
	 * Template builder post type
	 *
	 * @var string
	 */
	public string $textdomain = 'cptwooint';

	/**
	 * @var object
	 */
	protected $loader;

	/**
	 * Class Constructor
	 */
	private function __construct() {
		$this->loader = Loader::instance();
		$this->loader->add_action( 'admin_notices', $this, 'cptwooint_display_admin_notice' );
		$this->loader->add_action( 'admin_init', $this, 'cptwooint_spare_me', 5 );
		$this->loader->add_action( 'admin_footer', $this, 'deactivation_popup', 99 );
	}

	/**
	 * Remove the notice for the user if review already done or if the user does not want to
	 *
	 * @return void
	 */
	public function cptwooint_spare_me() {

		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'cptwooint_notice_nonce' ) ) {
			return;
		}

		if ( ! empty( $_GET['cptwooint_spare_me'] ) ) {
			$spare_me = absint( $_GET['cptwooint_spare_me'] );
			if ( 1 == $spare_me ) {
				update_option( 'cptwooint_spare_me', '1' );
			}
		}

		if ( ! empty( $_GET['cptwooint_remind_me'] ) ) {
			$remind_me = absint( $_GET['cptwooint_remind_me'] );
			if ( 1 == $remind_me ) {
				$get_activation_time = strtotime( 'now' );
				update_option( 'cptwooint_remind_me', $get_activation_time );
			}
		}

		if ( ! empty( $_GET['cptwooint_rated'] ) ) {
			$cptwooint_rated = absint( $_GET['cptwooint_rated'] );
			if ( 1 == $cptwooint_rated ) {
				update_option( 'cptwooint_rated', 'yes' );
			}
		}
	}

	protected function cptwooint_current_admin_url() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

		if ( ! $uri ) {
			return '';
		}

		return remove_query_arg(
			[
				'_wpnonce',
				'_wc_notice_nonce',
				'wc_db_update',
				'wc_db_update_nonce',
				'wc-hide-notice',
				'cptwooint_spare_me',
				'cptwooint_remind_me',
				'cptwooint_rated',
			],
			admin_url( $uri )
		);
	}

	/**
	 * Display Admin Notice, asking for a review
	 **/
	public function cptwooint_display_admin_notice() {
		if ( isset( $GLOBALS['cptwooint__notice'] ) ) {
			return;
		}
		// Added Lines Start.
		$nobug = get_option( 'cptwooint_spare_me' );
		$rated = get_option( 'cptwooint_rated' );
		if ( '1' == $nobug || 'yes' == $rated ) {
			return;
		}
		$now          = strtotime( 'now' );
		$install_date = get_option( 'cptwooint_plugin_activation_time' );
		$past_date    = strtotime( '+5 days', $install_date );
		$remind_time  = get_option( 'cptwooint_remind_me' );
		if ( ! $remind_time ) {
			$remind_time = $install_date;
		}
		$remind_due = strtotime( '+15 days', $remind_time );
		if ( ! $now > $past_date || $now < $remind_due ) {
			return;
		}
		// WordPress global variable.
		global $pagenow;
		$exclude = [
			'themes.php',
			'users.php',
			'tools.php',
			'options-general.php',
			'options-writing.php',
			'options-reading.php',
			'options-discussion.php',
			'options-media.php',
			'options-permalink.php',
			'options-privacy.php',
			'admin.php',
			'import.php',
			'export.php',
			'site-health.php',
			'export-personal-data.php',
			'erase-personal-data.php',
		];

		if ( ! in_array( $pagenow, $exclude, true ) ) {

			$args = [ '_wpnonce' => wp_create_nonce( 'cptwooint_notice_nonce' ) ];

			$dont_disturb = add_query_arg( $args + [ 'cptwooint_spare_me' => '1' ], $this->cptwooint_current_admin_url() );
			$remind_me    = add_query_arg( $args + [ 'cptwooint_remind_me' => '1' ], $this->cptwooint_current_admin_url() );
			$rated        = add_query_arg( $args + [ 'cptwooint_rated' => '1' ], $this->cptwooint_current_admin_url() );
			$reviewurl    = 'https://wordpress.org/support/plugin/cpt-woo-integration/reviews/?filter=5#new-post';
			?>
			<div class="notice cptwooint-review-notice cptwooint-review-notice--extended">
				<div class="cptwooint-review-notice_content">
					<h3>Enjoying 'Custom Post Type WooCommerce Integration'? </h3>
					<p>
						Thank you for choosing <strong>Custom Post Type Woocommerce Integration</strong>. If you have indeed benefited from our services, we kindly request that you, please consider giving us a 5-star rating on WordPress.org.
					</p>
					<div class="cptwooint-review-notice_actions">
						<a href="<?php echo esc_url( $reviewurl ); ?>"
						   class="cptwooint-review-button cptwooint-review-button--cta" target="_blank"><span>⭐ Yes, You Deserve It!</span></a>
						<a href="<?php echo esc_url( $rated ); ?>"
						   class="cptwooint-review-button cptwooint-review-button--cta cptwooint-review-button--outline"><span>😀 Already Rated!</span></a>
						<a href="<?php echo esc_url( $remind_me ); ?>"
						   class="cptwooint-review-button cptwooint-review-button--cta cptwooint-review-button--outline"><span>🔔 Remind Me Later</span></a>
					</div>
				</div>
			</div>
			<style>
				.cptwooint-review-button--cta {
					--e-button-context-color: #1677ff;
					--e-button-context-color-dark: #1677ff;
					--e-button-context-tint: rgb(75 47 157/4%);
					--e-focus-color: rgb(75 47 157/40%);
				}

				.cptwooint-review-notice {
					position: relative;
					margin: 5px 20px 5px 2px;
					border: 1px solid #ccd0d4;
					background: #fff;
					box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
					font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
					border-inline-start-width: 4px;
				}

				.cptwooint-review-notice.notice {
					padding: 0;
				}

				.cptwooint-review-notice:before {
					position: absolute;
					top: -1px;
					bottom: -1px;
					left: -4px;
					display: block;
					width: 4px;
					background: -webkit-linear-gradient(bottom, #5d3dfd 0%, #6939c6 100%);
					background: linear-gradient(0deg, #5d3dfd 0%, #6939c6 100%);
					content: "";
				}

				.cptwooint-review-notice_content {
					padding: 20px;
				}

				.cptwooint-review-notice_actions > * + * {
					margin-inline-start: 8px;
					-webkit-margin-start: 8px;
					-moz-margin-start: 8px;
				}

				.cptwooint-review-notice p {
					margin: 0;
					padding: 0;
					line-height: 1.5;
				}

				p + .cptwooint-review-notice_actions {
					margin-top: 1rem;
				}

				.cptwooint-review-notice h3 {
					margin: 0;
					font-size: 1.0625rem;
					line-height: 1.2;
				}

				.cptwooint-review-notice h3 + p {
					margin-top: 8px;
				}

				.cptwooint-review-button {
					display: inline-block;
					padding: 0.4375rem 0.75rem;
					border: 0;
					border-radius: 3px;;
					background: var(--e-button-context-color);
					color: #fff;
					vertical-align: middle;
					text-align: center;
					text-decoration: none;
					white-space: nowrap;
				}

				.cptwooint-review-button:active {
					background: var(--e-button-context-color-dark);
					color: #fff;
					text-decoration: none;
				}

				.cptwooint-review-button:focus {
					outline: 0;
					background: var(--e-button-context-color-dark);
					box-shadow: 0 0 0 2px var(--e-focus-color);
					color: #fff;
					text-decoration: none;
				}

				.cptwooint-review-button:hover {
					background: var(--e-button-context-color-dark);
					color: #fff;
					text-decoration: none;
				}

				.cptwooint-review-button.focus {
					outline: 0;
					box-shadow: 0 0 0 2px var(--e-focus-color);
				}

				.cptwooint-review-button--error {
					--e-button-context-color: #d72b3f;
					--e-button-context-color-dark: #ae2131;
					--e-button-context-tint: rgba(215, 43, 63, 0.04);
					--e-focus-color: rgba(215, 43, 63, 0.4);
				}

				.cptwooint-review-button.cptwooint-review-button--outline {
					border: 1px solid;
					background: 0 0;
					color: var(--e-button-context-color);
				}

				.cptwooint-review-button.cptwooint-review-button--outline:focus {
					background: var(--e-button-context-tint);
					color: var(--e-button-context-color-dark);
				}

				.cptwooint-review-button.cptwooint-review-button--outline:hover {
					background: var(--e-button-context-tint);
					color: var(--e-button-context-color-dark);
				}
			</style>
			<?php
		}
	}

	/***
	 *
	 * @return mixed
	 */
	public function deactivation_popup() {
		global $pagenow;
		if ( 'plugins.php' !== $pagenow ) {
			return;
		}

		$this->dialog_box_style();
		$this->deactivation_scripts();
		$td = esc_attr( $this->textdomain );
		?>
		<div id="deactivation-dialog-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above ?>" title="<?php esc_attr_e( 'Quick Feedback: How can we improve the plugin?', 'cpt-woo-integration' ); ?>">
			<div class="cptwooint-deactivate-modal">

				<p class="cptwooint-deactivate-intro">
					<?php esc_html_e( "We're sorry to see you go! Before you leave, could you spare a moment to tell us why? Your feedback helps us make the plugin better for everyone.", 'cpt-woo-integration' ); ?>
				</p>

				<div class="cptwooint-deactivate-support">
					<span class="cptwooint-deactivate-support__text"><?php esc_html_e( 'Having trouble? Our support team is here to help.', 'cpt-woo-integration' ); ?></span>
					<a class="cptwooint-deactivate-support__btn" target="_blank" rel="noopener" href="https://help.wptinysolutions.com/"><?php esc_html_e( 'Contact Support', 'cpt-woo-integration' ); ?> &rarr;</a>
				</div>

				<div class="cptwooint-deactivate-divider">
					<span><?php esc_html_e( 'Or tell us why you\'re leaving', 'cpt-woo-integration' ); ?></span>
				</div>

				<div class="cptwooint-deactivate-reasons" id="feedback-form-body-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">

					<label class="cptwooint-deactivate-reason">
						<input class="cptwooint-deactivate-reason__radio" type="radio" name="reason_key"
							   id="feedback-deactivate-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>-bug_issue_detected"
							   value="bug_issue_detected">
						<span class="cptwooint-deactivate-reason__icon">🐛</span>
						<span class="cptwooint-deactivate-reason__text"><?php esc_html_e( 'Bug or issue detected', 'cpt-woo-integration' ); ?></span>
					</label>

					<label class="cptwooint-deactivate-reason">
						<input class="cptwooint-deactivate-reason__radio" type="radio" name="reason_key"
							   id="feedback-deactivate-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>-no_longer_needed"
							   value="no_longer_needed">
						<span class="cptwooint-deactivate-reason__icon">👋</span>
						<span class="cptwooint-deactivate-reason__text"><?php esc_html_e( 'I no longer need the plugin', 'cpt-woo-integration' ); ?></span>
					</label>

					<label class="cptwooint-deactivate-reason">
						<input class="cptwooint-deactivate-reason__radio" type="radio" name="reason_key"
							   id="feedback-deactivate-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>-found_a_better_plugin"
							   value="found_a_better_plugin">
						<span class="cptwooint-deactivate-reason__icon">🔍</span>
						<span class="cptwooint-deactivate-reason__text"><?php esc_html_e( 'I found a better plugin', 'cpt-woo-integration' ); ?></span>
					</label>
					<div class="cptwooint-deactivate-better-plugin" id="cptwooint-better-plugin-input-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
						<input class="cptwooint-deactivate-better-plugin__input" type="text" name="reason_found_a_better_plugin"
							   placeholder="<?php esc_attr_e( 'Which plugin did you switch to?', 'cpt-woo-integration' ); ?>">
					</div>

					<label class="cptwooint-deactivate-reason">
						<input class="cptwooint-deactivate-reason__radio" type="radio" name="reason_key"
							   id="feedback-deactivate-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>-couldnt_get_the_plugin_to_work"
							   value="couldnt_get_the_plugin_to_work">
						<span class="cptwooint-deactivate-reason__icon">⚙️</span>
						<span class="cptwooint-deactivate-reason__text"><?php esc_html_e( "I couldn't get the plugin to work", 'cpt-woo-integration' ); ?></span>
					</label>

					<label class="cptwooint-deactivate-reason">
						<input class="cptwooint-deactivate-reason__radio" type="radio" name="reason_key"
							   id="feedback-deactivate-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>-temporary_deactivation"
							   value="temporary_deactivation">
						<span class="cptwooint-deactivate-reason__icon">⏸️</span>
						<span class="cptwooint-deactivate-reason__text"><?php esc_html_e( "It's a temporary deactivation", 'cpt-woo-integration' ); ?></span>
					</label>

					<span class="cptwooint-deactivate-error" id="cptwooint-reason-error-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></span>
				</div>

				<div class="cptwooint-deactivate-textarea-wrap" id="cptwooint-textarea-wrap-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
					<textarea id="deactivation-feedback-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
							  placeholder="<?php esc_attr_e( 'How can we improve the plugin? Any details help...', 'cpt-woo-integration' ); ?>"></textarea>
					<span class="cptwooint-deactivate-error" id="cptwooint-feedback-error-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></span>
				</div>

				<div class="cptwooint-deactivate-warning">
					⚠️ <?php esc_html_e( 'Deactivating will disable all WooCommerce integrations for your custom post types.', 'cpt-woo-integration' ); ?>
				</div>

			</div>
		</div>
		<?php
	}

	/***
	 *
	 * @return mixed
	 */
	public function dialog_box_style() {
		$td = esc_attr( $this->textdomain );
		?>
		<style>
			/* ── Overlay ── */
			.ui-widget-overlay.ui-front {
				position: fixed;
				inset: 0;
				z-index: 9;
				background: rgba(0, 0, 0, 0.55);
			}

			/* ── Dialog shell ── */
			#deactivation-dialog-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> {
				display: none;
			}

			.ui-dialog[aria-describedby="deactivation-dialog-<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"] {
				background: #fff;
				border-radius: 10px;
				box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
				border: none;
				padding: 0;
				overflow: hidden;
				z-index: 99999;
			}

			/* ── Title bar ── */
			.ui-dialog-titlebar-close { display: none; }

			.ui-dialog-title {
				font-size: 16px;
				font-weight: 700;
				color: #1d2327;
			}

			.ui-draggable .ui-dialog-titlebar {
				padding: 16px 20px;
				border-bottom: 1px solid #f0f0f0;
				background: #f9f9f9;
			}

			/* ── Modal body ── */
			.cptwooint-deactivate-modal {
				padding: 20px 20px 8px;
				display: flex;
				flex-direction: column;
				gap: 14px;
			}

			/* Intro text */
			.cptwooint-deactivate-intro {
				font-size: 13px;
				color: #50575e;
				margin: 0;
				line-height: 1.6;
			}

			/* Support banner */
			.cptwooint-deactivate-support {
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 12px;
				background: #f0f7ff;
				border: 1px solid #bdd7f5;
				border-radius: 6px;
				padding: 10px 14px;
			}

			.cptwooint-deactivate-support__text {
				font-size: 13px;
				color: #1d2327;
				font-weight: 500;
			}

			.cptwooint-deactivate-support__btn {
				display: inline-block;
				background: #2271b1;
				color: #fff;
				font-size: 12px;
				font-weight: 600;
				padding: 6px 14px;
				border-radius: 4px;
				text-decoration: none;
				white-space: nowrap;
				transition: background 0.2s;
			}

			.cptwooint-deactivate-support__btn:hover {
				background: #135e96;
				color: #fff;
			}

			/* OR divider */
			.cptwooint-deactivate-divider {
				display: flex;
				align-items: center;
				gap: 10px;
				font-size: 12px;
				color: #8c8f94;
				font-weight: 500;
				text-transform: uppercase;
				letter-spacing: 0.05em;
			}

			.cptwooint-deactivate-divider::before,
			.cptwooint-deactivate-divider::after {
				content: '';
				flex: 1;
				height: 1px;
				background: #e0e0e0;
			}

			/* Reasons list */
			.cptwooint-deactivate-reasons {
				display: flex;
				flex-direction: column;
				gap: 4px;
			}

			.cptwooint-deactivate-reason {
				display: flex;
				align-items: center;
				gap: 5px;
				padding: 8px 12px;
				border-radius: 6px;
				border: 1px solid transparent;
				cursor: pointer;
				transition: background 0.15s, border-color 0.15s;
			}

			.cptwooint-deactivate-reason:hover {
				background: #f6f7f7;
				border-color: #e0e0e0;
			}

			.cptwooint-deactivate-reason:has(input:checked) {
				background: #f0f7ff;
				border-color: #bdd7f5;
			}

			.cptwooint-deactivate-reason__radio {
				margin: 0;
				flex-shrink: 0;
				accent-color: #2271b1;
				width: 15px;
				height: 15px;
				cursor: pointer;
			}

			.cptwooint-deactivate-reason__icon {
				font-size: 16px;
				line-height: 1;
				flex-shrink: 0;
			}

			.cptwooint-deactivate-reason__text {
				font-size: 15px;
				color: #1d2327;
				line-height: 1.4;
			}

			/* "Better plugin" name input — hidden until its radio is checked */
			.cptwooint-deactivate-better-plugin {
				display: none;
				padding: 4px 12px 4px 48px;
			}

			.cptwooint-deactivate-better-plugin__input {
				width: 100%;
				border: 1px solid #c3c4c7;
				border-radius: 4px;
				padding: 6px 10px;
				font-size: 13px;
				color: #1d2327;
				box-sizing: border-box;
			}

			.cptwooint-deactivate-better-plugin__input:focus {
				border-color: #2271b1;
				outline: none;
				box-shadow: 0 0 0 1px #2271b1;
			}

			/* Textarea */
			.cptwooint-deactivate-textarea-wrap {
				display: flex;
				flex-direction: column;
				gap: 4px;
			}

			.cptwooint-deactivate-textarea-wrap textarea {
				width: 100%;
				min-height: 80px;
				border: 1px solid #c3c4c7;
				border-radius: 4px;
				padding: 10px;
				font-size: 13px;
				color: #1d2327;
				resize: vertical;
				box-sizing: border-box;
				transition: border-color 0.15s;
			}

			.cptwooint-deactivate-textarea-wrap textarea:focus {
				border-color: #2271b1;
				outline: none;
				box-shadow: 0 0 0 1px #2271b1;
			}

			/* Error messages */
			.cptwooint-deactivate-error {
				font-size: 12px;
				color: #d63638;
				min-height: 16px;
				display: block;
			}

			/* Warning notice */
			.cptwooint-deactivate-warning {
				background: #fff8e5;
				border-left: 3px solid #dba617;
				border-radius: 0 4px 4px 0;
				padding: 9px 12px;
				font-size: 12px;
				color: #50575e;
				line-height: 1.5;
			}

			/* ── Button bar ── */
			.ui-dialog-buttonset {
				background: #fff;
				border-top: 1px solid #f0f0f0;
				padding: 14px 20px;
				display: flex;
				gap: 10px;
				justify-content: flex-end;
			}

			.ui-dialog-buttonset button {
				min-width: 140px;
				height: 36px;
				padding: 0 16px;
				border-radius: 5px;
				font-size: 13px;
				font-weight: 600;
				cursor: pointer;
				transition: background 0.2s, color 0.2s;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				margin: 0;
				border: 1px solid #c3c4c7;
				background: #f6f7f7;
				color: #1d2327;
			}

			/* "Send Feedback & Deactivate" — primary action */
			.ui-dialog-buttonset button:first-child {
				background: #2271b1;
				border-color: #2271b1;
				color: #fff;
			}

			.ui-dialog-buttonset button:first-child:hover {
				background: #135e96;
				border-color: #135e96;
			}

			.ui-dialog-buttonset button:first-child:hover .deactive-loading-spinner {
				border-color: #fff;
				border-top-color: transparent;
			}

			/* "Skip & Deactivate" — secondary action */
			.ui-dialog-buttonset button:nth-child(2):hover {
				background: #dcdcde;
			}

			/* Loading spinner */
			.deactive-loading-spinner {
				display: inline-block;
				width: 10px;
				height: 10px;
				border: 2px solid #fff;
				border-top-color: transparent;
				border-radius: 50%;
				animation: cptwooint-spin 0.8s linear infinite;
				margin-left: 8px;
				flex-shrink: 0;
			}

			@keyframes cptwooint-spin {
				to { transform: rotate(360deg); }
			}
		</style>
		<?php
	}

	/***
	 *
	 * @return mixed
	 */
	public function deactivation_scripts() {
		wp_enqueue_script( 'jquery-ui-dialog' );
		$td = esc_js( $this->textdomain );
		?>
		<script>
			jQuery(document).ready(function ($) {
				var td          = '<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';
				var $dialog     = $('#deactivation-dialog-' + td);
				var $deactLink  = $('.deactivate #deactivate-cpt-woo-integration');

				// Show/hide "better plugin" text input when its radio is selected
				$dialog.on('change', 'input[type="radio"]', function () {
					var $betterInput = $('#cptwooint-better-plugin-input-' + td);
					if ($(this).val() === 'found_a_better_plugin') {
						$betterInput.slideDown(150);
					} else {
						$betterInput.slideUp(150);
					}
					$('#cptwooint-reason-error-' + td).text('');
				});

				$dialog.on('input', 'textarea', function () {
					$('#cptwooint-feedback-error-' + td).text('');
				});

				// Open dialog when Deactivate link is clicked
				$deactLink.on('click', function (e) {
					e.preventDefault();
					var deactivateHref = $deactLink.attr('href');

					var dialogbox = $dialog.dialog({
						modal:  true,
						width:  580,
						show: { effect: 'fadeIn',  duration: 250 },
						hide: { effect: 'fadeOut', duration: 150 },
						buttons: {
							Submit: function () {
								var $btn = $(this).parents('.ui-dialog.ui-front').find('.ui-dialog-buttonset button.ui-button:first-child');
								submitFeedback($btn, deactivateHref);
							},
							Cancel: function () {
								$(this).dialog('close');
								window.location.href = deactivateHref;
							}
						}
					});

					// Close on overlay click
					$(document).off('click.cptwooint-deactivate').on('click.cptwooint-deactivate', '.ui-widget-overlay.ui-front', function (e) {
						if ($(e.target).closest(dialogbox.parent()).length === 0) {
							dialogbox.dialog('close');
						}
					});

					// Rename buttons
					$('.ui-dialog-buttonpane button:contains("Submit")').text(<?php echo wp_json_encode( __( 'Send Feedback & Deactivate', 'cpt-woo-integration' ) ); ?>);
					$('.ui-dialog-buttonpane button:contains("Cancel")').text(<?php echo wp_json_encode( __( 'Skip & Deactivate', 'cpt-woo-integration' ) ); ?>);
				});

				function submitFeedback($btn, deactivateHref) {
					var reasons      = $dialog.find('input[type="radio"]:checked').val();
					var feedback     = $('#deactivation-feedback-' + td).val().trim();
					var betterPlugin = $dialog.find('input[name="reason_found_a_better_plugin"]').val();

					// Validate: reason required
					if (!reasons) {
						$('#cptwooint-reason-error-' + td).text(<?php echo wp_json_encode( __( 'Please choose a reason before submitting.', 'cpt-woo-integration' ) ); ?>);
						return;
					}

					// Validate: feedback required unless temporary deactivation
					if (reasons !== 'temporary_deactivation' && !feedback) {
						$('#cptwooint-feedback-error-' + td).text(<?php echo wp_json_encode( __( 'Kindly share a few details so we can address this in a future update.', 'cpt-woo-integration' ) ); ?>);
						return;
					}

					// Temporary deactivation — skip feedback, just deactivate
					if (reasons === 'temporary_deactivation') {
						window.location.href = deactivateHref;
						return;
					}

					$btn.html(<?php echo wp_json_encode( __( 'Sending…', 'cpt-woo-integration' ) ); ?> + ' <span class="deactive-loading-spinner"></span>');
					$btn.prop('disabled', true);

					$.ajax({
						url:      'https://www.wptinysolutions.com/wp-json/TinySolutions/pluginSurvey/v1/Survey/appendToSheet',
						method:   'GET',
						dataType: 'json',
						data: {
							website:       '<?php echo esc_url( home_url() ); ?>',
							reasons:       reasons || '',
							better_plugin: betterPlugin || '',
							feedback:      feedback,
							wpplugin:      'cptwooint',
						},
						complete: function () {
							window.location.href = deactivateHref;
						}
					});
				}
			});
		</script>
		<?php
	}
}
