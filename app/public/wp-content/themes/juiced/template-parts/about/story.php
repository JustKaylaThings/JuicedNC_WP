<?php
/**
 * About page Section — Our Story timeline ("Blended with Purpose. Built on
 * Passion."). From About option A.
 *
 * Two columns on the cream ground: on the left an orange "Our Story" eyebrow,
 * two-tone heading (lead green, accent orange), the intro, and an optional
 * button; on the right a vertical timeline on a green rule — each milestone
 * an orange year, green title and short description. The last milestone gets
 * a solid orange dot, marking where the story is headed. Stacks on phones.
 * The section carries id="our-story" — the mission band's button scrolls here.
 *
 * ACF is free (no repeaters), so milestones are a textarea, one per line as
 * "Year | Title | Description" (same pipe convention as the We Believe In
 * values). The year may be a word ("Next"), or empty to leave it off.
 *
 * Copy is ACF-editable on the About page (inc/acf-about-page.php). The intro
 * becomes one <p> per blank-line-separated block. No milestones → the intro
 * runs on its own; the button hides until a link is set.
 *
 * @package Juiced
 */

$eyebrow = juiced_page_field( 'about_story_eyebrow', 'Our Story' );
$lead    = juiced_page_field( 'about_story_lead', 'Blended with Purpose.' );
$accent  = juiced_page_field( 'about_story_accent', 'Built on Passion.' );
$body    = juiced_page_field( 'about_story_body', 'Juiced! started with a simple idea: create a place where healthy living and community connection go hand in hand.' );
$raw     = juiced_page_field(
	'about_timeline_items',
	"[Year] | The Idea | [How Juiced! began — who, where, and why.]\n"
	. "[Year] | Doors Open on Hill Street | [The first shop — a short memory from opening day.]\n"
	. "[Year] | Smooth Talk Is Born | Open mics, remote-work Mondays, game nights — the regulars made it theirs.\n"
	. "Next | Growing with Raleigh | We're proud of how far we've come, and even more excited about where we're going — together."
);
$label   = juiced_page_field( 'about_story_button_label', 'Read Our Full Story' );
$url     = juiced_page_field( 'about_story_button_url', '' );

// One <p> per blank-line-separated block.
$paragraphs = array_filter( array_map( 'trim', preg_split( '/\r?\n\s*\r?\n/', (string) $body ) ) );

// One milestone per line: "Year | Title | Description". Fewer pipes still
// work — a lone value is the title.
$milestones = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
	$line = trim( $line );
	if ( '' === $line ) {
		continue;
	}
	$parts = array_map( 'trim', explode( '|', $line, 3 ) );
	if ( 1 === count( $parts ) ) {
		array_unshift( $parts, '' );
	}
	$milestones[] = array(
		'year'  => $parts[0],
		'title' => $parts[1],
		'desc'  => isset( $parts[2] ) ? $parts[2] : '',
	);
}

if ( ! $lead && ! $accent && ! $paragraphs && ! $milestones ) {
	return;
}

$last = count( $milestones ) - 1;
?>
<section id="our-story" class="scroll-mt-24 bg-brand-paper text-brand-ink" aria-labelledby="about-story-h">
	<div class="mx-auto max-w-7xl px-4 py-16 md:py-26">
		<div class="grid gap-12 <?php echo $milestones ? 'lg:grid-cols-[2fr_3fr] lg:gap-16' : 'max-w-2xl'; ?>">

			<div>
				<?php if ( $eyebrow ) : ?>
					<p class="text-xs font-bold uppercase tracking-[.22em] text-brand-forest">
						<?php echo esc_html( $eyebrow ); ?>
					</p>
				<?php endif; ?>

				<h2 id="about-story-h" class="mt-3.5 font-display font-bold leading-none text-[clamp(2.5rem,5vw,3.75rem)]">
					<span class="text-brand-forest"><?php echo esc_html( $lead ); ?></span>
					<span class="text-brand-gold"><?php echo esc_html( $accent ); ?></span>
				</h2>

				<?php foreach ( $paragraphs as $paragraph ) : ?>
					<p class="mt-5 text-base leading-relaxed text-brand-bark/70 md:text-lg">
						<?php echo esc_html( $paragraph ); ?>
					</p>
				<?php endforeach; ?>

				<?php if ( $label && $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>"
					   class="mt-7 inline-flex items-center gap-2 rounded-full bg-brand-forest px-6 py-3 text-sm font-bold text-brand-cream transition-colors hover:bg-brand-gold hover:text-brand-ink">
						<?php echo esc_html( $label ); ?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( $milestones ) : ?>
				<ol class="ml-2 border-l-[3px] border-brand-forest">
					<?php foreach ( $milestones as $i => $milestone ) : ?>
						<li class="relative pl-8 md:pl-10 <?php echo $i < $last ? 'pb-11' : ''; ?>">
							<?php if ( $i === $last ) : ?>
								<span class="absolute -left-[12.5px] top-1 size-[22px] rounded-full bg-brand-gold" aria-hidden="true"></span>
							<?php else : ?>
								<span class="absolute -left-[9.5px] top-1.5 size-4 rounded-full border-[3px] border-brand-forest bg-brand-paper" aria-hidden="true"></span>
							<?php endif; ?>

							<?php if ( $milestone['year'] ) : ?>
								<p class="font-display text-xl font-bold leading-tight text-brand-gold md:text-[22px]">
									<?php echo esc_html( $milestone['year'] ); ?>
								</p>
							<?php endif; ?>

							<h3 class="mt-1.5 font-display text-2xl font-semibold leading-tight text-brand-forest md:text-[28px]">
								<?php echo esc_html( $milestone['title'] ); ?>
							</h3>

							<?php if ( $milestone['desc'] ) : ?>
								<p class="mt-2 text-base leading-relaxed text-brand-bark/70">
									<?php echo esc_html( $milestone['desc'] ); ?>
								</p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

		</div>
	</div>
</section>
