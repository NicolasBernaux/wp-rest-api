<?php
/*
Plugin Name: WP-REST-API
Version: 1.0
Author: @nicolasBernaux
Author URI: http://www.nicolas-bernaux.com
*/
namespace WpRestApi;

require_once __DIR__ . '/vendor/autoload.php';

use WpRestApi\Routes as Routes;

// require_once('./Routes/Listing.php');

class App {
	const API_PREFIX  = 'rest-api';
	const API_VERSION = 'v1';

	public function __construct() {
		Routes\Listing::get_instance();
		Routes\Slug::get_instance();
	}

	public function api_ini() {
	}
}

new \WpRestApi\App();
