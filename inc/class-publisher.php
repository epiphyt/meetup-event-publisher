<?php
namespace epiphyt\Meetup_Event_Publisher;

use DateTime;
use DateTimeZone;

/**
 * Publish a meetup.com post.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Meetup_Event_Publisher
 */
final class Publisher {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( Plugin::OPTION_PREFIX . '_api_update', [ self::class, 'publish_event' ], 20 );
	}
	
	/**
	 * Create or update an event.
	 * 
	 * @param	array		$event The event data
	 * @param	null|int	$post_id The post ID for update a post or null
	 */
	public static function create_or_update_event( array $event, ?int $post_id = null ): void {
		$login_button_markup = '<!-- wp:buttons -->
		<div class="wp-block-buttons"><!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . \sanitize_url( $event['url'] ) . '">' . \esc_html__( 'Signup now', 'meetup-event-publisher' ) . '</a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons -->';
		$post_array = [
			'post_content' => $event['description'] . $login_button_markup,
			'post_status' => 'publish',
			'post_title' => $event['name'],
			'meta_input' => [
				'local_date' => $event['start_date'],
				'meetup_id' => self::get_id_by_data( $event ),
			],
		];
		
		if ( ! empty( $post_id ) ) {
			$post_array['ID'] = $post_id;
			
			\wp_update_post( $post_array );
		}
		else {
			\wp_insert_post( $post_array );
		}
	}
	
	/**
	 * Get the event ID by its data (from the URL field).
	 * 
	 * @param	mixed[]	$data Meetup data
	 * @return	string Event ID
	 */
	private static function get_id_by_data( array $data ): string {
		return \end( \array_filter( \explode( '/', $data['url'] ) ) );
	}
	
	/**
	 * Publish the next event.
	 */
	public static function publish_event(): void {
		$slug = \get_option( Plugin::get_option_name( 'slug' ) );
		
		if ( ! empty( $slug ) ) {
			$event = Plugin::get_event( 'next', $slug );
			
			if ( \is_array( $event ) ) {
				self::set_event( $event );
			}
		}
		else {
			foreach ( Plugin::get_events( 'next' ) as $event ) {
				if ( \is_array( $event ) ) {
					self::set_event( $event );
				}
			}
		}
	}
	
	private static function set_event( array $event ) {
		$arguments = [
			'meta_key' => 'meetup_id',
			'meta_value' => $event['id'],
			'numberposts' => 1,
			'post_type' => 'post',
		];
		$posts = \get_posts( $arguments );
		$post_id = ! empty( $posts ) ? $posts[0]->ID : null;
		
		self::create_or_update_event( $event, $post_id );
	}
}
