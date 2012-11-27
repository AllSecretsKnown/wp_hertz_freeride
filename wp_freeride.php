<?php

/*
Plugin Name: WP Hertz Freeride
Description: Get all available Freerides from Hertz
Version: 1.0
Author: Peter Persson
Author URI: https://github.com/AllSecretsKnown/wp_hertz_freeride
*/
define( 'WP_TRAVEL_VERSION', '1.0' );

include_once dirname( __FILE__ ) . '/includes/ExtFreeriderAPI/FreeriderAPI.php';

class WpHertzFreerideWidget extends WP_Widget {

	/*
	 * Function to register all info about our widget into the wp_widget
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'WpHertzFreerideWidget',
			'description' => 'Get available freerides from Hertz'
		);
		$this->WP_Widget( 'WpHertzFreerideWidget', 'A Freeride Display', $widget_ops );
	}

	/*
	 * Function to display the widget on Admin page
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'  => '',
			'city'   => '',
			'option' => 'depart'
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$station = $instance['city'];
		$title   = $instance['title'];
		$option  = $instance['option'];
		?>

  <p>Title:
      <input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
  </p>
  <p>station:
      <input type="text" class="widefat" name="<?php echo $this->get_field_name( 'city' ); ?>" value="<?php echo esc_attr( $station ); ?>" />
  </p>

  <p>Sök på:<br>
      <select name="<?php echo $this->get_field_name( 'option' ); ?>">
          <option value="coming" <?php selected( $option, 'depart' ); ?> >Avfärdspunkt, Hämtar alla resor med angiven avfärdspunkt</option>
          <option value="going" <?php selected( $option, 'dest' ); ?> >Destination, Hämtar alla resor med angiven destination</option>
      </select>
  </p>

	<?php
	}

	/*
	 * Function to validate the submited content
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = wp_filter_nohtml_kses( $new_instance['title'] );
		$instance['city']  = wp_filter_nohtml_kses( trim( $new_instance['city'] ) );

		//Make sure opt is one of two white listed options
		if ( $instance['option'] === 'depart' OR $instance['option'] === 'dest' ) {
			$instance['option'] = $new_instance['option'];
		} else {
			$instance['option'] = 'depart';
		}

		return $instance;
	}

	/*
	 * Function to display the widget on the actual page
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		try {
			$freerider_api = new freeriderAPI();

			if ( $instance['option'] == 'depart' ) {
				$rides = $freerider_api->getOrigin( $instance['city'] );
			} else {
				$rides = $freerider_api->getDestination( $instance['city'] );
			}
		} catch ( Exception $e ) {
			exit();
		}


		echo $before_widget;
		echo '<section>';

		//This will echo widget title
		if ( isset( $instance['title'] ) && $instance['title'] != "" ) {
			echo $before_title . $instance['title'] . $after_title;
		}

		if ( isset( $rides ) && ! empty( $rides ) ) {
			?>
    <div class="">
        <h2 class="widget_title">Stad: <?php echo $instance['city']; ?></h2>
    </div>

		<?php
			foreach ( $rides as $ride ) {
				echo '<div class="fr_div">';
				echo '<p>Från: ' . $ride->origin . '</p>';
				echo '<p>Till: ' . $ride->destination . '</p>';
				echo '<p>Start: ' . $ride->startDate . '</p>';
				echo '<p>Slut: ' . $ride->endDate . '</p>';
				echo '<p>Bil: ' . $ride->carModel . '</p>';
				echo '</div>';
			}
		} else {
			echo '<div class="alert alert-error">Hittade inga resor, försök igen</div>';
		}

		echo '</section>';
		echo '<div class="clearfix"></div>';
		echo $after_widget;
	}
}

//Use widgets Init to register our widget
add_action( 'widgets_init', 'wp_freeride_widget_register' );

//Register the widget
function wp_freeride_widget_register() {
	register_widget( 'WpHertzFreerideWidget' );
}