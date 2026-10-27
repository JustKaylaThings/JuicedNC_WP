<?php
/**
 * Community Spotlight CPT — the monthly "Community" blog.
 *
 * Each post spotlights one person or local business. The month a spotlight
 * belongs to is its publish date, and a month holds at most two: publishing
 * (or scheduling) a third into a month that already has two is refused — the
 * post is saved as a draft instead and the editor says why.
 *
 * Title = the person or business's name, featured image = their photo,
 * excerpt = the one-paragraph intro on cards, content = the full story.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const JUICED_SPOTLIGHTS_PER_MONTH = 2;

// -------------------------------------------------------------------------
// 1. Custom Post Type: Spotlight
// -------------------------------------------------------------------------
function juiced_register_spotlight_cpt() {
    $labels = [
        'name'               => 'Community Spotlights',
        'singular_name'      => 'Spotlight',
        'menu_name'          => 'Community',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Spotlight',
        'edit_item'          => 'Edit Spotlight',
        'new_item'           => 'New Spotlight',
        'view_item'          => 'View Spotlight',
        'search_items'       => 'Search Spotlights',
        'not_found'          => 'No spotlights found',
        'not_found_in_trash' => 'No spotlights found in Trash',
        'featured_image'     => 'Photo',
        'set_featured_image' => 'Set photo',
        'remove_featured_image' => 'Remove photo',
    ];

    register_post_type( 'spotlight', [
        'labels'              => $labels,
        'public'              => true,
        // No archive: the Community page (page-templates/community.php) is the
        // index and the archive at /community/, same pattern as Herbs/Events.
        'has_archive'         => false,
        'supports'            => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
        'rewrite'             => [ 'slug' => 'community', 'with_front' => false ],
        'menu_icon'           => 'dashicons-groups',
        'menu_position'       => 21,
        'show_in_graphql'     => true,
        'graphql_single_name' => 'spotlight',
        'graphql_plural_name' => 'spotlights',
    ] );
}
add_action( 'init', 'juiced_register_spotlight_cpt' );

// -------------------------------------------------------------------------
// 2. ACF Field Group: Spotlight Fields
// -------------------------------------------------------------------------
function juiced_register_spotlight_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( [
        'key'    => 'group_spotlight_fields',
        'title'  => 'Spotlight Details',
        'position' => 'acf_after_title',
        'fields' => [
            [
                'key'           => 'field_spotlight_type',
                'label'         => 'Who is this?',
                'name'          => 'spotlight_type',
                'type'          => 'radio',
                'choices'       => [
                    'person'   => 'A person',
                    'business' => 'A local business',
                ],
                'default_value' => 'person',
                'layout'        => 'horizontal',
                'instructions'  => 'Shown as the badge on their card. The post title is their name. The publish date sets which month they are featured in — two spotlights per month at most.',
            ],
            [
                'key'          => 'field_spotlight_role',
                'label'        => 'Role / what they do',
                'name'         => 'spotlight_role',
                'type'         => 'text',
                'instructions' => 'One short line under the name, e.g. "Yoga instructor & Juiced! regular" or "Family-owned bakery on Hill Street".',
            ],
            [
                'key'          => 'field_spotlight_quote',
                'label'        => 'Pull quote',
                'name'         => 'spotlight_quote',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => 'Optional. A line in their own words, shown large on their page and on the Community page. No quotation marks needed.',
            ],
            [
                'key'          => 'field_spotlight_link_url',
                'label'        => 'Their link',
                'name'         => 'spotlight_link_url',
                'type'         => 'url',
                'instructions' => 'Optional. Their website or Instagram — shown as a button on their page.',
            ],
            [
                'key'          => 'field_spotlight_link_label',
                'label'        => 'Link button label',
                'name'         => 'spotlight_link_label',
                'type'         => 'text',
                'placeholder'  => 'Visit their website',
            ],
        ],
        'location' => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'spotlight',
                ],
            ],
        ],
        'show_in_graphql'    => 1,
        'graphql_field_name' => 'spotlightFields',
    ] );
}
add_action( 'acf/init', 'juiced_register_spotlight_acf_fields' );

// -------------------------------------------------------------------------
// 3. Two per month
// -------------------------------------------------------------------------

/**
 * Other published/scheduled spotlights in the same calendar month as a date.
 *
 * @param string $date    Post date (Y-m-d H:i:s, site time).
 * @param int    $exclude Post ID to leave out (the one being saved).
 * @return int[]
 */
