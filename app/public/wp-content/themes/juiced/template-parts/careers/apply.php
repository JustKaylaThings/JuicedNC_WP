<?php
/**
 * Careers — split screen: the pitch beside a three-step job application.
 *
 * The left half is the deep-green "Garden" panel: the two-tone headline,
 * subtext, an optional team photo and the "Why Work With Us" perks as tiles
 * (one per line of an ACF textarea; cleared, the tiles go). The right half is
 * the form, split into About you / Availability / Your story. The steps are
 * Alpine (jobApplication in assets/js/main.js): Next checks the step's
 * required fields before moving on. Without JS every step shows and the form
 * reads top to bottom with the submit button at the end.
 *
 * The form posts to admin-post.php (action=juiced_job_application), handled
 * by inc/page-forms.php; it redirects back with ?apply=sent|error|resume.
 * Sent swaps the whole form for a thank-you; the other two show a notice
 * above it. The optional résumé is emailed as an attachment.
 *
 * @package Juiced
 */

$lead    = juiced_page_field( 'careers_hero_lead', 'Join the' );
$accent  = juiced_page_field( 'careers_hero_accent', 'Juiced! Team' );
$subtext = juiced_page_field( 'careers_hero_subtext', 'Love fresh food and good people? Come make juice with us and get to know the neighborhood one cup at a time.' );
$image   = juiced_image_url( juiced_page_field( 'careers_hero_image', '' ), 'large' );

$why_heading = juiced_page_field( 'careers_why_heading', 'Why Choose Juiced!' );
$perks       = array_filter(
	array_map(
		'trim',
		explode(
			"\n",
			(string) juiced_page_field(
				'careers_perks',
				"Flexible part-time schedules\nA positive work environment\nGrowth and leadership opportunities\nDirect community engagement"
			)
		)
	)
);

$heading      = juiced_page_field( 'careers_form_heading', 'Apply Now' );
$intro        = juiced_page_field( 'careers_form_intro', 'It takes about five minutes. We read every application.' );
$button_label = juiced_page_field( 'careers_form_button', 'Submit Application' );
$locations    = juiced_location_names();

$steps = array(
	1 => __( 'About you', 'juiced' ),
	2 => __( 'Availability', 'juiced' ),
	3 => __( 'Your story', 'juiced' ),
);

