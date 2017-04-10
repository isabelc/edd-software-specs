<?php
/*
Plugin Name: Easy Digital Downloads - Specs
Plugin URI: https://isabelcastillo.com/docs/about-edd-software-specs
Description: Add specs to show extra details about your product when using Easy Digital Downloads.
Version: 2.1.alpha.1
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
		add_action( 'init', array( $this, 'metabox_fields') );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'edd_after_download_content', array( $this, 'specs' ), 30 );
		add_action( 'edd_receipt_files', array( $this, 'receipt' ), 10, 5 );
		add_filter('plugin_row_meta', array( $this, 'rate_link' ), 10, 2);
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'init', array( $this, 'cleanup_old_options' ) );

		if( ! defined( 'EDDSPECS_PLUGIN_DIR' ) ) {
			define( 'EDDSPECS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'EDDSPECS_PLUGIN_URL' ) ) {
			define( 'EDDSPECS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
	}

	public function enqueue() {
		wp_register_style( 'edd-specs', EDDSPECS_PLUGIN_URL . 'assets/edd-specs.css' );
		if ( is_singular( 'download' ) ) {
				wp_enqueue_style( 'edd-specs' );
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'easy-digital-downloads-software-specs', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
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
			echo eddspecs_display( $post->ID );
		}
	}
	
	/**
	 * Add the specs metabox fields
	 * @since 2.0
	 */
	public function metabox_fields() {
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
					'name' => __( 'Product Type', 'easy-digital-downloads-software-specs' ),
					'id'   => $prefix . 'apptype',
					'desc' => __( 'For example, WordPress plugin, or Game', 'easy-digital-downloads-software-specs' ),
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
					
				)
		);
		
		$box = new EDDSPECS_Metabox( array(
			'fields' => apply_filters( 'eddss_specs_fields', $fields, $prefix )
		) );

	}

	/**
	 * Add version to each download on edd_receipt.
	 *
	 * @since 1.5
	 */
	public function receipt( $filekey, $file, $item_ID, $payment_ID, $meta ) {
		// If EDD Software Licensing plugin or EDD Changelog is present, don't add our version to receipt.
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
		if ( $file == plugin_basename( __FILE__ ) ) {
			$rate_link = '<a href="https://wordpress.org/support/view/plugin-reviews/easy-digital-downloads-software-specs">' . __('Rate It', 'easy-digital-downloads-software-specs') . '</a>';
			$links[] = $rate_link;
		}
		return $links;
	}
	/** 
	 * Shortcode to insert specs widget anywhere
	 * @since 1.5.9
	 */
	public function edd_specs_shortcode($atts) {
		extract( shortcode_atts( 
			array(	'title' => __( 'Specs', 'easy-digital-downloads-software-specs' ),
					'isodate' => false,
			), 
			$atts
		));
		
		$atts['title'] = empty( $atts['title'] ) ? __( 'Specs', 'easy-digital-downloads-software-specs' ) : $atts['title'];
		ob_start();
		the_widget( 'edd_software_specs_widget', $atts ); 
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * For cleanup, remove old option.
	 * @since 1.9
	 * @todo at some future point, remove this and delete eddspecs_cleanup_one option on uninstall
	 */
	public function cleanup_old_options() {
		// Run this cleanup only once
		if ( get_option( 'eddspecs_cleanup_one' ) != 'completed' ) {
			delete_option( 'remove_specs_content_filter' );
			update_option( 'eddspecs_cleanup_one', 'completed' );
		}
	}
}
}
$EDD_Software_Specs = EDD_Software_Specs::get_instance();
add_shortcode( 'edd-software-specs', array( $EDD_Software_Specs, 'edd_specs_shortcode' ) );
require_once EDDSPECS_PLUGIN_DIR . 'widget-specs.php';
require_once EDDSPECS_PLUGIN_DIR . 'display.php';
require_once EDDSPECS_PLUGIN_DIR . 'class-eddspecs-metabox.php';
