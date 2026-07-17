<?php
/**
 * Homepage Section 6 — Locations ("Two Convenient Locations in Raleigh, NC").
 *
 * Light band: a centered two-tone heading over a three-part row — two locations
 * (green pin on the left, orange pin on the right) flanking a round center emblem.
 * Each location shows a name, a multi-line address, and a "Get Directions" link.
 * The names, addresses and links are the site-wide business info (Appearance →
 * Customize → Business Info, inc/customizer.php); the heading and emblem are
 * ACF-editable on the Home page (inc/acf-homepage.php), the emblem falling back
 * to the site logo.
 *
 * The design is intentionally fixed at two locations (matching the mockup); a
 * third would mean promoting this to a CPT.
 *
 * @package Juiced
 */

$juiced_l_heading = juiced_field( 'locations_heading', 'Two Convenient Locations in' );
$juiced_l_accent  = juiced_field( 'locations_heading_accent', 'Raleigh, NC' );

// Center emblem: ACF image → site custom logo → branded fallback circle.
$juiced_emblem = juiced_image_url( juiced_field( 'locations_emblem', '' ), 'thumbnail' );
if ( ! $juiced_emblem ) {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src           = wp_get_attachment_image_src( $logo_id, 'medium' );
		$juiced_emblem = $src ? $src[0] : '';
	}
}

// The two locations. Pin color is fixed by position to match the mockup.
$juiced_locations = array(
	array(
		'name'       => juiced_business_field( 'loc1_name', 'North Raleigh' ),
		'address'    => juiced_business_field( 'loc1_address', "8521 Six Forks Rd, Ste 101\nRaleigh, NC 27615" ),
		'directions' => juiced_business_field( 'loc1_directions', '' ),
		'pin'        => '#38a54c', // brand green
	),
	array(
		'name'       => juiced_business_field( 'loc2_name', 'Downtown Raleigh' ),
		'address'    => juiced_business_field( 'loc2_address', "220 W Martin St\nRaleigh, NC 27601" ),
		'directions' => juiced_business_field( 'loc2_directions', '' ),
		'pin'        => '#f2811f', // brand gold/orange
	),
);
?>
<section class="relative bg-brand-cream bg-grain overflow-hidden">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-20">

		<!-- Section heading -->
		<h2 class="text-center font-display text-3xl md:text-4xl text-brand-bark mb-12 md:mb-16">
			<?php echo esc_html( $juiced_l_heading ); ?>
			<span class="text-brand-gold"><?php echo esc_html( $juiced_l_accent ); ?></span>
		</h2>

		<div class="grid gap-10 md:grid-cols-3 items-center">

			<!-- Location 1 -->
			<?php $loc = $juiced_locations[0]; ?>
			<div class="flex items-start justify-center md:justify-end gap-4">
				<span class="shrink-0"><?php echo juiced_map_pin( $loc['pin'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?></span>
				<div class="min-w-0">
					<h3 class="font-display font-bold text-xl text-brand-bark"><?php echo esc_html( $loc['name'] ); ?></h3>
					<p class="mt-1 text-brand-bark/70 leading-relaxed"><?php echo nl2br( esc_html( $loc['address'] ) ); ?></p>
					<?php if ( $loc['directions'] ) : ?>
						<a href="<?php echo esc_url( $loc['directions'] ); ?>" target="_blank" rel="noopener noreferrer"
						   class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-brand-gold hover:text-brand-green transition-colors">
							<?php esc_html_e( 'Get Directions', 'juiced' ); ?>
							<span aria-hidden="true">&rarr;</span>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Center emblem -->
			<div class="flex justify-center">
				<?php if ( $juiced_emblem ) : ?>
					<img src="<?php echo esc_url( $juiced_emblem ); ?>" alt=""
						 class="w-28 h-28 md:w-32 md:h-32 rounded-full object-cover ring-1 ring-black/5 shadow-sm" loading="lazy" />
				<?php else : ?>
					<div class="w-28 h-28 md:w-32 md:h-32 rounded-full bg-brand-bark grid place-items-center text-brand-cream shadow-sm">
						<span class="font-display font-bold text-lg">Juiced!</span>
					</div>
				<?php endif; ?>
			</div>

			<!-- Location 2 -->
			<?php $loc = $juiced_locations[1]; ?>
			<div class="flex items-start justify-center md:justify-start gap-4">
				<span class="shrink-0"><?php echo juiced_map_pin( $loc['pin'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?></span>
				<div class="min-w-0">
					<h3 class="font-display font-bold text-xl text-brand-bark"><?php echo esc_html( $loc['name'] ); ?></h3>
					<p class="mt-1 text-brand-bark/70 leading-relaxed"><?php echo nl2br( esc_html( $loc['address'] ) ); ?></p>
					<?php if ( $loc['directions'] ) : ?>
						<a href="<?php echo esc_url( $loc['directions'] ); ?>" target="_blank" rel="noopener noreferrer"
						   class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-brand-gold hover:text-brand-green transition-colors">
							<?php esc_html_e( 'Get Directions', 'juiced' ); ?>
							<span aria-hidden="true">&rarr;</span>
						</a>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</div>
</section>
