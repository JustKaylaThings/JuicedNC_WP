<?php
/**
 * Single Herb Section — "You May Also Like".
 *
 * Up to five related herbs as compact library cards (card.php with
 * compact => true), so a herb looks the same here as it does in the library
 * grid it links back to. Herbs sharing a category with this one come first,
 * alphabetically; when that shelf runs short the row pads out with other
 * herbs rather than rendering half-empty. The mockup's side arrows are a
 * carousel affordance for more cards than fit — with the row capped at five,
 * everything already fits, so there is nothing to scroll to.
 *
 * A "View All Herbs" link rides the heading, mirroring the single event's
 * "More Upcoming Events" strip. Renders nothing when this is the only herb.
 *
 * @package Juiced
 */

$herb_id = get_the_ID();
$max     = 5;

$cat_ids = wp_get_post_terms( $herb_id, 'herb_category', array( 'fields' => 'ids' ) );
$cat_ids = is_wp_error( $cat_ids ) ? array() : $cat_ids;

// Same-category herbs first…
$related = array();
if ( $cat_ids ) {
	$related = get_posts(
		array(
			'post_type'      => 'herb',
			'posts_per_page' => $max,
			'post__not_in'   => array( $herb_id ),
			'orderby'        => 'title',
			'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'herb_category',
					'field'    => 'term_id',
					'terms'    => $cat_ids,
				),
			),
		)
	);
}

// …then pad with whatever else the library holds.
if ( count( $related ) < $max ) {
	$related = array_merge(
		$related,
		get_posts(
			array(
				'post_type'      => 'herb',
				'posts_per_page' => $max - count( $related ),
				'post__not_in'   => array_merge( array( $herb_id ), wp_list_pluck( $related, 'ID' ) ),
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		)
	);
}

if ( empty( $related ) ) {
	return;
}
?>
<section class="bg-brand-cream bg-grain">
	<div class="mx-auto max-w-7xl px-4 pb-16 md:pb-24">

		<div class="flex flex-wrap items-end justify-between gap-4">
			<h2 class="font-display text-3xl text-brand-bark md:text-4xl">
				<?php esc_html_e( 'You May Also Like', 'juiced' ); ?>
			</h2>
			<a href="<?php echo esc_url( juiced_herbs_page_url() ); ?>"
			   class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-gold hover:underline">
				<?php esc_html_e( 'View All Herbs', 'juiced' ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		</div>

		<div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-5 lg:grid-cols-5">
			<?php foreach ( $related as $juiced_related ) : ?>
				<?php
				$juiced_card = juiced_herb_card_data( $juiced_related );
				if ( $juiced_card ) {
					get_template_part(
						'template-parts/herbs/card',
						null,
						array(
							'card'    => $juiced_card,
							'compact' => true,
						)
					);
				}
				?>
			<?php endforeach; ?>
		</div>

	</div>
</section>
