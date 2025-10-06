<?php
namespace epiphyt\Meetup_Event_Publisher;

/*
Plugin Name:	Meetup Event Publisher
Description:	Retrieve events from meetup.com and publishes them as posts.
Author:			Epiphyt
Author URI:		https://epiph.yt/en/
Version:		2.1.0
License:		GPL2
License URI:	https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP:	8.3
Text Domain:	meetup-event-publisher
Domain Path:	/languages

Meetup Event Publisher is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Meetup Event Publisher is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Meetup Event Publisher. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
\defined( 'ABSPATH' ) || exit;

\define( 'MEETUP_EVENT_PUBLISHER_VERSION', '2.1.0' );
\define( 'MEETUP_EVENT_PUBLISHER_BASE', WP_PLUGIN_DIR . '/meetup-event-publisher/' );
\define( 'MEETUP_EVENT_PUBLISHER_FILE', __FILE__ );
\define( 'MEETUP_EVENT_PUBLISHER_URL', \plugin_dir_url( MEETUP_EVENT_PUBLISHER_FILE ) );

if ( ! \defined( 'MEETUP_EVENT_PUBLISHER_API_BASE' ) ) {
	if ( \wp_get_environment_type() === 'local' ) {
		\define( 'MEETUP_EVENT_PUBLISHER_API_BASE', 'http://wp-dev.test/wp-json/' );
	}
	else {
		\define( 'MEETUP_EVENT_PUBLISHER_API_BASE', 'https://wpmeetups.de/wp-json/' );
	}
}

/**
 * Autoload all necessary classes.
 * 
 * @param	string	$class_name The class name of the auto-loaded class
 */
\spl_autoload_register( function( string $class_name ) {
	$namespace = \strtolower( __NAMESPACE__ . '\\' );
	$path = \explode( '\\', $class_name );
	$filename = \str_replace( '_', '-', \strtolower( \array_pop( $path ) ) );
	$class_name = \str_replace(
		[ $namespace, '\\', '_' ],
		[ '', '/', '-' ],
		\strtolower( $class_name )
	);
	$string_position = \strrpos( $class_name, $filename );
	
	if ( $string_position !== false ) {
		$class_name = \substr_replace( $class_name, 'class-' . $filename, $string_position, \strlen( $filename ) );
	}
	
	$maybe_file = __DIR__ . '/inc/' . $class_name . '.php';
	
	if ( \file_exists( $maybe_file ) ) {
		require_once $maybe_file;
	}
} );

\add_action( 'plugins_loaded', [ Plugin::class, 'init' ] );
\register_activation_hook( \MEETUP_EVENT_PUBLISHER_FILE, [ Plugin::class, 'on_activation' ] );
\register_deactivation_hook( \MEETUP_EVENT_PUBLISHER_FILE, [ Plugin::class, 'on_deactivation' ] );
