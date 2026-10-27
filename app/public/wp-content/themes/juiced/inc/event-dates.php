<?php
/**
 * Event date resolution — PHP port of the Next.js src/lib/utils/event-dates.ts.
 *
 * Every event resolves to a single "next upcoming occurrence" in site-local time:
 * either its one-off event_date (kept visible through an optional multi-day
 * end_date), or the soonest calendar date matching its recurring day_of_week
 * (respecting monthly_ordinals). juiced_upcoming_events() runs a WP_Query, drops
 * events with no resolvable upcoming date, and sorts the rest by that date —
 * mirroring sortByUpcoming() so one-offs and weekly events share one timeline.
 *
 * All fields come from the "Event Details" ACF group registered in the
 * juiced-cpt plugin, with return_format normalized (dates 'Y-m-d', times 'H:i').
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Weekday name → PHP day-of-week index (0 = Sunday, matching date('w')). */
function juiced_day_index() {
    return [
        'Sunday'    => 0,
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6,
    ];
}

/** Parse "HH:MM" (or "HH:MM:SS") into [hour, minute]; [0, 0] on failure. */
function juiced_parse_time( $raw ) {
    if ( ! $raw || ! preg_match( '/^(\d{1,2}):(\d{2})/', trim( (string) $raw ), $m ) ) {
        return [ 0, 0 ];
    }
    return [ (int) $m[1], (int) $m[2] ];
}

/** Format a stored "HH:MM" time as a display string like "9:30 AM"; '' on empty. */
function juiced_format_event_time( $raw ) {
    if ( ! $raw ) {
        return '';
    }
    list( $hour, $minute ) = juiced_parse_time( $raw );
    $dt = ( new DateTimeImmutable( 'now', wp_timezone() ) )->setTime( $hour, $minute );
    return $dt->format( 'g:i A' );
}

/** Parse "YYYY-MM-DD" into a local-midnight DateTimeImmutable, or null. */
function juiced_parse_local_date( $iso, DateTimeZone $tz ) {
    if ( ! $iso || ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})/', trim( (string) $iso ), $m ) ) {
        return null;
    }
    return ( new DateTimeImmutable( 'now', $tz ) )
        ->setDate( (int) $m[1], (int) $m[2], (int) $m[3] )
        ->setTime( 0, 0, 0 );
}

/** Keep only canonical weekday names; accepts an array or a bare string. */
function juiced_normalize_days( $value ) {
    $index = juiced_day_index();
    $arr   = is_array( $value ) ? $value : ( $value ? [ $value ] : [] );
    $out   = [];
    foreach ( $arr as $item ) {
        if ( is_string( $item ) && isset( $index[ $item ] ) ) {
            $out[] = $item;
        }
    }
    return $out;
}

/**
 * Does a date satisfy a monthly_ordinals set? Ordinals: "1".."5" and "last".
 * Empty/null means "no filter" → always true.
 */
function juiced_matches_monthly_ordinals( DateTimeImmutable $date, $ordinals ) {
    if ( empty( $ordinals ) ) {
        return true;
    }
    $dom          = (int) $date->format( 'j' );
    $ordinal      = intdiv( $dom - 1, 7 ) + 1; // 1..5
    $days_in_month = (int) $date->format( 't' );
    $is_last      = ( $dom + 7 ) > $days_in_month;

    foreach ( (array) $ordinals as $raw ) {
        $token = strtolower( (string) $raw );
        if ( 'last' === $token ) {
            if ( $is_last ) {
                return true;
            }
            continue;
        }
        if ( (int) $token === $ordinal ) {
            return true;
        }
    }
    return false;
}

/**
 * Resolve an event post to its next upcoming DateTimeImmutable in local time.
 * Returns null when the event has no date info, or a one-off's run is fully past.
 *
 * "Past" means strictly before *today* — an event stays visible through the rest
 * of its day even after its start time, matching how the client thinks about it.
 *
 * @param int                    $post_id Event post ID.
 * @param DateTimeImmutable|null $now     Injectable "now" (site timezone) for tests.
 * @return DateTimeImmutable|null
 */
