=== Custom Option Page for ACF ===
Plugin Name: Custom Option Page for ACF
Author: Parimal Patel
Contributors: parimalpatel
Tags: acf, options page, options
Requires at least: 4.6
Tested up to: 6.8
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create and manage custom options pages in your WordPress admin using Advanced Custom Fields (ACF). Perfect for theme or plugin settings.

== Description ==
The ACF Custom Option Page plugin allows you to create custom option pages using Advanced Custom Fields (ACF). This plugin enhances your WordPress admin interface by providing a seamless way to manage options with ACF's powerful field management system.

== Installation ==
1. Upload the `custom-option-page-for-acf` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Ensure the Advanced Custom Fields (ACF) plugin is installed and activated, as this plugin is dependent on it.

**Usage Example:**

This plugin saves values in the WordPress options table using the format `copacf_options_FIELD_NAME`.

To retrieve an option value on the frontend, use WordPress’s native get_option() function:

    get_option('copacf_options_FIELD_NAME')

Replace FIELD_NAME with the actual name of your ACF field.

== Frequently Asked Questions ==

= Does this plugin require ACF? =
Yes, this plugin requires the Advanced Custom Fields (ACF) plugin to be installed and activated.

= Is this plugin compatible with the latest WordPress version? =
Yes, tested up to WordPress 6.8. Always check the 'Tested up to' value for the latest compatibility.

== Screenshots ==
1. The custom option page interface showing the main settings panel.
2. Example of field management with ACF integration, displaying various field types.
3. Example of ACF Option Page.

== Changelog ==

= 1.1 =
* fixes some minor changes

= 1.0 =
* Initial Release

= 1.2 =
* fixes some minor changes
