<?php
/**
 * Contact page Section 2 — Let's Connect + Send Us a Message.
 *
 * Two columns on the cream band: the left lists the ways to reach the shop
 * (phone / email / address / hours, each with a colored circle icon); the
 * right is the message form. The form posts to admin-post.php and is handled
 * by inc/contact-form.php, which redirects back here with ?contact=sent|error
 * — the notice above the form reads that flag.
 *
 * The business info itself (phone / email / address / hours) comes from the
 * site-wide settings in Appearance → Customize → Business Info
 * (inc/customizer.php) so the client enters it once and every page stays in
 * sync. Phone and hours render one labelled line per location when the two
 * locations' values differ, and a single unlabelled line when they match (or
 * only one is set); the address is location 1's — the primary shop — since
 * Visit Us below lists both in full. Headings and the form copy are
 * ACF-editable on the Contact page itself (inc/acf-contact-page.php), all
 * with mockup defaults. A row hides when its fields are cleared; the topic
 * dropdown hides when the topics field is cleared.
 *
 * @package Juiced
 */

$connect_heading = juiced_page_field( 'contact_connect_heading', 'Let\'s Connect' );
$form_heading    = juiced_page_field( 'contact_form_heading', 'Send Us a Message' );

// Site-wide business info, entered once in the Customizer.
$loc1_name = juiced_business_field( 'loc1_name', 'North Raleigh' );
$loc2_name = juiced_business_field( 'loc2_name', 'Downtown Raleigh' );
$phone1    = juiced_business_field( 'loc1_phone', '(919) 123-JUICE (5842)' );
$phone2    = juiced_business_field( 'loc2_phone', '' );
$email     = juiced_business_field( 'business_email', 'hello@juicedjuicebar.com' );
$address   = juiced_business_field( 'loc1_address', "8521 Six Forks Rd, Ste 101\nRaleigh, NC 27615" );
$hours1    = juiced_business_field( 'loc1_hours', "Mon – Fri: 7:00 AM – 8:00 PM\nSat: 8:00 AM – 8:00 PM\nSun: 9:00 AM – 6:00 PM" );
$hours2    = juiced_business_field( 'loc2_hours', '' );

$button_label = juiced_page_field( 'contact_form_button', 'Send Message' );

// Dropdown topics — one per line in ACF, mockup defaults here.
$topics = array_filter( array_map( 'trim', explode( "\n", (string) juiced_page_field(
	'contact_form_topics',
	"General Question\nEvents & Partnerships\nFeedback\nSomething Else"
) ) ) );

// tel: href from a display number's digits ("(919) 123-JUICE (5842)" → 9191235842,
// dropping the vanity-word expansion so the digits aren't doubled).
$juiced_tel = static function ( $phone ) {
	$tel = preg_replace( '/\s*\(\d+\)\s*$/', '', (string) $phone );
	$tel = preg_replace( '/[^0-9+]/', '', $tel );
	return $tel ? 'tel:' . $tel : '';
};

// One line per location when both are set and differ, one bare line otherwise.
$juiced_per_location = static function ( $value1, $value2, $href_cb = null ) use ( $loc1_name, $loc2_name ) {
	$lines = array();
	if ( $value1 && $value2 && $value1 !== $value2 ) {
		$lines[] = array( 'label' => $loc1_name, 'text' => $value1, 'href' => $href_cb ? $href_cb( $value1 ) : '' );
		$lines[] = array( 'label' => $loc2_name, 'text' => $value2, 'href' => $href_cb ? $href_cb( $value2 ) : '' );
	} elseif ( $value1 || $value2 ) {
		$value   = $value1 ? $value1 : $value2;
		$lines[] = array( 'label' => '', 'text' => $value, 'href' => $href_cb ? $href_cb( $value ) : '' );
	}
	return $lines;
};

