<?php
/**
 * Event CPT — post type, `event_category` taxonomy, and `eventFields` ACF group.
 *
 * Backs the homepage "This Week at Juiced" strip and (later) the full events
 * calendar. Recurring events are described by day_of_week + monthly_ordinals;
 * one-off events by event_date (+ optional end_date). The theme resolves each
 * record to its next upcoming occurrence in inc/event-dates.php.
 *
 * Lives in the plugin (not the theme) so events survive a theme switch, matching
 * herb.php / menu-item.php. GraphQL flags are kept for parity with the outgoing
 * headless frontend during the cutover; the classic theme reads via get_field().
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -------------------------------------------------------------------------
// Custom Post Type: Event
// -------------------------------------------------------------------------
function juiced_register_event_cpt() {
    $labels = [
        'name'               => 'Events',
        'singular_name'      => 'Event',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Event',
        'edit_item'          => 'Edit Event',
        'new_item'           => 'New Event',
        'view_item'          => 'View Event',
        'search_items'       => 'Search Events',
        'not_found'          => 'No events found',
        'not_found_in_trash' => 'No events found in Trash',
    ];

    register_post_type( 'event', [
        'labels'              => $labels,
        'public'              => true,
        // No CPT archive: /events is a real WP Page (the "Events Page" template
        // in the theme) so its copy is client-editable via ACF. Singles live at
        // /event/{slug}, which keeps that page's slug free.
        'has_archive'         => false,
        'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
        'rewrite'             => [ 'slug' => 'event' ],
        'menu_icon'           => 'dashicons-calendar-alt',
        'show_in_graphql'     => true,
        'graphql_single_name' => 'event',
        'graphql_plural_name' => 'events',
    ] );
}
add_action( 'init', 'juiced_register_event_cpt' );

// -------------------------------------------------------------------------
// Taxonomy: Event Category
// -------------------------------------------------------------------------
function juiced_register_event_category_taxonomy() {
    $labels = [
        'name'          => 'Event Categories',
        'singular_name' => 'Event Category',
        'search_items'  => 'Search Event Categories',
        'all_items'     => 'All Event Categories',
        'edit_item'     => 'Edit Event Category',
        'update_item'   => 'Update Event Category',
        'add_new_item'  => 'Add New Event Category',
        'new_item_name' => 'New Event Category Name',
        'menu_name'     => 'Event Categories',
    ];

    register_taxonomy( 'event_category', [ 'event' ], [
        'labels'              => $labels,
        'hierarchical'        => true,
        'public'              => true,
        'rewrite'             => [ 'slug' => 'event-category' ],
        'show_in_graphql'     => true,
        'graphql_single_name' => 'eventCategory',
        'graphql_plural_name' => 'eventCategories',
    ] );
}
add_action( 'init', 'juiced_register_event_category_taxonomy' );

// -------------------------------------------------------------------------
// ACF Field Group: Event Details
// -------------------------------------------------------------------------
function juiced_register_event_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    $weekdays = [
        'Monday'    => 'Monday',
        'Tuesday'   => 'Tuesday',
        'Wednesday' => 'Wednesday',
        'Thursday'  => 'Thursday',
        'Friday'    => 'Friday',
        'Saturday'  => 'Saturday',
        'Sunday'    => 'Sunday',
    ];

    acf_add_local_field_group( [
        'key'    => 'group_event_details',
        'title'  => 'Event Details',
        'fields' => [
            // --- One-off scheduling ---------------------------------------
            [
                'key'                => 'field_event_date',
                'label'              => 'Event date (one-off)',
                'name'               => 'event_date',
                'type'               => 'date_picker',
                'instructions'       => 'For a single event on a specific date. Leave blank for a recurring weekly event (set "Days of week" instead).',
                'display_format'     => 'F j, Y',
                'return_format'      => 'Y-m-d',
                'first_day'          => 0,
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'eventDate',
            ],
            [
                'key'                => 'field_event_end_date',
                'label'              => 'End date (multi-day only)',
                'name'               => 'end_date',
                'type'               => 'date_picker',
                'instructions'       => 'Last day of a multi-day event. Leave blank for a single day. Ignored for recurring events.',
                'display_format'     => 'F j, Y',
                'return_format'      => 'Y-m-d',
                'first_day'          => 0,
                'conditional_logic'  => [
                    [
                        [ 'field' => 'field_event_date', 'operator' => '!=empty' ],
                    ],
                ],
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'endDate',
            ],
            // --- Recurring scheduling -------------------------------------
            [
                'key'                => 'field_event_day_of_week',
                'label'              => 'Days of week (recurring)',
                'name'               => 'day_of_week',
                'type'               => 'checkbox',
                'instructions'       => 'For a weekly recurring event. Check every day it runs. Leave blank if this is a one-off (set "Event date" instead).',
                'choices'            => $weekdays,
                'return_format'      => 'value',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'dayOfWeek',
            ],
            [
                'key'                => 'field_event_monthly_ordinals',
                'label'              => 'Which weeks of the month',
                'name'               => 'monthly_ordinals',
                'type'               => 'checkbox',
                'instructions'       => 'Optional. Limit a recurring event to certain weeks — e.g. check "1st" and "3rd" for a 1st-and-3rd-week event. Leave all blank for every week.',
                'choices'            => [
                    '1'    => '1st',
                    '2'    => '2nd',
                    '3'    => '3rd',
                    '4'    => '4th',
                    '5'    => '5th',
                    'last' => 'Last',
                ],
                'return_format'      => 'value',
                'conditional_logic'  => [
                    [
                        [ 'field' => 'field_event_day_of_week', 'operator' => '!=empty' ],
                    ],
                ],
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'monthlyOrdinals',
            ],
            // --- Time + place ---------------------------------------------
            [
                'key'                => 'field_event_start_time',
                'label'              => 'Start time',
                'name'               => 'start_time',
                'type'               => 'time_picker',
                'display_format'     => 'g:i a',
                'return_format'      => 'H:i',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'startTime',
            ],
            [
                'key'                => 'field_event_end_time',
                'label'              => 'End time',
                'name'               => 'end_time',
                'type'               => 'time_picker',
                'display_format'     => 'g:i a',
                'return_format'      => 'H:i',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'endTime',
            ],
            [
                'key'                => 'field_event_location',
                'label'              => 'Location',
                'name'               => 'location',
                'type'               => 'text',
                'instructions'       => 'Where the event happens, e.g. "Rock Quarry" or "The Shop". Blank shows a default.',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'location',
            ],
            [
                'key'                => 'field_event_cost',
                'label'              => 'Cost',
                'name'               => 'cost',
                'type'               => 'text',
                'instructions'       => 'Free-text, e.g. "Free" or "$10". Optional.',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'cost',
            ],
            [
                'key'                => 'field_event_audience',
                'label'              => 'Who it\'s for',
                'name'               => 'audience',
                'type'               => 'text',
                'instructions'       => 'Shown as a tag beside the category on the Events page, e.g. "All Ages", "18+", "All Levels". Blank hides the tag.',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'audience',
            ],
            [
                'key'                => 'field_event_signup_url',
                'label'              => 'Sign-up / tickets URL',
                'name'               => 'signup_url',
                'type'               => 'url',
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'signUpUrl',
            ],
            // --- Header announcement tie-in (rendered in a later phase) ----
            [
                'key'                => 'field_event_show_as_announcement',
                'label'              => 'Show as header announcement',
                'name'               => 'show_as_announcement',
                'type'               => 'true_false',
                'instructions'       => 'When on, this event also appears in the rotating banner at the top of the site.',
                'ui'                 => 1,
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'showAsAnnouncement',
            ],
            [
                'key'                => 'field_event_announcement_text',
                'label'              => 'Announcement text (override)',
                'name'               => 'announcement_text',
                'type'               => 'text',
                'instructions'       => 'Optional custom banner copy. Blank uses the event title.',
                'conditional_logic'  => [
                    [
                        [ 'field' => 'field_event_show_as_announcement', 'operator' => '==', 'value' => '1' ],
                    ],
                ],
                'show_in_graphql'    => 1,
                'graphql_field_name' => 'announcementText',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'event',
                ],
            ],
        ],
        'show_in_graphql'    => 1,
        'graphql_field_name' => 'eventFields',
        'graphql_types'      => [ 'Event' ],
    ] );
}
add_action( 'acf/init', 'juiced_register_event_acf_fields' );
