<?php
namespace WpRestApi\Routes;

use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WpRestApi\App;
use WpRestApi\AbstractApi;

class Slug extends AbstractApi {
	const SLUG = 'slug';

	public function init() {
		register_rest_route(
			App::API_PREFIX,
			App::API_VERSION . '/' . self::SLUG . '/(?P<slug>\S+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'single' ),
				'args'     => array(
					'post_type' => array(),
				),
			)
		);
	}

	public function single( WP_REST_Request $request ) {
		$params = $request->get_params();

		if ( empty( $params ) || ! $params['slug'] ) {
			$response = new WP_REST_Response( array( 'No params found' ) );
			$response->set_status( 500 );

			return $response;
		}

		$post_type = array_key_exists( 'post_type', $params ) ? $params['post_type'] : 'any';
		$query     = new WP_Query(
			array(
				'name'           => $params['slug'],
				'post_type'      => $post_type,
				'posts_per_page' => 1,
			)
		);

		$posts = $query->get_posts();
		$post  = $posts[0];

		if ( empty( $post ) ) {
			return array();
		}

		$response = $post;

		if ( function_exists( 'get_fields' ) ) {
			$acf_fields = get_fields( $post->ID );

			if ( ! empty( $acf_fields ) ) {
				$response = array_merge( (array) $response, $acf_fields );
			}
		}

		// @TODO: get featured image
		// @TODO: add filter for images

		return $response;
	}
}

