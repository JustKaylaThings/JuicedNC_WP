<?php
/**
 * About page Section — We Believe In.
 *
 * Left-aligned two-tone heading ("We Believe" green, "In" orange) over a row
 * of white rounded value cards — icon, title, blurb — four across on desktop,
 * two on tablet, stacked on phones. From About option A.
 *
 * ACF is free (no repeaters), so the values are a textarea, one line per card
 * as "Title | Short description" — the same convention as the herb Key
 * Benefits panel. The icon is keyword-matched from the title via the local
 * map below (health → sprout, community → people, positivity → heart,
 * growth → sun; anything else gets the leaf).
 *
 * Copy is ACF-editable on the About page (inc/acf-about-page.php); clearing
 * the values hides the section.
 *
 * "Juiced!" in the copy shows as the logo (juiced_logo_text()).
 *
 * @package Juiced
 */

$heading = juiced_page_field( 'about_believe_heading', 'We Believe' );
$accent  = juiced_page_field( 'about_believe_accent', 'In' );
$raw     = juiced_page_field(
	'about_believe_items',
	"Health First | We make it easy and delicious to choose what's good for you.\n"
	. "Community Always | We show up for our community and create spaces that bring us closer together.\n"
	. "Positivity | Good vibes, good energy, and good people — that's the Juiced! way.\n"
	. "Growth | We're committed to learning, evolving, and supporting one another every step of the way."
);

// One value card per line: "Title | Short description"; a line with no pipe is
// a title-only entry rather than an error.
$values = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
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

if ( ! $values ) {
	return;
}

/**
 * Icon for a value card, keyword-matched from its title (first hit wins).
 * Same idea as juiced_herb_topic_icon(), but these are brand values, not herb
 * topics, so the map lives here.
 */
$juiced_value_icons = array(
	'health'  => array(
		'text' => 'text-brand-forest',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20v-8" /><path d="M12 12C12 8.5 9.5 6 5.5 6 5.5 9.5 8 12 12 12Z" /><path d="M12 12c0-3.5 2.5-6 6.5-6 0 3.5-2.5 6-6.5 6Z" /></svg>',
	),
	'communi' => array(
		'text' => 'text-brand-rust',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3" /><path d="M4 19c0-3 2.2-5 5-5s5 2 5 5" /><circle cx="16.5" cy="9" r="2.5" /><path d="M16.5 14.5c2.4.3 3.5 2 3.5 4.5" /></svg>',
	),
	'positiv' => array(
		'text' => 'text-brand-forest',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20.5 4.7 13a5 5 0 1 1 7.3-6.8A5 5 0 1 1 19.3 13Z" /></svg>',
	),
	'grow'    => array(
		'text' => 'text-brand-rust',
		'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4" /><path d="M12 2.5v2M12 19.5v2M2.5 12h2M19.5 12h2M5.3 5.3l1.4 1.4M17.3 17.3l1.4 1.4M5.3 18.7l1.4-1.4M17.3 6.7l1.4-1.4" /></svg>',
	),
);

$juiced_value_fallback = array(
	'text' => 'text-brand-forest',
	'icon' => '<svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z" /><path d="M2 21c0-3 1.85-5.36 5.08-6" /></svg>',
);
?>
<section class="bg-brand-cream text-brand-ink" aria-labelledby="about-believe-h">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-26">

		<?php if ( $heading || $accent ) : ?>
			<h2 id="about-believe-h" class="font-display font-bold leading-none text-brand-forest text-[clamp(2.25rem,4.5vw,3.25rem)]">
				<?php echo esc_html( $heading ); ?>
				<span class="text-brand-gold"><?php echo esc_html( $accent ); ?></span>
			</h2>
		<?php endif; ?>

		<div class="mt-11 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
			<?php foreach ( $values as $value ) : ?>
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
				<div class="flex flex-col gap-2.5 rounded-[22px] border border-brand-forest/15 bg-white p-6 md:p-7">
					<div class="<?php echo esc_attr( $topic['text'] ); ?>">
						<?php echo $topic['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
					</div>
					<h3 class="mt-1 font-display text-2xl font-bold leading-tight text-brand-forest"><?php echo esc_html( $value['title'] ); ?></h3>
					<?php if ( $value['desc'] ) : ?>
						<p class="text-[15px] leading-relaxed text-brand-bark/75"><?php echo juiced_logo_text( $value['desc'], 'inline-block h-[1.9em] w-auto -my-[.5em] align-middle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
