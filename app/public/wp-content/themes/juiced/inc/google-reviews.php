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
 * ── API KEY (required) ────────────────────────────────────────────────────────
 * The key is read from a PHP constant so it never lives in the theme repo. Add
 * this to wp-config.php (above the "That's all, stop editing" line):
 *
 *     define( 'JUICED_GOOGLE_PLACES_API_KEY', 'your-key-here' );
 *
 * The key needs "Places API (New)" enabled in Google Cloud (NOT the legacy
 * "Places API") with billing on. Restrict it to the Places API + your server IP.
 * If the constant is absent, every function here returns [] and the reviews
 * strip simply hides — nothing breaks.
 *
 * Place IDs are configured per location in Appearance → Customize → Business
 * Info (see juiced_review_place_ids()).
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

/** The Places API key, from wp-config.php, or '' when unset. */
function juiced_google_api_key() {
	return ( defined( 'JUICED_GOOGLE_PLACES_API_KEY' ) && JUICED_GOOGLE_PLACES_API_KEY )
		? (string) JUICED_GOOGLE_PLACES_API_KEY
		: '';
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
 * Merge reviews across all given Place IDs: keep 4-star-and-up with real text,
 * sort newest first, and cap the count. Mirrors the storefront's
 * GoogleReviewsSection filtering.
 *
 * @param array<int, string> $place_ids Place IDs to pull from.
 * @param int                $limit     Max reviews to return.
 * @return array<int, array{author:string,photo:string,rating:int,relative:string,text:string,time:int}>
 */
function juiced_community_reviews( $place_ids, $limit = 9 ) {
	$merged = array();
	foreach ( (array) $place_ids as $place_id ) {
		if ( ! $place_id ) {
			continue;
		}
		foreach ( juiced_place_reviews( $place_id ) as $review ) {
			if ( $review['rating'] >= 4 && '' !== trim( (string) $review['text'] ) ) {
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
