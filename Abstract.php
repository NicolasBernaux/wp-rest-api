<?php
namespace WpRestApi;

abstract class AbstractApi {
	private static $api = array();

	abstract public function init();

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'init' ) );
	}

	public static function get_current_class() {
		return get_called_class();
	}

	public static function get_instance() {
		$class = self::get_current_class();

		if ( ! isset( self::$api[ $class ] ) || ! is_object( self::$api[ $class ] ) ) {
			self::$api[ $class ] = new $class();
		}

		return self::$api[ $class ];
	}
}
