<?php
/**
 * Email sign-up popup ("Garden Modal", Oct 2026) — rendered site-wide from
 * footer.php.
 *
 * A deep-green card with faint leaf outlines, centred over a dimmed page. It
 * opens once per visitor after a delay or a scroll (the `signupPopup` Alpine
 * component in assets/js/main.js decides when) and stays away for a while
 * after it is dismissed, and for good after a sign-up.
 *
 * Submits to inc/newsletter.php with fetch(); the reply swaps the form for a
 * thank-you. All copy and the on/off switch are ACF-editable on the Home page
 * (Footer & Newsletter tab, inc/acf-homepage.php).
 *
 * "Juiced!" in the copy shows as the logo (juiced_logo_text()).
 *
 * @package Juiced
 */

// On unless the switch was saved off. (Reads the raw meta: ACF only applies
// the field's "on" default once the Home page has been saved again.)
if ( '0' === get_post_meta( juiced_front_id(), 'popup_enabled', true ) ) {
	return;
}

$juiced_pp_eyebrow = juiced_field( 'popup_eyebrow', 'The Juiced! List' );
$juiced_pp_heading = juiced_field( 'popup_heading', 'Get the good stuff first.' );
$juiced_pp_body    = juiced_field( 'popup_body', 'New drinks, pop-up events and community news from our garden to your inbox. No spam, just juice.' );
$juiced_pp_button  = juiced_field( 'popup_button', 'Count me in' );

// Leaf outline from assets/images/garden-leaves.svg, reused at two corners.
$juiced_pp_leaf = '<path d="M0 0C10-26 40-34 62-30C56-6 30 10 0 0Z"/><path d="M0 0C20-10 40-20 62-30"/><path d="M22-10l4-10M38-18l5-8"/>';
?>
<div x-data="signupPopup" x-cloak x-show="open"
	 @keydown.escape.window="dismiss()"
	 class="fixed inset-0 z-[100] flex items-center justify-center bg-brand-forest-deep/65 p-4"
	 x-transition:enter="transition ease-out duration-200"
	 x-transition:enter-start="opacity-0"
	 x-transition:enter-end="opacity-100"
	 x-transition:leave="transition ease-in duration-150"
	 x-transition:leave-start="opacity-100"
	 x-transition:leave-end="opacity-0"
	 @click.self="dismiss()">

	<div x-ref="dialog" role="dialog" aria-modal="true" aria-labelledby="juiced-popup-heading"
		 @keydown.tab="trap($event)"
		 class="relative w-full max-w-[480px] overflow-hidden rounded-[28px] bg-brand-forest px-6 pt-12 pb-9 text-center text-white shadow-2xl ring-1 ring-brand-mint/25 sm:px-11">

		<svg class="pointer-events-none absolute -top-8 -left-10 h-44 w-44" viewBox="0 0 120 120" fill="none" stroke="#cfe8d3" stroke-opacity=".18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<g transform="translate(20 80) rotate(-28)"><?php echo $juiced_pp_leaf; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?></g>
		</svg>
		<svg class="pointer-events-none absolute -right-12 -bottom-14 h-52 w-52" viewBox="0 0 120 120" fill="none" stroke="#cfe8d3" stroke-opacity=".18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<g transform="translate(30 90) rotate(-50)"><?php echo $juiced_pp_leaf; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static markup. ?></g>
		</svg>

		<button type="button" @click="dismiss()" aria-label="<?php esc_attr_e( 'Close', 'juiced' ); ?>"
			class="absolute top-3.5 right-3.5 flex h-11 w-11 cursor-pointer items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>

		<div class="relative flex flex-col items-center gap-3.5">
			<p class="eyebrow m-0 text-brand-gold"><?php echo juiced_logo_text( $juiced_pp_eyebrow, 'inline-block h-[2.6em] w-auto -my-[.9em] align-middle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></p>

			<template x-if="state !== 'done'">
				<div class="flex w-full flex-col items-center gap-3.5">
					<h2 id="juiced-popup-heading" class="m-0 font-display text-[clamp(2.25rem,8vw,2.75rem)] leading-[1.02]"><?php echo esc_html( $juiced_pp_heading ); ?></h2>
					<?php if ( $juiced_pp_body ) : ?>
						<p class="m-0 max-w-[340px] text-base leading-normal text-brand-mint"><?php echo esc_html( $juiced_pp_body ); ?></p>
					<?php endif; ?>

					<form @submit.prevent="submit($el)" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"
						  class="mt-2.5 flex w-full flex-col gap-2.5">
						<input type="hidden" name="action" value="juiced_newsletter" />
						<input type="hidden" name="source" value="popup" />
						<div class="absolute -left-[9999px]" aria-hidden="true">
							<label for="juiced-popup-website"><?php esc_html_e( 'Leave this empty', 'juiced' ); ?></label>
							<input id="juiced-popup-website" type="text" name="juiced_website" tabindex="-1" autocomplete="off" />
						</div>

						<label class="sr-only" for="juiced-popup-email"><?php esc_html_e( 'Email address', 'juiced' ); ?></label>
						<input x-ref="email" id="juiced-popup-email" type="email" name="email" required autocomplete="email"
							placeholder="<?php esc_attr_e( 'you@email.com', 'juiced' ); ?>"
							class="h-13 w-full rounded-full border-0 bg-brand-cream px-5 text-base text-brand-ink placeholder:text-brand-ink/50 focus:outline-none focus-visible:ring-4 focus-visible:ring-brand-gold/60" />
						<?php juiced_captcha_field(); ?>
						<button type="submit" :disabled="state === 'sending'"
							class="h-13 cursor-pointer rounded-full bg-brand-gold text-base font-bold text-brand-ink hover:bg-brand-cream disabled:cursor-wait disabled:opacity-70 transition-colors">
							<span x-show="state !== 'sending'"><?php echo esc_html( $juiced_pp_button ); ?></span>
							<span x-show="state === 'sending'"><?php esc_html_e( 'Joining…', 'juiced' ); ?></span>
						</button>
						<p x-show="state === 'error'" role="alert" class="m-0 text-sm font-medium text-brand-gold">
							<?php esc_html_e( 'That didn’t go through. Check your email address and try again.', 'juiced' ); ?>
						</p>
					</form>

					<button type="button" @click="dismiss()" class="mt-1 cursor-pointer text-[13px] text-brand-mint underline underline-offset-2 hover:text-white">
						<?php esc_html_e( 'No thanks, I’m good', 'juiced' ); ?>
					</button>
				</div>
			</template>

			<template x-if="state === 'done'">
				<div class="flex flex-col items-center gap-3.5" role="status">
					<h2 id="juiced-popup-heading" class="m-0 font-display text-[clamp(2.25rem,8vw,2.75rem)] leading-[1.02]"><?php esc_html_e( 'You’re on the list!', 'juiced' ); ?></h2>
					<p class="m-0 max-w-[340px] text-base leading-normal text-brand-mint"><?php echo juiced_logo_text( __( 'Thanks for joining. Keep an eye on your inbox for what’s next at Juiced!', 'juiced' ), 'inline-block h-[1.9em] w-auto -my-[.5em] align-middle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?></p>
					<button type="button" @click="dismiss()"
						class="mt-2.5 h-13 cursor-pointer rounded-full bg-brand-gold px-8 text-base font-bold text-brand-ink hover:bg-brand-cream transition-colors">
						<?php esc_html_e( 'Back to the site', 'juiced' ); ?>
					</button>
				</div>
			</template>
		</div>
	</div>
</div>
