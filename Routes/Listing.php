<?php
namespace WpRestApi\Routes;

use WP_Query;
use WpRestApi\App;
use WpRestApi\AbstractApi;

class Listing extends AbstractApi {
	const SLUG = 'listing';

	public function init() {
		register_rest_route(
			App::API_PREFIX,
			App::API_VERSION . '/' . self::SLUG,
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'listing' ),
			)
		);
	}

	public function listing() {
		$query = new WP_Query(
			array(
				'post_type'     => 'any',
				'post_status'   => 'publish',
				'post_per_page' => '-1',
			)
		);

		$posts = $query->get_posts();

		if ( empty( $posts ) ) {
			return array();
		}

		$links = array();
		foreach ( $posts as $post ) {
			$permalink = get_permalink( $post );
			$links[]   = parse_url($permalink, PHP_URL_PATH);
		}

		return $links;
	}
}

