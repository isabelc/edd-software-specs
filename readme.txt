=== Easy Digital Downloads - Software Specs ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: software, specs, SoftwareApplication, application, microdata, schema, schema.org, easy digital downloads, edd specs
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.8
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add software specs and Software Application microdata to your downloads when using Easy Digital Downloads plugin.

== Description ==

= New Since Version 1.5.9 =

* Use it as a shortcode instead: 

`[edd-software-specs download_id="###"]`


= Description =

This is an extension for [Easy Digital Downloads](http://wordpress.org/plugins/easy-digital-downloads/) that does several things if you enable it for a download: 

* It adds a Specs table below your single download content (option to use it instead as a widget, and/or as a shortcode). The Specs table displays these fields:


  * Release date
  * Last updated date
  * Current version (compatible with EDD Software Licensing plugin)
  * Software application type
  * File format
  * File size
  * Requirements
  * Price
  * Currency code

 You can leave a field blank to omit that row from the table. There are 2 exceptions to this. 
1.  The `Last updated date` field, since leaving that field blank will disable the entire table.
2.  The `Version` field. This plugin is compatible with **EDD Software Licensing plugin** and with **EDD Changelog Plugin**. If EDD Software Licensing plugin is present, and you have enabled it for a download, that version will override this version in the Specs table on the downloads page. In that case, if you leave the Specs version field blank, the Specs table on the site will still show the version from EDD Software Licensing. So, EDD Software Specs plugin gives priority to the version entered in **EDD Software Licensing plugin**, then **EDD Changelog Plugin**, in that order.

 In addition to leaving fields blank, you can add code to add more rows to the table (see FAQs).

* It replaces EDD's default microdata itemptype `Product` with `SoftwareApplication`.

* It moves the microdata itemtype declaration up to the body element so as to nest the `name` property within the itemscope. *

* It adds `offers`, `price`, and `currency` microdata in order to generate Google rich snippets for Software Applications.

* In addition, it adds these microdata properties of `SoftwareApplication`:

`description
datepublished
datemodified
softwareversion
applicationcategory
fileformat
filesize
requirements`


* It adds the "Current Version" of the download to the purchase receipt "Products" list (on EDD's `edd_receipt shortcode` shortcode). This is only if EDD Software Licensing plugin or EDD Changelog plugin is not active.

* It lets you enable the Specs table only for downloads that need it. See FAQ for details.


For more info, [see the FAQ](http://wordpress.org/plugins/easy-digital-downloads-software-specs/faq/), and the [Installation Instructions](http://wordpress.org/plugins/easy-digital-downloads-software-specs/installation/). Or see the [plugin's web page](http://isabelcastillo.com/docs/category/easy-digital-downloads-software-specs-plugin), or [GitHub](https://github.com/isabelc/edd-software-specs).



== Installation ==
1. Download the plugin to your computer.
2. Extract the contents.
3. Upload (via FTP) to a sub folder of the WordPress plugins directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.

**After Activating the Plugin**

Go to the Downloads editor and enter specs for your existing digital products. Then "View Download" to see the specs table.


== Frequently Asked Questions ==

= How do add Specs to the sidebar instead of below the content? =

Use it as a widget instead. Go to **Appearance --> Widgets** to use the widget.


= How do I insert the Specs wherever I want with a shortcode? =

Paste this shortcode inside a post or a page where you want the Specs table to appear.:

`[edd-software-specs download_id="###"]`

in which the ### is the **post ID** of the download item. If you are using EDD's `purchase_link` shortcode for a download on a page, take the same `id` number from that shortcode.


= How To Disable Specs For a Specific Download =

Leave the `Date of Last Update` field empty. If that field is blank, no Specs table will show up for that download, and Microdata will not be altered for that download.


= Why am I not getting rich snippets in Google's Structured Data Testing Tool? =

You have to select a Software Application Type for the download. "OtherApplication" doesn't qualify for rich snippets, unless, outside of this plugin, you've added either "aggregateRating" or "operatingSystems" for the particular download. Go to the download's Specs meta box to select the Software Application Type.


= How do I add a row to the Specs display table? =

Add something like this to your functions:

`
/**
 * Add a custom row to EDD Software Specs table
*/

function my_add_specs_table_row() {

	echo '<tr><td>';
	echo 'YOUR CUSTOM TABLE ROW LABEL';
	echo '</td><td>';
	echo 'YOUR CUSTOM TABLE ROW VALUE';
	echo '</td></tr>';
}

add_action ( 'eddss_add_specs_table_row', 'my_add_specs_table_row');

`

For example, say you added a custom meta field to the downloads, and you want to show this custom meta in the Specs table. Say your custom field is 'my_license'. Then you add this to your functions:

`
/**
 * Add a custom row with a custom meta field to EDD Software Specs table
*/

function my_add_specs_table_row() {

	global $post;

	echo '<tr><td>';
	echo 'License';
	echo '</td><td>';
	echo get_post_meta($post->ID, 'my_license', true);
	echo '</td></tr>';
}

add_action ( 'eddss_add_specs_table_row', 'my_add_specs_table_row');
`

= How can I give back? =

Please [rate the plugin](http://wordpress.org/support/view/plugin-reviews/easy-digital-downloads-software-specs). Thank you.


== Screenshots ==

1. Front-end: Specs table as shown on single download page
2. Back-end: Specs meta box on single download editor
== Changelog ==

= 1.8 =
* Fix - Remove Product microdata with the new filter. 
* Tweak - Add the SoftwareApplication microdata by inserting a span element rather than in the body tag since this method uses less memory.
* Maintenance - Removed 'softwareApplicationCategory' since Google no longer recognizes it.

= 1.7.2 =
* New: Added .pot localization file.

= 1.7.1 =
* Fix: The edd_software_specs_shortcode shortcode was called incorrectly. Thanks to Austin Passy.
* Maintenance - Removed 2 PHP notices. Thanks to Keiser Media.

= 1.7 =
* New - The textdomain has changed to easy-digital-downloads-software-specs. You must update your .mo files accordingly.
* Maintenance - Updated widget to work with the WordPress 4.0 live customizer.
* Maintenance - Use singleton class.

= 1.6.1 =
* Fix: specs content filter should ignore widget setting to surpress specs if the widget is not active
* Maintenance: Updated rate link and plugin URI.
* Maintenance: Tested and passed for WP 3.9 compatibility.

= 1.6 =
* Bug fix: shortcode now works on any page or post for a download.
* Tweak: added CSS for compatibility with TWenty Fourteen theme's thin, dark sidebars.
* Tweak: Updated readme with missing details about compatibility with EDD Software Licensing plugin.

= 1.5.9 =
* Bug fix: USD money format was showing up even when left empty.
* Bug fix: CSS for backend Specs fields is better mobile responsive.
* New: shortcode to insert Specs table into posts and pages.
* Tested for WP 3.8 compatibility.

= 1.5.8 =
* Tested for WP 3.7.1 compatibility.

= 1.5.7 =
* New: added widget.
* Updated FAQ.

= 1.5.6 =
* Tweak: minified CSS.
* Tested for WP 3.6.1 compatibility.

= 1.5.5 =
* Bug fix: metabox class was causing conflict with some plugins, which broke the image uploader for inserting media into posts.

= 1.5.4 =
* Update: compatible with WP 3.6
* Tweak: fixed typo, wrong version number.

= 1.5.3 =
* Tweak: changed Last updated date format to display F j, Y instead of Y-m-d.
* New: dropped compatibility with EDD Versions plugin since download_history shortcode is deprecated since EDD 1.6.
* New: added compatibility with EDD Changelog plugin.
* New: adds Current Version to edd_receipt shortcode.

= 1.4 =
* New: Specs table only shows if enabled for a given download.
* New: blank fields will not show up on table.
* New: microdata type is only altered if Specs table is enabled for a given download.

= 1.3 =
* Minor tweaks to readme. Changed plugin url, added donate link, added rate it link.

= 1.2 = 
* New: added ability to insert custom rows into specs table.
* Fixed minor WP notice that appeared if modified date had not been set yet.


= 0.3: April 15, 2013 = 
* Fixed compatibility issue with the cmb_Meta_Box class

= 0.2: April 9, 2013 =

* Added compatibility with EDD Versions plugin.

= 0.1: April 9, 2013 =

* Initial release.

== Upgrade Notice ==

= 1.8 =
Fix: Removing Product microdata now required use of new EDD filter.

= 1.7.1 =
Fixed the shortcode call which was giving errors. Thanks to Austin Passy.

= 1.7 =
Textdomain has changed to word with WordPress core language packs.

= 1.6 =
Bug fix: Specs table shortcode now works on any page or post. Updated FAQ.

= 1.5.9 =
New: shortcode to insert Specs table into posts.

= 1.5.7 =
New Specs widget so you can use it on the sidebar. Updated FAQ.