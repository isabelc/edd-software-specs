/**
 * Custom jQuery for Datepicker Field
 */
jQuery(document).ready(function ($) {
	'use strict';
	/**
	 * Initialize jQuery UI datepicker (this will be moved inline in a future release)
	 */
	$('.isamb_datepicker').each(function () {
		$('#' + jQuery(this).attr('id')).datepicker();
		// $('#' + jQuery(this).attr('id')).datepicker({ dateFormat: 'yy-mm-dd' });
		// For more options see http://jqueryui.com/demos/datepicker/#option-dateFormat
	});
	// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
	$("#ui-datepicker-div").wrap('<div class="cmb_element" />');

});