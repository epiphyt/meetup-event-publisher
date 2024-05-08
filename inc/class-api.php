<?php
namespace epiphyt\Meetup_Event_Publisher;

/**
 * API functionality class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Meetup_Event_Publisher
 */
final class API {
	const BASE_URL = 'https://api.meetup.com/';
	
	/**
	 * Initialize functions.
	 */
	public static function get_events(): array {
		$slug = \get_option( Plugin::get_option_name( 'slug' ) );
		
		if ( ! $slug ) {
			return [];
		}
		
		$url = self::BASE_URL . $slug . '/events';
		$request = \wp_remote_get( $url );
		$response = \wp_remote_retrieve_body( $request );
		
		if ( ! self::is_json( $response ) ) {
			return [];
		}
		
		$json = \json_decode( $response, true );
		
		if ( ! empty( $json['errors'] ) ) {
			\error_log( 'Request: ' . $url . \PHP_EOL ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			\error_log( print_r( $json, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
			
			return [];
		}
		
		return \array_unique( $json, \SORT_REGULAR );
	}
	
	/**
	 * Check if given string is a valid JSON.
	 * 
	 * @param	string	$string The string to check
	 * @return	bool Whether the string is valid JSON
	 */
	public static function is_json( string $string ): bool {
		if ( ! \is_string( $string ) ) {
			return false;
		}
		
		\json_decode( $string );
		
		return \json_last_error() === \JSON_ERROR_NONE;
	}
}