function juiced_event_upcoming( $post_id, DateTimeImmutable $now = null ) {
    if ( ! function_exists( 'get_field' ) ) {
        return null;
    }
    $tz  = wp_timezone();
    $now = $now ?: new DateTimeImmutable( 'now', $tz );

    $event_date = get_field( 'event_date', $post_id );
    $end_date   = get_field( 'end_date', $post_id );
    $start_time = get_field( 'start_time', $post_id );
    $days       = juiced_normalize_days( get_field( 'day_of_week', $post_id ) );
    $ordinals   = get_field( 'monthly_ordinals', $post_id );

    list( $hour, $minute ) = juiced_parse_time( $start_time );
    $start_of_today        = $now->setTime( 0, 0, 0 );

    // One-off: use the explicit event_date, surfaced through any end_date.
    if ( $event_date ) {
        $start = juiced_parse_local_date( $event_date, $tz );
        if ( ! $start ) {
            return null;
        }
        $start      = $start->setTime( $hour, $minute );
        $end        = juiced_parse_local_date( $end_date ? $end_date : $event_date, $tz );
        $end_cutoff = $end ?: $start;
        if ( $end_cutoff < $start_of_today ) {
            return null;
        }
        // Anchor an in-progress run to today so it sorts among current events.
        return $start < $start_of_today ? $start_of_today : $start;
    }

    // Recurring: soonest upcoming match across all configured weekdays.
    if ( empty( $days ) ) {
        return null;
    }
    $index   = juiced_day_index();
    $soonest = null;

    foreach ( $days as $day ) {
        $target_dow = $index[ $day ];
        $candidate  = $now->setTime( $hour, $minute, 0 );
        $today_dow  = (int) $candidate->format( 'w' );
        $offset     = ( $target_dow - $today_dow + 7 ) % 7;
        $candidate  = $candidate->modify( "+{$offset} days" );

        $resolved = $candidate;
        if ( ! empty( $ordinals ) ) {
            $resolved = null;
            $probe    = $candidate;
            // 8 tries covers 5 ordinals + "last" + month-boundary headroom.
            for ( $i = 0; $i < 8; $i++ ) {
                if ( juiced_matches_monthly_ordinals( $probe, $ordinals ) ) {
                    $resolved = $probe;
                    break;
                }
                $probe = $probe->modify( '+7 days' );
            }
        }

        if ( $resolved && ( ! $soonest || $resolved < $soonest ) ) {
            $soonest = $resolved;
        }
    }
    return $soonest;
}

/**
 * The concrete start/end of an event's next occurrence, for calendar exports.
 *
 * One resolver shared by every "add to calendar" surface (the Google/Outlook
 * links on the single-event page and the .ics endpoint in inc/event-ics.php),
 * so they can't disagree about when the event is. Rules:
 *
 *  - no start_time      → an all-day entry (end date exclusive, per ICS)
 *  - no end_time        → two hours, the calendar convention for "an evening"
 *  - end before start   → the event runs past midnight; end is next day
 *
 * @param int $post_id Event post ID.
 * @return array{start:DateTimeImmutable,end:DateTimeImmutable,all_day:bool}|null
 *         Null when the event has no upcoming occurrence (unschedulable, or a
 *         one-off whose run is over) — nothing a calendar could hold.
 */
function juiced_event_occurrence_span( $post_id ) {
    $start = juiced_event_upcoming( $post_id );
    if ( ! $start ) {
        return null;
    }

    if ( ! get_field( 'start_time', $post_id ) ) {
        return [ 'start' => $start, 'end' => $start->modify( '+1 day' ), 'all_day' => true ];
    }

    $end     = $start->modify( '+2 hours' );
    $end_raw = get_field( 'end_time', $post_id );
    if ( $end_raw ) {
        list( $end_h, $end_m ) = juiced_parse_time( $end_raw );
        $end = $start->setTime( $end_h, $end_m );
        if ( $end <= $start ) {
            $end = $end->modify( '+1 day' );
        }
    }

    return [ 'start' => $start, 'end' => $end, 'all_day' => false ];
}

