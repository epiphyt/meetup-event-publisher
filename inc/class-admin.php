<?php
namespace epiphyt\Meetup_Event_Publisher;

/**
 * Admin functionality class.
 * 
 * @author	Epiphyt
 * @license	GPL2
 * @package	epiphyt\Meetup_Event_Publisher
 */
final class Admin {
	/**
	 * Initialize functions.
	 */
	public static function init(): void {
		\add_action( 'admin_init', [ self::class, 'register_settings' ] );
	}
	
	/**
	 * Register settings.
	 */
	public static function register_settings(): void {
		\add_settings_section(
			Plugin::OPTION_PREFIX,
			null,
			null,
			'writing',
		);
		
		\add_settings_field(
			Plugin::get_option_name( 'slug' ),
			__( 'Meetup.com Slug', 'meetup-event-publisher' ),
			[ self::class, 'settings_field_input' ],
			'writing',
			Plugin::OPTION_PREFIX,
			[
				'classes' => [
					'regular-text',
				],
				'option' => Plugin::get_option_name( 'slug' ),
				'type' => 'text',
			],
		);
		\register_setting( 'writing', Plugin::get_option_name( 'slug' ) );
	}
	
	/**
	 * Display a settings field as input.
	 * 
	 * @param	array	$args Settings arguments
	 */
	public static function settings_field_input( array $args ): void {
		$option = get_option( $args['option'], '' );
		
		// get default
		if ( $option === '' && isset( $args['default'] ) ) {
			$option = $args['default'];
		}
		
		if ( $args['type'] !== 'checkbox' && $args['type'] !== 'radio' ) :
		// get additional attributes
		$additional_attributes = '';
		
		if ( isset( $args['accept'] ) ) {
			$additional_attributes .= ' accept="' . esc_attr( $args['accept'] ) . '"';
		}
		
		if ( isset( $args['max'] ) ) {
			$additional_attributes .= ' max="' . esc_attr( $args['max'] ) . '"';
		}
		
		if ( isset( $args['min'] ) ) {
			$additional_attributes .= ' min="' . esc_attr( $args['min'] ) . '"';
		}
		?>
		<input id="<?php echo esc_attr( $args['option'] ); ?>" type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $args['option'] ); ?>" value="<?php echo esc_attr( $option ); ?>"<?php echo $additional_attributes . ( ! empty( $args['classes'] ) ? ' class="' . implode( ' ', array_map( 'sanitize_html_class', $args['classes'] ) ) . '"' : '' ); ?>><?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
		<label for="<?php echo esc_attr( $args['option'] ); ?>"><input id="<?php echo esc_attr( $args['option'] ); ?>" type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $args['option'] ); ?>" value="yes"<?php checked( 'yes', $option ); ?>><?php echo esc_html( $args['label'] ); ?></label>
		<?php
		endif;
		
		if ( ! empty( $args['description'] ) ) {
			echo '<p>' . esc_html( $args['description'] ) . '</p>';
		}
	}
}
