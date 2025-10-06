<ul class="meetup-event-publisher__meetup-list">
	<?php foreach ( $events as $event ) : ?>
	<li class="meetup-event-publisher__meetup-item">
		<?php if ( ! \in_array( 'organizer', $attributes['hidden'] ) ) : ?>
		<p class="meetup-event-publisher__meetup-organizer">
			<?php
			\printf(
				'%1$s%2$s%3$s',
				! empty( $event['organizer']['url'] ) ? '<a href="' . \esc_url( $event['organizer']['url'] ) . '">' : '',
				! empty( $event['organizer']['name'] ) ? \esc_html( $event['organizer']['name'] ) : '',
				! empty( $event['organizer']['url'] ) ? '</a>' : '',
			)
			?>
		</p>
		<?php
		endif;
		if ( ! \in_array( 'title', $attributes['hidden'] ) ) :
		?>
		<h3 class="meetup-event-publisher__meetup-title"><a href="<?php echo \esc_url( $event['url'] ); ?>"><?php echo \esc_html( $event['name'] ); ?></a></h3>
		<?php
		endif;
		if ( ! \in_array( 'meta', $attributes['hidden'] ) ) :
		?>
		<p class="meetup-event-publisher__meetup-meta">
			<?php
			try {
				$date_time = new DateTimeImmutable( $event['start_date'] );
				echo \esc_html( \wp_date( \get_option( 'date_format' ) . ' | ' . \get_option( 'time_format' ), $date_time->getTimestamp() ) );
			}
			catch( \Throwable ) {}
			?>
		</p>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>
