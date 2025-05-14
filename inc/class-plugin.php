<?php
namespace epiphyt\Meetup_Event_Publisher;

use DateTime;
use DateTimeZone;

/**
 * The main plugin class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Meetup_Event_Publisher
 */
final class Plugin {
	const OPTION_PREFIX = 'meetup_event_publisher';
	
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'load_textdomain' ], 0 );
		\add_action( self::OPTION_PREFIX . '_api_update', [ self::class, 'get_events_from_api' ] );
		
		Admin::init();
		Publisher::init();
		Shortcodes::init();
	}
	
	/**
	 * Deregister the cron job.
	 */
	private static function deregister_cron(): void {
		if ( ! \wp_next_scheduled( self::OPTION_PREFIX . '_api_update' ) ) {
			return;
		}
		
		\wp_clear_scheduled_hook( self::OPTION_PREFIX . '_api_update' );
	}
	
	/**
	 * Get data of a single event.
	 * 
	 * You can get it via ID, local date, name or special keyword 'next'.
	 * 
	 * @param	string	$event Which event to get
	 * @param	string	$slug Meetup slug
	 * @return	array Event data
	 */
	public static function get_event( string $event, string $slug ): array {
		$events = (array) \get_option( self::get_option_name( 'events' ) );
		
		if ( empty( $events ) ) {
			return [];
		}
		
		if ( ! empty( $events[ $slug ] ) ) {
			$events = $events[ $slug ];
		}
		
		if ( $event === 'next' ) {
			if ( ! isset( $events[0] ) ) {
				return [];
			}
			
			$event = $events[0];
		}
		else {
			foreach ( $events as $_event ) {
				if (
					$event === $_event['id']
					|| $event === $_event['local_date']
					|| $event === $_event['name']
				) {
					$event = $_event;
					break;
				}
			}
		}
		
		return $event;
	}
	
	/**
	 * Get events data.
	 * 
	 * @param	string	$event Which event to get
	 * @return	mixed[] List of event data
	 */
	public static function get_events( string $event ): array {
		$events = [];
		$event_list = (array) \get_option( self::get_option_name( 'events' ) );
		
		if ( $event === 'next' ) {
			foreach ( $event_list as $meetup_events ) {
				if ( ! isset( $meetup_events[0] ) ) {
					continue;
				}
				
				$events[] = $meetup_events[0];
			}
		}
		else {
			foreach ( $event_list as $meetup_events ) {
				foreach ( $meetup_events as $_event ) {
					if (
						$event === $_event['id']
						|| $event === $_event['local_date']
						|| $event === $_event['name']
					) {
						$events[] = $_event;
						break;
					}
				}
			}
		}
		
		return $events;
	}
	
	/**
	 * Get events from Meetup.com.
	 */
	public static function get_events_from_api(): void {
		$events = API::get_events();
		
		\update_option( self::get_option_name( 'events' ), $events );
	}
	
	/**
	 * Get an option name with plugin prefix.
	 * 
	 * @param	string	$option Option name
	 * @return	string Option name with plugin prefix
	 */
	public static function get_option_name( string $option ): string {
		return self::OPTION_PREFIX . '_' . $option;
	}
	
	/**
	 * Load translations.
	 */
	public static function load_textdomain(): void {
		\load_plugin_textdomain( 'meetup-event-publisher', false, \dirname( \plugin_basename( \MEETUP_EVENT_PUBLISHER_FILE ) ) . '/languages' );
	}
	
	/**
	 * Tasks running on activation.
	 */
	public static function on_activation(): void {
		self::register_cron();
	}
	
	/**
	 * Tasks running on deactivation.
	 */
	public static function on_deactivation(): void {
		self::deregister_cron();
	}
	
	/**
	 * Register the cron job.
	 */
	private static function register_cron(): void {
		$date = new DateTime( 'today 00:00:00', new DateTimeZone( \get_option( 'timezone_string' ) ) );
		
		\wp_schedule_event( $date->getTimestamp() + $date->getOffset(), 'daily', self::OPTION_PREFIX . '_api_update' );
	}
}
