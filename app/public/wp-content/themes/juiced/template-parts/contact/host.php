<?php
/**
 * Contact page Section 4 — Host an Event.
 *
 * A green garden band under Visit Us inviting people to book the space: a
 * two-tone heading (cream + orange, like the hero), a short pitch, and an
 * orange "Inquire Today" button that goes straight to the form on the Host an
 * Event page (its form section carries id="event-form").
 *
 * Copy is ACF-editable on the Contact page (inc/acf-contact-page.php), with
 * defaults below. Clearing both heading fields hides the band; clearing the
 * button label hides the button.
 *
 * "Juiced!" in the heading shows as the logo (juiced_logo_text()).
 *
 * @package Juiced
 */

$heading = juiced_page_field( 'contact_host_heading', 'Host Your Next Event' );
$accent  = juiced_page_field( 'contact_host_accent', 'at Juiced!' );
$body    = juiced_page_field( 'contact_host_body', "Birthdays, workshops, pop-ups, book clubs, and wellness meetups — we've hosted them all." );
$label   = juiced_page_field( 'contact_host_button', 'Inquire Today' );
$url     = juiced_host_event_page_url();

if ( ! $heading && ! $accent ) {
	return;
}

if ( $url ) {
	$url .= '#event-form';
}
?>
<section class="bg-brand-forest bg-garden text-brand-cream" aria-labelledby="contact-host-h">
	<div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-16 md:py-20 lg:flex-row lg:items-center lg:justify-between lg:gap-16">

		<div class="max-w-2xl">
			<h2 id="contact-host-h" class="font-display font-bold leading-[1.02] text-[clamp(2.25rem,5vw,3.5rem)]">
				<?php echo esc_html( $heading ); ?>
				<span class="block text-brand-gold"><?php echo juiced_logo_text( $accent ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></span>
			</h2>

			<?php if ( $body ) : ?>
				<p class="mt-5 text-lg font-medium leading-relaxed text-brand-mint md:text-xl">
					<?php echo esc_html( $body ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( $label && $url ) : ?>
			<a href="<?php echo esc_url( $url ); ?>"
			   class="inline-flex shrink-0 items-center gap-2 self-start rounded-full bg-brand-gold px-7 py-4 text-brand-ink lg:self-center btn-sticker">
				<?php echo esc_html( $label ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		<?php endif; ?>

	</div>
</section>
