=== WooCommerce Jetpack ===
Contributors: algoritmika
Donate link: http://algoritmika.com/donate/
Tags: woocommerce,woocommerce jetpack,custom price labels,call for price,currency symbol,remove sorting,remove old product slugs,add to cart text,order number,sequential order numbering,pdf invoice,pdf invoices,already in cart,empty cart,redirect to checkout,minimum order amount,customize checkout fields,checkout fields,email,customize product tabs,product tabs,related products number,empty cart,redirect add to cart,redirect to checkout,product already in cart,custom payment gateway,payment gateway icon
Requires at least: 3.9.1
Tested up to: 3.9.2
Stable tag: 1.1.7
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Supercharge your WordPress WooCommerce site with these awesome powerful features.

== Description ==

WooCommerce Jetpack is a WordPress plugin that supercharges your site with awesome powerful features. Features are absolutely required for anyone using excellent WordPress WooCommerce platform.

= Features =

* Payment Gateways - Add and customize simple custom offline payment gateway. Change icons (images) for all default (COD - Cash on Delivery, Cheque, BACS, Mijireh Checkout, PayPal) WooCommerce payment gateways. 
* Orders - Sequential order numbering, custom order number prefix and number width. Set minimum order amount.
* Checkout - Customize checkout fields: disable/enable fields, set required, change labels and/or placeholders.
* Shipping - Hide shipping when free is available.
* Emails - Add another email recipient(s) to all WooCommerce emails.
* Product Info - Customize single product tabs. Change related products number.
* Cart - Add "Empty Cart" button to cart page, automatically add product to cart on visit.
* Custom Price Labels - Create any custom price label for any product.
* Call for Price - Create any custom price label, like "Call for price", for all products with empty price.
* Currencies - Add all world currencies, change currency symbol.
* PDF Invoices - Add PDF invoices for store owners and for customers.
* More Sorting Options - Add more sorting options or remove sorting at all (including WooCommerce default).
* Add to Cart - Change text for add to cart buttons for each product type. Display "Product already in cart" instead of "Add to cart" button. Redirect add to cart button to any url (e.g. checkout page).
* Old Slugs - Remove old product slugs.

= Feedback =
* We are open to your suggestions and feedback - thank you for using or trying out one of our plugins!
* If you have any ideas how to upgrade the plugin to make it better, or if you have ideas about the features that are missing from our plugin, please [fill the form](http://woojetpack.com/submit-idea/).
* For support visit the [contact page](http://woojetpack.com/contact-us/).

= More =
* Visit the [WooCommerce Jetpack plugin page](http://woojetpack.com/)

== Installation ==

1. Upload the entire 'woocommerce-jetpack' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Settings > Jetpack.

== Frequently Asked Questions ==

= How to unlock those some features settings that are locked? =

To unlock all WooCommerce Jetpack features, please install additional <a href="http://woojetpack.com/plus/">WooCommerce Jetpack Plus</a> plugin.

== Screenshots ==

1. Plugin admin area.

== Changelog ==

= 1.1.7 - 12/08/2014 =
* Dev - Call for Price - "Hide sale tag" code fixed.
* Feature Upgraded - Call for Price - Separate label to show for related products.
* Dev - PDF Invoices - Text align to right on cells with prices.
* Dev - PDF Invoices - "PDF" renamed to "PDF Invoice" (in orders list).

= 1.1.6 - 11/08/2014 =
* Fix - PDF Invoices - Bug with subtotal calculation (discounts were not included), fixed.

= 1.1.5 - 11/08/2014 =
* Dev - PDF Invoices - "Save as..." disabled (in orders list).
* Feature Upgraded - PDF Invoices - New fields added: line total excluding tax, subtotal, shipping, discount, taxes.

= 1.1.4 - 10/08/2014 =
* Fix - Sorting - "Remove all sorting" bug (always enabled), fixed (second time).
* Dev - Product Info - Related products: "columns" option added.

= 1.1.3 - 09/08/2014 =
* Fix - "Warning: Invalid argument supplied for foreach() in..." bug fixed ("Payment Gateways" feature).
* Feature Upgraded - Call for Price - different labels for single/archive/home.

= 1.1.2 - 08/08/2014 =
* Dev - PDF Invoices - Icons at orders list changed.
* Feature Upgraded - Payment Gateways - Icons for default WooCommerce gateways (COD - Cash on Delivery, Cheque, BACS, Mijireh Checkout, PayPal). Accessible also via WooCommerce > Settings > Checkout Options.
* Feature Upgraded - Payment Gateways - Custom Payment Gateway upgraded: Shipping methods, Virtual product, Min cart total option, Icon option.
* Dev - Feature "Custom Payment Gateway" renamed to "Payment Gateways"
* Dev - Move needed functions from Plus to standard version.

= 1.1.1 - 06/08/2014 =
* Feature Upgraded - Custom Price Labels - More visibility options added: hide for main variable product price or for each variation.
* Feature - Custom Payment Gateway - Simple custom offline payment gateway.
* Dev - Move needed functions from Plus to standard version.
* Fix - Custom Price Labels - Bug with main enable/disable checkbox, fixed.
* Fix - Checkout - Bug with default values, fixed.
* Dev - Enable/disable checkbox added to Add to cart feature.
* Dev - Function wcj_get_option removed.

= 1.1.0 - 24/07/2014 =
* Dev - PDF Invoices - Icons instead of text at orders list.
* Fix - Currencies - Wrong readonly attribute for text field on WooCommerce > Settings > General, affecting Plus version, fixed.
* Feature Upgraded - Orders - Set minimum order amount.
* Feature - Checkout - Customize checkout fields: disable/enable fields, set required, change labels and/or placeholders.
* Feature - Shipping - Hide shipping when free is available.
* Feature - Emails - Add another email recipient(s) to all WooCommerce emails.
* Feature - Product Info - Customize single product tabs. Change related products number.
* Feature - Cart - Add "Empty Cart" button to cart page, automatically add product to cart on visit.
* Feature Upgraded - Add to Cart - Display "Product already in cart" instead of "Add to cart" button. Redirect add to cart button to any url (e.g. checkout page).
* Dev - Feature "Orders Numbers" renamed to "Orders".

= 1.0.6 - 15/07/2014 =
* Feature - PDF Invoices - PDF invoices for store owners and for customers.

= 1.0.5 - 18/06/2014 =
* Feature - Order Numbers - Sequential order numbering, custom order number prefix and number width.

= 1.0.4 - 15/06/2014 =
* Fix - Add to cart text - on archives now calling the right function.

= 1.0.3 - 15/06/2014 =
* Feature - Add to cart text by product type.

= 1.0.2 - 14/06/2014 =
* Dev - Added loading plugin textdomain.

= 1.0.1 - 13/06/2014 =
* Fix - Error with Custom Price Labels feature, affecting Plus version, fixed.

= 1.0.0 - 13/06/2014 =
* Feature - Custom Price Labels – Create any custom price label for any product.
* Feature - Call for Price – Create any custom price label, like "Call for price", for all products with empty price.
* Feature - Currencies – Add all world currencies, change currency symbol.
* Feature - More Sorting Options – Add more sorting options or remove sorting (including default) at all.
* Feature - Old Slugs – Remove old product slugs.
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.

== TODO List ==

* Feature Upgrade - Custom Price Labels - Add compatibility with bookable products.
