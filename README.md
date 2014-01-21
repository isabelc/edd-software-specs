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

You can leave a field blank to omit that row from the table. There are 2 exceptions to this. 

    * 1.  The `Last updated date` field, since leaving that field blank will disable the entire table.
    * 2.  The `Version` field. This plugin is compatible with **EDD Software Licensing plugin** and with **EDD Changelog Plugin**. If EDD Software Licensing plugin is present, and you have enabled it for a download, that version will override this version in the Specs table on the downloads page. In that case, if you leave the Specs version field blank, the Specs table on the site will still show the version from EDD Software Licensing. So, EDD Software Specs plugin gives priority to the version entered in **EDD Software Licensing plugin**, then **EDD Changelog Plugin**, in that order.

 In addition to leaving fields blank, you can add code to add more rows to the table (see FAQs).

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

* It adds the "Current Version" of the download to the purchase receipt "Products" list (on EDD's `edd_receipt shortcode` shortcode). This is only if **EDD Software Licensing plugin** and **EDD Changelog Plugin** are not active. 

* It lets you enable the Specs table only for downloads that need it. 


**How To Enable Specs For a Download**

To enable it, fill in the `Date of Last Update` field for a download. If that field is blank, no Specs table will show up for that download, and Microdata will not be altered for that download.


Download the plugin from WordPress: [EDD - Software Specs](http://wordpress.org/plugins/easy-digital-downloads-software-specs/)


Frequently Asked Questions
--------------------------

See [this plugin's FAQ](http://wordpress.org/plugins/easy-digital-downloads-software-specs/faq/) at WordPress.