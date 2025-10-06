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
	/**
	 * Initialize functions.
	 */
	public static function get_events(): array {
		$slug = \get_option( Plugin::get_option_name( 'slug' ) );
		$url = \MEETUP_EVENT_PUBLISHER_API_BASE . 'meetups/v1/events';
		
		if ( ! empty( $slug ) ) {
			$parameters = \http_build_query( [ 'meetup' => $slug ] );
			$url .= '?' . $parameters;
		}
		
		$request = \wp_remote_get( $url );
		$response = \wp_remote_retrieve_body( $request );
		
		if ( ! \json_validate( $response ) ) {
			return [];
		}
		
		$json = \json_decode( $response, true );
		
		if ( ! empty( $json['code'] ) || ! empty( $json['message'] ) ) {
			\error_log( 'Request: ' . $url . \PHP_EOL ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			\error_log( print_r( $json, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
			
			return [];
		}
		
		return $json;
	}
}
