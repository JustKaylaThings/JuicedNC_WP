<?php
/**
 * Live Google reviews — PHP port of the storefront's src/lib/google-places.ts.
 *
 * Fetches each shop's reviews from the Google Places API (New) with
 * wp_remote_get(), normalizes them, and caches the result in a 24h transient
 * (the classic-WP equivalent of the old Next.js fetch-level ISR). Each shop's
 * star rating and review count are cached alongside. The homepage community
 * section (template-parts/home/community.php) merges the two locations, keeps
 * 4-star-and-up, and runs them as a marquee under the Instagram card.
 *
 * ── SETTINGS ──────────────────────────────────────────────────────────────────
 * Everything is set in Appearance → Customize → Google Reviews & Maps
 * (inc/customizer.php): show/hide, the API key, each shop's Place ID
 * (see juiced_review_place_ids()), the lowest star rating and how many to show.
 * Saving that section clears the cache so changes show right away.
 *
 * The API key can instead be defined in wp-config.php, which wins over the
 * Customizer field:
 *
 *     define( 'JUICED_GOOGLE_PLACES_API_KEY', 'your-key-here' );
 *
 * The key needs "Places API (New)" enabled in Google Cloud (NOT the legacy
 * "Places API") with billing on. With no key, every function here returns []
 * and the reviews strip simply hides — nothing breaks.
 *
 * @package Juiced
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Places API (New) single-place endpoint. */
const JUICED_GR_ENDPOINT = 'https://places.googleapis.com/v1/places/';
/** Only request the fields we render, to keep the response small. */
const JUICED_GR_FIELDMASK = 'id,displayName,rating,userRatingCount,googleMapsUri,reviews';

/** The Places API key — wp-config.php first, then the Customizer — or ''. */
function juiced_google_api_key() {
	if ( defined( 'JUICED_GOOGLE_PLACES_API_KEY' ) && JUICED_GOOGLE_PLACES_API_KEY ) {
		return (string) JUICED_GOOGLE_PLACES_API_KEY;
	}
	return trim( (string) get_theme_mod( 'google_places_api_key', '' ) );
}

/**
 * Whether the homepage reviews strip is switched on in the Customizer.
 *
 * @return bool
 */
function juiced_reviews_enabled() {
	return (bool) get_theme_mod( 'reviews_show', true );
}

/**
 * Fetch + normalize reviews for one Place ID, cached in a transient for 24h.
 * Returns an array of review rows, or [] on any failure so callers degrade
 * gracefully (missing key, API/billing error, brand-new listing with no reviews).
 *
 * @param string $place_id Google Maps Place ID.
 * @return array<int, array{author:string,photo:string,rating:int,relative:string,text:string,time:int,link:string}>
 */
function juiced_place_reviews( $place_id ) {
	$place_id = trim( (string) $place_id );
	$api_key  = juiced_google_api_key();
	if ( '' === $place_id || '' === $api_key ) {
		return array();
	}

	$cache_key = 'juiced_greviews_' . md5( $place_id );
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$response = wp_remote_get(
		JUICED_GR_ENDPOINT . rawurlencode( $place_id ) . '?languageCode=en',
		array(
			'timeout' => 8,
			'headers' => array(
				'X-Goog-Api-Key'   => $api_key,
				'X-Goog-FieldMask' => JUICED_GR_FIELDMASK,
			),
		)
	);

	// On a transport error or non-200, cache empty for a short window so a broken
	// key or outage doesn't hit the API on every single page load.
	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		set_transient( $cache_key, array(), HOUR_IN_SECONDS );
		set_transient( juiced_place_stats_key( $place_id ), array(), HOUR_IN_SECONDS );
		return array();
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	set_transient(
		juiced_place_stats_key( $place_id ),
		array(
			'rating' => isset( $data['rating'] ) ? (float) $data['rating'] : 0,
			'count'  => isset( $data['userRatingCount'] ) ? (int) $data['userRatingCount'] : 0,
		),
		DAY_IN_SECONDS
	);
	if ( ! is_array( $data ) || empty( $data['reviews'] ) ) {
		set_transient( $cache_key, array(), DAY_IN_SECONDS );
		return array();
	}

	$reviews = array();
	foreach ( $data['reviews'] as $rv ) {
		$text = '';
		if ( isset( $rv['text']['text'] ) ) {
			$text = $rv['text']['text'];
		} elseif ( isset( $rv['originalText']['text'] ) ) {
			$text = $rv['originalText']['text'];
		}
		$reviews[] = array(
			'author'   => isset( $rv['authorAttribution']['displayName'] ) ? $rv['authorAttribution']['displayName'] : 'Anonymous',
			'photo'    => isset( $rv['authorAttribution']['photoUri'] ) ? $rv['authorAttribution']['photoUri'] : '',
			'rating'   => isset( $rv['rating'] ) ? (int) $rv['rating'] : 0,
			'relative' => isset( $rv['relativePublishTimeDescription'] ) ? $rv['relativePublishTimeDescription'] : '',
			'text'     => $text,
			'time'     => isset( $rv['publishTime'] ) ? (int) strtotime( $rv['publishTime'] ) : 0,
			'link'     => isset( $rv['googleMapsUri'] ) ? $rv['googleMapsUri'] : '',
		);
	}

	set_transient( $cache_key, $reviews, DAY_IN_SECONDS );
	return $reviews;
}

