<?php
/**
 * Locations page Section 2 — Find Your Juiced!
 *
 * Centered two-tone heading over one white card split into two location
 * columns, divided by a hairline. Each column: pin + location name + address,
 * icon rows for phone / hours / parking, an interior photo, a map image, and
 * a tinted card with a Get Directions button and that location's DoorDash
 * ordering button. Location 1 dresses in brand green, location 2 in the
 * mockup's pink — matching the hero's pills.
 *
 * Each location's name, address, phone, hours, parking and directions link
 * are the site-wide business info in Appearance → Customize → Business Info
 * (inc/customizer.php) — the same settings the homepage cards and the Contact
 * page read, so the client enters them once. Only this section's
 * dressing (headings, photos, maps, ordering links) is ACF-editable on the
 * Locations page itself (inc/acf-locations-page.php). Empty fields drop
 * their block: a detail row, photo, map, or button hides when its field is
 * cleared; a slot hides when its name and address are both empty; the
 * section renders nothing when both slots are. The Get Directions button
 * falls back to a Google Maps search for the address when no URL is set.
 *
 * @package Juiced
 */

$lead   = juiced_page_field( 'locations_find_lead', 'Find Your' );
$accent = juiced_page_field( 'locations_find_accent', 'Juiced!' );

$directions_label = juiced_page_field( 'locations_directions_label', 'Get Directions' );

// Per-slot palette, matching the hero pills (slot 1 green, slot 2 pink).
$palettes = array(
	1 => array(
		'text'   => 'text-brand-green',
		'tint'   => 'bg-brand-green/10',
		'button' => 'bg-brand-green',
	),
	2 => array(
		'text'   => 'text-[#dd1f69]',
		'tint'   => 'bg-[#dd1f69]/10',
		'button' => 'bg-[#dd1f69]',
	),
);

$locations = array();
foreach ( range( 1, 2 ) as $i ) {
	$name    = juiced_business_field( "loc{$i}_name", '' );
	$address = juiced_business_field( "loc{$i}_address", '' );
	if ( ! $name && ! $address ) {
		continue;
	}

	$directions = juiced_business_field( "loc{$i}_directions", '' );
	if ( ! $directions && $address ) {
		$directions = 'https://www.google.com/maps/search/?api=1&query='
			. rawurlencode( trim( preg_replace( '/\s+/', ' ', $name . ' ' . $address ) ) );
	}

	$locations[] = array(
		'name'        => $name,
		'address'     => $address,
		'phone'       => juiced_business_field( "loc{$i}_phone", '' ),
		'hours'       => juiced_business_field( "loc{$i}_hours", '' ),
		'parking'     => juiced_business_field( "loc{$i}_parking", '' ),
		'photo'       => juiced_image_url( juiced_page_field( "locations_loc{$i}_photo", '' ), 'juiced_card' ),
		'map'         => juiced_image_url( juiced_page_field( "locations_loc{$i}_map", '' ), 'large' ),
		'order_url'   => juiced_page_field( "locations_order_url{$i}", '' ),
		'order_label' => juiced_page_field( "locations_order_label{$i}", 1 === $i ? 'Order Rock Quarry' : 'Order Hill Street' ),
		'directions'  => $directions,
		'palette'     => $palettes[ $i ],
	);
}

if ( ! $locations ) {
	return;
}

