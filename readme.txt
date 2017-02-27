=== Easy Digital Downloads - Specs ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: specs, edd, easy digital downloads, edd specs, custom fields
Requires at least: 3.8
Tested up to: 4.8-alpha-40127
Stable tag: 2.0
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add specs to show extra details about your product when using Easy Digital Downloads.

== Description ==

This is an extension for Easy Digital Downloads that adds a Specs table table below your single download content (option to use it instead as a widget, and/or as a shortcode). The Specs table has these fields by default:


  * Release date
  * Last updated date
  * Current version (compatible with EDD Software Licensing plugin)
  * Product type
  * File format
  * File size
  * Requirements
  * Price
  * Currency code


You can leave a field blank to omit that row from the table. There are 2 exceptions to this. 
1.  The `Last updated date` field, since leaving that field blank will disable the entire table.
2.  The `Version` field. This plugin is compatible with **EDD Software Licensing plugin** and with **EDD Changelog Plugin**. If EDD Software Licensing plugin is present, and you have enabled it for a download, that version will override this version in the Specs table on the downloads page. In that case, if you leave the Specs version field blank, the Specs table on the site will still show the version from EDD Software Licensing. So, EDD Specs plugin gives priority to the version entered in **EDD Software Licensing plugin**, then **EDD Changelog Plugin**, in that order.

In addition to leaving fields blank, you can add code to add more rows to the table (see FAQs).

* It adds the "Current Version" of the download to the purchase receipt "Products" list (on EDD's `edd_receipt` shortcode). This is only if EDD Software Licensing plugin or EDD Changelog plugin is not active because those plugins will add their own version.

* It lets you enable the Specs table only for downloads that need it. See FAQ for details.

== Installation ==
1. In your WordPress dashboard, go to "Plugins -> Add New", and search for "Easy Digital Downloads - Specs".
2. Click to install and then Activate the plugin.

**After Activating the Plugin**

When you are adding or editing a Download, you’ll see a box for “Specs.” Enter your specs for the product. Then, you’ll see the specs when you view the download on the front of your site.

== Frequently Asked Questions ==

= How do I add a custom field to the Specs? =

See [How do I add a custom field to the Specs?](https://isabelcastillo.com/docs/about-edd-software-specs#docs-customfield)

= How do I add Specs to the sidebar instead of below the content? =

Use it as a widget instead. Go to **Appearance --> Widgets** to use the widget.

= How do I insert the Specs wherever I want with a shortcode? =

Paste this shortcode inside a post or a page where you want the Specs table to appear.:

`[edd-software-specs download_id="###"]`

in which the ### is the **post ID** of the download item. If you are using EDD's `purchase_link` shortcode for a download on a page, take the same `id` number from that shortcode.


= How To Disable Specs For a Specific Download =

Leave the `Date of Last Update` field empty. If that field is blank, no Specs table will show up for that download.


= Does this plugin have more documentation? =

See the [full documentation](https://isabelcastillo.com/docs/about-edd-software-specs).

= How can I give back? =

Please [rate the plugin](https://wordpress.org/support/view/plugin-reviews/easy-digital-downloads-software-specs). Thank you.


== Screenshots ==

1. Front-end: Specs table as shown on single download page
== Changelog ==

= 2.0 =
* New - Removed SoftwareApplication schema microdata. This plugin will no longer output any structured data. Originally, when this extension was first created, EDD's core Product microdata was broken because it had the "name" property outside of the Product itemscope. So, I added my own desired microdata schema, which was for the `SoftwareApplication` type. At some point since then, EDD has fixed the microdata markup for Products. It is now valid. And, based on user comments and reviews, people are using this extension to display the Specs and not actually for the purpose of replacing the Product schema. So, this plugin has discontinued all microdata in favor of using EDDs core Product microdata.
* New - In light of the change described above, the plugin has been renamed to Easy Digital Downloads - Specs, to remove the "Software" from the plugin name.
* Fix - Variable-priced product will now show correct price range in the Specs table.
* Code refactoring - Some script handles have been renamed. 


	* If you have custom code that is targeting the script with the handle `isamb-scripts`, it must be renamed to `edd-specs`. This was/is only ever loaded on the admin side, on the Edit Download page.
	* If you have custom code that is targeting the style sheet with the handle `isamb-styles`, it must be renamed to `edd-specs-admin`. This was/is only ever loaded on the admin side, on the Edit Download page.
	* If you have custom code that is targeting the style sheet with the handle `edd-software-specs`, it must be renamed to `edd-specs`.
	* The `isabelc_Meta_Box` PHP class is renamed to `EDDSPECS_Metabox`;


* Tweak - The plugin textdomain should be loaded on the init action rather than the plugins_loaded action.
* Tweak - Updated links to plugin URI and plugin documentation.
* Code refactoring - Simplified the metabox class.

= 1.9 =
* BREAKING CHANGE - Removed the eddss_add_specs_table_row hook in favor of an easier way to add custom fields to the specs box. See https://isabelcastillo.com/docs/about-edd-software-specs#docs-customfield
* Fix - Only show the sidebar widget on single downloads.
* Tweak - Synced the duplicate HTML into one external function.

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
