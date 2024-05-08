<?php
namespace epiphyt\Meetup_Event_Publisher;

use DateTimeZone;

/**
 * Admin functionality class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Meetup_Event_Publisher
 */
final class Shortcodes {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_shortcode( 'meetup_event', [ self::class, 'render_shortcode' ] );
	}
	
	/**
	 * Render the meetup_event shortcode.
	 * 
	 * @param	array|string	$attributes Shortcode attributes
	 * @return	string Shortcode content
	 */
	public static function render_shortcode( $attributes ): string {
		$attributes = \shortcode_atts(
			[
				'event' => 'next',
				'exclude_protocol' => 'no',
				'field' => 'name',
			],
			$attributes
		);
		$event = Plugin::get_event( $attributes['event'] );
		
		if ( empty( $event ) ) {
			return '';
		}
		
		switch ( $attributes['field'] ) {
			case 'link':
				if ( $attributes['exclude_protocol'] === 'yes' ) {
					return \preg_replace( '/^https?:\/\//', '', $event[ $attributes['field'] ] );
				}
				break;
			case 'local_date':
				$date = new \DateTime( $event[ $attributes['field'] ] );
				
				return \wp_date( \get_option( 'date_format' ), $date->getTimestamp() );
		}
		
		return $event[ $attributes['field'] ] ?? '';
	}
}
