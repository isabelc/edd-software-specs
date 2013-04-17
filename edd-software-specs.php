<?php
/*
Plugin Name: Easy Digital Downloads - Software Specs
Plugin URI: http://isabelcastillo.com/easy-digital-downloads-software-specs/
Description: Add software specs and Software Application Microdata to your downloads when using Easy Digital Downloads plugin.
Version: 1.2
Author: Isabel Castillo
Author URI: http://isabelcastillo.com
License: GPL2
Text Domain: edd-specs
Domain Path: lang

Copyright 2013 Isabel Castillo

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(!class_exists('EDD_Software_Specs')) {
class EDD_Software_Specs{
    public function __construct() {

		add_filter( 'isa_meta_boxes', array( $this, 'specs_metabox' ) );
		add_action( 'init', array( $this, 'init_specs_metabox'), 9999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	    add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		/* remove EDD's itemtype product, will do SoftwareApplication instead, up at the body element */
		remove_filter( 'the_content', 'edd_microdata_wrapper', 10 );
		add_filter( 'the_content', array( $this, 'featureList_wrap' ), 20 );
		add_filter( 'body_class', array( $this, 'softwareapp_body_class' ) );
		add_action( 'edd_after_download_content', array( $this, 'specs' ), 30 );// EDD-Related is 50
		/* add Current version field to download_history */
		add_action( 'edd_download_history_header_end', array( $this, 'download_history_header' ) );
		add_action( 'edd_download_history_row_end', array( $this, 'download_history_row' ), 10, 2 );// @requires EDD v1.3+
   }

   	public function enqueue() {
			
		if ( is_singular( 'download' ) ) {
	            wp_register_style('edd-software-specs', plugins_url('/edd-software-specs.css', __FILE__));
	            wp_enqueue_style('edd-software-specs');
		}
	}

	public function load_textdomain() {

		load_plugin_textdomain( 'edd-specs', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	}

	/**
	 * Add description Microdata to downloads content
	 * 
	 * @since 0.1
	 */
	
	public function featureList_wrap( $content ) {
		global $post;
		if ( $post->post_type == 'download' && is_singular() && is_main_query() ) {
			$content = '<div itemprop="description">' . $content . '</div>';
		}
		return $content;
	}
	
	/**
	 * Add SoftwareApplication Microdata to single downloads
	 * 
	 * @since 0.1
	 */
	
	public function softwareapp_body_class( $classes ) {
			$backtrace = debug_backtrace();
			if ( $backtrace[4]['function'] === 'body_class' )
				echo ' itemscope itemtype="http://schema.org/SoftwareApplication" ';
			return $classes;
	}
	
	
	/**
	 * Basically same as edd_price, but has itemprop="price" on it
	 * Price
	 *
	 * Displays a formatted price for a download.
	 *
	 * @access      public
	 * @since       1.0
	 * @param       int $download_id The ID of the download price to show
	 * @param		bool $echo Whether to echo or return the results
	 * @return      void
	 */
	public function smartest_isa_edd_price( $download_id, $echo = true ) {
		if ( edd_has_variable_prices( $download_id ) ) {
			$prices = edd_get_variable_prices( $download_id );
			// Return the lowest price
			$price_float = 0;
	        foreach ($prices as $key => $value)
	            if ( ( ( (float)$prices[ $key ]['amount'] ) < $price_float ) or ( $price_float == 0 ) )
	                $price_float = (float)$prices[ $key ]['amount'];
	            $price = edd_sanitize_amount( $price_float );
		} else {
			$price = edd_get_download_price( $download_id );// @isa try use this for diaplay my price
		}
		$price = apply_filters( 'edd_download_price', $price, $download_id );
		$price = '<span class="edd_price" id="edd_price_' . $download_id . '" itemprop="price">' . $price . '</span>';
		if ( $echo )
			echo $price;
		else
			return $price;
	}
	
	public function specs() {
	
		global $post;
		$dm = get_post_meta($post->ID, '_smartest_lastupdate', true);
		$pc = get_post_meta($post->ID, '_smartest_pricecurrency', true);
		$isa_curr = empty($pc) ? 'USD' : $pc;

		/* compatible with EDDV, if it's active, use its version instead of ours */

		if ( ! defined( 'TB_EDDV_PLUGIN_FILE' ) ) {
			// get my own specs version
			$vKey = '_smartest_currentversion';
		} else { 
			// get eddv meta version
			$vKey = '_edd_sl_version';
		}

		$sVersion = get_post_meta($post->ID, $vKey, true);

		// 1st close featurList element and open new div to pair up with closing div inserted by featureList_wrap()
		echo '</div><div>'; ?>
			<link itemprop="SoftwareApplicationCategory" href="http://schema.org/<?php echo get_post_meta($post->ID, '_smartest_software_apptype', true); ?>"/>
		<?php echo '<table id="isa-edd-specs"><caption>'. __( 'Specs', 'edd-specs' ). '</caption>
								<tr>
									<td>'. __( 'Release date:', 'edd-specs' ). '</td>
									<td>
	<meta itemprop="datePublished" content="'. get_post_time('Y-m-d', false, $post->ID). '">
							'. get_post_time('F j, Y', false, $post->ID, true). '</td>
								</tr>
								<tr>
									<td>'. __( 'Last updated:', 'edd-specs' ). '</td>
	
												<td><meta itemprop="dateModified" content="';// @ test begin this line

			$moddate = ($dm) ? date('Y-m-d', $dm) : '';
echo $moddate . '">' . $moddate . '</td>
							</tr>
								<tr>
									<td>' . __( 'Current version:', 'edd-specs' ) . '</td>
									<td itemprop="softwareVersion">' . $sVersion . '</td>
								</tr>
								<tr>
									<td>'. __( 'Software application type:', 'edd-specs' ) .'</td>
	
									<td itemprop="applicationCategory">'. get_post_meta($post->ID, '_smartest_apptype', true) . '</td>
								</tr>

							<tr>
									<td>'. __( 'File format:', 'edd-specs' ). '</td>
									<td itemprop="fileFormat">'. get_post_meta($post->ID, '_smartest_filetype', true) .'</td>
								</tr>
								<tr>
									<td>'. __( 'File size:', 'edd-specs' ) . '</td>
									<td itemprop="fileSize">' . get_post_meta($post->ID, '_smartest_filesize', true) . '</td>
								</tr>
								<tr>
									<td>' . __( 'Requirements:', 'edd-specs' ) . '</td>
									<td itemprop="requirements">' . get_post_meta($post->ID, '_smartest_requirements', true) . '</td>
								</tr>
								<tr itemprop="offers" itemscope itemtype="http://schema.org/Offer">
									<td>' . __( 'Price:', 'edd-specs' ) . '</td>
									<td><span>'. 
									$this->smartest_isa_edd_price($post->ID, false) . // don't echo
									' </span>
									 <span itemprop="priceCurrency">' . $isa_curr . '</span>			</td></tr>
			</table>';
	
	
	} // end function easy-digital-downloads-specs
	
	
	/**
	 * adds specs metabox to downloads
	 */
	
	public function specs_metabox( $ic_meta_boxes ) {
		$prefix = '_smartest_';
	$ic_meta_boxes[] = array(
			'id'         => 'download_specs_meta_box',
			'title'      => __( 'Specs', 'edd-specs' ),
			'pages'      => array( 'download'), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'Date of Last Update', 'edd-specs' ),
					'id'   => $prefix . 'lastupdate',
					'type' => 'text_date_timestamp',
				),
				array(
					'name' => __( 'Current Version', 'edd-specs' ),
					'id'   => $prefix . 'currentversion',
					'desc' => __( 'If EDD Versions plugin is active, it will take precedence, and this field will be ignored.', 'edd-specs' ),
					'type' => 'text_small',
				),

				array(
					'name' => __( 'Software Application Type - For Display', 'edd-specs' ),
					'id'   => $prefix . 'apptype',
					'desc' => __( 'Text to display. For example, WordPress plugin, or Game', 'edd-specs' ),
					'type'    => 'text',
				),
				array(
					'name' => __( 'Software Application Type - For Microdata', 'edd-specs' ),
					'id'   => $prefix . 'software_apptype',
					'desc' => __( 'Select the Microdata type that fits your product best.', 'edd-specs' ),
					'type'    => 'select',
					'options' => array(
						array( 'name' => __( 'OtherApplication (if application doesn\'t map to any of the categories listed)', 'edd-specs' ), 'value' => 'OtherApplication', ),
						array( 'name' => __( 'BrowserApplication (web browser, RSS reader, browser add-on/plug-in)', 'edd-specs' ), 'value' => 'BrowserApplication', ),
						array( 'name' => __( 'BusinessApplication (office suites, sales and marketing apps, project management apps)', 'edd-specs' ), 'value' => 'BusinessApplication', ),
						array( 'name' => __( 'CommunicationApplication (email , VOIP application)', 'edd-specs' ), 'value' => 'CommunicationApplication', ),
	
						array( 'name' => __( 'DesignApplication (graphic design, pro audio/video, modeling, CAD/CAM)', 'edd-specs' ), 'value' => 'DesignApplication', ),
						array( 'name' => __( 'DesktopEnhancementApplication', 'edd-specs' ), 'value' => 'DesktopEnhancementApplication', ),
						array( 'name' => __( 'DeveloperApplication (compilers, debuggers)', 'edd-specs' ), 'value' => 'DeveloperApplication', ),
	
	
						array( 'name' => __( 'DriverApplication (OS drivers)', 'edd-specs' ), 'value' => 'DriverApplication', ),
						array( 'name' => __( 'EntertainmentApplication (music, sports, TV)', 'edd-specs' ), 'value' => 'EntertainmentApplication', ),
						array( 'name' => __( 'EducationalApplication', 'edd-specs' ), 'value' => 'EducationalApplication', ),
	
	
						array( 'name' => __( 'FinanceApplication (accounting, finance, tax)', 'edd-specs' ), 'value' => 'FinanceApplication', ),
						array( 'name' => __( 'GameApplication (action, arcades, etc)', 'edd-specs' ), 'value' => 'GameApplication', ),
						array( 'name' => __( 'HealthApplication', 'edd-specs' ), 'value' => 'HealthApplication', ),
	
	
						array( 'name' => __( 'HomeApplication (decoration, landscaping, DIY)', 'edd-specs' ), 'value' => 'HomeApplication', ),
						array( 'name' => __( 'LifestyleApplication (cooking, diary, organizers)', 'edd-specs' ), 'value' => 'LifestyleApplication', ),
						array( 'name' => __( 'MedicalApplication', 'edd-specs' ), 'value' => 'MedicalApplication', ),
	
						array( 'name' => __( 'MultimediaApplication (audio/video player, consumer photo/video editor)', 'edd-specs' ), 'value' => 'MultimediaApplication', ),
						array( 'name' => __( 'NetworkingApplication', 'edd-specs' ), 'value' => 'NetworkingApplication', ),
						array( 'name' => __( 'ReferenceApplication (books, reference)', 'edd-specs' ), 'value' => 'ReferenceApplication', ),
	
	
						array( 'name' => __( 'SecurityApplication (antivirus, firewall, encryption)', 'edd-specs' ), 'value' => 'SecurityApplication', ),
						array( 'name' => __( 'ShoppingApplication', 'edd-specs' ), 'value' => 'ShoppingApplication', ),
						array( 'name' => __( 'SocialNetworkingApplication', 'edd-specs' ), 'value' => 'SocialNetworkingApplication', ),
	
	
						array( 'name' => __( 'SportsApplication', 'edd-specs' ), 'value' => 'SportsApplication', ),
						array( 'name' => __( 'TravelApplication', 'edd-specs' ), 'value' => 'TravelApplication', ),
						array( 'name' => __( 'UtilitiesApplication (system tools, utilities)', 'edd-specs' ), 'value' => 'UtilitiesApplication', ),
	
					),
				),
	
	
				array(
					'name' => __( 'File type', 'edd-specs' ),
					'id'   => $prefix . 'filetype',
					'desc' => __( 'For example, .zip, or .eps', 'edd-specs' ),
					'type'    => 'text',
				),
	
	
				array(
					'name' => __( 'File Size', 'edd-specs' ),
					'id'   => $prefix . 'filesize',
					'type' => 'text_small',
				),
	
				array(
					'name' => __( 'Requirements', 'edd-specs' ),
					'id'   => $prefix . 'requirements',
					'desc' => __( 'For example, WordPress 3.3.1+, or a certain required plugin. Separate requirements with commas.', 'edd-specs' ),
					'type' => 'text',
				),
	
				array(
					'name' => __( 'Price Currency', 'edd-specs' ),
					'id'   => $prefix . 'pricecurrency',
					'desc' => sprintf(__( 'The type of currency that the price refers to. Use 3-letter %1$s.', 'edd-specs' ), 
											'<a href="http://en.wikipedia.org/wiki/ISO_4217" title="ISO 4217 currency codes" target="_blank">ISO 4217 format</a>. Defaults to "USD" if blank.'
									),
					'std'  => 'USD',
					'type' => 'text_small',
					
				),
	
	)
		);


	return $ic_meta_boxes;
	} // end specs_metabox


	public function init_specs_metabox() {
		if ( ! class_exists( 'isabelc_Meta_Box' ) ) 
			require_once plugin_dir_path( __FILE__ ) . 'lib/metabox/init.php';
	}

	
	/**
	 * Show Current Version column header on downloads table of 
	 * [download_history] shortcode. 
	 *
	 * @since 0.2
	 */
	
	public function download_history_header() {

		/* compatible with EDDV plugin, only add our column if EDDV is not active */

		if ( ! defined( 'TB_EDDV_PLUGIN_FILE' ) ) {
	
			echo '<th class="edd_download_download_version">' . __( 'Current Version', 'smartestb' ) . '</th>';
		}
	
	}
	
	
	/**
	 * Show Version number cell on downloads table of 
	 * [download_history] shortcode. 
	 *
	 * @since 0.2
	 */
	 
	public function download_history_row( $puchase_id, $download_id ){

		/* compatible with EDDV plugin, only add our column if EDDV is not active */

		if ( ! defined( 'TB_EDDV_PLUGIN_FILE' ) ) {
			echo '<td class="edd_download_download_version">'.get_post_meta( $download_id, '_smartest_currentversion', true ).'</td>';
		}
	}
	

}
}
$EDD_Software_Specs = new EDD_Software_Specs();