function juiced_spotlights_in_month( $date, $exclude = 0 ) {
    $ts = strtotime( $date );

    return get_posts( [
        'post_type'      => 'spotlight',
        'post_status'    => [ 'publish', 'future' ],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'post__not_in'   => $exclude ? [ $exclude ] : [],
        'date_query'     => [
            [
                'year'  => (int) gmdate( 'Y', $ts ),
                'month' => (int) gmdate( 'n', $ts ),
            ],
        ],
    ] );
}

/**
 * Refuse a third spotlight in a month: save it as a draft instead and leave a
 * note for the editor screen to explain.
 */
function juiced_spotlight_enforce_monthly_limit( $data, $postarr ) {
    if ( 'spotlight' !== $data['post_type'] || ! in_array( $data['post_status'], [ 'publish', 'future' ], true ) ) {
        return $data;
    }

    $post_id = isset( $postarr['ID'] ) ? (int) $postarr['ID'] : 0;
    $others  = juiced_spotlights_in_month( $data['post_date'], $post_id );

    if ( count( $others ) >= JUICED_SPOTLIGHTS_PER_MONTH ) {
        $data['post_status'] = 'draft';
        set_transient( 'juiced_spotlight_limit_' . get_current_user_id(), date_i18n( 'F Y', strtotime( $data['post_date'] ) ), 60 );
    }

    return $data;
}
add_filter( 'wp_insert_post_data', 'juiced_spotlight_enforce_monthly_limit', 10, 2 );

// WordPress would still say "Post published." — drop that message so the
// notice below is the only one.
function juiced_spotlight_limit_redirect( $location ) {
    if ( get_transient( 'juiced_spotlight_limit_' . get_current_user_id() ) ) {
        $location = remove_query_arg( 'message', $location );
    }
    return $location;
}
add_filter( 'redirect_post_location', 'juiced_spotlight_limit_redirect' );

function juiced_spotlight_limit_notice() {
    $key   = 'juiced_spotlight_limit_' . get_current_user_id();
    $month = get_transient( $key );

    if ( ! $month ) {
        return;
    }
    delete_transient( $key );

    printf(
        '<div class="notice notice-warning is-dismissible"><p><strong>Saved as a draft, not published.</strong> %s already has %d spotlights — the most one month can hold. Change the publish date to another month, or unpublish one of the others first.</p></div>',
        esc_html( $month ),
        (int) JUICED_SPOTLIGHTS_PER_MONTH
    );
}
add_action( 'admin_notices', 'juiced_spotlight_limit_notice' );

// -------------------------------------------------------------------------
// 4. Admin list: photo, type, and month columns
// -------------------------------------------------------------------------
function juiced_spotlight_admin_columns( $columns ) {
    $new = [];
    foreach ( $columns as $key => $label ) {
        if ( 'title' === $key ) {
            $new['spotlight_thumbnail'] = 'Photo';
        }
        if ( 'date' === $key ) {
            $new['spotlight_type']  = 'Type';
            $new['spotlight_month'] = 'Featured in';
        }
        $new[ $key ] = $label;
    }
    return $new;
}
add_filter( 'manage_spotlight_posts_columns', 'juiced_spotlight_admin_columns' );

function juiced_spotlight_admin_column_content( $column, $post_id ) {
    if ( 'spotlight_thumbnail' === $column ) {
        $img = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
        echo $img
            ? sprintf( '<img src="%s" width="60" height="60" style="border-radius:4px;object-fit:cover;" alt="" />', esc_url( $img ) )
            : '<span style="color:#bbb;">—</span>';
    } elseif ( 'spotlight_type' === $column ) {
        echo 'business' === get_field( 'spotlight_type', $post_id ) ? 'Business' : 'Person';
    } elseif ( 'spotlight_month' === $column ) {
        echo esc_html( get_the_date( 'F Y', $post_id ) );
    }
}
add_action( 'manage_spotlight_posts_custom_column', 'juiced_spotlight_admin_column_content', 10, 2 );