/**
 * Merge reviews across all given Place IDs: keep those at or above the
 * Customizer's lowest rating (4 by default) with real text, sort newest first,
 * and cap at the Customizer's count (8 by default). Mirrors the storefront's
 * GoogleReviewsSection filtering.
 *
 * @param array<int, string> $place_ids Place IDs to pull from.
 * @param int|null           $limit     Max reviews to return; null uses the Customizer setting.
 * @return array<int, array{author:string,photo:string,rating:int,relative:string,text:string,time:int}>
 */
function juiced_community_reviews( $place_ids, $limit = null ) {
	if ( ! juiced_reviews_enabled() ) {
		return array();
	}
	$min    = max( 1, min( 5, (int) get_theme_mod( 'reviews_min_rating', 4 ) ) );
	$limit  = null === $limit ? max( 1, (int) get_theme_mod( 'reviews_count', 8 ) ) : $limit;
	$merged = array();
	foreach ( (array) $place_ids as $place_id ) {
		if ( ! $place_id ) {
			continue;
		}
		foreach ( juiced_place_reviews( $place_id ) as $review ) {
			if ( $review['rating'] >= $min && '' !== trim( (string) $review['text'] ) ) {
				$merged[] = $review;
			}
		}
	}

	usort(
		$merged,
		static function ( $a, $b ) {
			return $b['time'] <=> $a['time'];
		}
	);

	return array_slice( $merged, 0, $limit );
}

/**
 * The two shops' Place IDs. They default to the real Juiced! shops so reviews
 * light up as soon as the API key is set; the client can override them in the
 * Customizer (Business Info).
 *
 * @return array<int, string>
 */
function juiced_review_place_ids() {
	return array(
		juiced_business_field( 'loc1_place_id', 'ChIJqz-S5g9frIkR7BBvS1h_DvE' ), // Rock Quarry Road
		juiced_business_field( 'loc2_place_id', 'ChIJB2o8cbNfrIkR86nWswGqshk' ), // Hill Street
	);
}

/**
 * Transient key for one place's rating + review count.
 *
 * @param string $place_id Google Maps Place ID.
 * @return string
 */
function juiced_place_stats_key( $place_id ) {
	return 'juiced_gplace_' . md5( trim( (string) $place_id ) );
}

/**
 * One place's star rating and total review count, cached by
 * juiced_place_reviews() from the same API call.
 *
 * @param string $place_id Google Maps Place ID.
 * @return array{rating?:float,count?:int} Empty when unknown.
 */
function juiced_place_stats( $place_id ) {
	$place_id = trim( (string) $place_id );
	if ( '' === $place_id || '' === juiced_google_api_key() ) {
		return array();
	}

	$stats = get_transient( juiced_place_stats_key( $place_id ) );
	if ( false === $stats ) {
		// Reviews cached before stats were stored: refetch both once.
		delete_transient( 'juiced_greviews_' . md5( $place_id ) );
		juiced_place_reviews( $place_id );
		$stats = get_transient( juiced_place_stats_key( $place_id ) );
	}

	return is_array( $stats ) ? $stats : array();
}

/**
 * Combined rating (weighted by each shop's review count) and total count.
 *
 * @param array<int, string> $place_ids Place IDs to combine.
 * @return array{rating?:float,count?:int} Empty when no shop has ratings.
 */
function juiced_reviews_summary( $place_ids ) {
	$count = 0;
	$sum   = 0;
	foreach ( (array) $place_ids as $place_id ) {
		$stats = juiced_place_stats( $place_id );
		if ( ! empty( $stats['count'] ) ) {
			$count += $stats['count'];
			$sum   += $stats['rating'] * $stats['count'];
		}
	}

	return $count ? array( 'rating' => round( $sum / $count, 1 ), 'count' => $count ) : array();
}

/**
 * A line for each shop's Place ID control in the Customizer saying whether its
 * reviews are loading, e.g. "Connected: 4.8 stars from 175 Google reviews."
 *
 * @param int $i Location number (1 or 2).
 * @return string Leading space included; '' when there's nothing to say.
 */
function juiced_review_status_note( $i ) {
	if ( '' === juiced_google_api_key() ) {
		return ' ' . __( 'Not connected yet: add the API key above.', 'juiced' );
	}
	$ids   = juiced_review_place_ids();
	$stats = isset( $ids[ $i - 1 ] ) ? juiced_place_stats( $ids[ $i - 1 ] ) : array();
	if ( empty( $stats['count'] ) ) {
		return ' ' . __( 'Not connected: Google sent nothing back. Check the API key (Places API (New) enabled, billing on) and the Place ID.', 'juiced' );
	}
	/* translators: 1: star rating, 2: number of reviews. */
	return ' ' . sprintf( __( 'Connected: %1$s stars from %2$s Google reviews.', 'juiced' ), number_format_i18n( $stats['rating'], 1 ), number_format_i18n( $stats['count'] ) );
}

/**
 * Drop the cached reviews and ratings for the current Place IDs, so the next
 * page load refetches. Hooked before and after a Customizer save, which clears
 * both the old and the new IDs when one is changed, so a new key, Place ID or
 * setting shows at once.
 */
function juiced_flush_review_cache() {
	foreach ( juiced_review_place_ids() as $place_id ) {
		delete_transient( 'juiced_greviews_' . md5( trim( (string) $place_id ) ) );
		delete_transient( juiced_place_stats_key( $place_id ) );
	}
}
add_action( 'customize_save', 'juiced_flush_review_cache' );
add_action( 'customize_save_after', 'juiced_flush_review_cache' );
