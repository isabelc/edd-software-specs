<?php
/**
 * Adds EDD Software Specs widget
 *
 * @author	Isabel Castillo
 * @package 	EDD Software Specs
 * @extends 	WP_Widget
 */
class edd_software_specs_widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'edd_software_specs_widget',
			__('EDD Software Specs', 'easy-digital-downloads-software-specs'),
			array( 'description' => __( 'Display the Software Specs table.', 'easy-digital-downloads-software-specs' ), 'customize_selective_refresh' => true, )
		);
	}
	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {

		if ( ! is_singular( 'download' ) ) {
			return;
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Specs', 'easy-digital-downloads-software-specs' ) : $instance['title'], $instance, $this->id_base );
		$isodate = isset($instance['isodate']) ? $instance['isodate'] : false;
		$download_id = isset($instance['download_id']) ? $instance['download_id'] : false;

		if(! $download_id) {
			global $post;
			$download_id = $post->ID;
		}

		if ( ! get_post_meta($download_id, '_smartest_lastupdate', true) ) {
			return;
		}
		
		wp_enqueue_style('edd-software-specs');
		echo $args['before_widget'];
		echo eddspecs_display( false, $download_id, $title, $isodate );
		echo $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['isodate'] = isset( $new_instance['isodate'] ) ? $new_instance['isodate'] : false;
		return $instance;
	}

	/**
	 * Back-end widget form.
	 */
	public function form( $instance ) {
		$defaults = array( 
					'title' => __('Specs','easy-digital-downloads-software-specs'),
					'isodate' => 'on'
					);
 		$instance = wp_parse_args( (array) $instance, $defaults );
    	?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'easy-digital-downloads-software-specs' ); ?></label><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" /></p>
		<p><input id="<?php echo $this->get_field_id( 'isodate' ); ?>" name="<?php echo $this->get_field_name( 'isodate' ); ?>" type="checkbox" class="checkbox" <?php checked( $instance['isodate'], 'on' ); ?> /><label for="<?php echo $this->get_field_id( 'isodate' ); ?>"><?php _e( ' Use ISO 8601 date format (YYYY-MM-DD) instead of nice date. Useful if less space is available in sidebar.', 'easy-digital-downloads-software-specs' ); ?></label></p>
		<?php 
	}
}
?>