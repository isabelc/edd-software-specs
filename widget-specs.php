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
			__('EDD Software Specs', 'edd-specs'),
			array( 'description' => __( 'Display the Software Specs table.', 'edd-specs' ), )
		);
	}
	/**
	 * Front-end display of widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters('widget_title', $instance['title']);
		$isodate = isset($instance['isodate']) ? $instance['isodate'] : false;

		echo $before_widget;

		global $post, $EDD_Software_Specs;

		$dm = get_post_meta($post->ID, '_smartest_lastupdate', true);
		$pc = get_post_meta($post->ID, '_smartest_pricecurrency', true);
		$isa_curr = empty($pc) ? 'USD' : $pc;
	
		/* compatible with EDD Changelog plugin. If it's active and its version is entered, use its version instead of ours */
	
		$eddchangelog_version = get_post_meta( $post->ID, '_edd_sl_version', TRUE );

		if ( empty( $eddchangelog_version ) ) {

			// get my own specs version
			$vKey = '_smartest_currentversion';

		} else {
			// get EDD Changelog's version
			$vKey = '_edd_sl_version';
		}
	
		$sVersion = get_post_meta($post->ID, $vKey, true);
		$appt = get_post_meta($post->ID, '_smartest_apptype', true);
		$filt = get_post_meta($post->ID, '_smartest_filetype', true);
		$fils = get_post_meta($post->ID, '_smartest_filesize', true);
		$reqs = get_post_meta($post->ID, '_smartest_requirements', true);
		$pric = $EDD_Software_Specs->smartest_isa_edd_price($post->ID, false); // don't echo

		// only show specs if last updated date is entered
		if($dm) {
		?>
		<link itemprop="SoftwareApplicationCategory" href="http://schema.org/<?php echo get_post_meta($post->ID, '_smartest_software_apptype', true); ?>"/>
			<?php echo '<table id="isa-edd-specs"><caption>';
				if ( ! empty( $title ) ) echo $title;

				echo '</caption><tr>
										<td>'. __( 'Release date:', 'edd-specs' ). '</td>
										<td>
		<meta itemprop="datePublished" content="'. get_post_time('Y-m-d', false, $post->ID). '">';
		if($isodate == true) { echo get_post_time('Y-m-d', false, $post->ID); }
		else { echo get_post_time('F j, Y', false, $post->ID, true); }

								echo '</td>
									</tr>
									<tr>
										<td>'. __( 'Last updated:', 'edd-specs' ). '</td>
		
													<td><meta itemprop="dateModified" content="';
		$moddate = ($dm) ? date('Y-m-d', $dm) : '';
		$moddatenice = ($dm) ? date('F j, Y', $dm) : '';
		echo $moddate . '">';

		if( $isodate == true ) { echo $moddate; }
		else { echo $moddatenice; }

		echo '</td>
								</tr>';
			if($sVersion) {


								echo '<tr>
										<td>' . __( 'Current version:', 'edd-specs' ) . '</td>
										<td itemprop="softwareVersion">' . $sVersion . '</td>
									</tr>';

			}


			if($appt) {
								echo '<tr>
										<td>'. __( 'Software application type:', 'edd-specs' ) .'</td>
		
										<td itemprop="applicationCategory">'. $appt . '</td>
									</tr>';
			}

			if($filt) {			

	
								echo '<tr>
										<td>'. __( 'File format:', 'edd-specs' ). '</td>
										<td itemprop="fileFormat">'. $filt .'</td>
									</tr>';

			}

			if($fils) {			

	
								echo '<tr>
										<td>'. __( 'File size:', 'edd-specs' ) . '</td>
										<td itemprop="fileSize">' . $fils . '</td>
									</tr>';

			}

			if($reqs) {			


									echo '<tr>
										<td>' . __( 'Requirements:', 'edd-specs' ) . '</td>
										<td itemprop="requirements">' . $reqs . '</td>
									</tr>';

			}

			if($pric) {			


									echo '<tr itemprop="offers" itemscope itemtype="http://schema.org/Offer">
										<td>' . __( 'Price:', 'edd-specs' ) . '</td>
										<td><span>'. $pric . ' </span>
										 <span itemprop="priceCurrency">' . $isa_curr . '</span>			</td></tr>';

			}
	
			do_action( 'eddss_add_specs_table_row' );
				echo '</table>';

		} // end if($dm)	


// END CONTENT--------------------------------------------------------------------------------



		echo $after_widget;

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['remove_specs_content_filter'] = $new_instance['remove_specs_content_filter'];
		$instance['isodate'] = $new_instance['isodate'];		
		update_option('remove_specs_content_filter', $instance['remove_specs_content_filter']);

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$defaults = array( 
					'title' => 'Specs',
					'isodate' => 'on',
					'remove_specs_content_filter' => 'on',
					);
 		$instance = wp_parse_args( (array) $instance, $defaults );
    	?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'edd-specs' ); ?></label><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" /></p>


	<p><input id="<?php echo $this->get_field_id( 'isodate' ); ?>" name="<?php echo $this->get_field_name( 'isodate' ); ?>" type="checkbox" class="checkbox" <?php checked( $instance['isodate'], 'on' ); ?> /><label for="<?php echo $this->get_field_id( 'isodate' ); ?>"><?php _e( ' Use ISO 8601 date format (YYYY-MM-DD) instead of nice date. Useful if less space is available in sidebar.', 'edd-specs' ); ?></label></p>


	<p><input id="<?php echo $this->get_field_id( 'remove_specs_content_filter' ); ?>" name="<?php echo $this->get_field_name( 'remove_specs_content_filter' ); ?>" type="checkbox" class="checkbox" <?php checked( $instance['remove_specs_content_filter'], 'on' ); ?> /><label for="<?php echo $this->get_field_id( 'remove_specs_content_filter' ); ?>"><?php _e( ' Remove Specs from below content, since I will use this widget instead.', 'edd-specs' ); ?></label></p>
		<?php 
	}

}
?>