<?php
/*
Plugin Name: Easy Digital Downloads - Software Specs
Plugin URI: https://isabelcastillo.com/docs/about-edd-software-specs
Description: Add software specs and Software Application Microdata to your downloads when using Easy Digital Downloads plugin.
Version: 1.9.1.alpha2
Author: Isabel Castillo
Author URI: https://isabelcastillo.com
License: GPL2
Text Domain: easy-digital-downloads-software-specs
Domain Path: lang

Copyright 2013 - 2017 Isabel Castillo

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
if ( ! class_exists('EDD_Software_Specs' ) ) {
class EDD_Software_Specs{

	private static $instance = null;
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_filter( 'isa_meta_boxes', array( $this, 'specs_metabox' ) );
		add_action( 'init', array( $this, 'init'), 9999 );
		add_filter( 'edd_add_schema_microdata', array( $this, 'remove_microdata') );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_filter( 'the_content', array( $this, 'featureList_wrap' ), 20 );
		add_action( 'loop_start', array( $this, 'microdata_open' ), 10 );
		add_action( 'loop_end', array( $this, 'microdata_close' ), 10 );
		add_action( 'edd_after_download_content', array( $this, 'specs' ), 30 );
		add_action( 'edd_receipt_files', array( $this, 'receipt' ), 10, 5 );
		add_filter('plugin_row_meta', array( $this, 'rate_link' ), 10, 2);
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'init', array( $this, 'cleanup_old_options' ) );

		if( ! defined( 'EDDSPECS_PLUGIN_DIR' ) ) {
			define( 'EDDSPECS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
	}

	public function enqueue() {
		wp_register_style('edd-software-specs', plugins_url('/edd-software-specs.css', __FILE__));
		if ( is_singular( 'download' ) ) {
				wp_enqueue_style('edd-software-specs');
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'easy-digital-downloads-software-specs', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Add description Microdata to downloads content
	 * 
	 * @since 0.1
	 */
	
	public function featureList_wrap( $content ) {
		global $post;
		$dm = get_post_meta($post->ID, '_smartest_lastupdate', true);

		// add to conditions - only if last updated date is entered
		if ( ($post->post_type == 'download') && is_singular() && is_main_query() && $dm ) {
			$content = '<div itemprop="description">' . $content . '</div>';
		}
		return $content;
	}
	
	public function specs() {
	
		global $post;

		// only show if not surpressed by widget, and if shortcode is not present
		$surpress = '';
		if ( has_shortcode( $post->post_content, 'edd-software-specs') ) {
			$surpress = true;
		}
		if ( empty( $surpress ) && is_active_widget( false, false, 'edd_software_specs_widget', true ) ) {
			$surpress = true;
		}
		if ( ! $surpress ) {
			echo eddspecs_display( true, $post->ID );
		}
	}
	
	/**
	 * adds specs metabox to downloads
	 */
	
	public function specs_metabox( $ic_meta_boxes ) {
		$prefix = '_smartest_';

		$fields = array(
				array(
					'name' => __( 'Date of Last Update', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'lastupdate',
					'type' => 'text_date_timestamp',
				),
				array(
					'name' => __( 'Current Version', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'currentversion',
					'desc' => __( 'If EDD Software Licensing or EDD Changelog plugin is enabled for this download, its version will take precedence in that order, and this field will be ignored.', 'easy-digital-downloads-software-specs' ),
					'type' => 'text_small',
				),

				array(
					'name' => __( 'Software Application Type', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'apptype',
					'desc' => __( 'Text to display (also used for microdata). For example, WordPress plugin, or Game', 'easy-digital-downloads-software-specs' ),
					'type'    => 'text',
				),
				array(
					'name' => __( 'File type', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'filetype',
					'desc' => __( 'For example, .zip, or .eps', 'easy-digital-downloads-software-specs' ),
					'type'    => 'text',
				),
				array(
					'name' => __( 'File Size', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'filesize',
					'type' => 'text_small',
				),
				array(
					'name' => __( 'Requirements', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'requirements',
					'desc' => __( 'For example, WordPress 3.3.1+, or a certain required plugin. Separate requirements with commas.', 'easy-digital-downloads-software-specs' ),
					'type' => 'text',
				),
				array(
					'name' => __( 'Price Currency', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'pricecurrency',
					'desc' => sprintf(__( 'The type of currency that the price refers to. Use 3-letter %1$s. For US Dollar, use USD.', 'easy-digital-downloads-software-specs' ), 
											'<a href="https://en.wikipedia.org/wiki/ISO_4217#Active_codes" title="ISO 4217 currency codes" target="_blank">ISO 4217 format</a>.'
									),
					'type' => 'text_small',
					
				),
		);

		$ic_meta_boxes[] = array(
			'id'         => 'download_specs_meta_box',
			'title'      => __( 'Specs', 'easy-digital-downloads-software-specs' ),
			'pages'      => array( 'download'), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,
			'fields'     => apply_filters( 'eddss_specs_fields', $fields, $prefix )
		);

	return $ic_meta_boxes;
	} // end specs_metabox

	public function init() {
		if ( ! class_exists( 'isabelc_Meta_Box' ) ) 
			require_once EDDSPECS_PLUGIN_DIR . 'lib/metabox/init.php';
	}


	/**
	 * remove EDD's itemtype product
	 * @param bool $ret the default return value
	 * @since 1.4
	 */

	public function remove_microdata( $ret ) {
		global $post;

		if ( ! is_object( $post ) ) {
			return $ret;
		}

		if ( get_post_meta($post->ID, '_smartest_lastupdate', true) ) {
			return false;				
		} else {
			return $ret;
		}

	}

	/**
	 * Add version to each download on edd_receipt.
	 *
	 * @since 1.5
	 */

	function receipt( $filekey, $file, $item_ID, $payment_ID, $meta ) {

		
		// If EDD Software Licensing plugin or EDD Changelog is present, don't add Software Specs version to receipt.
		$eddchangelog_version = get_post_meta( $item_ID, '_edd_sl_version', TRUE );

		if ( empty( $eddchangelog_version ) ) {
			$eddsspecs_ver = get_post_meta( $item_ID, '_smartest_currentversion', true );
			if ( ! empty( $eddsspecs_ver ) )
					printf( '<li id="sspecs_download_version" style="text-indent:48px;"> - %1$s %2$s</li>',
						__( 'Current Version:', 'easy-digital-downloads-software-specs' ),
						esc_html( $eddsspecs_ver )
			);		

		}
	}

	/** 
	 * Registers the EDD Related Downloads Widget.
	 * @since 1.5.7
	 */
	public function register_widgets() {
		register_widget( 'edd_software_specs_widget' );
	}

	// rate link on manage plugin page, since 1.4
	public function rate_link($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$rate_link = '<a href="https://wordpress.org/support/view/plugin-reviews/easy-digital-downloads-software-specs">' . __('Rate It', 'easy-digital-downloads-software-specs') . '</a>';
			$links[] = $rate_link;
		}
		return $links;
	}
	/** 
	 * Shortcode to insert specs widget anywhere
	 * @since 1.5.9
	 */
	public function edd_software_specs_shortcode($atts) {
		extract( shortcode_atts( 
			array(	'title' => __( 'Specs', 'easy-digital-downloads-software-specs' ),
					'isodate' => false,
			), 
			$atts
		));
		
		$atts['title'] = empty($atts['title']) ? __( 'Specs', 'easy-digital-downloads-software-specs' ) : $atts['title'];
		ob_start();
		the_widget( 'edd_software_specs_widget', $atts ); 
		$output = ob_get_clean();
		return $output;

	}

	/**
	* Add SoftwareApplication Microdata to single downloads
	*
	* @since 1.8
	* @return void
	*/
	public function microdata_open() {
		global $post;
		static $microdata_open = NULL;
		if( true === $microdata_open || ! is_object( $post ) ) {
			return;
		}
		if ( $post && $post->post_type == 'download' && is_singular( 'download' ) && is_main_query() ) {
			// only add microdata if last updated date is entered
			if( get_post_meta($post->ID, '_smartest_lastupdate', true) ) {
				$microdata_open = true;
				echo '<span itemscope itemtype="http://schema.org/SoftwareApplication">';
			}
		}
	}
	/**
	* Close the SoftwareApplication Microdata wrapper on single downloads
	*
	* @since 1.8
	* @return void
	*/
	public function microdata_close() {
		global $post;
		static $microdata_close = NULL;
		if( true === $microdata_close || ! is_object( $post ) ) {
			return;
		}
		if ( $post && $post->post_type == 'download' && is_singular( 'download' ) && is_main_query() ) {
			// only add microdata if last updated date is entered
			if( get_post_meta($post->ID, '_smartest_lastupdate', true) ) {
				$microdata_close = true;
				echo '</span>';
			}
		}
	}
	/**
	 * For cleanup, remove old option.
	 * @since 1.9
	 */
	public function cleanup_old_options() {
		// Run this cleanup only once
		// @todo remove this block in version 2.0, and del eddspecs_cleanup_one on uninstall
		if ( get_option( 'eddspecs_cleanup_one' ) != 'completed' ) {
			delete_option( 'remove_specs_content_filter' );
			update_option( 'eddspecs_cleanup_one', 'completed' );
		}
	}
}
}
$EDD_Software_Specs = EDD_Software_Specs::get_instance();
add_shortcode( 'edd-software-specs', array( $EDD_Software_Specs, 'edd_software_specs_shortcode' ) );
require_once EDDSPECS_PLUGIN_DIR . 'widget-specs.php';
require_once EDDSPECS_PLUGIN_DIR . 'display.php';
