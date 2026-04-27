<?php
/**
 * Site Settings — ACF field group for the options page + custom WPGraphQL
 * bridge that reads `copacf_options_*` options written by the
 * "Custom Option Page for ACF" plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -------------------------------------------------------------------------
// 7. ACF Field Group: Site Settings
//
// Depends on the "Custom Option Page for ACF" plugin (custom-option-page-for-acf)
// to supply the options page. That plugin stores field values as flat
// WordPress options under the key prefix `copacf_options_<field_name>`, not in
// the ACF options-page registry — so wpgraphql-acf does NOT expose these
// automatically. See section 8 for the custom GraphQL shape that reads from
// wp_options directly.
//
// Before this works: install + activate `custom-option-page-for-acf`, then
// create an options page through the plugin's admin UI and replace
// OPTIONS_PAGE_SLUG below with the menu slug it generated.
// -------------------------------------------------------------------------
if ( ! defined( 'JUICED_SITE_SETTINGS_OPTIONS_PAGE_SLUG' ) ) {
    // TODO: replace with the slug the custom-option-page-for-acf plugin assigns
    // to the Site Settings options page after you create it in WP admin.
    define( 'JUICED_SITE_SETTINGS_OPTIONS_PAGE_SLUG', 'site-settings' );
}

function juiced_register_site_settings_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( [
        'key'    => 'group_site_settings',
        'title'  => 'Site Settings',
        'fields' => [
            [
                'key'          => 'field_site_header_announcement',
                'label'        => 'Header Announcement',
                'name'         => 'header_announcement',
                'type'         => 'text',
                'instructions' => 'Short tagline shown in the top utility strip. Leave blank to use the default.',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => JUICED_SITE_SETTINGS_OPTIONS_PAGE_SLUG,
                ],
            ],
        ],
    ] );
}
add_action( 'acf/init', 'juiced_register_site_settings_acf_fields' );

// -------------------------------------------------------------------------
// 8. WPGraphQL: expose Site Settings at siteSettings.siteSettingsFields.*
//
// The storefront queries `siteSettings.siteSettingsFields.headerAnnouncement`
// and falls back to a default string when null. The resolver reads the WP
// option written by custom-option-page-for-acf and returns null when the
// plugin is inactive or the field is empty, so the storefront fallback always
// kicks in cleanly.
// -------------------------------------------------------------------------
function juiced_register_site_settings_graphql() {
    if ( ! function_exists( 'register_graphql_object_type' ) ) {
        return;
    }

    register_graphql_object_type( 'SiteSettingsFields', [
        'description' => 'Editable site-wide settings from WP admin.',
        'fields'      => [
            'headerAnnouncement' => [
                'type'        => 'String',
                'description' => 'Short tagline shown in the top utility strip. Null when unset.',
                'resolve'     => function () {
                    $val = get_option( 'copacf_options_header_announcement' );
                    return ( is_string( $val ) && $val !== '' ) ? $val : null;
                },
            ],
        ],
    ] );

    register_graphql_object_type( 'SiteSettings', [
        'description' => 'Site-wide editable settings.',
        'fields'      => [
            'siteSettingsFields' => [
                'type'    => 'SiteSettingsFields',
                'resolve' => function () {
                    return new \stdClass();
                },
            ],
        ],
    ] );

    register_graphql_field( 'RootQuery', 'siteSettings', [
        'type'        => 'SiteSettings',
        'description' => 'Site-wide settings edited from WP admin.',
        'resolve'     => function () {
            return new \stdClass();
        },
    ] );
}
add_action( 'graphql_register_types', 'juiced_register_site_settings_graphql' );
