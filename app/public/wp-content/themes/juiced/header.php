<?php
/**
 * Theme header — Section 1.
 *
 * Opens <html>, prints <head> via wp_head(), and renders the dark site header:
 * optional scheduled announcement bar, logo, primary nav, and the Order Ahead
 * button. The header sits on the page's first section, so it shares its
 * background: deep green with the leaf pattern on the pages redesigned in the
 * 2026 "Garden" look (homepage, Locations, Events, About, Contact), ink on the others
 * until they're redesigned too.
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

<?php $juiced_garden = is_front_page() || is_page_template( array( 'page-templates/locations.php', 'page-templates/events.php', 'page-templates/about.php', 'page-templates/contact.php', 'page-templates/host-event.php', 'page-templates/careers.php', 'page-templates/menu.php', 'page-templates/community.php' ) ) || is_singular( array( 'event', 'spotlight' ) ); ?>
<!-- Sticky: stays pinned while scrolling and compacts (smaller logo, less
	 padding, a shadow) once the page leaves the top. Compacting makes the
	 header about 54px shorter, and the browser's scroll anchoring pulls the
	 scroll position back by that much, so one threshold would flip it straight
	 back and the header twitches. It compacts past 72px and only expands again
	 under 8px, a gap wider than that jump. -->
<?php
// The homepage runs edge to edge: its bar and hero span the full window
// (with a wider side gutter from md) instead of the 1280px column.
$juiced_bar_width = is_front_page() ? 'md:px-14 lg:px-20 xl:px-28 2xl:px-[max(10rem,calc(50vw-40rem))]' : 'mx-auto max-w-7xl';
?>
<header class="site-header <?php echo $juiced_garden ? 'bg-brand-forest bg-garden bg-garden-above' : 'bg-brand-ink'; ?> text-brand-cream"
		x-data="{ menuOpen: false, scrolled: false }"
		x-init="scrolled = window.scrollY > 72"
		@scroll.window.passive="scrolled = window.scrollY > (scrolled ? 8 : 72)"
		:class="{ 'is-scrolled': scrolled }"
		@click.outside="menuOpen = false" @keydown.escape.window="menuOpen = false">
	<div class="site-header__bar <?php echo esc_attr( $juiced_bar_width ); ?> flex items-center justify-between gap-6 px-4 py-4">

		<div class="shrink-0">
			<?php // The Juiced! wordmark ships with the theme (the same file as the About hero), not the Customizer logo. ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home">
				<?php echo juiced_logo( 'custom-logo', true ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the helper. ?>
			</a>
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
			<?php juiced_order_button( __( 'Order Ahead', 'juiced' ), 'hidden sm:inline-flex items-center gap-2 rounded-full bg-brand-gold px-5 py-2 text-sm text-brand-ink btn-sticker' ); ?>

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
		<div class="mx-auto max-w-7xl px-4 py-4 max-h-[calc(100dvh-5rem)] overflow-y-auto">
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
			<?php juiced_order_button( __( 'Order Ahead', 'juiced' ), 'mt-4 inline-flex sm:hidden items-center gap-2 rounded-full bg-brand-gold px-5 py-2 text-sm text-brand-ink btn-sticker' ); ?>
		</div>
	</nav>
</header>

<main id="main">
