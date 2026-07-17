<?php
/**
 * Theme footer — Section 8.
 *
 * Closes <main>, then renders two site-wide bands from the approved mockup:
 *   1. The orange "Stay in the Loop" newsletter band (ACF-editable copy; posts to
 *      the client's email-provider form URL).
 *   2. The dark editorial footer: brand/logo + tagline + social, five link
 *      columns (each an optional nav-menu location with default links), and the
 *      copyright / legal bar.
 *
 * All copy is read via juiced_field() (stored on the Home page, rendered site-
 * wide — the same pattern the header uses for the Order Ahead link).
 *
 * @package Juiced
 */

// --- Newsletter band copy -------------------------------------------------
// The band's own pitch, per the mockups — deliberately not "Stay in the Loop",
// which the Events sidebar signup uses a few hundred pixels above it.
// Newlines in the heading render as line breaks (nl2br below) — the default
// puts "Good Vibes." on its own line, per the approved design.
$juiced_nl_heading = juiced_field( 'newsletter_heading', "Good Things. Good People.\nGood Vibes." );
$juiced_nl_body    = juiced_field( 'newsletter_body', 'Sign up for updates on new menu items, events and community happenings.' );
$juiced_nl_action  = juiced_field( 'newsletter_action_url', '' );
$juiced_nl_field   = juiced_field( 'newsletter_field_name', 'EMAIL' );

// Decorative fruit background for the band, uploaded via ACF on the Home page
// (Footer & Newsletter tab). Desktop only — on mobile the stacked layout would
// put fruit behind the text, so we fall back to the flat brand-gold.
$juiced_nl_img_field = juiced_field( 'newsletter_bg_image', array() );
$juiced_nl_img       = is_array( $juiced_nl_img_field ) && ! empty( $juiced_nl_img_field['url'] )
	? $juiced_nl_img_field['url']
	: '';

// --- Footer copy + link targets -------------------------------------------
$juiced_tagline   = juiced_field( 'footer_tagline', 'Blending together health and community.' );
$juiced_copyright = juiced_field( 'footer_copyright', 'Juiced! Juice Bar. All Rights Reserved.' );
$juiced_privacy   = juiced_field( 'footer_privacy_url', '' );
$juiced_terms     = juiced_field( 'footer_terms_url', '' );

$juiced_ig_url = juiced_field( 'community_instagram_url', '' );
$juiced_fb_url = juiced_field( 'footer_facebook_url', '' );
$juiced_tt_url = juiced_field( 'footer_tiktok_url', '' );

// Default column links reuse URLs already configured elsewhere in ACF, so the
// footer points somewhere useful before the client curates footer menus.
$juiced_menu_url   = juiced_field( 'menu_link_url', '#' );
$juiced_events_url = juiced_field( 'events_link_url', '#' );
$juiced_order_url  = juiced_field( 'order_ahead_url', '#' );
?>
</main><!-- #main -->

