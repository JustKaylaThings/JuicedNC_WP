<?php
/**
 * About page Section 2 — Our Mission + We Believe In.
 *
 * One cream band, two columns: the mission statement on the left — two-tone
 * heading ("Our" dark, "Mission" orange), body, and a green "Our Story" button
 * that jumps to the Our Story section further down the page (its partial
 * carries id="our-story") — and the "We Believe In" value cards on the right,
 * a four-up row of icon / title / blurb divided by hairlines.
 *
 * ACF is free (no repeaters), so the values are a textarea, one line per card
 * as "Title | Short description" — the same convention as the herb Key
 * Benefits panel. The icon is keyword-matched from the title via the local
 * map below (health → sprout, community → people, positivity → heart,
 * growth → sun; anything else gets the leaf).
 *
 * Copy is ACF-editable on the About page (inc/acf-about-page.php); defaults
 * below match the approved mockup. Empty fields drop their block: no values →
 * the mission stands alone; no mission → the values take the full width;
 * clearing the button label hides the button.
 *
 * @package Juiced
 */

$heading = juiced_page_field( 'about_mission_heading', 'Our' );
$accent  = juiced_page_field( 'about_mission_accent', 'Mission' );
$body    = juiced_page_field( 'about_mission_body', 'To blend health and community by serving delicious, nutrient-packed offerings and creating spaces where people can connect, grow, and thrive together.' );
$label   = juiced_page_field( 'about_mission_button_label', 'Our Story' );
$url     = juiced_page_field( 'about_mission_button_url', '#our-story' );

$believe_heading = juiced_page_field( 'about_believe_heading', 'We Believe In' );
$believe_raw     = juiced_page_field(
	'about_believe_items',
	"Health First | We make it easy and delicious to choose what's good for you.\n"
	. "Community Always | We show up for our community and create spaces that bring us closer together.\n"
	. "Positivity | Good vibes, good energy, and good people — that's the Juiced! way.\n"
	. "Growth | We're committed to learning, evolving, and supporting one another every step of the way."
);

// One value card per line: "Title | Short description"; a line with no pipe is
// a title-only entry rather than an error.
$values = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) $believe_raw ) as $line ) {
	$line = trim( $line );
	if ( '' === $line ) {
		continue;
	}
	$parts    = array_map( 'trim', explode( '|', $line, 2 ) );
	$values[] = array(
		'title' => $parts[0],
		'desc'  => isset( $parts[1] ) ? $parts[1] : '',
	);
}

$has_mission = $heading || $accent || $body;

if ( ! $has_mission && ! $values ) {
	return;
}

/**
 * Icon for a We Believe In card, keyword-matched from its title (first hit
 * wins). Same idea as juiced_herb_topic_icon(), but these are brand values,
 * not herb topics, so the map lives here.
 */
$juiced_value_icons = array(
	'health'  => array(
		'text' => 'text-brand-green',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20v-8" /><path d="M12 12C12 8.5 9.5 6 5.5 6 5.5 9.5 8 12 12 12Z" /><path d="M12 12c0-3.5 2.5-6 6.5-6 0 3.5-2.5 6-6.5 6Z" /></svg>',
	),
	'communi' => array(
		'text' => 'text-brand-gold',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3" /><path d="M4 19c0-3 2.2-5 5-5s5 2 5 5" /><circle cx="16.5" cy="9" r="2.5" /><path d="M16.5 14.5c2.4.3 3.5 2 3.5 4.5" /></svg>',
	),
	'positiv' => array(
		'text' => 'text-[#e0559a]',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20.5 4.7 13a5 5 0 1 1 7.3-6.8A5 5 0 1 1 19.3 13Z" /></svg>',
	),
	'grow'    => array(
		'text' => 'text-brand-gold',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4" /><path d="M12 2.5v2M12 19.5v2M2.5 12h2M19.5 12h2M5.3 5.3l1.4 1.4M17.3 17.3l1.4 1.4M5.3 18.7l1.4-1.4M17.3 6.7l1.4-1.4" /></svg>',
	),
);

$juiced_value_fallback = array(
	'text' => 'text-brand-green',
	'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" /><path d="M2 21c0-3 1.85-5.36 5.08-6" /></svg>',
);
?>
<section class="bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">
		<div class="grid items-center gap-12 <?php echo ( $has_mission && $values ) ? 'lg:grid-cols-3 lg:gap-14' : ''; ?>">

			<?php if ( $has_mission ) : ?>
				<div class="max-w-md">

					<h2 class="font-display text-3xl text-brand-bark md:text-4xl">
						<?php echo esc_html( $heading ); ?>
						<span class="text-brand-gold"><?php echo esc_html( $accent ); ?></span>
					</h2>

					<?php if ( $body ) : ?>
						<p class="mt-4 text-base leading-relaxed text-brand-bark/70 md:text-lg">
							<?php echo esc_html( $body ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $label && $url ) : ?>
						<a href="<?php echo esc_url( $url ); ?>"
						   class="mt-7 inline-flex items-center gap-2 rounded-full bg-brand-green px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-brand-gold">
							<?php echo esc_html( $label ); ?>
							<span aria-hidden="true">&rarr;</span>
						</a>
					<?php endif; ?>

				</div>
			<?php endif; ?>

			<?php if ( $values ) : ?>
				<div class="<?php echo $has_mission ? 'lg:col-span-2' : ''; ?>">

					<?php if ( $believe_heading ) : ?>
						<h2 class="text-center font-display text-2xl text-brand-bark md:text-3xl">
							<?php echo esc_html( $believe_heading ); ?>
						</h2>
					<?php endif; ?>

					<div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-0">
						<?php foreach ( $values as $i => $value ) : ?>
							<?php
							$topic = $juiced_value_fallback;
							$slug  = sanitize_title( $value['title'] );
							foreach ( $juiced_value_icons as $keyword => $candidate ) {
								if ( false !== strpos( $slug, $keyword ) ) {
									$topic = $candidate;
									break;
								}
							}
							?>
							<div class="text-center lg:px-5 <?php echo $i > 0 ? 'lg:border-l lg:border-brand-bark/10' : ''; ?>">
								<div class="flex justify-center <?php echo esc_attr( $topic['text'] ); ?>">
									<?php echo $topic['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
								</div>
								<h3 class="mt-3 font-bold text-brand-bark"><?php echo esc_html( $value['title'] ); ?></h3>
								<?php if ( $value['desc'] ) : ?>
									<p class="mt-2 text-sm leading-relaxed text-brand-bark/70"><?php echo esc_html( $value['desc'] ); ?></p>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