// Contact rows: label + lines + circle color + inline icon. A row drops out
// when its fields are cleared. Colors follow the mockup (green / pink /
// orange / gold) — literal classes so Tailwind's @source scan compiles them.
$rows = array(
	array(
		'label'  => __( 'Phone', 'juiced' ),
		'lines'  => $juiced_per_location( $phone1, $phone2, $juiced_tel ),
		'circle' => 'bg-brand-green',
		'icon'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l1.5 4.5-2 1.5a12 12 0 0 0 5.5 5.5l1.5-2L20 15v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg>',
	),
	array(
		'label'  => __( 'Email', 'juiced' ),
		'lines'  => $email ? array(
			array(
				'label' => '',
				'text'  => $email,
				'href'  => 'mailto:' . $email,
			),
		) : array(),
		'circle' => 'bg-[#e0559a]',
		'icon'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
	),
	array(
		'label'  => __( 'Address', 'juiced' ),
		'lines'  => $address ? array(
			array(
				'label' => '',
				'text'  => $address,
				'href'  => '',
			),
		) : array(),
		'circle' => 'bg-brand-gold',
		'icon'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>',
	),
	array(
		'label'  => __( 'Hours', 'juiced' ),
		'lines'  => $juiced_per_location( $hours1, $hours2 ),
		'circle' => 'bg-brand-yellow',
		'icon'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
	),
);

// Result flag set by the redirect from inc/contact-form.php.
$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag.