// Result flag set by the redirect from inc/page-forms.php.
$status = isset( $_GET['apply'] ) ? sanitize_key( wp_unslash( $_GET['apply'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag.

$input  = juiced_form_input_classes() . ' bg-white';
$label  = 'mb-1.5 block text-sm font-semibold text-brand-forest';
$legend = 'font-display text-2xl font-bold text-brand-forest focus:outline-none md:text-3xl';
$next   = 'inline-flex cursor-pointer items-center gap-2 rounded-full bg-brand-gold px-7 py-3.5 text-[15px] font-bold text-brand-ink transition-colors hover:bg-brand-forest hover:text-brand-cream';
$back   = 'inline-flex cursor-pointer items-center gap-2 rounded-full px-4 py-3.5 text-[15px] font-bold text-brand-forest underline-offset-4 hover:underline';
?>
<section class="relative -mt-px grid bg-brand-cream text-brand-ink lg:grid-cols-2" id="apply">

	<div class="bg-brand-forest bg-garden text-brand-cream">
		<div class="mx-auto max-w-2xl px-4 pt-8 pb-14 sm:px-8 md:pt-10 md:pb-20 lg:ml-auto lg:mr-0 lg:max-w-[640px] lg:pl-4 lg:pr-16">

			<h1 class="font-display font-bold leading-[.9] tracking-[-.03em] text-[clamp(3rem,9vw,4.25rem)] lg:text-[clamp(2.75rem,4.6vw,4.25rem)]">
				<?php if ( $lead ) : ?>
					<span class="block"><?php echo esc_html( $lead ); ?></span>
				<?php endif; ?>
				<?php if ( $accent ) : ?>
					<span class="block text-brand-gold"><?php echo esc_html( $accent ); ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( $subtext ) : ?>
				<p class="mt-6 max-w-md text-lg font-medium leading-relaxed md:text-xl"><?php echo esc_html( $subtext ); ?></p>
			<?php endif; ?>

			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt=""
					 class="mt-8 aspect-[4/3] w-full rounded-3xl object-cover" loading="eager" fetchpriority="high" />
			<?php endif; ?>

			<?php if ( $perks ) : ?>
				<?php if ( $why_heading ) : ?>
					<h2 class="mt-10 font-display text-2xl font-bold text-brand-gold md:text-3xl"><?php echo esc_html( $why_heading ); ?></h2>
				<?php endif; ?>
				<ul class="mt-4 grid gap-3 sm:grid-cols-2">
					<?php foreach ( $perks as $perk ) : ?>
						<li class="flex items-center gap-3 rounded-2xl bg-brand-forest-deep/60 px-4 py-3.5">
							<span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-gold text-brand-ink" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
							</span>
							<span class="text-[15px] leading-snug text-brand-mint"><?php echo esc_html( $perk ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
	</div>

	<div x-data="jobApplication">
		<div class="mx-auto max-w-2xl px-4 pt-8 pb-14 sm:px-8 md:pt-10 md:pb-20 lg:ml-0 lg:mr-auto lg:max-w-[640px] lg:pl-16 lg:pr-4">

			<?php if ( 'sent' === $status ) : ?>

				<!-- Sent: the thank-you stands in for the whole form. -->
				<div class="flex flex-col items-start gap-5" role="status">
					<span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-forest text-brand-cream" aria-hidden="true">
						<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
					</span>
					<h2 class="font-display text-3xl font-bold text-brand-forest md:text-4xl"><?php esc_html_e( 'Thanks for applying!', 'juiced' ); ?></h2>
					<p class="max-w-md text-lg leading-relaxed text-brand-ink/75"><?php esc_html_e( 'We\'ve got your application and will reach out if it\'s a fit.', 'juiced' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
					   class="mt-2 inline-flex items-center gap-2 rounded-full bg-brand-forest px-7 py-3.5 text-[15px] font-bold text-brand-cream transition-colors hover:bg-brand-gold hover:text-brand-ink">
						<?php esc_html_e( 'Back to home', 'juiced' ); ?>
						<span aria-hidden="true">&rarr;</span>
					</a>
				</div>

			<?php else : ?>

			<?php if ( $heading ) : ?>
				<h2 class="font-display text-3xl font-bold text-brand-forest md:text-4xl"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="mt-2 text-brand-ink/75"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>

			<?php if ( 'resume' === $status ) : ?>
				<div class="mt-6 rounded-xl bg-brand-rust/10 px-5 py-4 text-sm font-semibold text-brand-rust" role="alert">
					<?php esc_html_e( 'We couldn\'t take that résumé. Please upload a PDF or Word file under 5 MB, or apply without one.', 'juiced' ); ?>
				</div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="mt-6 rounded-xl bg-brand-rust/10 px-5 py-4 text-sm font-semibold text-brand-rust" role="alert">
					<?php esc_html_e( 'Something went wrong sending your application. Please check the required fields and try again.', 'juiced' ); ?>
				</div>
			<?php endif; ?>

			<!-- Progress bar — only meaningful once Alpine splits the steps. -->
			<ol class="mt-8 grid grid-cols-3 gap-2" x-cloak aria-label="<?php esc_attr_e( 'Application steps', 'juiced' ); ?>">
				<?php foreach ( $steps as $n => $name ) : ?>
					<li class="flex flex-col gap-2" :aria-current="step === <?php echo (int) $n; ?> ? 'step' : null">
						<span class="h-1.5 rounded-full transition-colors" :class="step >= <?php echo (int) $n; ?> ? 'bg-brand-forest' : 'bg-brand-forest/15'"></span>
						<span class="text-[13px] font-semibold" :class="step >= <?php echo (int) $n; ?> ? 'text-brand-forest' : 'text-brand-ink/60'"><?php echo (int) $n; ?> · <?php echo esc_html( $name ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>

			<form class="mt-8" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="juiced_job_application" />
				<?php wp_nonce_field( 'juiced_job_application', 'juiced_apply_nonce' ); ?>

				<!-- Honeypot — hidden from people, tempting to bots. -->
				<p class="hidden" aria-hidden="true">
					<label>Leave this field empty <input type="text" name="juiced_website" tabindex="-1" autocomplete="off" /></label>
				</p>

				<!-- Step 1 — About you -->
				<fieldset class="space-y-5" x-ref="step1" x-show="step === 1">
					<legend class="<?php echo esc_attr( $legend ); ?>" tabindex="-1"><?php echo esc_html( $steps[1] ); ?></legend>
					<div class="grid gap-5 sm:grid-cols-2">
						<div>
							<label for="apply-first-name" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'First name', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
							<input type="text" id="apply-first-name" name="first_name" required autocomplete="given-name" class="<?php echo esc_attr( $input ); ?>" />
						</div>
						<div>
							<label for="apply-last-name" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Last name', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
							<input type="text" id="apply-last-name" name="last_name" required autocomplete="family-name" class="<?php echo esc_attr( $input ); ?>" />
						</div>
					</div>
					<div>
						<label for="apply-email" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Email', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
						<input type="email" id="apply-email" name="email" required autocomplete="email" class="<?php echo esc_attr( $input ); ?>" />
					</div>
					<div>
						<label for="apply-phone" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Phone', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
						<input type="tel" id="apply-phone" name="phone" required autocomplete="tel" class="<?php echo esc_attr( $input ); ?>" />
					</div>
					<?php if ( $locations ) : ?>
						<div>
							<label for="apply-location" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Which shop?', 'juiced' ); ?></label>
							<select id="apply-location" name="location" class="<?php echo esc_attr( $input ); ?>">
								<option value=""><?php esc_html_e( 'Either is fine', 'juiced' ); ?></option>
								<?php foreach ( $locations as $location ) : ?>
									<option value="<?php echo esc_attr( $location ); ?>"><?php echo esc_html( $location ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
					<div class="flex justify-end pt-2" x-cloak>
						<button type="button" class="<?php echo esc_attr( $next ); ?>" @click="next()">
							<?php
							/* translators: %s: name of the next step. */
							echo esc_html( sprintf( __( 'Next: %s', 'juiced' ), strtolower( $steps[2] ) ) );
							?>
							<span aria-hidden="true">&rarr;</span>
						</button>
					</div>
				</fieldset>

				<!-- Step 2 — Availability. mt-10 spaces the steps when they all show
					 (no JS); Alpine drops it since only one shows at a time. -->
				<fieldset class="mt-10 space-y-5" x-ref="step2" x-show="step === 2" :class="{ 'mt-10': false }">
					<legend class="<?php echo esc_attr( $legend ); ?>" tabindex="-1"><?php echo esc_html( $steps[2] ); ?></legend>
					<div>
						<label for="apply-start" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'When can you start?', 'juiced' ); ?></label>
						<input type="date" id="apply-start" name="start_date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" class="<?php echo esc_attr( $input ); ?>" />
					</div>
					<div>
						<label for="apply-availability" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Your availability', 'juiced' ); ?> <span class="text-brand-rust">*</span></label>
						<textarea id="apply-availability" name="availability" rows="4" required
								  placeholder="<?php esc_attr_e( 'Which days and times can you work? e.g. weekday mornings, all day Saturday.', 'juiced' ); ?>"
								  class="<?php echo esc_attr( $input ); ?> resize-y"></textarea>
					</div>
					<div class="flex items-center justify-between gap-4 pt-2" x-cloak>
						<button type="button" class="<?php echo esc_attr( $back ); ?>" @click="back()"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Back', 'juiced' ); ?></button>
						<button type="button" class="<?php echo esc_attr( $next ); ?>" @click="next()">
							<?php
							/* translators: %s: name of the next step. */
							echo esc_html( sprintf( __( 'Next: %s', 'juiced' ), strtolower( $steps[3] ) ) );
							?>
							<span aria-hidden="true">&rarr;</span>
						</button>
					</div>
				</fieldset>

				<!-- Step 3 — Your story, then submit -->
				<fieldset class="mt-10 space-y-5" x-ref="step3" x-show="step === 3" :class="{ 'mt-10': false }">
					<legend class="<?php echo esc_attr( $legend ); ?>" tabindex="-1"><?php echo esc_html( $steps[3] ); ?></legend>
					<div>
						<label for="apply-about" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Tell us about yourself', 'juiced' ); ?></label>
						<textarea id="apply-about" name="about" rows="5"
								  placeholder="<?php esc_attr_e( 'Past jobs, what you\'re into, why Juiced!', 'juiced' ); ?>"
								  class="<?php echo esc_attr( $input ); ?> resize-y"></textarea>
					</div>
					<div>
						<label for="apply-resume" class="<?php echo esc_attr( $label ); ?>"><?php esc_html_e( 'Résumé (optional)', 'juiced' ); ?></label>
						<input type="file" id="apply-resume" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
							   aria-describedby="apply-resume-help"
							   class="block w-full text-sm text-brand-ink/75 file:mr-4 file:cursor-pointer file:rounded-full file:border-0 file:bg-brand-forest file:px-5 file:py-2.5 file:text-sm file:font-bold file:text-brand-cream hover:file:bg-brand-gold hover:file:text-brand-ink" />
						<p id="apply-resume-help" class="mt-1.5 text-sm text-brand-ink/60"><?php esc_html_e( 'PDF or Word, up to 5 MB.', 'juiced' ); ?></p>
					</div>

					<?php juiced_captcha_field(); ?>

					<div class="flex items-center justify-between gap-4 pt-2">
						<button type="button" class="<?php echo esc_attr( $back ); ?>" @click="back()" x-cloak><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Back', 'juiced' ); ?></button>
						<button type="submit"
								class="ml-auto inline-flex cursor-pointer items-center gap-2 rounded-full bg-brand-forest px-7 py-3.5 text-[15px] font-bold text-brand-cream transition-colors hover:bg-brand-gold hover:text-brand-ink">
							<?php echo esc_html( $button_label ); ?>
							<span aria-hidden="true">&rarr;</span>
						</button>
					</div>
				</fieldset>
			</form>

			<?php endif; ?>

		</div>
	</div>

</section>
