<?php
/**
 * Theme header — Section 1.
 *
 * Opens <html>, prints <head> via wp_head(), and renders the dark site header:
 * optional scheduled announcement bar, logo, primary nav, and the Order Ahead
 * button. The header sits on the dark hero, so it shares the ink background.
 *
 * Below md the nav collapses behind the hamburger, which toggles an Alpine
 * drawer under the bar: the same wp_nav_menu stacked (.mobile-nav), plus an
 * Order Ahead button below sm where the bar's own is hidden. JS-only — with
 * JS off the drawer stays hidden (x-cloak), same as before it existed.
 *
 * @package Juiced
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-brand-paper text-brand-ink' ); ?>>
<?php wp_body_open(); ?>

<a class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-brand-gold focus:text-white focus:px-4 focus:py-2" href="#main">
	<?php esc_html_e( 'Skip to content', 'juiced' ); ?>
</a>

<?php
// Scheduled announcement bar (Phase 4 resolves the active window). Hidden unless set.
$juiced_announcement = function_exists( 'juiced_active_announcement' )
	? juiced_active_announcement()
	: '';
if ( $juiced_announcement ) :
	?>
	<div class="bg-brand-green text-brand-cream text-center text-sm py-2 px-4">
		<?php echo esc_html( $juiced_announcement ); ?>
	</div>
<?php endif; ?>

<header class="bg-brand-ink text-brand-cream" x-data="{ menuOpen: false }"
		@click.outside="menuOpen = false" @keydown.escape.window="menuOpen = false">
	<div class="mx-auto max-w-7xl flex items-center justify-between gap-6 px-4 py-4">

		<div class="shrink-0">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="font-display text-2xl text-brand-gold">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<nav class="hidden md:block" aria-label="<?php esc_attr_e( 'Primary', 'juiced' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-nav',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<div class="flex items-center gap-3">
			<a href="<?php echo esc_url( juiced_field( 'order_ahead_url', '#' ) ); ?>"
			   class="hidden sm:inline-flex items-center gap-2 rounded-full bg-brand-gold px-5 py-2 text-sm font-semibold text-white hover:bg-brand-green-light transition">
				<?php esc_html_e( 'Order Ahead', 'juiced' ); ?>
			</a>

			<button type="button" class="md:hidden text-brand-cream cursor-pointer"
					@click="menuOpen = !menuOpen"
					:aria-expanded="menuOpen"
					aria-controls="mobile-nav"
					:aria-label="menuOpen ? '<?php echo esc_attr__( 'Close menu', 'juiced' ); ?>' : '<?php echo esc_attr__( 'Open menu', 'juiced' ); ?>'"
					aria-label="<?php esc_attr_e( 'Open menu', 'juiced' ); ?>">
				<span class="text-2xl" x-show="!menuOpen">&#9776;</span>
				<span class="text-2xl" x-cloak x-show="menuOpen">&times;</span>
			</button>
		</div>

	</div>

	<!-- Mobile drawer: full-width panel under the bar, closed by the X, a tap
		 outside the header, or Escape. -->
	<nav id="mobile-nav" class="md:hidden border-t border-white/10" x-cloak x-show="menuOpen"
		 x-transition:enter="transition ease-out duration-150"
		 x-transition:enter-start="-translate-y-2 opacity-0"
		 x-transition:enter-end="translate-y-0 opacity-100"
		 aria-label="<?php esc_attr_e( 'Primary', 'juiced' ); ?>">
		<div class="mx-auto max-w-7xl px-4 py-4">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-nav',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
			<a href="<?php echo esc_url( juiced_field( 'order_ahead_url', '#' ) ); ?>"
			   class="mt-4 inline-flex sm:hidden items-center gap-2 rounded-full bg-brand-gold px-5 py-2 text-sm font-semibold text-white hover:bg-brand-green-light transition">
				<?php esc_html_e( 'Order Ahead', 'juiced' ); ?>
			</a>
		</div>
	</nav>
</header>

<main id="main">
