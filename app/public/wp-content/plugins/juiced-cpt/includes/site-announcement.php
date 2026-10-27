<?php
/**
 * Site Announcement CPT — scheduled header announcements picked by the Next.js
 * storefront and exposed via WPGraphQL as `siteAnnouncements`.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -------------------------------------------------------------------------
// 9. Custom Post Type: Site Announcement
//
// One post per scheduled header announcement. The storefront fetches all of
// them and picks the first whose window matches the current moment in
// America/New_York; otherwise it falls back to
// siteSettings.siteSettingsFields.headerAnnouncement.
//
// `page-attributes` support exposes menu_order so the store owner can
// drag-reorder announcements in the admin list — lower menu_order wins when
// two windows overlap.
// -------------------------------------------------------------------------
function juiced_register_site_announcement_cpt() {
    $labels = [
        'name'               => 'Announcements',
        'singular_name'      => 'Announcement',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Announcement',
        'edit_item'          => 'Edit Announcement',
        'new_item'           => 'New Announcement',
        'view_item'          => 'View Announcement',
        'search_items'       => 'Search Announcements',
        'not_found'          => 'No announcements found',
        'not_found_in_trash' => 'No announcements found in Trash',
    ];

    register_post_type( 'site_announcement', [
        'labels'              => $labels,
        // Admin-only CPT, but publicly_queryable must be true so WPGraphQL
        // exposes posts to unauthenticated list queries.
        'public'              => false,
        'publicly_queryable'  => true,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_rest'        => true,
        'show_in_menu'        => true,
        'hierarchical'        => false,
        'has_archive'         => false,
        'supports'            => [ 'title', 'page-attributes' ],
        'menu_icon'           => 'dashicons-megaphone',
        'show_in_graphql'     => true,
        'graphql_single_name' => 'siteAnnouncement',
        'graphql_plural_name' => 'siteAnnouncements',
    ] );
}
add_action( 'init', 'juiced_register_site_announcement_cpt' );

// -------------------------------------------------------------------------
// 10. ACF Field Group: Announcement Schedule
//
// Empty schedule fields mean "always on". event_date takes priority over
// day_of_week. Times are stored as "HH:mm:ss" strings.
// -------------------------------------------------------------------------
function juiced_register_site_announcement_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( [
        'key'    => 'group_site_announcement_fields',
        'title'  => 'Announcement Schedule',
        'fields' => [
            [
                'key'                => 'field_site_announcement_event_date',
                'label'              => 'Start Date',
                'name'               => 'event_date',
                'type'               => 'date_picker',
                'instructions'       => 'Optional. Start of a one-off announcement window, or the single date for a one-day run. Takes priority over Day of Week.',
                'display_format'     => 'F j, Y',
                'return_format'      => 'Y-m-d',
                'first_day'          => 0,
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'eventDate',
            ],
            [
                'key'                => 'field_site_announcement_end_date',
                'label'              => 'End Date (optional)',
                'name'               => 'end_date',
                'type'               => 'date_picker',
                'instructions'       => 'Optional. Inclusive end of a multi-day one-off. Leave blank for a single-day run. Ignored unless Start Date is set.',
                'display_format'     => 'F j, Y',
                'return_format'      => 'Y-m-d',
                'first_day'          => 0,
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'endDate',
            ],
            [
                'key'                => 'field_site_announcement_day_of_week',
                'label'              => 'Day of Week',
                'name'               => 'day_of_week',
                'type'               => 'select',
                'instructions'       => 'Optional. Used when Event Date is empty. Leave blank to match every day.',
                'choices'            => [
                    'Sunday'    => 'Sunday',
                    'Monday'    => 'Monday',
                    'Tuesday'   => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday'  => 'Thursday',
                    'Friday'    => 'Friday',
                    'Saturday'  => 'Saturday',
                ],
                'allow_null'         => 1,
                'ui'                 => 1,
                'return_format'      => 'value',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'dayOfWeek',
            ],
            [
                'key'                => 'field_site_announcement_start_time',
                'label'              => 'Start Time',
                'name'               => 'start_time',
                'type'               => 'time_picker',
                'instructions'       => 'Optional. Leave blank for start of day.',
                'display_format'     => 'g:i a',
                'return_format'      => 'H:i:s',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'startTime',
            ],
            [
                'key'                => 'field_site_announcement_end_time',
                'label'              => 'End Time',
                'name'               => 'end_time',
                'type'               => 'time_picker',
                'instructions'       => 'Optional. Leave blank for end of day.',
                'display_format'     => 'g:i a',
                'return_format'      => 'H:i:s',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'endTime',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'site_announcement',
                ],
            ],
        ],
        'show_in_graphql'    => 1,
        'graphql_field_name' => 'siteAnnouncementFields',
    ] );
}
add_action( 'acf/init', 'juiced_register_site_announcement_acf_fields' );
