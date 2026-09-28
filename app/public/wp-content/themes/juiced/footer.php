<?php
/**
 * Theme footer — Section 8.
 *
 * Closes <main>, then renders two site-wide bands from the approved mockup:
 *   1. The warm-cream "Join the Juiced! family" sign-up band ("Garden" redesign,
 *      Sep 2026; ACF-editable copy; posts to the client's email-provider form URL).
 *   2. The deep-green footer: "Juiced!" wordmark, tagline, social icons and
 *      copyright, beside three link columns (Community, Wellness, Visit) — each
 *      an optional nav-menu location with default links.
 *
 * All copy is read via juiced_field() (stored on the Home page, rendered site-
 * wide — the same pattern the header uses for the Order Ahead link).
 *
 * @package Juiced
 */

// --- Join the family (sign-up band) --------------------------------------
// Posts to the client's email-provider form URL (Mailchimp etc.); with none set
// the Join button just reloads the page.
$juiced_nl_heading = juiced_field( 'newsletter_heading', 'Join the' );
$juiced_nl_accent  = juiced_field( 'newsletter_heading_accent', 'Juiced! family.' );
$juiced_nl_body    = juiced_field( 'newsletter_body', 'Hear about events first, plus new menu drops and community shout-outs.' );
$juiced_nl_action  = juiced_field( 'newsletter_action_url', '' );
$juiced_nl_field   = juiced_field( 'newsletter_field_name', 'EMAIL' );

// --- Footer copy + link targets -------------------------------------------
$juiced_tagline   = juiced_field( 'footer_tagline', 'Blending together health and community.' );
$juiced_copyright = juiced_field( 'footer_copyright', 'Juiced! Juice Bar · Raleigh, NC' );
$juiced_privacy   = juiced_field( 'footer_privacy_url', '' );
$juiced_terms     = juiced_field( 'footer_terms_url', '' );

$juiced_ig_url = juiced_field( 'community_instagram_url', 'https://www.instagram.com/juicednc/' );
$juiced_fb_url = juiced_field( 'footer_facebook_url', '' );
$juiced_tt_url = juiced_field( 'footer_tiktok_url', '' );

// Default column links (used until a menu is assigned to that column in
// Appearance → Menus). Pages are looked up by slug so they survive a migration.
$juiced_page_url = static function ( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
};
$juiced_directions = static function ( $i ) use ( $juiced_page_url ) {
	$url     = juiced_business_field( "loc{$i}_directions", '' );
	$address = trim( preg_replace( '/\s+/', ' ', (string) juiced_business_field( "loc{$i}_address", '' ) ) );
	if ( ! $url && $address ) {
		$url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( juiced_business_field( "loc{$i}_name", '' ) . ' ' . $address );
	}
	return $url ? $url : $juiced_page_url( 'locations' );
};
?>
</main><!-- #main -->

<!-- Join the family (sign-up band) -->
<section class="bg-[#f5ecdc] text-brand-ink" aria-labelledby="juiced-join-heading">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20 grid gap-8 md:grid-cols-2 md:gap-12 items-center">
		<h2 id="juiced-join-heading" class="font-display font-bold leading-[.95] tracking-[-.02em] text-brand-forest text-[clamp(2.75rem,6vw,4.5rem)]">
			<span class="block"><?php echo esc_html( $juiced_nl_heading ); ?></span>
			<span class="block text-brand-rust"><?php echo esc_html( $juiced_nl_accent ); ?></span>
		</h2>

		<div class="flex flex-col gap-3.5">
			<?php if ( $juiced_nl_body ) : ?>
				<p class="m-0 text-lg leading-normal font-medium"><?php echo esc_html( $juiced_nl_body ); ?></p>
			<?php endif; ?>
			<form action="<?php echo esc_url( $juiced_nl_action ); ?>" method="post"
				<?php echo $juiced_nl_action ? 'target="_blank" rel="noopener"' : ''; ?>
				class="flex flex-col gap-2.5 sm:flex-row">
				<label class="sr-only" for="juiced-newsletter-email"><?php esc_html_e( 'Email address', 'juiced' ); ?></label>
				<input id="juiced-newsletter-email" type="email" required autocomplete="email"
					name="<?php echo esc_attr( $juiced_nl_field ); ?>"
					placeholder="<?php esc_attr_e( 'Your email address', 'juiced' ); ?>"
					class="h-14 w-full min-w-0 sm:flex-1 rounded-full border-2 border-brand-forest bg-brand-cream px-5.5 text-base text-brand-ink placeholder:text-brand-ink/50 focus:outline-none focus-visible:ring-4 focus-visible:ring-brand-gold/50" />
				<button type="submit"
					class="h-14 shrink-0 cursor-pointer rounded-full bg-brand-gold px-7.5 text-base font-bold text-brand-ink hover:bg-brand-forest hover:text-brand-cream transition-colors">
					<?php esc_html_e( 'Join', 'juiced' ); ?>
				</button>
			</form>
		</div>
	</div>