/**
 * iCalendar recurrence rule for a recurring event; '' for one-offs.
 *
 * Mirrors juiced_event_occurs_on()'s reading of day_of_week/monthly_ordinals:
 * plain weekly → "FREQ=WEEKLY;BYDAY=MO,WE"; with ordinals → monthly, one BYDAY
 * pair per ordinal × day ("FREQ=MONTHLY;BYDAY=-1FR" for a last-Friday event).
 * The same string works in an .ics RRULE line and Google Calendar's recur=
 * parameter, which is why it lives here rather than in the ICS endpoint.
 *
 * @param int $post_id Event post ID.
 * @return string
 */
function juiced_event_rrule( $post_id ) {
    if ( ! function_exists( 'get_field' ) || get_field( 'event_date', $post_id ) ) {
        return '';
    }
    $days = juiced_normalize_days( get_field( 'day_of_week', $post_id ) );
    if ( empty( $days ) ) {
        return '';
    }

    $codes = [
        'Sunday'    => 'SU',
        'Monday'    => 'MO',
        'Tuesday'   => 'TU',
        'Wednesday' => 'WE',
        'Thursday'  => 'TH',
        'Friday'    => 'FR',
        'Saturday'  => 'SA',
    ];
    $by = [];
    foreach ( $days as $day ) {
        $by[] = $codes[ $day ];
    }

    $ordinals = get_field( 'monthly_ordinals', $post_id );
    if ( ! empty( $ordinals ) ) {
        $pairs = [];
        foreach ( (array) $ordinals as $ordinal ) {
            $ordinal = strtolower( (string) $ordinal );
            $nth     = 'last' === $ordinal ? '-1' : (string) (int) $ordinal;
            if ( '0' === $nth ) {
                continue;
            }
            foreach ( $by as $code ) {
                $pairs[] = $nth . $code;
            }
        }
        if ( $pairs ) {
            return 'FREQ=MONTHLY;BYDAY=' . implode( ',', $pairs );
        }
    }

    return 'FREQ=WEEKLY;BYDAY=' . implode( ',', $by );
}

/**
 * Human-readable schedule for an event — what the single-event page shows.
 *
 * Cards only ever show the *next occurrence* (juiced_event_upcoming()); the
 * event's own page should instead say what the schedule is, so a visitor can
 * tell a one-off from a weekly ritual:
 *
 *   one-off            "July 22, 2026"
 *   multi-day          "July 22 – 24, 2026"
 *   weekly             "Every Tuesday"
 *   several days       "Every Tuesday & Thursday"
 *   with ordinals      "1st & 3rd Tuesday of the month"
 *
 * @param int $post_id Event post ID.
 * @return string Label, or '' when the event has no date info at all.
 */
function juiced_event_schedule_label( $post_id ) {
    if ( ! function_exists( 'get_field' ) ) {
        return '';
    }
    $tz = wp_timezone();

    // One-off: the explicit date, as a range when a multi-day end is set.
    $event_date = get_field( 'event_date', $post_id );
    if ( $event_date ) {
        $start = juiced_parse_local_date( $event_date, $tz );
        if ( ! $start ) {
            return '';
        }
        $end = juiced_parse_local_date( get_field( 'end_date', $post_id ), $tz );
        if ( $end && $end > $start ) {
            return $start->format( 'Y-m' ) === $end->format( 'Y-m' )
                ? $start->format( 'F j' ) . ' – ' . $end->format( 'j, Y' )
                : $start->format( 'F j, Y' ) . ' – ' . $end->format( 'F j, Y' );
        }
        return $start->format( 'F j, Y' );
    }

    // Recurring: "Every Tuesday", optionally narrowed by ordinals.
    $days = juiced_normalize_days( get_field( 'day_of_week', $post_id ) );
    if ( empty( $days ) ) {
        return '';
    }

    $join = static function ( $items ) {
        $last = array_pop( $items );
        return $items ? implode( ', ', $items ) . ' & ' . $last : $last;
    };

    $ordinals = get_field( 'monthly_ordinals', $post_id );
    if ( ! empty( $ordinals ) ) {
        $names = [ '1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th', '5' => '5th', 'last' => 'Last' ];
        $picked = [];
        foreach ( (array) $ordinals as $token ) {
            $token = strtolower( (string) $token );
            if ( isset( $names[ $token ] ) ) {
                $picked[] = $names[ $token ];
            }
        }
        if ( $picked ) {
            return $join( $picked ) . ' ' . $join( $days ) . ' of the month';
        }
    }

    return 'Every ' . $join( $days );
}

