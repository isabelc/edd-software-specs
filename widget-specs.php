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
			array( 'description' => __( 'Display the Software Specs table.', 'easy-digital-downloads-software-specs' ), )
		);
	}
	/**
	 * Front-end display of widget.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Specs', 'easy-digital-downloads-software-specs' ) : $instance['title'], $instance, $this->id_base );
		$isodate = isset($instance['isodate']) ? $instance['isodate'] : false;
		$download_id = isset($instance['download_id']) ? $instance['download_id'] : false;

		if(! $download_id) {
			global $post;
			$download_id = $post->ID;
		}

		global $EDD_Software_Specs;

		$dm = get_post_meta($download_id, '_smartest_lastupdate', true);
		$pc = get_post_meta($download_id, '_smartest_pricecurrency', true);
		$isa_curr = $pc ? $pc : '';// @new
	
		/* If EDD Software Licensing or EDD Changelog plugin is active and its version is entered, use their version instead of ours */
	
		$eddchangelog_version = get_post_meta( $download_id, '_edd_sl_version', TRUE );

		if ( empty( $eddchangelog_version ) ) {

			// get my own specs version
			$vKey = '_smartest_currentversion';

		} else {
			// get EDD Changelog's version
			$vKey = '_edd_sl_version';
		}
	
		$sVersion = get_post_meta($download_id, $vKey, true);
		$appt = get_post_meta($download_id, '_smartest_apptype', true);
		$filt = get_post_meta($download_id, '_smartest_filetype', true);
		$fils = get_post_meta($download_id, '_smartest_filesize', true);
		$reqs = get_post_meta($download_id, '_smartest_requirements', true);
		$pric = $EDD_Software_Specs->smartest_isa_edd_price($download_id, false); // don't echo

		wp_enqueue_style('edd-software-specs');
		echo $args['before_widget'];

		// only show specs if last updated date is entered
		if($dm) {
		?>
		<link itemprop="SoftwareApplicationCategory" href="http://schema.org/<?php echo get_post_meta($download_id, '_smartest_software_apptype', true); ?>"/>
			<?php echo '<table id="isa-edd-specs"><caption>';
				if ( $title ) {
					echo $title;
				}
				echo '</caption><tr>
					<td>'. __( 'Release date:', 'easy-digital-downloads-software-specs' ). '</td>
					<td>
					<meta itemprop="datePublished" content="'. get_post_time('Y-m-d', false, $download_id). '">';
		if($isodate == true) { echo get_post_time('Y-m-d', false, $download_id); }
		else { echo get_post_time('F j, Y', false, $download_id, true); }

								echo '</td>
									</tr>
									<tr>
										<td>'. __( 'Last updated:', 'easy-digital-downloads-software-specs' ). '</td>
		
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
										<td>' . __( 'Current version:', 'easy-digital-downloads-software-specs' ) . '</td>
										<td itemprop="softwareVersion">' . $sVersion . '</td>
									</tr>';

			}


			if($appt) {
								echo '<tr>
										<td>'. __( 'Software application type:', 'easy-digital-downloads-software-specs' ) .'</td>
		
										<td itemprop="applicationCategory">'. $appt . '</td>
									</tr>';
			}

			if($filt) {			

	
								echo '<tr>
										<td>'. __( 'File format:', 'easy-digital-downloads-software-specs' ). '</td>
										<td itemprop="fileFormat">'. $filt .'</td>
									</tr>';

			}

			if($fils) {			

	
								echo '<tr>
										<td>'. __( 'File size:', 'easy-digital-downloads-software-specs' ) . '</td>
										<td itemprop="fileSize">' . $fils . '</td>
									</tr>';

			}

			if($reqs) {			


									echo '<tr>
										<td>' . __( 'Requirements:', 'easy-digital-downloads-software-specs' ) . '</td>
										<td itemprop="requirements">' . $reqs . '</td>
									</tr>';

			}

			if($pric && $isa_curr) {
					echo '<tr itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<td>' . __( 'Price:', 'easy-digital-downloads-software-specs' ) . '</td>
						<td><span>'. $pric . ' </span>
					<span itemprop="priceCurrency">' . $isa_curr . '</span>			</td></tr>';
			}
	
			do_action( 'eddss_add_specs_table_row' );
			echo '</table>';

		} // end if($dm)	
		echo $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
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
	 */
	public function form( $instance ) {
		$defaults = array( 
					'title' => __('Specs','easy-digital-downloads-software-specs'),
					'isodate' => 'on',
					'remove_specs_content_filter' => 'on',
					);
 		$instance = wp_parse_args( (array) $instance, $defaults );
    	?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'easy-digital-downloads-software-specs' ); ?></label><input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" /></p>
		<p><input id="<?php echo $this->get_field_id( 'isodate' ); ?>" name="<?php echo $this->get_field_name( 'isodate' ); ?>" type="checkbox" class="checkbox" <?php checked( $instance['isodate'], 'on' ); ?> /><label for="<?php echo $this->get_field_id( 'isodate' ); ?>"><?php _e( ' Use ISO 8601 date format (YYYY-MM-DD) instead of nice date. Useful if less space is available in sidebar.', 'easy-digital-downloads-software-specs' ); ?></label></p>
		<p><input id="<?php echo $this->get_field_id( 'remove_specs_content_filter' ); ?>" name="<?php echo $this->get_field_name( 'remove_specs_content_filter' ); ?>" type="checkbox" class="checkbox" <?php checked( $instance['remove_specs_content_filter'], 'on' ); ?> /><label for="<?php echo $this->get_field_id( 'remove_specs_content_filter' ); ?>"><?php _e( ' Remove Specs from below content, since I will use this widget instead.', 'easy-digital-downloads-software-specs' ); ?></label></p>
		<?php 
	}
}
?>