<?php if ( $juiced_nl_heading ) : ?>
	<!-- Newsletter band -->
	<section class="relative overflow-hidden bg-brand-gold text-white">
		<?php if ( $juiced_nl_img ) : ?>
			<div class="hidden md:block absolute inset-0 bg-cover bg-center" aria-hidden="true"
				style="background-image: url('<?php echo esc_url( $juiced_nl_img ); ?>');"></div>
		<?php endif; ?>
		<div class="relative mx-auto max-w-7xl px-4 py-10 md:py-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
			<div class="max-w-xl">
				<h2 class="font-display text-3xl md:text-4xl leading-tight"><?php echo nl2br( esc_html( $juiced_nl_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped, then newlines converted to <br>. ?></h2>
				<?php if ( $juiced_nl_body ) : ?>
					<p class="mt-2 text-sm md:text-base text-white/85"><?php echo esc_html( $juiced_nl_body ); ?></p>
				<?php endif; ?>
			</div>

			<form action="<?php echo esc_url( $juiced_nl_action ); ?>" method="post"
				<?php echo $juiced_nl_action ? 'target="_blank" rel="noopener"' : ''; ?>
				class="w-full md:w-auto flex items-center bg-white rounded-full p-1.5 shadow-sm"
				aria-label="<?php esc_attr_e( 'Newsletter signup', 'juiced' ); ?>">
				<label class="sr-only" for="juiced-newsletter-email"><?php esc_html_e( 'Email address', 'juiced' ); ?></label>
				<input id="juiced-newsletter-email" type="email" required
					name="<?php echo esc_attr( $juiced_nl_field ); ?>"
					placeholder="<?php esc_attr_e( 'Enter your email address', 'juiced' ); ?>"
					class="flex-1 md:w-72 min-w-0 bg-transparent px-4 py-2 text-brand-ink placeholder:text-brand-ink/40 focus:outline-none" />
				<button type="submit"
					class="shrink-0 rounded-full bg-brand-ink px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-green transition">
					<?php esc_html_e( 'Sign Up', 'juiced' ); ?>
				</button>
			</form>
		</div>
	</section>
<?php endif; ?>

<footer class="bg-brand-ink text-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-14 grid gap-10 md:grid-cols-6">

		<!-- Brand column -->
		<div class="md:col-span-1">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="font-display text-2xl text-brand-gold">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $juiced_tagline ) : ?>
				<p class="mt-4 text-sm text-brand-cream/70 max-w-[16rem]"><?php echo esc_html( $juiced_tagline ); ?></p>
			<?php endif; ?>

			<?php if ( $juiced_ig_url || $juiced_fb_url || $juiced_tt_url ) : ?>
				<div class="mt-5 flex items-center gap-3">
					<?php if ( $juiced_ig_url ) : ?>
						<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener"
							class="grid place-items-center w-9 h-9 rounded-full bg-white/10 text-brand-cream hover:bg-brand-gold hover:text-white transition"
							aria-label="<?php esc_attr_e( 'Instagram', 'juiced' ); ?>">
							<?php echo juiced_social_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						</a>
					<?php endif; ?>
					<?php if ( $juiced_fb_url ) : ?>
						<a href="<?php echo esc_url( $juiced_fb_url ); ?>" target="_blank" rel="noopener"
							class="grid place-items-center w-9 h-9 rounded-full bg-white/10 text-brand-cream hover:bg-brand-gold hover:text-white transition"
							aria-label="<?php esc_attr_e( 'Facebook', 'juiced' ); ?>">
							<?php echo juiced_social_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						</a>
					<?php endif; ?>
					<?php if ( $juiced_tt_url ) : ?>
						<a href="<?php echo esc_url( $juiced_tt_url ); ?>" target="_blank" rel="noopener"
							class="grid place-items-center w-9 h-9 rounded-full bg-white/10 text-brand-cream hover:bg-brand-gold hover:text-white transition"
							aria-label="<?php esc_attr_e( 'TikTok', 'juiced' ); ?>">
							<?php echo juiced_social_icon( 'tiktok' ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Link columns -->
		<nav class="md:col-span-5 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-8"
			aria-label="<?php esc_attr_e( 'Footer', 'juiced' ); ?>">
			<?php
			juiced_footer_column(
				'footer_menu',
				__( 'Menu', 'juiced' ),
				array(
					array( 'label' => __( 'Smoothies', 'juiced' ), 'url' => $juiced_menu_url ),
					array( 'label' => __( 'Juices', 'juiced' ), 'url' => $juiced_menu_url ),
					array( 'label' => __( 'Bowls', 'juiced' ), 'url' => $juiced_menu_url ),
					array( 'label' => __( 'Wellness Shots', 'juiced' ), 'url' => $juiced_menu_url ),
					array( 'label' => __( 'Extras', 'juiced' ), 'url' => $juiced_menu_url ),
				)
			);

			juiced_footer_column(
				'footer_events',
				__( 'Events', 'juiced' ),
				array(
					array( 'label' => __( 'This Week', 'juiced' ), 'url' => $juiced_events_url ),
					array( 'label' => __( 'Calendar', 'juiced' ), 'url' => $juiced_events_url ),
					array( 'label' => __( 'Private Events', 'juiced' ), 'url' => '#' ),
				)
			);

			juiced_footer_column(
				'footer_about',
				__( 'About', 'juiced' ),
				array(
					array( 'label' => __( 'Our Story', 'juiced' ), 'url' => '#' ),
					array( 'label' => __( 'Community', 'juiced' ), 'url' => '#' ),
					array( 'label' => __( 'Careers', 'juiced' ), 'url' => '#' ),
				)
			);

			juiced_footer_column(
				'footer_locations',
				__( 'Locations', 'juiced' ),
				array(
					array(
						'label' => juiced_business_field( 'loc1_name', 'North Raleigh' ),
						'url'   => juiced_business_field( 'loc1_directions', '#' ),
					),
					array(
						'label' => juiced_business_field( 'loc2_name', 'Downtown Raleigh' ),
						'url'   => juiced_business_field( 'loc2_directions', '#' ),
					),
				)
			);

			juiced_footer_column(
				'footer_order',
				__( 'Order', 'juiced' ),
				array(
					array( 'label' => __( 'Order Ahead', 'juiced' ), 'url' => $juiced_order_url ),
					array( 'label' => __( 'Gift Cards', 'juiced' ), 'url' => '#' ),
				)
			);
			?>
		</nav>
	</div>

	<!-- Bottom bar -->
	<div class="border-t border-white/10">
		<div class="mx-auto max-w-7xl px-4 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-brand-cream/60">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $juiced_copyright ); ?></p>
			<?php if ( $juiced_privacy || $juiced_terms ) : ?>
				<p class="flex items-center gap-4">
					<?php if ( $juiced_privacy ) : ?>
						<a href="<?php echo esc_url( $juiced_privacy ); ?>" class="hover:text-brand-gold transition"><?php esc_html_e( 'Privacy Policy', 'juiced' ); ?></a>
					<?php endif; ?>
					<?php if ( $juiced_privacy && $juiced_terms ) : ?>
						<span aria-hidden="true" class="text-brand-cream/25">|</span>
					<?php endif; ?>
					<?php if ( $juiced_terms ) : ?>
						<a href="<?php echo esc_url( $juiced_terms ); ?>" class="hover:text-brand-gold transition"><?php esc_html_e( 'Terms of Service', 'juiced' ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