/**
 * Does an event fall on a given date?
 *
 * juiced_event_upcoming() answers "when is this event next on", which is what a
 * list needs. A calendar needs the other question — "is this event on that
 * day" — for every day it draws, so this tests a date against the same rules
 * rather than walking forward from now. A weekly event answers true for every
 * matching weekday, not just its next one.
 *
 * Takes a pre-read config array rather than a post ID: the calendar asks this
 * hundreds of times (months × days × events), and re-reading ACF fields inside
 * that loop would be hundreds of redundant lookups.
 *
 * @param array             $event Config from juiced_event_calendar_config().
 * @param DateTimeImmutable $date  Local midnight on the day being tested.
 * @param DateTimeZone      $tz    Site timezone.
 * @return bool
 */
function juiced_event_occurs_on( array $event, DateTimeImmutable $date, DateTimeZone $tz ) {
    // One-off: on any day of its run, inclusive of a multi-day end_date.
    if ( ! empty( $event['event_date'] ) ) {
        $start = juiced_parse_local_date( $event['event_date'], $tz );
        if ( ! $start ) {
            return false;
        }
        $end = juiced_parse_local_date( $event['end_date'] ? $event['end_date'] : $event['event_date'], $tz );
        $end = $end ?: $start;

        return $date >= $start && $date <= $end;
    }

    // Recurring: matching weekday, and within any monthly_ordinals filter.
    if ( empty( $event['days'] ) ) {
        return false;
    }
    $index = juiced_day_index();
    $dow   = (int) $date->format( 'w' );

    foreach ( $event['days'] as $day ) {
        if ( isset( $index[ $day ] ) && $index[ $day ] === $dow ) {
            return juiced_matches_monthly_ordinals( $date, $event['ordinals'] );
        }
    }
    return false;
}

/**
 * Every published event's date config plus what the day panel shows, read once.
 *
 * Sorted by start time (then title) so a day's events list in the order they
 * happen — the planner keeps this order when it groups events by day.
 *
 * @return array<int, array>
 */
function juiced_event_calendar_config() {
    if ( ! function_exists( 'get_field' ) ) {
        return [];
    }

    $query = new WP_Query( [
        'post_type'      => 'event',
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'no_found_rows'  => true,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ] );

    $events = [];
    foreach ( $query->posts as $post ) {
        $event_date = get_field( 'event_date', $post->ID );
        $start_raw  = get_field( 'start_time', $post->ID );
        $start      = juiced_format_event_time( $start_raw );
        $end        = juiced_format_event_time( get_field( 'end_time', $post->ID ) );
        $cost       = get_field( 'cost', $post->ID );

        $events[] = [
            'title'      => $post->post_title,
            'url'        => get_permalink( $post->ID ),
            'schedule'   => juiced_event_schedule_label( $post->ID ),
            'time'       => $start && $end ? $start . ' – ' . $end : $start,
            'location'   => (string) get_field( 'location', $post->ID ),
            'cost'       => $cost ? $cost : __( 'Free', 'juiced' ),
            'signup'     => (string) get_field( 'signup_url', $post->ID ),
            'sort'       => $start_raw ? sprintf( '%02d:%02d', ...juiced_parse_time( $start_raw ) ) : '99:99',
            'event_date' => $event_date,
            'end_date'   => get_field( 'end_date', $post->ID ),
            'days'       => juiced_normalize_days( get_field( 'day_of_week', $post->ID ) ),
            'ordinals'   => get_field( 'monthly_ordinals', $post->ID ),
        ];
    }

    usort(
        $events,
        static function ( $a, $b ) {
            return [ $a['sort'], $a['title'] ] <=> [ $b['sort'], $b['title'] ];
        }
    );
    return $events;
}

