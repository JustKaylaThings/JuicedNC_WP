<?php
/**
 * "Order Ahead" picker — which shop, then which app.
 *
 * Rendered once per page from footer.php and opened by every Order Ahead
 * button (juiced_order_button() in inc/helpers.php dispatches 'order-picker'
 * with the button). Two layouts from one panel:
 *
 * - md and up (tablet/desktop): a dropdown pinned under the button that was
 *   clicked, listing every location with its app buttons. It is position:
 *   fixed at the button's measured spot, so it closes on scroll or resize
 *   rather than drifting off the button.
 * - Phones: a bottom sheet over a dimmed page, with a location toggle and
 *   large app buttons for the chosen shop. The page behind stops scrolling.
 *
 * Either closes on Escape, a click outside, or the X (sheet). The locations
 * and links come from Business Info (juiced_order_locations()); with only one
 * location the toggle is dropped. No links at all → nothing renders and the
 * buttons stay plain links.
 *
 * @package Juiced
 */

$juiced_order_locs = juiced_order_locations();

if ( ! $juiced_order_locs ) {
	return;
}
$juiced_order_multi = count( $juiced_order_locs ) > 1;
?>
<div x-data="{
		open: false,
		desktop: false,
		loc: 0,
		top: 0,
		right: 0,
		trigger: null,
		show(el) {
			if (this.open && this.trigger === el) { this.close(); return; }
			if (this.trigger && this.trigger !== el) this.trigger.setAttribute('aria-expanded', 'false');
			this.trigger = el;
			this.desktop = window.matchMedia('(min-width: 768px)').matches;
			const r = el.getBoundingClientRect();
			this.top = r.bottom + 12;
			this.right = Math.max(16, window.innerWidth - r.right);
			this.open = true;
			el.setAttribute('aria-expanded', 'true');
			document.documentElement.classList.toggle('overflow-hidden', !this.desktop);
			this.$nextTick(() => this.$refs.panel.focus({ preventScroll: true }));
		},
		close(refocus = true) {
			if (!this.open) return;
			this.open = false;
			document.documentElement.classList.remove('overflow-hidden');
			if (this.trigger) {
				this.trigger.setAttribute('aria-expanded', 'false');
				if (refocus) this.trigger.focus({ preventScroll: true });
			}
		}
	}"
	@order-picker.window="show($event.detail)"
	@keydown.escape.window="close()"
	@scroll.window.passive="if (open && desktop) close(false)"
	@resize.window="close(false)">

	<!-- Phone backdrop. -->
	<div x-cloak x-show="open && !desktop" @click="close()"
		 x-transition.opacity.duration.200ms
		 class="fixed inset-0 z-[90] bg-brand-ink/55 md:hidden" aria-hidden="true"></div>

	<div x-ref="panel" x-cloak x-show="open" tabindex="-1"
		 role="dialog" aria-label="<?php esc_attr_e( 'Order ahead', 'juiced' ); ?>"
		 @click.outside="if (!trigger || !trigger.contains($event.target)) close(false)"
		 :style="desktop ? { top: top + 'px', right: right + 'px' } : {}"
		 x-transition:enter="transition ease-out duration-200"
		 x-transition:enter-start="translate-y-full md:-translate-y-1 md:opacity-0"
		 x-transition:enter-end="translate-y-0 md:opacity-100"
		 x-transition:leave="transition ease-in duration-150"
		 x-transition:leave-start="translate-y-0 md:opacity-100"
		 x-transition:leave-end="translate-y-full md:-translate-y-1 md:opacity-0"
		 class="fixed inset-x-0 bottom-0 z-[100] max-h-[85dvh] overflow-y-auto rounded-t-[28px] bg-brand-cream px-5 pt-3 pb-8 text-brand-ink shadow-[0_-10px_40px_rgba(0,0,0,.25)] outline-none
				md:inset-x-auto md:bottom-auto md:w-[380px] md:rounded-[22px] md:p-5 md:shadow-[0_24px_60px_rgba(10,30,14,.4)]">

		<!-- Phone: bottom sheet. -->
		<div class="md:hidden">
			<div class="mx-auto mb-4 h-1.5 w-11 rounded-full bg-brand-clay" aria-hidden="true"></div>
			<div class="flex items-center justify-between">
				<h2 class="font-display text-3xl font-bold text-brand-forest"><?php esc_html_e( 'Order ahead', 'juiced' ); ?></h2>
				<button type="button" @click="close()" aria-label="<?php esc_attr_e( 'Close', 'juiced' ); ?>"
						class="grid size-11 place-items-center rounded-full bg-brand-paper text-brand-forest">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
				</button>
			</div>

			<?php if ( $juiced_order_multi ) : ?>
				<div role="group" aria-label="<?php esc_attr_e( 'Location', 'juiced' ); ?>"
					 class="mt-4 grid gap-1 rounded-full bg-brand-paper p-1" style="grid-template-columns: repeat(<?php echo (int) count( $juiced_order_locs ); ?>, minmax(0, 1fr));">
					<?php foreach ( $juiced_order_locs as $juiced_k => $juiced_loc ) : ?>
						<button type="button" @click="loc = <?php echo (int) $juiced_k; ?>"
								:aria-pressed="(loc === <?php echo (int) $juiced_k; ?>).toString()"
								class="min-h-11 rounded-full text-[15px] transition-colors"
								:class="loc === <?php echo (int) $juiced_k; ?> ? 'bg-brand-forest font-bold text-brand-cream' : 'font-semibold text-brand-forest'">
							<?php echo esc_html( $juiced_loc['name'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php foreach ( $juiced_order_locs as $juiced_k => $juiced_loc ) : ?>
				<div x-show="loc === <?php echo (int) $juiced_k; ?>" <?php echo $juiced_k ? 'x-cloak' : ''; ?>>
					<p class="mx-1 mt-3.5 text-sm text-brand-bark/75">
						<?php echo esc_html( $juiced_order_multi ? $juiced_loc['street'] : trim( $juiced_loc['name'] . ' · ' . $juiced_loc['street'], ' ·' ) ); ?>
					</p>
					<div class="mt-4 flex flex-col gap-2.5">
						<?php foreach ( $juiced_loc['apps'] as $juiced_app ) : ?>
							<a href="<?php echo esc_url( $juiced_app['url'] ); ?>" target="_blank" rel="noopener"
							   class="flex min-h-14 items-center justify-between rounded-2xl px-5 text-[17px] font-bold transition-colors <?php echo esc_attr( $juiced_app['class'] ); ?>">
								<?php echo esc_html( $juiced_app['label'] ); ?>
								<span aria-hidden="true">&rarr;</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Tablet/desktop: dropdown. -->
		<div class="hidden md:block">
			<p class="mb-3.5 text-xs font-bold uppercase tracking-[.2em] text-brand-forest"><?php esc_html_e( 'Order from…', 'juiced' ); ?></p>
			<div class="flex flex-col gap-2.5">
				<?php foreach ( $juiced_order_locs as $juiced_loc ) : ?>
					<div class="flex flex-col gap-3 rounded-2xl bg-brand-paper p-4">
						<div class="flex items-baseline justify-between gap-3">
							<span class="font-display text-[22px] font-semibold text-brand-forest"><?php echo esc_html( $juiced_loc['name'] ); ?></span>
							<?php if ( $juiced_loc['street'] ) : ?>
								<span class="text-[13px] text-brand-bark/75"><?php echo esc_html( $juiced_loc['street'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="flex flex-wrap gap-2">
							<?php foreach ( $juiced_loc['apps'] as $juiced_app ) : ?>
								<a href="<?php echo esc_url( $juiced_app['url'] ); ?>" target="_blank" rel="noopener"
								   class="inline-flex min-h-11 items-center rounded-full px-4 text-sm font-bold transition-colors <?php echo esc_attr( $juiced_app['class'] ); ?>">
									<?php echo esc_html( $juiced_app['label'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</div>
