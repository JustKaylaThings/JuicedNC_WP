<?php
/**
 * Special Offer.
 *
 * @package TinySolutions\cptwooint
 */

namespace TinySolutions\cptwooint\Controllers\Notice;

use TinySolutions\cptwooint\Traits\SingletonTrait;
use TinySolutions\cptwooint\Abs\Discount;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Black Friday Offer.
 */
class SpecialDiscount extends Discount {

	/**
	 * Singleton Trait.
	 */
	use SingletonTrait;

	/**
	 * @return array
	 */
	public function the_options(): array {
		return [
			'option_name'      => 'cptwooint_black_friday_2025',
			'prev_option_name' => 'cptwooint_black_friday_2024',
			'start_date'       => '10 November 2025',
			'end_date'         => '10 December 2025',
			'image_url'        => 'https://ps.w.org/cpt-woo-integration/assets/icon-256x256.gif',
			'check_pro'        => true,
			'notice_for'       => '🥳 Cyber Savings extended: 30% off',
			'notice_message'   => "Don't miss out on our biggest sale of the year! Get your
						<b> Custom Post Type WooCommerce Integration Pro plan</b> with <b>UP TO 30% OFF</b>! Limited time
						offer!!",
		];
	}
}