/**
 * Data for the Events page planner (template-parts/events/planner.php): a run
 * of months from the current one, plus one table of the events they reference.
 *
 * Shaped for a grid drawn client-side: each month carries only what a grid
 * can't derive — label, length, starting weekday — and `ev`, mapping a day
 * number to the indexes of the events on it. Events sit once in `events` rather
 * than repeated on every day a weekly one falls on, which keeps the JSON to a
 * fraction of the size.
 *
 * An event's `s` flag marks a one-off ("special") so the calendar can dot it
 * orange; recurring events dot green.
 *
 * @param int $count How many months, including the current one.
 * @return array{events: array<int, array>, months: array<int, array>}
 */
function juiced_event_planner_data( $count = 12 ) {
    $tz     = wp_timezone();
    $now    = new DateTimeImmutable( 'now', $tz );
    $cursor = $now->modify( 'first day of this month' )->setTime( 0, 0, 0 );
    $config = juiced_event_calendar_config();

    $events = [];
    foreach ( $config as $event ) {
        $events[] = [
            't' => $event['title'],
            'u' => $event['url'],
            'k' => $event['schedule'],
            'h' => $event['time'],
            'l' => $event['location'],
            'p' => $event['cost'],
            'g' => $event['signup'] ? esc_url_raw( $event['signup'] ) : '',
            's' => ! empty( $event['event_date'] ),
        ];
    }

    $months = [];
    for ( $m = 0; $m < (int) $count; $m++ ) {
        $first = $cursor->modify( "+{$m} months" );
        $days  = (int) $first->format( 't' );
        $on    = [];

        for ( $d = 1; $d <= $days; $d++ ) {
            $date = $first->setDate( (int) $first->format( 'Y' ), (int) $first->format( 'n' ), $d );
            foreach ( $config as $k => $event ) {
                if ( juiced_event_occurs_on( $event, $date, $tz ) ) {
                    $on[ $d ][] = $k;
                }
            }
        }

        $months[] = [
            'key'      => $first->format( 'Y-m' ),
            'label'    => $first->format( 'F Y' ),
            'y'        => (int) $first->format( 'Y' ),
            'm'        => (int) $first->format( 'n' ),
            'lead'     => (int) $first->format( 'w' ), // Blank cells before the 1st.
            'days'     => $days,
            'prevDays' => (int) $first->modify( '-1 day' )->format( 't' ),
            'today'    => 0 === $m ? (int) $now->format( 'j' ) : 0,
            // An object even when empty, so the client can index it by day.
            'ev'       => (object) $on,
        ];
    }

    return [ 'events' => $events, 'months' => $months ];
}

/**
 * Published events sorted ascending by next upcoming occurrence, past dropped.
 *
 * @param int $limit Max events to return.
 * @return array<int, array{post: WP_Post, when: DateTimeImmutable, recurring: bool}>
 */
function juiced_upcoming_events( $limit = 3 ) {
    $query = new WP_Query( [
        'post_type'      => 'event',
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'no_found_rows'  => true,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ] );

    $rows = [];
    foreach ( $query->posts as $post ) {
        $when = juiced_event_upcoming( $post->ID );
        if ( ! $when ) {
            continue;
        }
        $days   = juiced_normalize_days( get_field( 'day_of_week', $post->ID ) );
        $rows[] = [
            'post'      => $post,
            'when'      => $when,
            'recurring' => ! get_field( 'event_date', $post->ID ) && ! empty( $days ),
        ];
    }

    usort(
        $rows,
        static function ( $a, $b ) {
            return $a['when']->getTimestamp() <=> $b['when']->getTimestamp();
        }
    );

    return array_slice( $rows, 0, $limit );
}

/**
 * The featured event among upcoming rows (from juiced_upcoming_events()): the
 * event picked in $pick if it is still upcoming, else the soonest one-off
 * (non-recurring) event. Null when there is neither.
 *
 * @param array $rows Upcoming rows, soonest first.
 * @param int   $pick Event post ID chosen in the admin, or 0.
 * @return array|null
 */
function juiced_featured_event( array $rows, $pick = 0 ) {
    $pick     = (int) $pick;
    $fallback = null;
    foreach ( $rows as $row ) {
        if ( $pick && $row['post']->ID === $pick ) {
            return $row;
        }
        if ( ! $fallback && ! $row['recurring'] ) {
            $fallback = $row;
        }
    }
    return $fallback;
}
