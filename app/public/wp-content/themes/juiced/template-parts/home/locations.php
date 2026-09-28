<?php
/**
 * Homepage Section 6 — Two neighborhoods ("Garden" redesign, Sep 2026).
 *
 * Full-bleed, two panels side by side (stacked on phones): orange for the
 * first location, green for the second. Each has a storefront photo on top,
 * then a small label, the name, address · phone, hours · parking and a
 * "Get directions" link.
 *
 * The names, addresses, phones, hours, parking and directions links are the
 * site-wide business info (Appearance → Customize → Business Info,
 * inc/customizer.php); a blank directions link falls back to a Google Maps
 * search for the address, like the Locations page. The small labels and
 * photos are ACF-editable on the Home page (inc/acf-homepage.php); a panel
 * with no photo gets a plain decorative block.
 *
 * Fixed at two locations to match the design; a location with no name is
 * skipped and the other then spans the full width.
 *
 * @package Juiced
 */

$juiced_hoods = array();
$juiced_hood_styles = array(
	1 => array(
		'panel'  => 'bg-brand-gold text-brand-ink',
		'label'  => 'text-brand-ink',
		'link'   => 'text-brand-ink hover:text-brand-forest-deep',
		'block'  => 'bg-brand-rust',
		'circle' => 'bg-brand-gold',
		'label_default' => 'Neighborhood 01',
		'focus'  => 'object-[50%_22%]', // Rock Quarry storefront: keep the "Juice Bar" sign in frame.
	),
	2 => array(
		'panel'  => 'bg-brand-forest text-brand-cream',
		'label'  => 'text-brand-gold',
		'link'   => 'text-brand-cream hover:text-brand-gold',
		'block'  => 'bg-brand-forest-deep',
		'circle' => 'bg-brand-forest',
		'label_default' => 'Neighborhood 02 · Events home',
		'focus'  => 'object-[50%_52%]', // Hill Street: tall shot, crop past the sky to the mural wall.
	),
);

foreach ( $juiced_hood_styles as $i => $style ) {
	$name = juiced_business_field( "loc{$i}_name", '' );
	if ( ! $name ) {
		continue;
	}

	// Address lines and hours ranges are stored one per line; show them inline.
	$address = implode( ', ', array_filter( array_map( 'trim', preg_split( '/\R/', (string) juiced_business_field( "loc{$i}_address", '' ) ) ) ) );
	$hours   = implode( ' · ', array_filter( array_map( 'trim', preg_split( '/\R/', (string) juiced_business_field( "loc{$i}_hours", '' ) ) ) ) );
	$phone   = juiced_business_field( "loc{$i}_phone", '' );
	$parking = juiced_business_field( "loc{$i}_parking", '' );

	$directions = juiced_business_field( "loc{$i}_directions", '' );
	if ( ! $directions && $address ) {
		$directions = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $name . ' ' . $address );
	}

	$image = juiced_field( "locations_photo_{$i}", '' );

	$juiced_hoods[] = array(
		'name'       => $name,
		'label'      => juiced_field( "locations_label_{$i}", $style['label_default'] ),
		'address'    => $address,
		'phone'      => $phone,
		'hours'      => implode( ' · ', array_filter( array( $hours, $parking ) ) ),
		'directions' => $directions,
		'img'        => juiced_image_url( $image, 'large' ),
		'alt'        => is_array( $image ) ? ( $image['alt'] ?? '' ) : '',
		'style'      => $style,
	);
}

if ( ! $juiced_hoods ) {
	return;
}
?>
<section class="grid <?php echo count( $juiced_hoods ) > 1 ? 'md:grid-cols-2' : ''; ?>" aria-label="<?php esc_attr_e( 'Our locations', 'juiced' ); ?>">
	<?php foreach ( $juiced_hoods as $hood ) : ?>
		<?php $style = $hood['style']; ?>
		<div class="flex flex-col <?php echo esc_attr( $style['panel'] ); ?>">
			<figure class="relative m-0! h-56 md:h-70 overflow-hidden">
				<?php if ( $hood['img'] ) : ?>
					<img src="<?php echo esc_url( $hood['img'] ); ?>" alt="<?php echo esc_attr( $hood['alt'] ); ?>"
						 class="h-full w-full object-cover <?php echo esc_attr( $style['focus'] ); ?>" loading="lazy" />
				<?php else : ?>
					<div class="relative h-full w-full overflow-hidden <?php echo esc_attr( $style['block'] ); ?>" aria-hidden="true">
						<span class="absolute -right-12 -top-16 h-64 w-64 rounded-full opacity-60 <?php echo esc_attr( $style['circle'] ); ?>"></span>
						<span class="absolute left-[14%] -bottom-10 h-36 w-36 rounded-full opacity-40 <?php echo esc_attr( $style['circle'] ); ?>"></span>
					</div>
				<?php endif; ?>
			</figure>

			<div class="flex flex-col gap-2 px-6 py-8 md:px-10 lg:px-16 lg:py-9 text-base leading-normal">
				<?php if ( $hood['label'] ) : ?>
					<span class="text-xs font-bold uppercase tracking-[.22em] <?php echo esc_attr( $style['label'] ); ?>"><?php echo esc_html( $hood['label'] ); ?></span>
				<?php endif; ?>
				<h3 class="font-display text-[34px] md:text-[40px] font-bold leading-[1.1]"><?php echo esc_html( $hood['name'] ); ?></h3>
				<?php if ( $hood['address'] || $hood['phone'] ) : ?>
					<p class="m-0">
						<?php echo esc_html( $hood['address'] ); ?>
						<?php if ( $hood['address'] && $hood['phone'] ) : ?>
							<span aria-hidden="true">·</span>
						<?php endif; ?>
						<?php if ( $hood['phone'] ) : ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $hood['phone'] ) ); ?>" class="whitespace-nowrap underline-offset-2 hover:underline"><?php echo esc_html( $hood['phone'] ); ?></a>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<?php if ( $hood['hours'] ) : ?>
					<p class="m-0"><?php echo esc_html( $hood['hours'] ); ?></p>
				<?php endif; ?>
				<?php if ( $hood['directions'] ) : ?>
					<a href="<?php echo esc_url( $hood['directions'] ); ?>" target="_blank" rel="noopener noreferrer"
					   class="mt-1 self-start font-bold underline underline-offset-4 transition-colors <?php echo esc_attr( $style['link'] ); ?>">
						<?php esc_html_e( 'Get directions', 'juiced' ); ?> <span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</section>