// Detail-row icons (phone / hours / parking), colored per location.
$juiced_detail_icons = array(
	'phone'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l1.5 4.5-2 1.5a12 12 0 0 0 5.5 5.5l1.5-2L20 15v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z" /></svg>',
	'hours'   => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>',
	'parking' => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13 6.5 8a2 2 0 0 1 1.9-1.5h7.2A2 2 0 0 1 17.5 8L19 13" /><path d="M4 13h16a1 1 0 0 1 1 1v3h-2.2M3 17V14a1 1 0 0 1 1-1" /><circle cx="7.2" cy="17" r="1.8" /><circle cx="16.8" cy="17" r="1.8" /><path d="M9 17h6" /></svg>',
);
?>
<section class="bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">

		<?php if ( $lead || $accent ) : ?>
			<h2 class="text-center font-display text-3xl text-brand-bark md:text-4xl">
				<?php echo esc_html( $lead ); ?>
				<span class="text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h2>
		<?php endif; ?>

		<div class="mt-10 grid rounded-3xl bg-white shadow-sm md:mt-12 md:grid-cols-2 md:divide-x md:divide-brand-bark/10">
			<?php foreach ( $locations as $location ) : ?>
				<?php $palette = $location['palette']; ?>
				<div class="p-6 md:p-10">

					<div class="flex gap-6">
						<div class="grow">

							<div class="flex items-start gap-4">
								<span class="shrink-0 <?php echo esc_attr( $palette['text'] ); ?>">
									<svg viewBox="0 0 24 24" width="34" height="34" fill="currentColor" aria-hidden="true"><path d="M12 2a7.5 7.5 0 0 0-7.5 7.5c0 5.3 6.4 11.6 6.9 12.1a.9.9 0 0 0 1.2 0c.5-.5 6.9-6.8 6.9-12.1A7.5 7.5 0 0 0 12 2Zm0 10.2a2.9 2.9 0 1 1 0-5.8 2.9 2.9 0 0 1 0 5.8Z" /></svg>
								</span>
								<div>
									<?php if ( $location['name'] ) : ?>
										<h3 class="font-display text-2xl <?php echo esc_attr( $palette['text'] ); ?>">
											<?php echo esc_html( $location['name'] ); ?>
										</h3>
									<?php endif; ?>
									<?php if ( $location['address'] ) : ?>
										<?php // Tags hug the text: whitespace-pre-line would render the template's own newlines/indentation. ?>
										<p class="mt-2 whitespace-pre-line text-sm font-semibold leading-relaxed text-brand-bark/90 md:text-base"><?php echo esc_html( $location['address'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>

							<ul class="mt-6 space-y-3 text-sm text-brand-bark/80 md:text-base">
								<?php foreach ( array( 'phone', 'hours', 'parking' ) as $detail ) : ?>
									<?php if ( $location[ $detail ] ) : ?>
										<li class="flex items-center gap-4">
											<!-- Icon gutter matches the pin's width so every
												 text line shares the same left edge. -->
											<span class="flex w-[34px] shrink-0 justify-center <?php echo esc_attr( $palette['text'] ); ?>">
												<?php echo $juiced_detail_icons[ $detail ]; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
											</span>
											<?php echo esc_html( $location[ $detail ] ); ?>
										</li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>

						</div>

						<?php if ( $location['photo'] ) : ?>
							<img src="<?php echo esc_url( $location['photo'] ); ?>" alt=""
								 class="hidden h-52 w-2/5 shrink-0 self-start rounded-2xl object-cover sm:block md:h-60" loading="lazy" />
						<?php endif; ?>
					</div>

					<?php if ( $location['map'] ) : ?>
						<img src="<?php echo esc_url( $location['map'] ); ?>" alt=""
							 class="mt-6 h-48 w-full rounded-2xl object-cover md:h-56" loading="lazy" />
					<?php endif; ?>

					<?php if ( ( $directions_label && $location['directions'] ) || $location['order_url'] ) : ?>
						<div class="mt-6 flex flex-wrap items-center gap-3 rounded-2xl <?php echo esc_attr( $palette['tint'] ); ?> p-5 md:p-6">

							<?php if ( $directions_label && $location['directions'] ) : ?>
								<a href="<?php echo esc_url( $location['directions'] ); ?>" target="_blank" rel="noopener"
								   class="inline-flex items-center gap-2 rounded-full <?php echo esc_attr( $palette['button'] ); ?> px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-gold">
									<?php echo esc_html( $directions_label ); ?>
									<span aria-hidden="true">&rarr;</span>
								</a>
							<?php endif; ?>

							<?php if ( $location['order_url'] ) : ?>
								<!-- DoorDash-branded button: DoorDash red with the wing mark,
									 since the ordering links go to DoorDash. -->
								<a href="<?php echo esc_url( $location['order_url'] ); ?>" target="_blank" rel="noopener"
								   class="inline-flex items-center gap-2.5 rounded-full bg-[#ff3008] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#d62a07]">
									<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
										<path d="M2.9 5.5c-.5 0-.9.4-.9.9s.4.9.9.9h12.8c1.2 0 2.2.7 2.7 1.7H8.1c-.5 0-.9.4-.9.9s.4.9.9.9h10.4c-.3 1.2-1.4 2.1-2.7 2.1H5.4c-.5 0-.9.4-.9.9s.4.9.9.9h10.4c2.9 0 5.2-2.4 5.2-5.3 0-2.7-2.2-4.9-5-4.9H2.9Z" />
									</svg>
									<?php echo esc_html( $location['order_label'] ); ?>
								</a>
							<?php endif; ?>

						</div>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
