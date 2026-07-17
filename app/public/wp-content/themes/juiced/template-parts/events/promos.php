<?php
/**
 * Events page Section 3d — the sidebar promo blocks under the calendar.
 *
 * Four stacked blocks: the "Host Your Event" enquiry box, a photo, a newsletter
 * signup, and the social links. Only the host box is a filled card — it carries
 * the one commercial ask on the page, and making everything a card would leave
 * nothing to look at first.
 *
 * Fields are split deliberately between page-local and site-wide:
 *
 *  - Headings and body copy are Events-page fields (inc/acf-events-page.php),
 *    because the design says different things here than the footer does — the
 *    sidebar's "Stay in the Loop" sits above an orange band running its own
 *    pitch, so one shared heading field can't serve both.
 *  - The signup form's action URL and field name, and the three social URLs,
 *    are the existing site-wide fields the footer already reads (juiced_field(),
 *    stored on the Home page). Those are one mailing list and one set of
 *    accounts; duplicating them would mean the client changing an address in
 *    two places and this page silently keeping the stale one.
 *
 * Blocks with nothing configured hide rather than render empty, which is why
 * the socials disappear wholesale when no URLs are set — three icons linking
 * nowhere are worse than no icons.
 *
 * @package Juiced
 */

// Host box.
$juiced_host_heading = juiced_page_field( 'events_host_heading', 'Host Your Event at Juiced!' );
$juiced_host_body    = juiced_page_field( 'events_host_body', "Looking for a vibrant space for your next gathering, workshop, or pop-up? We'd love to host you!" );
$juiced_host_label   = juiced_page_field( 'events_host_cta_label', 'Inquire Today' );
$juiced_host_url     = juiced_page_field( 'events_host_cta_url', '' );

// Photo.
$juiced_image = juiced_image_url( juiced_page_field( 'events_sidebar_image', '' ), 'juiced_card' );

// Signup — copy is page-local, the endpoint is the site-wide one.
$juiced_signup_heading = juiced_page_field( 'events_signup_heading', 'Stay in the Loop' );
$juiced_signup_body    = juiced_page_field( 'events_signup_body', 'Be the first to know about upcoming events, new offerings, and community highlights.' );
$juiced_nl_action      = juiced_field( 'newsletter_action_url', '' );
$juiced_nl_field       = juiced_field( 'newsletter_field_name', 'EMAIL' );

// Socials — site-wide, same accounts the footer links to.
$juiced_follow_heading = juiced_page_field( 'events_follow_heading', 'Follow Us' );
$juiced_follow_body    = juiced_page_field( 'events_follow_body', "See what's happening at Juiced! Tag us in your photos!" );
$juiced_ig_url         = juiced_field( 'community_instagram_url', '' );
$juiced_fb_url         = juiced_field( 'footer_facebook_url', '' );
$juiced_tt_url         = juiced_field( 'footer_tiktok_url', '' );
$juiced_handle         = $juiced_ig_url ? juiced_instagram_handle( $juiced_ig_url ) : '';

$juiced_socials = array(
	'instagram' => $juiced_ig_url,
	'facebook'  => $juiced_fb_url,
	'tiktok'    => $juiced_tt_url,
);
$juiced_socials = array_filter( $juiced_socials );
?>

<!-- Host your event -->
<?php if ( $juiced_host_heading ) : ?>
	<div class="rounded-2xl bg-brand-paper p-5 ring-1 ring-black/5">
		<h2 class="font-display text-xl leading-tight text-brand-bark">
			<?php echo esc_html( $juiced_host_heading ); ?>
		</h2>

		<?php if ( $juiced_host_body ) : ?>
			<p class="mt-2 text-sm leading-relaxed text-brand-bark/70">
				<?php echo esc_html( $juiced_host_body ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $juiced_host_url && $juiced_host_label ) : ?>
			<a href="<?php echo esc_url( $juiced_host_url ); ?>"
			   class="mt-4 inline-flex items-center gap-2 rounded-full bg-brand-gold px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-green">
				<?php echo esc_html( $juiced_host_label ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>

<!-- Photo -->
<?php if ( $juiced_image ) : ?>
	<img src="<?php echo esc_url( $juiced_image ); ?>" alt=""
		 class="aspect-[4/3] w-full rounded-2xl object-cover" loading="lazy" />
<?php endif; ?>

<!-- Stay in the loop -->
<?php if ( $juiced_signup_heading ) : ?>
	<div>
		<h2 class="font-display text-xl text-brand-bark">
			<?php echo esc_html( $juiced_signup_heading ); ?>
		</h2>

		<?php if ( $juiced_signup_body ) : ?>
			<p class="mt-2 text-sm leading-relaxed text-brand-bark/70">
				<?php echo esc_html( $juiced_signup_body ); ?>
			</p>
		<?php endif; ?>

		<?php
		// Same list as the footer band, so the id must differ — two identical
		// ids on one page would point both labels at the first input.
		?>
		<form action="<?php echo esc_url( $juiced_nl_action ); ?>" method="post"
			<?php echo $juiced_nl_action ? 'target="_blank" rel="noopener"' : ''; ?>
			class="mt-4 flex items-center rounded-full bg-white p-1.5 shadow-sm ring-1 ring-black/5"
			aria-label="<?php esc_attr_e( 'Newsletter signup', 'juiced' ); ?>">
			<label class="sr-only" for="juiced-events-newsletter-email"><?php esc_html_e( 'Email address', 'juiced' ); ?></label>
			<input id="juiced-events-newsletter-email" type="email" required
				name="<?php echo esc_attr( $juiced_nl_field ); ?>"
				placeholder="<?php esc_attr_e( 'Enter your email', 'juiced' ); ?>"
				class="min-w-0 flex-1 bg-transparent px-3 py-1.5 text-sm text-brand-ink placeholder:text-brand-ink/40 focus:outline-none" />
			<button type="submit"
				class="shrink-0 cursor-pointer rounded-full bg-brand-ink px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-green">
				<?php esc_html_e( 'Sign Up', 'juiced' ); ?>
			</button>
		</form>
	</div>
<?php endif; ?>

<!-- Follow us -->
<?php if ( $juiced_socials ) : ?>
	<div>
		<h2 class="font-display text-xl text-brand-bark">
			<?php echo esc_html( $juiced_follow_heading ); ?>
		</h2>

		<?php if ( $juiced_follow_body ) : ?>
			<p class="mt-2 text-sm leading-relaxed text-brand-bark/70">
				<?php echo esc_html( $juiced_follow_body ); ?>
			</p>
		<?php endif; ?>

		<div class="mt-4 flex flex-wrap items-center gap-3">
			<?php foreach ( $juiced_socials as $juiced_network => $juiced_url ) : ?>
				<a href="<?php echo esc_url( $juiced_url ); ?>" target="_blank" rel="noopener"
				   class="grid h-10 w-10 place-items-center rounded-full bg-brand-ink text-white transition-colors hover:bg-brand-gold"
				   aria-label="<?php echo esc_attr( ucfirst( $juiced_network ) ); ?>">
					<?php echo juiced_social_icon( $juiced_network ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
				</a>
			<?php endforeach; ?>

			<?php if ( $juiced_handle ) : ?>
				<a href="<?php echo esc_url( $juiced_ig_url ); ?>" target="_blank" rel="noopener"
				   class="text-sm font-semibold text-brand-gold hover:underline">
					<?php echo esc_html( $juiced_handle ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