$input_classes = 'w-full rounded-xl border border-brand-bark/15 bg-white px-4 py-3 text-sm text-brand-bark placeholder:text-brand-bark/45 focus:border-brand-green focus:outline-none focus:ring-2 focus:ring-brand-green/25';
?>
<section class="bg-brand-cream bg-grain" id="message">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">
		<div class="grid gap-12 lg:grid-cols-5 lg:gap-16">

			<!-- Let's Connect -->
			<div class="lg:col-span-2">

				<?php if ( $connect_heading ) : ?>
					<h2 class="font-display text-3xl text-brand-bark md:text-4xl">
						<?php echo esc_html( $connect_heading ); ?>
					</h2>
				<?php endif; ?>

				<ul class="mt-8 space-y-7">
					<?php foreach ( $rows as $row ) : ?>
						<?php if ( ! $row['lines'] ) : continue; endif; ?>
						<li class="flex items-start gap-4">
							<span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-white <?php echo esc_attr( $row['circle'] ); ?>">
								<?php echo $row['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
							</span>
							<div>
								<h3 class="font-semibold text-brand-bark"><?php echo esc_html( $row['label'] ); ?></h3>
								<?php foreach ( $row['lines'] as $line ) : ?>
									<?php $text = ( $line['label'] ? $line['label'] . ': ' : '' ) . $line['text']; ?>
									<?php if ( $line['href'] ) : ?>
										<a href="<?php echo esc_url( $line['href'] ); ?>"
										   class="mt-0.5 block text-sm text-brand-bark/70 transition-colors hover:text-brand-green md:text-base">
											<?php echo esc_html( $text ); ?>
										</a>
									<?php else : ?>
										<?php // Tags hug the text: whitespace-pre-line would render the template's own newlines. ?>
										<p class="mt-0.5 whitespace-pre-line text-sm leading-relaxed text-brand-bark/70 md:text-base"><?php echo esc_html( $text ); ?></p>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>

			</div>

			<!-- Send Us a Message -->
			<div class="lg:col-span-3">

				<?php if ( $form_heading ) : ?>
					<h2 class="font-display text-3xl text-brand-bark md:text-4xl">
						<?php echo esc_html( $form_heading ); ?>
					</h2>
				<?php endif; ?>

				<?php if ( 'sent' === $status ) : ?>
					<div class="mt-6 rounded-xl bg-brand-green/10 px-5 py-4 text-sm font-semibold text-brand-green" role="status">
						<?php esc_html_e( 'Thanks for reaching out — your message is on its way. We\'ll get back to you soon!', 'juiced' ); ?>
					</div>
				<?php elseif ( 'error' === $status ) : ?>
					<div class="mt-6 rounded-xl bg-[#dd1f69]/10 px-5 py-4 text-sm font-semibold text-[#dd1f69]" role="alert">
						<?php esc_html_e( 'Something went wrong sending your message. Please try again, or email us directly.', 'juiced' ); ?>
					</div>
				<?php endif; ?>

				<form class="mt-6 space-y-4" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="juiced_contact" />
					<?php wp_nonce_field( 'juiced_contact', 'juiced_contact_nonce' ); ?>

					<!-- Honeypot — hidden from people, tempting to bots. The handler
						 drops any submission that fills it. -->
					<p class="hidden" aria-hidden="true">
						<label>Leave this field empty <input type="text" name="juiced_website" tabindex="-1" autocomplete="off" /></label>
					</p>

					<div class="grid gap-4 sm:grid-cols-2">
						<div>
							<label for="juiced-first-name" class="sr-only"><?php esc_html_e( 'First Name', 'juiced' ); ?></label>
							<input type="text" id="juiced-first-name" name="first_name" required
								   placeholder="<?php esc_attr_e( 'First Name', 'juiced' ); ?>"
								   class="<?php echo esc_attr( $input_classes ); ?>" />
						</div>
						<div>
							<label for="juiced-last-name" class="sr-only"><?php esc_html_e( 'Last Name', 'juiced' ); ?></label>
							<input type="text" id="juiced-last-name" name="last_name"
								   placeholder="<?php esc_attr_e( 'Last Name', 'juiced' ); ?>"
								   class="<?php echo esc_attr( $input_classes ); ?>" />
						</div>
					</div>

					<div>
						<label for="juiced-email" class="sr-only"><?php esc_html_e( 'Email Address', 'juiced' ); ?></label>
						<input type="email" id="juiced-email" name="email" required
							   placeholder="<?php esc_attr_e( 'Email Address', 'juiced' ); ?>"
							   class="<?php echo esc_attr( $input_classes ); ?>" />
					</div>

					<div>
						<label for="juiced-phone" class="sr-only"><?php esc_html_e( 'Phone Number', 'juiced' ); ?></label>
						<input type="tel" id="juiced-phone" name="phone"
							   placeholder="<?php esc_attr_e( 'Phone Number', 'juiced' ); ?>"
							   class="<?php echo esc_attr( $input_classes ); ?>" />
					</div>

					<?php if ( $topics ) : ?>
						<div class="relative">
							<label for="juiced-topic" class="sr-only"><?php esc_html_e( 'How can we help you?', 'juiced' ); ?></label>
							<select id="juiced-topic" name="topic"
									class="<?php echo esc_attr( $input_classes ); ?> appearance-none pr-10 text-brand-bark/70">
								<option value=""><?php esc_html_e( 'How can we help you?', 'juiced' ); ?></option>
								<?php foreach ( $topics as $topic ) : ?>
									<option value="<?php echo esc_attr( $topic ); ?>"><?php echo esc_html( $topic ); ?></option>
								<?php endforeach; ?>
							</select>
							<span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-brand-bark/50" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
							</span>
						</div>
					<?php endif; ?>

					<div>
						<label for="juiced-message" class="sr-only"><?php esc_html_e( 'Your Message', 'juiced' ); ?></label>
						<textarea id="juiced-message" name="message" rows="6" required
								  placeholder="<?php esc_attr_e( 'Your Message', 'juiced' ); ?>"
								  class="<?php echo esc_attr( $input_classes ); ?> resize-y"></textarea>
					</div>

					<button type="submit"
							class="inline-flex items-center gap-2 rounded-full bg-brand-gold px-7 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-green">
						<?php echo esc_html( $button_label ); ?>
						<span aria-hidden="true">&rarr;</span>
					</button>
				</form>

			</div>

		</div>
	</div>
</section>
