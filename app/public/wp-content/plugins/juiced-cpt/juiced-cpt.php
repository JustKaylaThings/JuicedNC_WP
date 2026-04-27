<?php
/**
 * Plugin Name: Juiced CPT
 * Description: Registers Juiced NC CPTs, taxonomies, ACF field groups, and the Site Settings GraphQL bridge. Feature code lives in includes/.
 * Version: 1.1.0
 * Author: Juiced NC
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'JUICED_CPT_DIR', plugin_dir_path( __FILE__ ) );

require_once JUICED_CPT_DIR . 'includes/herb.php';
require_once JUICED_CPT_DIR . 'includes/menu-item.php';
require_once JUICED_CPT_DIR . 'includes/site-settings.php';
require_once JUICED_CPT_DIR . 'includes/site-announcement.php';
