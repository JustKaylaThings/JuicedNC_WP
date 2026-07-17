<?php
/**
 * Single Herb Section 2 — Overview + Key Benefits.
 *
 * Two columns on the cream ground: the herb's story on the left — post content
 * as the paragraph, then the `benefits` field as a green checklist, one line
 * per row — and the pale-green "Key Benefits" panel on the right, fed by the
 * `key_benefits` field ("Title | Short description", one per line). Panel
 * icons come from keywords in each title via juiced_herb_topic_icon(), the
 * same map the tags use everywhere else, so "Supports Digestion" here wears
 * the same bowl as a Digestion tag on a card.
 *
 * Section identity ("Overview", "Key Benefits") is hardcoded like "Find Your
 * Vibe" — the copy inside is what's editable. Empty fields drop their block;
 * with the left column empty the panel takes the full width, and with nothing
 * at all the section renders nothing.
 *
 * @package Juiced
 */

$herb_id = get_the_ID();

$overview = trim( (string) get_the_content() );

// The checklist: one line per row, blanks skipped.
$bullets = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) get_field( 'benefits', $herb_id ) ) as $line ) {
	$line = trim( $line );
	if ( '' !== $line ) {
		$bullets[] = $line;
	}
}

// The panel: "Title | Short description" per line; a line with no pipe is a
// title-only entry rather than an error.
$key_benefits = array();
foreach ( preg_split( '/\r\n|\r|\n/', (string) get_field( 'key_benefits', $herb_id ) ) as $line ) {
	$line = trim( $line );
	if ( '' === $line ) {
		continue;
	}
	$parts          = array_map( 'trim', explode( '|', $line, 2 ) );
	$key_benefits[] = array(
		'title' => $parts[0],
		'desc'  => isset( $parts[1] ) ? $parts[1] : '',
	);
}

$has_left = $overview || $bullets;

if ( ! $has_left && ! $key_benefits ) {
	return;
}
?>
<section class="bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 py-14 md:py-20">
		<div class="grid gap-10 lg:grid-cols-5 lg:gap-12">

			<?php if ( $has_left ) : ?>
				<div class="lg:col-span-2">
					<h2 class="font-display text-3xl text-brand-bark md:text-4xl"><?php esc_html_e( 'Overview', 'juiced' ); ?></h2>

					<?php if ( $overview ) : ?>
						<div class="entry-content mt-4 text-brand-bark/80">
							<?php the_content(); ?>
						</div>
					<?php endif; ?>

					<?php if ( $bullets ) : ?>
						<ul class="mt-6 space-y-3">
							<?php foreach ( $bullets as $bullet ) : ?>
								<li class="flex items-start gap-3">
									<span class="mt-0.5 shrink-0 text-brand-green">
										<svg viewBox="0 0 24 24" width="19" height="19" fill="currentColor" aria-hidden="true">
											<circle cx="12" cy="12" r="10" />
											<path d="m8 12.4 2.6 2.6 5.6-5.6" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
									<span class="text-base text-brand-bark"><?php echo esc_html( $bullet ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $key_benefits ) : ?>
				<div class="<?php echo $has_left ? 'lg:col-span-3' : 'lg:col-span-5'; ?>">
					<div class="rounded-3xl bg-brand-green/10 p-7 md:p-9">
						<h2 class="font-display text-2xl text-brand-green md:text-3xl"><?php esc_html_e( 'Key Benefits', 'juiced' ); ?></h2>

						<div class="mt-7 grid gap-x-8 gap-y-7 sm:grid-cols-2">
							<?php foreach ( $key_benefits as $benefit ) : ?>
								<?php $topic = juiced_herb_topic_icon( sanitize_title( $benefit['title'] ) ); ?>
								<div class="flex items-start gap-4">
									<span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-white shadow-sm <?php echo esc_attr( $topic['text'] ); ?> [&_svg]:h-5 [&_svg]:w-5">
										<?php echo $topic['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG. ?>
									</span>
									<div>
										<h3 class="font-semibold text-brand-bark"><?php echo esc_html( $benefit['title'] ); ?></h3>
										<?php if ( $benefit['desc'] ) : ?>
											<p class="mt-1 text-sm leading-relaxed text-brand-bark/70"><?php echo esc_html( $benefit['desc'] ); ?></p>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
