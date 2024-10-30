<?php
/*
Plugin Name: Kingsley's WhatPulse Widget
Description: A helpful widget for WhatPulse statistics!
Version: 1.3
Author: Kingsley Muir
Author URI: http://kingsley-muir.com
License: WTFPLv2
*/

/*
Copyright © 2016 Kingsley Muir <me@kingsley-muir.com>
This work is free. You can redistribute it and/or modify it under the
terms of the Do What The Fuck You Want To Public License, Version 2,
as published by Sam Hocevar. See http://www.wtfpl.net/ for more details.
*/
class kwp_widget extends WP_Widget {
	
	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'kwp_widget', 
			
			// Widget name will appear in UI
			'Kingsley\'s WhatPulse Widget', 
			
			// Widget description
			array( 'description' => 'Kingsley\'s WhatPulse widget' ) 
		);
	}
	
	public $args = array(
		'before_title'	=> '<h3 class="widget-title">',
		'after_title'	=> '</h3>',
		'before_widget'	=> '<div class="widget-wrap">',
		'after_widget'	=> '</div>'
	);
	
	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$query = http_build_query( array('user' => $instance['UserId'], 'formatted' => 'yes', 'format' => 'json' ) );
		
		$url = "http://api.whatpulse.org/user.php?" . $query;
		$response = wp_remote_get( $url );
		
		$body = wp_remote_retrieve_body( $response );
		
		if( 200 != wp_remote_retrieve_response_code( $response ) )
			echo "Something went wrong: " . wp_remote_retrieve_response_message( $response );
		else {
			$result = json_decode( $body );
			
			echo $args['before_widget'];
			echo $args['before_title'] . apply_filters( 'widget_title', 'WhatPulse Stats' ) . $args['after_title'];

			echo '<div class="textwidget">';
			
			if( $instance['Date Joined'] == 'on' )
				echo "Date Joined: {$result->DateJoined}<br />";
				
			if($instance['Pulses'] == 'on' )
				echo "Total Pulses: {$result->Pulses}<br />";
				
			if( $instance['Keys'] == 'on' )
				echo "Key's pressed: {$result->Keys}<br />";
				
			if( $instance['Clicks'] == 'on' )
				echo "Clicks: {$result->Clicks}<br />";
			
			if( $instance['Download'] == 'on' )
				echo "Downloaded: {$result->Download}<br />";
			
			if( $instance['Upload'] == 'on' )
				echo "Uploaded: {$result->Upload}<br />";
				
			if( $instance['Uptime Short'] == 'on' )
				echo "Uptime: {$result->UptimeShort}<br />";
			
			
			if( $instance['Show Ranks'] == 'on' ) {
				echo "<br /><h4>Rankings</h4>";
				echo "Keys: ", $result->Ranks->Keys, "<br />";
				echo "Clicks: ", $result->Ranks->Clicks, "<br />";
				echo "Upload: ", $result->Ranks->Upload, "<br />";
				echo "Uptime: ", $result->Ranks->Uptime, "<br />";
			}
			echo '</div>';
			
			echo $args['after_widget'];
		}
	}
	
	// Widget Backend 
	public function form( $instance ) {
		if( empty( $instance['UserId'] ) )
			$instance['UserId'] = '';
		if( empty( $instance['Date Joined'] ) )
			$instance['Date Joined'] = '';
		if( empty( $instance['Pulses'] ) )
			$instance['Pulses'] = '';
		if( empty( $instance['Keys'] ) ) 
			$instance['Keys'] = '';
		if( empty( $instance['Clicks'] ) )
			$instance['Clicks'] = '';
		if( empty( $instance['Download'] ) )
			$instance['Download'] = '';
		if( empty( $instance['Upload'] ) )
			$instance['Upload'] = '';
		if( empty( $instance['Uptime Short'] ) )
			$instance['Uptime Short'] = '';
		if( empty( $instance['Show Ranks'] ) )
			$instance['Show Ranks'] = '';
		?>
		<label for="<?php echo $this->get_field_id( 'UserId' ); ?>">UserId: </label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'UserId' ); ?>" name="<?php echo $this->get_field_name( 'UserId' );?>" type="text" value="<?php echo esc_attr( $instance['UserId'] );?>" />
		<p>Show the following: </p>
		<?php
		// Widget admin form
		foreach( $instance as $key => $val ) {
			if( $key == 'UserId' ) continue;
			?>
			<input id="<?php echo $this->get_field_id( $key ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="checkbox" <?php if ( $val == 'on') echo "checked=\"checked\""; ?> /> <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $key;?></label> 
			<br />
			<?php
		}
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$new_instance1['UserId']		=  ( ! empty( $new_instance['UserId'] ) ) ? $new_instance['UserId'] : '';
		$new_instance1['Date Joined']	= ( ! empty( $new_instance['Date Joined'] ) ) ? $new_instance['Date Joined'] : '';
		$new_instance1['Pulses']		= ( ! empty( $new_instance['Pulses'] ) ) ? $new_instance['Pulses'] : '';
		$new_instance1['Keys']			= ( ! empty( $new_instance['Keys'] ) ) ? $new_instance['Keys'] : '';
		$new_instance1['Clicks']		= ( ! empty( $new_instance['Clicks'] ) ) ? $new_instance['Clicks'] : '';
		$new_instance1['Download']		= ( ! empty( $new_instance['Download'] ) ) ? $new_instance['Download'] : '';
		$new_instance1['Upload'] 		= ( ! empty( $new_instance['Upload'] ) ) ? $new_instance['Upload'] : '';
		$new_instance1['Uptime Short']	= ( ! empty( $new_instance['Uptime Short'] ) ) ? $new_instance['Uptime Short'] : '';
		$new_instance1['Show Ranks'] 	= ( ! empty( $new_instance['Show Ranks'] ) ) ? $new_instance['Show Ranks'] : '';

		return $new_instance1;
	}
} // Class wpb_widget ends here

// Register and load the widget
function kwp_load_widget() {
	register_widget( 'kwp_widget' );
}
add_action( 'widgets_init', 'kwp_load_widget' );