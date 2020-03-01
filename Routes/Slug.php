<?php
namespace WpRestApi\Routes;

use Helper\Helper;
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
			App::API_VERSION . '/' . self::SLUG,
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'single' ),
				'args'     => array(
					'post_type' => array(),
				),
			)
		);
		register_rest_route(
			App::API_PREFIX,
			App::API_VERSION . '/' . self::SLUG . '/(?P<name>\S+)',
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

		// Defaults args
		$args = array(
			'post_type'      => 'any',
			'posts_per_page' => 1,
		);

		// If no name get front page
		if ( ! array_key_exists( 'name', $params ) || ! $params['name'] ) {
			$args['p'] = get_option( 'page_on_front' );
		}

		$args = array_merge( $args, $params );

		$query = new WP_Query( $args );
		$posts = $query->get_posts();

		if ( empty( $posts ) ) {
			return array();
		}

		$post     = $posts[0];
		$response = $post;

		if ( function_exists( 'get_fields' ) ) {
			$acf_fields = get_fields( $post->ID );

			if ( ! empty( $acf_fields ) ) {
				foreach ( $acf_fields as &$field ) {
					if ( is_array( $field ) &&
						array_key_exists( 'type', $field ) &&
						'image' === $field['type'] &&
						array_key_exists( 'ID', $field ) &&
						! empty( $field['ID'] )
					) {
						$field = Helper::image( $field['ID'] );
					}
				}
				$response = array_merge( (array) $response, $acf_fields );
			}
		}

		$meta = get_post_meta( $post->ID );
		if ( ! empty( $meta ) ) {
			$response = array_merge( (array) $response, array( 'meta' => $meta ) );
		}

		$response = apply_filters( 'wp_rest_api_alter_slug', $response, 10, 1 );

		$featured_image = Helper::image( $post );
		if ( $featured_image ) {
			$response['featured_image'] = $featured_image;
		}

		return $response;
	}
}

