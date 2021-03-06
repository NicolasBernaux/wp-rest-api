<?php
namespace WpRestApi\Routes;

use WP_REST_Request;
use WpRestApi\App;
use WpRestApi\AbstractApi;

class Menu extends AbstractApi {
	const SLUG = 'menu';

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

	public function listing( WP_REST_Request $request ) {
		$params = $request->get_params();
		$menus  = wp_get_nav_menus( $params );

		if ( empty( $menus ) ) {
			return;
		};

		$response = array();
		foreach ( $menus as $menu ) {
			$menu_items              = wp_get_nav_menu_items( $menu->slug );
			$mapped_items            = $this->map_items( $menu_items );
			$response[ $menu->slug ] = $this->build_tree( $mapped_items );
		}

		return $response;
	}

	public function map_items( array &$items ) {
		$mapped_items = array();

		foreach ( $items as $item ) {
			$url = $item->url;
			if ( strpos( $item->url, $_SERVER['HTTP_HOST'] ) ) {
				$parsed_url = wp_parse_url( $item->url );
				$url        = $parsed_url['path'];
			}

			$mapped_items[ $item->ID ]                   = new \stdClass();
			$mapped_items[ $item->ID ]->ID               = $item->ID;
			$mapped_items[ $item->ID ]->menu_item_parent = $item->menu_item_parent;
			$mapped_items[ $item->ID ]->url              = $url;
			$mapped_items[ $item->ID ]->title            = $item->title;
			$mapped_items[ $item->ID ]->type_label       = $item->type_label;
		}

		return $mapped_items;
	}

	public function build_tree( array $items, $parent_id = 0 ) {
		$branch = array();
		foreach ( $items as &$item ) {
			if ( (int) $item->menu_item_parent === $parent_id ) {
				$children = $this->build_tree( $items, $item->ID );
				if ( $children ) {
					$item->children = $children;
				}

				$branch[ $item->ID ] = $item;
				unset( $item );
			}
		}

		return $branch;
	}
}

