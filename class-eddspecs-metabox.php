<?php
/**
* Handles the custom metabox and fields for Specs.
* @since 2.0
*/
class EDDSPECS_Metabox {
	protected $_meta_box;

	function __construct( $ic_meta_box ) {
		if ( !is_admin() ) return;

		$this->_meta_box = $ic_meta_box;
	
		add_action( 'admin_menu', array( &$this, 'add' ) );
		add_action( 'save_post', array( &$this, 'save' ) );
	}

	// Add the metabox
	function add() {
		add_meta_box( 'download_specs_meta_box', __( 'Specs', 'easy-digital-downloads-software-specs' ), array(&$this, 'show'), 'download', 'normal', 'high' ) ;
	}
	
	// Show fields
	function show() {
		global $post;

		wp_enqueue_script( 'edd-specs' );
		wp_enqueue_style( 'edd-software-specs-admin' );

		// Use nonce for verification
		echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';
		echo '<table class="form-table cmb_metabox">';

		foreach ( $this->_meta_box['fields'] as $field ) {
			// Set up blank or default values for empty ones
			if ( ! isset( $field['name'] ) ) $field['name'] = '';
			if ( ! isset( $field['desc'] ) ) $field['desc'] = '';
			if ( ! isset( $field['std'] ) ) $field['std'] = '';
				
			$meta = get_post_meta( $post->ID, $field['id'], true );

			$value = '' !== $meta ? $meta : $field['std'];

			echo '<tr>';
			echo '<th style="width:18%"><label for="', $field['id'], '">', $field['name'], '</label></th>';
			echo '<td>';
						
			switch ( $field['type'] ) {
				case 'text':
					echo '<input type="text" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', esc_attr( $value ), '" />','<p class="cmb_metabox_description">', esc_html( $field['desc'] ), '</p>';
					break;
				case 'text_small':
					echo '<input class="cmb_text_small" type="text" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', esc_attr( $value ), '" /><span class="cmb_metabox_description">', esc_html( $field['desc'] ), '</span>';
					break;
				case 'text_date_timestamp':
					echo '<input class="cmb_text_small isamb_datepicker" type="text" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', '' !== $meta ? date( 'm\/d\/Y', $meta ) : $field['std'], '" /><span class="cmb_metabox_description">', esc_html( $field['desc'] ), '</span>';
					break;

			}
			
			echo '</td>','</tr>';
		}
		echo '</table>';
	}

	// Save data from metabox
	function save( $post_id)  {

		// verify nonce
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename(__FILE__) ) ) {
			return $post_id;
		}

		// check autosave
		if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// check permissions
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		foreach ( $this->_meta_box['fields'] as $field ) {
			$name = $field['id'];			
			$old = get_post_meta( $post_id, $name, true );
			$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : null;
			
			if ( $field['type'] == 'text_date_timestamp' ) {
				$new = strtotime( $new );
			}

			$new = apply_filters('cmb_validate_' . $field['type'], $new, $post_id, $field);	
			
			if ( '' !== $new && $new != $old  ) {
				$new = sanitize_text_field( $new );
				update_post_meta( $post_id, $name, $new );
			} elseif ( '' == $new ) {
				delete_post_meta( $post_id, $name );
			}
			
		}
	}
}

/**
 * Register script and style
 */
function isamb_scripts( $hook ) {
	wp_register_script( 'edd-specs', EDDSPECS_PLUGIN_URL . 'assets/edd-specs.js', array( 'jquery-ui-core', 'jquery-ui-datepicker' ) );
		
	wp_register_style( 'edd-software-specs-admin', EDDSPECS_PLUGIN_URL . 'assets/edd-software-specs-admin.css' );
}
add_action( 'admin_enqueue_scripts', 'isamb_scripts' );