</section>

<footer class="bg-brand-forest-deep text-brand-mint">
	<div class="mx-auto max-w-7xl px-4 py-12 md:py-14 flex flex-col gap-10 md:flex-row md:justify-between">

		<!-- Brand column -->
		<div class="flex flex-col gap-2.5">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="self-start font-display text-[44px] font-bold leading-none text-brand-gold hover:text-brand-cream transition-colors">Juiced!</a>
			<?php if ( $juiced_tagline ) : ?>
				<p class="m-0 text-[15px]"><?php echo esc_html( $juiced_tagline ); ?></p>
			<?php endif; ?>

			<?php if ( $juiced_ig_url || $juiced_fb_url || $juiced_tt_url ) : ?>
				<div class="mt-2 flex items-center gap-2.5">
					<?php
					foreach ( array(
						'instagram' => array( $juiced_ig_url, __( 'Instagram', 'juiced' ) ),
						'facebook'  => array( $juiced_fb_url, __( 'Facebook', 'juiced' ) ),
						'tiktok'    => array( $juiced_tt_url, __( 'TikTok', 'juiced' ) ),
					) as $network => $social ) :
						if ( ! $social[0] ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( $social[0] ); ?>" target="_blank" rel="noopener noreferrer"
							class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-brand-cream hover:bg-brand-gold hover:text-brand-ink transition-colors"
							aria-label="<?php echo esc_attr( $social[1] ); ?>">
							<?php echo juiced_social_icon( $network ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php juiced_footer_copyright( 'hidden md:block m-0 mt-auto pt-4 text-[13px]', $juiced_copyright, $juiced_privacy, $juiced_terms ); ?>
		</div>

		<!-- Link columns -->
		<nav class="grid grid-cols-2 gap-x-10 gap-y-8 sm:grid-cols-3 md:gap-x-18" aria-label="<?php esc_attr_e( 'Footer', 'juiced' ); ?>">
			<?php
			juiced_footer_column(
				'footer_community',
				__( 'Community', 'juiced' ),
				array(
					array( 'label' => __( 'Events', 'juiced' ), 'url' => juiced_events_page_url() ),
					array( 'label' => __( 'About us', 'juiced' ), 'url' => $juiced_page_url( 'about' ) ),
					array( 'label' => __( 'Host an event', 'juiced' ), 'url' => $juiced_page_url( 'contact' ) ),
				)
			);

			juiced_footer_column(
				'footer_wellness',
				__( 'Wellness', 'juiced' ),
				array(
					array( 'label' => __( 'Menu', 'juiced' ), 'url' => $juiced_page_url( 'menu' ) ),
					array( 'label' => __( 'Herb Library', 'juiced' ), 'url' => $juiced_page_url( 'herbs' ) ),
				)
			);

			juiced_footer_column(
				'footer_visit',
				__( 'Visit', 'juiced' ),
				array(
					array( 'label' => juiced_business_field( 'loc1_name', 'Rock Quarry' ), 'url' => $juiced_directions( 1 ) ),
					array( 'label' => juiced_business_field( 'loc2_name', 'Hill Street' ), 'url' => $juiced_directions( 2 ) ),
					array( 'label' => __( 'Contact', 'juiced' ), 'url' => $juiced_page_url( 'contact' ) ),
				)
			);
			?>
		</nav>

		<?php // On phones the copyright moves below the link columns. ?>
		<?php juiced_footer_copyright( 'md:hidden m-0 text-[13px]', $juiced_copyright, $juiced_privacy, $juiced_terms ); ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
