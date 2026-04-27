<?php
/*
Plugin Name: Custom Option Page for ACF
Description: Creates custom option pages with ACF.
Version: 1.2
Author: Parimal Patel
Tested up to: 6.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: custom-option-page-for-acf
*/

/* Plugin Licence

Copyright 2014 PARIMAL PATEL (email : patelparimal1991@gmail.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

namespace CustomOptionPageACF;

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
register_activation_hook(__FILE__, __NAMESPACE__ . '\\copacf_check_acf_plugin');
add_action('init', __NAMESPACE__ . '\\copacf_check_acf_plugin_active');
add_action('acf/include_location_rules', __NAMESPACE__ . '\\copacf_include_location_rules', 10);
add_action('admin_menu', __NAMESPACE__ . '\\copacf_admin_menu');
add_action('admin_init', __NAMESPACE__ . '\\copacf_admin_init');
add_action('wp_loaded', __NAMESPACE__ . '\\copacf_register_pages');

global $copacf_custom_option_pages;
$copacf_custom_option_pages = [];

function copacf_check_acf_plugin() {
    $active_plugins = (array) get_option('active_plugins', []);
    if (!in_array('advanced-custom-fields/acf.php', $active_plugins)) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('This plugin requires Advanced Custom Fields (ACF). Sorry about that.');
    }
}

function copacf_check_acf_plugin_active() {
    $active_plugins = (array) get_option('active_plugins', []);
    if (!in_array('advanced-custom-fields/acf.php', $active_plugins)) {
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

function copacf_include_location_rules() {
    require_once plugin_dir_path(__FILE__) . 'location.php';
}

function copacf_get_custom_options_pages() {
    global $copacf_custom_option_pages;

    if (empty($copacf_custom_option_pages)) {
        return [];
    }

    $pages = [];
    foreach ($copacf_custom_option_pages as $page) {
        $cache_key = 'copacf_acf_cop_post_id_' . md5($page['menu_slug']);
        $post_id = wp_cache_get($cache_key);

        if ($post_id === false) {
            $posts = get_posts([
                'post_name' => $page['menu_slug'],
                'post_type' => 'acf-cop',
                'post_status' => 'publish',
                'fields' => 'ids',
                'numberposts' => 1,
            ]);
            $post_id = !empty($posts) ? $posts[0] : 0;
            if ($post_id) {
                wp_cache_set($cache_key, $post_id, '', HOUR_IN_SECONDS);
            }
        }

        if (!empty($post_id)) {
            $pages[] = get_post($post_id);
        }
    }

    return $pages;
}

function copacf_add_custom_options_page(array $args) {
    global $copacf_custom_option_pages;

    if (empty($args) || !is_array($args)) {
        return;
    }

    $default = [
        'parent_slug' => '',
        'page_title'  => '',
        'menu_title'  => '',
        'capability'  => 'manage_options',
        'menu_slug'   => '',
        'icon_url'    => '',
        'position'    => null,
    ];

    $args = wp_parse_args($args, $default);
    $copacf_custom_option_pages[] = $args;
}

function copacf_admin_menu() {
    global $copacf_custom_option_pages;

    if (!empty($copacf_custom_option_pages)) {
        foreach ($copacf_custom_option_pages as $page) {
            if (empty($page['page_title']) || empty($page['menu_title']) || empty($page['capability']) || empty($page['menu_slug'])) {
                continue;
            }

            if (empty($page['parent_slug'])) {
                add_menu_page(
                    $page['page_title'],
                    $page['menu_title'],
                    $page['capability'],
                    $page['menu_slug'],
                    __NAMESPACE__ . '\\copacf_render_page_layout',
                    $page['icon_url'],
                    $page['position']
                );
            } else {
                add_submenu_page(
                    $page['parent_slug'],
                    $page['page_title'],
                    $page['menu_title'],
                    $page['capability'],
                    $page['menu_slug'],
                    __NAMESPACE__ . '\\copacf_render_page_layout'
                );
            }
        }
    }
}

function copacf_render_page_layout() {
    global $copacf_custom_option_pages;

    $page_slug = !empty($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

    if (empty($page_slug) || empty($copacf_custom_option_pages)) {
        return;
    }

    $current_page = null;
    foreach ($copacf_custom_option_pages as $page) {
        if ($page['menu_slug'] === $page_slug) {
            $current_page = $page;
            break;
        }
    }

    if (empty($current_page)) {
        return;
    }

    $cache_key = 'copacf_acf_cop_post_id_' . md5($current_page['menu_slug']);
    $post_id = wp_cache_get($cache_key);

    if ($post_id === false) {
        $posts = get_posts([
            'post_name' => $current_page['menu_slug'],
            'post_type' => 'acf-cop',
            'post_status' => 'publish',
            'fields' => 'ids',
            'numberposts' => 1,
        ]);
        $post_id = !empty($posts) ? $posts[0] : 0;
        if ($post_id) {
            wp_cache_set($cache_key, $post_id, '', HOUR_IN_SECONDS);
        }
    }

    $post_id = intval($post_id);

    // Nonce verification
    if (isset($_POST['acf_nonce']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['acf_nonce'])), 'acf_form_nonce')) {
        echo '<div class="error"><p>Nonce verification failed. Please try again.</p></div>';
        return;
    }

    ?>
    <div class="wrap">
        <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
        <br/>
        <div id="copacf_acf_custom_options_page_postbox" class="postbox">
            <div class="inside">
                <?php
                acf_form([
                    'post_id'           => $post_id,
                    'post_title'        => false,
                    'post_content'      => false,
                    'uploader'          => 'wp',
                    'form_attributes'   => ['id' => 'post_' . $post_id],
                    'html_before_fields' => wp_nonce_field('acf_form_nonce', 'acf_nonce', true, false)
                ]);
                ?>
            </div>
        </div>
    </div>
    <?php
}

function copacf_admin_init() {
    global $copacf_custom_option_pages;

    $page_slug = !empty($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

    if (empty($page_slug) || empty($copacf_custom_option_pages)) {
        return;
    }

    $current_page = null;
    foreach ($copacf_custom_option_pages as $page) {
        if ($page['menu_slug'] === $page_slug) {
            $current_page = $page;
            break;
        }
    }

    if (empty($current_page)) {
        return;
    }

    acf_enqueue_scripts();

    // Nonce verification
    if (isset($_POST['acf_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['acf_nonce'])), 'acf_form_nonce')) {
        if (acf_validate_save_post(true)) {
            $acf_nonce = isset($_POST['acf_nonce']) ? sanitize_text_field(wp_unslash($_POST['acf_nonce'])) : '';
            $acf_data = isset($_POST['acf']) ? array_map('sanitize_text_field', wp_unslash($_POST['acf'])) : [];
            $acf_post_id = isset($_POST['_acf_post_id']) ? absint(wp_unslash($_POST['_acf_post_id'])) : 0;

            if (!empty($acf_data) && is_array($acf_data)) {
                foreach ($acf_data as $field_key => $field_value) {
                    $field_object = get_field_object($field_key);
                    if ($field_object) {
                        $field_name = $field_object['name'];
                        update_option("copacf_options_{$field_name}", $field_value);
                        update_option("copacf__options_{$field_name}", $field_key);
                    }
                }
            }
            if ($acf_post_id) {
                acf_save_post($acf_post_id);
            }
        }
    }

    do_action('acf/input/admin_enqueue_scripts');
    do_action('acf/input/admin_head');
}

function copacf_register_pages() {
    global $copacf_custom_option_pages;

    if (!empty($copacf_custom_option_pages)) {
        foreach ($copacf_custom_option_pages as $page) {
            $cache_key = 'copacf_acf_cop_post_id_' . md5($page['menu_slug']);
            $post_id = wp_cache_get($cache_key);

            if ($post_id === false) {
                $posts = get_posts([
                    'name' => $page['menu_slug'],
                    'post_type' => 'acf-cop',
                    'post_status' => 'publish',
                    'fields' => 'ids',
                    'numberposts' => 1,
                ]);
                $post_id = !empty($posts) ? $posts[0] : 0;
                if ($post_id) {
                    wp_cache_set($cache_key, $post_id, '', HOUR_IN_SECONDS);
                }
            }

            $post_args = [
                'post_title'    => $page['page_title'],
                'post_name'     => $page['menu_slug'],
                'post_type'     => 'acf-cop',
                'post_status'   => 'publish',
                'post_content'  => serialize($page),
                'comment_status' => 'closed',
                'ping_status'   => 'closed'
            ];

            if (empty($post_id)) {
                $new_post_id = wp_insert_post($post_args);
                if ($new_post_id) {
                    wp_cache_set($cache_key, $new_post_id, '', HOUR_IN_SECONDS);
                }
            } else {
                $post_args['ID'] = $post_id;
                wp_update_post($post_args);
            }
        }
    }
}

// Register default options page
copacf_add_custom_options_page([
    'parent_slug' => '',
    'page_title'  => 'Custom Options',
    'menu_title'  => 'Custom Options',
    'capability'  => 'manage_options',
    'menu_slug'   => 'custom-options',
    'icon_url'    => '',
    'position'    => null,
]);