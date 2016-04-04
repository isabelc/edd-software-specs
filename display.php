<?php
/**
* The HTML to display the Specs.
*/
function eddspecs_display( $prepend = '', $post_id = '', $title = '', $isodate = '' ) {

	// only show if modified date is entered
	$dm = get_post_meta($post_id, '_smartest_lastupdate', true);

	if ( ! $dm ) {
		return;
	}

	global $EDD_Software_Specs;

	$post_date = get_post_time('F j, Y', false, $post_id, true);
	$modified_date = ($dm) ? date('F j, Y', $dm) : '';


	$post_date_iso = get_post_time('Y-m-d', false, $post_id);
	$modified_date_iso = ($dm) ? date('Y-m-d', $dm) : '';		

	// @test iso date.

	if($isodate == true) {
		$post_date = $post_date_iso;
		$modified_date = $modified_date_iso;
	}

	$pc = get_post_meta($post_id, '_smartest_pricecurrency', true);
	$isa_curr = $pc ? $pc : '';// @test need? 
	
	/* compat with EDD Software Licensing plugin. If it's active and its version is entered, use its version instead of ours */
	
	$eddchangelog_version = get_post_meta( $post_id, '_edd_sl_version', TRUE );

	if ( empty( $eddchangelog_version ) ) {
		// get my own specs version
		$vKey = '_smartest_currentversion';
	} else {
		// get EDD Software Licensing's version
		$vKey = '_edd_sl_version';
	}
	
	$sVersion = get_post_meta($post_id, $vKey, true);
	$appt = get_post_meta($post_id, '_smartest_apptype', true);
	$filt = get_post_meta($post_id, '_smartest_filetype', true);
	$fils = get_post_meta($post_id, '_smartest_filesize', true);
	$reqs = get_post_meta($post_id, '_smartest_requirements', true);
	$pric = $EDD_Software_Specs->smartest_isa_edd_price($post_id, false);

	$out = '';

	if ( $prepend ) {

		// 1st close the featureList div element and open new div to pair up with closing div inserted by featureList_wrap()

		$out .= '</div><div>';
	}

	$out .= '<table id="isa-edd-specs"><caption>';

	$out .= $title ? $title : __( 'Specs', 'easy-digital-downloads-software-specs' );

	$out .= '</caption><tr>
					<td>'. __( 'Release date:', 'easy-digital-downloads-software-specs' ). '</td>
					<td>
					<meta itemprop="datePublished" content="'. $post_date_iso . '">' . $post_date .

					'</td></tr><tr><td>'. __( 'Last updated:', 'easy-digital-downloads-software-specs' ). '</td><td><meta itemprop="dateModified" content="' . $modified_date_iso . '">' . $modified_date . '</td>
								</tr>';

	if($sVersion) {

			$out .= '<tr>
						<td>' . __( 'Current version:', 'easy-digital-downloads-software-specs' ) . '</td>
										<td itemprop="softwareVersion">' . $sVersion . '</td>
					</tr>';

	}

	if($appt) {
			$out .= '<tr>
						<td>'. __( 'Software application type:', 'easy-digital-downloads-software-specs' ) .'</td>
		
							<td itemprop="applicationCategory">'. $appt . '</td>
						</tr>';
	}

	if($filt) {			
			$out .= '<tr>
										<td>'. __( 'File format:', 'easy-digital-downloads-software-specs' ). '</td>
										<td itemprop="fileFormat">'. $filt .'</td>
									</tr>';

	}

	if($fils) {			
	
		$out .= '<tr>
										<td>'. __( 'File size:', 'easy-digital-downloads-software-specs' ) . '</td>
										<td itemprop="fileSize">' . $fils . '</td>
									</tr>';

	}

	if($reqs) {			

			$out .= '<tr>
										<td>' . __( 'Requirements:', 'easy-digital-downloads-software-specs' ) . '</td>
										<td itemprop="requirements">' . $reqs . '</td>
									</tr>';
	}

	if($pric && $isa_curr) {
		$out .= '<tr itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<td>' . __( 'Price:', 'easy-digital-downloads-software-specs' ) . '</td>
						<td><span>'. $pric . ' </span>
					<span itemprop="priceCurrency">' . $isa_curr . '</span>			</td></tr>';
	}

	do_action( 'eddss_add_specs_table_row' );
	$out .= '</table>';

	return $out;
}
