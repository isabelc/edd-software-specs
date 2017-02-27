<?php
/**
* The HTML to display the Specs.
*/
function eddspecs_display( $post_id = '', $title = '', $isodate = '' ) {
	$out = '';
	// only show if modified date is entered
	$mod_date = get_post_meta( $post_id, '_smartest_lastupdate', true );

	if ( ! $mod_date ) {
		return;
	}

	$post_date = get_post_time( 'F j, Y', false, $post_id, true );
	$modified_date = ( $mod_date ) ? date( 'F j, Y', $mod_date ) : '';

	if ( true == $isodate ) {
		$post_date = get_post_time( 'Y-m-d', false, $post_id) ;
		$modified_date = $mod_date ? date('Y-m-d', $mod_date ) : '';
	}
	
	/* compatibility with EDD Software Licensing plugin. If it's active and its version is entered, use its version instead of ours */
	
	$eddchangelog_version = get_post_meta( $post_id, '_edd_sl_version', true );
	$version_key = empty( $eddchangelog_version ) ? '_smartest_currentversion' : '_edd_sl_version';

	$version = get_post_meta( $post_id, $version_key, true );
	$type = get_post_meta( $post_id, '_smartest_apptype', true );
	$file_format = get_post_meta( $post_id, '_smartest_filetype', true );
	$filesize = get_post_meta( $post_id, '_smartest_filesize', true );
	$reqs = get_post_meta( $post_id, '_smartest_requirements', true );
	$currency = get_post_meta( $post_id, '_smartest_pricecurrency', true );
	$price = edd_has_variable_prices( $post_id ) ? edd_price_range( $post_id ) : edd_price( $post_id, false );

	$out .= '<table id="isa-edd-specs"><caption>';
	$out .= $title ? $title : __( 'Specs', 'easy-digital-downloads-software-specs' );
	$out .= '</caption><tr>
			<td>'. __( 'Release date:', 'easy-digital-downloads-software-specs' ) .
			'</td><td>' .
			esc_html( $post_date ) . '</td></tr><tr><td>' .
			__( 'Last updated:', 'easy-digital-downloads-software-specs' ) .
			'</td><td>' . esc_html( $modified_date ) . '</td></tr>';

	if ( $version ) {
		$out .= '<tr><td>' .
				__( 'Current version:', 'easy-digital-downloads-software-specs' ) .
				'</td><td>' . esc_html( $version ) . '</td></tr>';
	}

	if ( $type ) {
		$out .= '<tr><td>'.
				__( 'Product type:', 'easy-digital-downloads-software-specs' ) .
				'</td><td>'. esc_html( $type ) . '</td></tr>';
	}

	if ( $file_format ) {
		$out .= '<tr><td>' .
				__( 'File format:', 'easy-digital-downloads-software-specs' ) .
				'</td><td>'. esc_html( $file_format ) .'</td></tr>';
	}

	if ( $filesize ) {
		$out .= '<tr><td>'. __( 'File size:', 'easy-digital-downloads-software-specs' ) .
				'</td><td>' . esc_html( $filesize ) . '</td></tr>';
	}

	if ( $reqs ) {			
		$out .= '<tr><td>' . __( 'Requirements:', 'easy-digital-downloads-software-specs' ) .
				'</td><td>' . esc_html( $reqs ) . '</td></tr>';
	}

	if ( $price && $currency ) {
		$out .= '<tr><td>' .
				__( 'Price:', 'easy-digital-downloads-software-specs' ) .
				'</td><td><span>'. wp_kses_post( $price ) . ' </span><span>' .
				esc_html( $currency ) . '</span>	</td></tr>';
	}

	// Add custom rows, if any

	$custom_fields = get_option('eddss_custom_fields');
	if ( is_array( $custom_fields ) && isset( $custom_fields[0] ) ) {
		foreach ( $custom_fields as $field ) {

			$val = get_post_meta($post_id, '_smartest_' . $field['id'], true);

			if ( $val ) {
				$out .= '<tr><td>' . esc_html( $field['name'] ) . '</td><td>' . esc_html( $val ) . '</td></tr>';
	    	}
		}
	}
	
	$out .= '</table>';

	return $out;
}

/**
 * Add custom fields to EDD Specs metabox in backend
*/
function eddss_custom_specs_fields( $fields, $prefix ) {

	// do we have any custom fields? 
	$custom_fields = get_option('eddss_custom_fields');

	if ( is_array( $custom_fields ) && isset( $custom_fields[0] ) ) {

		foreach ( $custom_fields as $field ) {

			// add a text field to the Specs meta box
			$fields[] = array(
				'name' => $field['name'],
				'id'   => $prefix . $field['id'],
				'desc' => $field['desc'],
				'type'    => 'text',
			);

		}
	}
	return $fields;
}
add_filter( 'eddss_specs_fields', 'eddss_custom_specs_fields', 10, 2 );
