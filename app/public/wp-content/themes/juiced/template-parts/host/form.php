<?php
/**
 * Host an Event Section 3 — Good to Know + the event inquiry form
 * (Host an Event option D).
 *
 * Two columns on the cream band. Left: a carousel of photos of the space
 * (template-parts/host/carousel.php, hidden until photos are uploaded), then
 * a "Good to Know" FAQ (native <details>, the first one open; one
 * "Question | Answer" per line of an ACF textarea, hidden when cleared).
 * Right: the form in a mint card. Stacks on phones, left column first.
 * Stacks on phones with the FAQ first.
 *
 * Posts to admin-post.php (action=juiced_host_event), handled by
 * inc/page-forms.php, which redirects back with ?host=sent|error for the
 * notice above the form. Event types are one per line of an ACF textarea.
 * There's no "Which shop?" question: events are hosted at Hill Street only.
 *
 * @package Juiced
 */

$heading      = juiced_page_field( 'host_form_heading', 'Pitch Your Event' );
$intro        = juiced_page_field( 'host_form_intro', 'The more you share, the faster we can say yes. Rough ideas are welcome too.' );
$faq_heading  = juiced_page_field( 'host_faq_heading', 'Good to Know' );
$faq_raw      = juiced_page_field(
	'host_faq_items',
	"Does it cost anything? | [Answer from the owners: free, a minimum spend, or a flat fee.]\n"
	. "How far ahead should I ask? | [e.g. at least X weeks.]\n"
	. "Can we bring our own food or gear? | [Answer.]\n"
	. "Will you promote our event? | Once it's set, we'll add it to our Events page and help spread the word."
);

// One FAQ per line: "Question | Answer".
$faqs = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) $faq_raw ) as $line ) {
	$parts = array_map( 'trim', explode( '|', $line, 2 ) );
	if ( '' !== $parts[0] ) {
		$faqs[] = array(
			'q' => $parts[0],
			'a' => isset( $parts[1] ) ? $parts[1] : '',
		);
	}
}
$button_label = juiced_page_field( 'host_form_button', 'Send Event Idea' );
$event_types  = array_filter(
	array_map(
		'trim',
		explode(
			"\n",
			(string) juiced_page_field( 'host_form_types', "Run club or fitness\nWorkshop or class\nPop-up or market\nFundraiser\nPrivate gathering\nSomething else" )
		)
	)
);

