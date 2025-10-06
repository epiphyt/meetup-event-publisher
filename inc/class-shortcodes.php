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
		\add_shortcode( 'meetup_event', [ self::class, 'render_meetup_event' ] );
		\add_shortcode( 'meetup_event_list', [ self::class, 'meetup_event_list' ] );
	}
	
	/**
	 * Render the meetup_event shortcode.
	 * 
	 * @param	array|string	$attributes Shortcode attributes
	 * @return	string Shortcode content
	 */
	public static function render_meetup_event( $attributes ): string {
		$attributes = \shortcode_atts(
			[
				'event' => 'next',
				'exclude_protocol' => 'no',
				'fallback' => '',
				'field' => 'name',
				'slug' => \get_option( Plugin::get_option_name( 'slug' ) ),
			],
			$attributes
		);
		$event = Plugin::get_event( $attributes['event'], $attributes['slug'] );
		
		if ( empty( $event ) ) {
			return ! empty( $attributes['fallback'] ) ? \esc_html( $attributes['fallback'] ) : '';
		}
		
		switch ( $attributes['field'] ) {
			case 'link':
			case 'url':
				if ( ! isset( $event['url'] ) && ! empty( $attributes['fallback'] ) ) {
					return \esc_html( $attributes['fallback'] );
				}
				
				if ( $attributes['exclude_protocol'] === 'yes' ) {
					return \rawurldecode( \preg_replace( '/^https?:\/\//', '', $event['url'] ) );
				}
				
				return \esc_url( $event['url'] );
			case 'local_date':
			case 'start_date':
				try {
					$date = new \DateTimeImmutable( $event['start_date'] );
					
					return \esc_html( \wp_date( \get_option( 'date_format' ), $date->getTimestamp() ) );
				}
				catch ( \Throwable ) {
					return ! empty( $attributes['fallback'] ) ? \esc_html( $attributes['fallback'] ) : '';
				}
			default:
				$parts = \explode( '.', $attributes['field'] );
				$value = $event;
				
				foreach ( $parts as $part ) {
					if ( ! isset( $value[ $part ] ) ) {
						return ! empty( $attributes['fallback'] ) ? \esc_html( $attributes['fallback'] ) : '';
					}
					
					$value = $value[ $part ];
				}
				
				if ( ! \is_string( $value ) ) {
					return ! empty( $attributes['fallback'] ) ? \esc_html( $attributes['fallback'] ) : '';
				}
				
				return $value;
		}
	}
	
	/**
	 * Render the meetup_event_list shortcode.
	 * 
	 * @param	array|string	$attributes Shortcode attributes
	 * @return	string Shortcode content
	 */
	public static function meetup_event_list( $attributes ): string {
		$attributes = \shortcode_atts(
			[
				'fallback' => \__( 'There is currently no data available for this meetup.', 'meetup-event-publisher' ),
				'hidden' => '',
				'limit' => 10,
				'slug' => '',
			],
			$attributes
		);
		$attributes['hidden'] = \array_map( 'trim', \explode( ',', $attributes['hidden'] ) );
		$events = Plugin::get_events();
		
		if ( ! empty( $attributes['slug'] ) ) {
			if ( ! \get_option( Plugin::get_option_name( 'slug' ) ) ) {
				$events = $events[ $attributes['slug'] ] ?? [];
			}
		}
		else if ( ! \array_is_list( $events ) ) {
			$_events = [];
			
			foreach ( $events as $meetup_events ) {
				$_events = \array_merge( $_events, $meetup_events );
			}
			
			$events = $_events;
		}
		
		\usort( $events, static function( $a, $b ): int {
			return \strtotime( $a['start_date'] ) - \strtotime( $b['start_date'] );
		} );
		
		if ( \is_numeric( $attributes['limit'] ) ) {
			\array_splice( $events, $attributes['limit'] );
		}
		
		if ( empty( $events ) ) {
			return '<p>' . ( ! empty( $attributes['fallback'] ) ? \esc_html( $attributes['fallback'] ) : '' ) . '</p>';
		}
		
		\ob_start();
		include __DIR__ . '/../templates/meetup-event.php';
		
		return (string) \ob_get_clean();
	}
}
