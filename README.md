EDD Software Specs
==================

Add software specs and Software Application microdata to your downloads when using Easy Digital Downloads plugin.

This is an extension for [Easy Digital Downloads](http://wordpress.org/plugins/easy-digital-downloads/) that does several things if you enable it for a download: 

* It adds a Specs table below your single download content. The Specs table displays these:

 * Release date
 * Last updated date
 * Current version
 * Software application type
 * File format
 * File size
 * Requirements
 * Price
 * Currency code

You can leave a field blank to omit that row from the table. (Except the `Last updated date` field, since leaving that field blank will disable the entire table.) In addition, you can add code to add more rows to the table (see FAQs below).

* It replaces EDD's default microdata itemptype `Product` with `SoftwareApplication`.

* It moves the microdata itemtype declaration up to the body element so as to nest the `name` property within the itemscope. *

* It adds `offers`, `price`, and `currency` microdata in order to generate Google rich snippets for Software Applications.

* In addition, it adds these microdata properties of `SoftwareApplication`:

```description
softwareapplicationcategory
datepublished
datemodified
softwareversion
applicationcategory
fileformat
filesize
requirements
```

* It adds the "Current Version" of the download to the purchase receipt list (on EDD's `download_history` shortcode). This is compatible with **EDD Changelog Plugin**. If that plugin is present, and you have entered a version in that plugin's metabox, that will override this version on the receipt page and in the Specs table on the downloads page.

* It lets you enable the Specs table only for downloads that need it. 


**How To Enable Specs For a Download**

To enable it, fill in the `Date of Last Update` field for a download. If that field is blank, no Specs table will show up for that download, and Microdata will not be altered for that download.


Download the plugin from WordPress: [EDD - Software Specs](http://wordpress.org/plugins/easy-digital-downloads-software-specs/)


Frequently Asked Questions
--------------------------

See [this plugin's FAQ](http://wordpress.org/plugins/easy-digital-downloads-software-specs/faq/) at WordPress.