// Result flag set by the redirect from inc/page-forms.php.
$status = isset( $_GET['host'] ) ? sanitize_key( wp_unslash( $_GET['host'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag.

// White fields on the mint card (the shared classes are cream, for white cards).
$input = str_replace( 'bg-brand-cream', 'bg-white', juiced_form_input_classes() );
$label = 'mb-1.5 block text-sm font-semibold text-brand-forest';

// Photos of the space, above Good to Know (empty when none are uploaded).
ob_start();
get_template_part( 'template-parts/host/carousel' );
$carousel = trim( ob_get_clean() );
?>
<section class="bg-brand-cream text-brand-ink">
	<div class="mx-auto grid max-w-7xl items-start gap-12 px-4 py-16 md:py-22 <?php echo ( $faqs || $carousel ) ? 'lg:grid-cols-[2fr_3fr] lg:gap-14' : 'max-w-3xl'; ?>">

		<?php if ( $faqs || $carousel ) : ?>
			<div class="min-w-0">
				<?php echo $carousel; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the partial. ?>

				<?php if ( $faqs ) : ?>
				<?php if ( $faq_heading ) : ?>
					<h2 class="font-display font-bold leading-none text-brand-forest text-[clamp(2.25rem,4.5vw,2.75rem)]"><?php echo esc_html( $faq_heading ); ?></h2>
				<?php endif; ?>
				<div class="mt-6 border-b border-brand-forest/20">
					<?php foreach ( $faqs as $n => $faq ) : ?>
						<details class="group border-t border-brand-forest/20 py-3.5" <?php echo 0 === $n ? 'open' : ''; ?>>
							<summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-4 font-display text-xl font-semibold text-brand-forest [&::-webkit-details-marker]:hidden">
								<?php echo esc_html( $faq['q'] ); ?>
								<span class="shrink-0 text-2xl leading-none text-brand-gold transition-transform group-open:rotate-45" aria-hidden="true">+</span>
							</summary>
							<?php if ( $faq['a'] ) : ?>
								<p class="mt-2 text-base leading-relaxed text-brand-ink/75"><?php echo esc_html( $faq['a'] ); ?></p>
							<?php endif; ?>
						</details>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div id="event-form" class="scroll-mt-24 rounded-[28px] bg-brand-mint p-6 sm:p-8 md:p-10">

			<?php if ( $heading ) : ?>
				<h2 class="font-display text-3xl font-bold text-brand-forest md:text-4xl"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="mt-2 text-brand-ink/80"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>

			<?php if ( 'sent' === $status ) : ?>
				<div class="mt-6 rounded-xl bg-brand-forest/10 px-5 py-4 text-sm font-semibold text-brand-forest" role="status">
					<?php esc_html_e( 'Thanks! Your event idea is on its way. We\'ll be in touch soon to talk it through.', 'juiced' ); ?>
				</div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="mt-6 rounded-xl bg-brand-rust/10 px-5 py-4 text-sm font-semibold text-brand-rust" role="alert">
					<?php esc_html_e( 'Something went wrong sending your idea. Please check the required fields and try again.', 'juiced' ); ?>
				</div>
			<?php endif; ?>

			<form class="mt-6 space-y-5" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="juiced_host_event" />
				<?php wp_nonce_field( 'juiced_host_event', 'juiced_host_nonce' ); ?>

				<!-- Honeypot — hidden from people, tempting to bots. -->
				<p class="hidden" aria-hidden="true">
					<label>Leave this field empty <input type="text" name="juiced_website" tabindex="-1" autocomplete="off" /></label>
				</p>

				<div class="grid gap-5 sm:grid-cols-2">
					<div>
						<label for="host-first-name" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'First name', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
						<input type="text" id="host-first-name" name="first_name" required autocomplete="given-name" class="<?php echo esc_attr( $input ); ?>" />
					</div>
					<div>
						<label for="host-last-name" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Last name', 'juiced' ); ?></label>
						<input type="text" id="host-last-name" name="last_name" autocomplete="family-name" class="<?php echo esc_attr( $input ); ?>" />
					</div>
					<div>
						<label for="host-email" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Email', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
						<input type="email" id="host-email" name="email" required autocomplete="email" class="<?php echo esc_attr( $input ); ?>" />
					</div>
					<div>
						<label for="host-phone" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Phone', 'juiced' ); ?></label>
						<input type="tel" id="host-phone" name="phone" autocomplete="tel" class="<?php echo esc_attr( $input ); ?>" />
					</div>
				</div>

				<div>
					<label for="host-organization" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Group or organization', 'juiced' ); ?></label>
					<input type="text" id="host-organization" name="organization" autocomplete="organization" class="<?php echo esc_attr( $input ); ?>" />
				</div>

				<div class="grid gap-5 sm:grid-cols-3">
					<?php if ( $event_types ) : ?>
						<div>
							<label for="host-type" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Type of event', 'juiced' ); ?></label>
							<select id="host-type" name="event_type" class="<?php echo esc_attr( $input ); ?>">
								<option value=""><?php esc_html_e( 'Choose one', 'juiced' ); ?></option>
								<?php foreach ( $event_types as $type ) : ?>
									<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
					<div>
						<label for="host-date" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Preferred date', 'juiced' ); ?></label>
						<input type="date" id="host-date" name="event_date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" class="<?php echo esc_attr( $input ); ?>" />
					</div>
					<div>
						<label for="host-guests" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'How many people?', 'juiced' ); ?></label>
						<input type="number" id="host-guests" name="guests" min="1" inputmode="numeric" class="<?php echo esc_attr( $input ); ?>" />
					</div>
				</div>

				<div>
					<label for="host-details" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Tell us about it', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
					<textarea id="host-details" name="details" rows="6" required
							  placeholder="<?php esc_attr_e( 'What you have in mind, who it\'s for, and anything you\'d need from us.', 'juiced' ); ?>"
							  class="<?php echo esc_attr( $input ); ?> resize-y"></textarea>
				</div>

				<?php juiced_captcha_field(); ?>

				<button type="submit"
						class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-brand-forest px-7 py-3.5 text-[15px] font-bold text-brand-cream transition-colors hover:bg-brand-gold hover:text-brand-ink">
					<?php echo esc_html( $button_label ); ?>
					<span aria-hidden="true">&rarr;</span>
				</button>
			</form>

		</div>
	</div>
	<script>
		// Prefill from the Contact form's "Host an Event" hand-off
		// (template-parts/contact/connect.php). Read once, then cleared;
		// fields the visitor already filled are left alone.
		( function () {
			var data;
			try {
				data = JSON.parse( sessionStorage.getItem( 'juicedHostPrefill' ) || 'null' );
				sessionStorage.removeItem( 'juicedHostPrefill' );
			} catch ( e ) {}
			if ( ! data ) {
				return;
			}
			var ids = { first_name: 'host-first-name', last_name: 'host-last-name', email: 'host-email', phone: 'host-phone', details: 'host-details' };
			Object.keys( ids ).forEach( function ( key ) {
				var el = document.getElementById( ids[ key ] );
				if ( el && ! el.value && typeof data[ key ] === 'string' ) {
					el.value = data[ key ];
				}
			} );
		} )();
	</script>
</section>
