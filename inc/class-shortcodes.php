<?php
namespace epiphyt\Meetup_Event_Publisher;

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
				'slug' => \get_option( Plugin::get_option_name( 'slug' ) ),
			],
			$attributes
		);
		$event = Plugin::get_event( $attributes['event'], $attributes['slug'] );
		
		if ( empty( $event ) ) {
			return '';
		}
		
		switch ( $attributes['field'] ) {
			case 'link':
			case 'url':
				if ( $attributes['exclude_protocol'] === 'yes' ) {
					return \preg_replace( '/^https?:\/\//', '', $event['url'] );
				}
				
				return $event['url'];
			case 'local_date':
			case 'start_date':
				$date = new \DateTimeImmutable( $event['start_date'] );
				
				return \wp_date( \get_option( 'date_format' ), $date->getTimestamp() );
			default:
				$parts = \explode( '.', $attributes['field'] );
				$value = $event;
				
				foreach ( $parts as $part ) {
					if ( ! isset( $value[ $part ] ) ) {
						return '';
					}
					
					$value = $value[ $part ];
				}
				
				if ( ! \is_string( $value ) ) {
					return '';
				}
				
				return $value;
		}
	}
}
