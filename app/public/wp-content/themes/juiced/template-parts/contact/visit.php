<?php
/**
 * Contact page Section 3 — Visit Us.
 *
 * "Visit Us" heading and tagline over the two white location cards, side by
 * side on one row (pin + name + address + storefront photo + Get Directions).
 *
 * The location names, addresses and directions links are the site-wide
 * business info in Appearance → Customize → Business Info (inc/customizer.php)
 * — the same settings the homepage cards and the Locations page use, so the
 * client enters them once. Only this section's dressing (heading, tagline,
 * card photos) is ACF-editable on the Contact page.
 *
 * Empty fields drop their block: a slot hides when its name and address are
 * both empty, the photo hides when cleared, and the whole section renders
 * nothing when both slots are empty. Get Directions falls back to a Google
 * Maps search for the address when no URL is set.
 *
 * @package Juiced
 */

$heading = juiced_page_field( 'contact_visit_heading', 'Visit Us' );
$subtext = juiced_page_field( 'contact_visit_subtext', 'Two locations. One community.' );

$directions_label = juiced_page_field( 'contact_visit_directions_label', 'Get Directions' );

// Per-slot pin color, matching the mockup (slot 1 green, slot 2 gold).
$pin_colors = array(
	1 => 'text-brand-green',
	2 => 'text-brand-gold',
);

$locations = array();
foreach ( range( 1, 2 ) as $i ) {
	$name    = juiced_business_field( "loc{$i}_name", 1 === $i ? 'North Raleigh' : 'Downtown Raleigh' );
	$address = juiced_business_field( "loc{$i}_address", 1 === $i ? "8521 Six Forks Rd, Ste 101\nRaleigh, NC 27615" : "220 W Martin St\nRaleigh, NC 27601" );
	if ( ! $name && ! $address ) {
		continue;
	}

	$directions = juiced_business_field( "loc{$i}_directions", '' );
	if ( ! $directions && $address ) {
		$directions = 'https://www.google.com/maps/search/?api=1&query='
			. rawurlencode( trim( preg_replace( '/\s+/', ' ', $name . ' ' . $address ) ) );
	}

	$locations[] = array(
		'name'       => $name,
		'address'    => $address,
		'photo'      => juiced_image_url( juiced_page_field( "contact_visit_loc{$i}_photo", '' ), 'juiced_card' ),
		'directions' => $directions,
		'pin'        => $pin_colors[ $i ],
	);
}

if ( ! $locations ) {
	return;
}
?>
<section class="bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 pb-14 md:pb-20">

		<?php if ( $heading ) : ?>
			<h2 class="font-display text-3xl text-brand-bark md:text-4xl">
				<?php echo esc_html( $heading ); ?>
			</h2>
		<?php endif; ?>

		<?php if ( $subtext ) : ?>
			<p class="mt-2 text-sm text-brand-bark/70 md:text-base"><?php echo esc_html( $subtext ); ?></p>
		<?php endif; ?>

		<!-- Location cards — side by side from md up, stacked on small screens -->
		<div class="mt-8 grid gap-6 md:grid-cols-2 lg:gap-8">
			<?php foreach ( $locations as $location ) : ?>
				<div class="flex items-center gap-5 rounded-2xl bg-white p-5 shadow-sm">

					<span class="shrink-0 self-start <?php echo esc_attr( $location['pin'] ); ?>" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor"><path d="M12 2a7.5 7.5 0 0 0-7.5 7.5c0 5.3 6.4 11.6 6.9 12.1a.9.9 0 0 0 1.2 0c.5-.5 6.9-6.8 6.9-12.1A7.5 7.5 0 0 0 12 2Zm0 10.2a2.9 2.9 0 1 1 0-5.8 2.9 2.9 0 0 1 0 5.8Z"/></svg>
					</span>

					<div class="grow">
						<?php if ( $location['name'] ) : ?>
							<h3 class="font-display text-lg text-brand-bark"><?php echo esc_html( $location['name'] ); ?></h3>
						<?php endif; ?>
						<?php if ( $location['address'] ) : ?>
							<?php // Tags hug the text: whitespace-pre-line would render the template's own newlines. ?>
							<p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-brand-bark/70"><?php echo esc_html( $location['address'] ); ?></p>
						<?php endif; ?>
						<?php if ( $directions_label && $location['directions'] ) : ?>
							<a href="<?php echo esc_url( $location['directions'] ); ?>" target="_blank" rel="noopener"
							   class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-green transition-colors hover:text-brand-gold">
								<?php echo esc_html( $directions_label ); ?>
								<span aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( $location['photo'] ) : ?>
						<img src="<?php echo esc_url( $location['photo'] ); ?>" alt=""
							 class="hidden h-24 w-32 shrink-0 rounded-xl object-cover sm:block" loading="lazy" />
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
