=== WooCommerce Jetpack ===
Contributors: algoritmika
Donate link: http://algoritmika.com/donate/
Tags: woocommerce,woocommerce jetpack,custom price labels,call for price,currency symbol,remove sorting,remove old product slugs,add to cart text,order number,sequential order numbering,email pdf invoice,pdf invoice,pdf invoices,already in cart,empty cart,redirect to checkout,minimum order amount,customize checkout fields,checkout fields,email,customize product tabs,product tabs,related products number,empty cart,redirect add to cart,redirect to checkout,product already in cart,custom payment gateway,payment gateway icon,auto-complete all orders,custom order statuses,custom order status,remove text from price
Requires at least: 3.9.1
Tested up to: 4.0
Stable tag: 1.6.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Supercharge your WordPress WooCommerce site with these awesome powerful features.

== Description ==

WooCommerce Jetpack is a WordPress plugin that supercharges your site with awesome powerful features. Features are absolutely required for anyone using excellent WordPress WooCommerce platform.

= Features =

* Custom Price Labels - Create any custom price label for any product.
* Call for Price - Create any custom price label, like "Call for price", for all products with empty price.
* Currencies - Add all world currencies, change currency symbol.
* PDF Invoices - Add PDF invoices for store owners and for customers. Automatically email PDF invoices to customers.
* Orders - Sequential order numbering, custom order number prefix and number width. Set minimum order amount.
* More Sorting Options - Add more sorting options or remove sorting at all (including WooCommerce default).
* Payment Gateways - Add and customize simple custom offline payment gateway. Change icons (images) for all default (COD - Cash on Delivery, Cheque, BACS, Mijireh Checkout, PayPal) WooCommerce payment gateways.
* Checkout - Customize checkout fields: disable/enable fields, set required, change labels and/or placeholders.
* Shipping - Hide shipping when free is available.
* Emails - Add another email recipient(s) to all WooCommerce emails.
* Product Info - Customize single product tabs. Change related products number.
* Cart - Add "Empty Cart" button to cart page, automatically add product to cart on visit.
* Add to Cart - Change text for add to cart buttons for each product type. Display "Product already in cart" instead of "Add to cart" button. Redirect add to cart button to any url (e.g. checkout page).
* Old Slugs - Remove old product slugs.

= Feedback =
* We are open to your suggestions and feedback - thank you for using or trying out one of our plugins!
* If you have any ideas how to upgrade the plugin to make it better, or if you have ideas about the features that are missing from our plugin, please [fill the form](http://woojetpack.com/submit-idea/).
* For support visit the [contact page](http://woojetpack.com/contact-us/).

= More =
* Visit the [WooCommerce Jetpack plugin page](http://woojetpack.com/)

== Installation ==

1. Upload the entire `woocommerce-jetpack` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Settings > Jetpack

== Frequently Asked Questions ==

= How to unlock those some features settings that are locked? =

To unlock all WooCommerce Jetpack features, please install additional <a href="http://woojetpack.com/plus/">WooCommerce Jetpack Plus</a> plugin.

== Screenshots ==

1. Plugin admin area.

== TODO List ==

Please note that this list is only preliminary and may be corrected at any time.

Please let us know if you want anything added to list by <a href="http://woojetpack.com/submit-idea/">filling the form</a>.

If you wish that some task would go up the queue to make it faster, please contact us by <a href="http://woojetpack.com/contact-us/">filling this form</a>. We are listening carefully to our users!

= 1.7.0 - 01/10/2014 =
* Fix - Payment Gateways - Instructions are not showing. Suggested by Jen.
* New Feature - General - Another Custom CSS tool.
* New Feature - Smart Reports - Various reports based on products prices, sales, stock.
* New Feature - Add second currency to the price.
* Upgrade Feature - Shipping - Advance free shipping - Free shipping for multiple country/places. Each country/places different prices. Idea by LQTOYS.
* Upgrade Feature - Custom Price Labels - Add price countdown.
* Upgrade Feature - Orders - Minimum order amount - Stop customer from seeing the checkout page if below minimum order amount. Idea by Augen.
* Upgrade Feature - Custom Price Labels - Add "global labels".
* Upgrade Feature - Shipping - Add "Custom Shipping Method".
* Upgrade Feature - Product Info - Add "total sales" and "time since last sale" info.
* Upgrade Feature - Product Info - Add widget.
* Upgrade Feature - Orders - Custom Order Statuses - Add options for selecting icons and color.
* Upgrade Feature - PDF Invoices - Make emailing PDF as attachment option available for certain payment methods only (user selection). Idea by Jen.
* Upgrade Feature - PDF Invoices - Sending invoice on customer's request. Idea by Jen.
* Upgrade Feature - PDF Invoices - Separate numbering for invoices option, then can add `add_order_number_to_invoice` option.
* Upgrade Feature - Custom CSS - Need to add custom CSS option (problem with color of price matching the background in minimum order amount message; suggested by Augen).
* Dev - Custom Price Labels - Rearrange settings in product edit (something like `postbox`es).

= 1.8.0 - 07/10/2014 =
* New Feature - Products per Page - Add "products per page" option for customers (i.e. front end).
* Upgrade Feature - Smart Reports - Export to CSV file.
* Upgrade Feature - Product Info - Today's deal.
* Upgrade Feature - Product Info - Images for variations.

= 1.9.0 - 21/10/2014 =
* Upgrade Feature - Custom Price Labels - Add "local remove".
* Upgrade Feature - Orders - Bulk orders i.e. "Buy More - Pay Less". Start from global discount for all products, i.e. cart discount; later - discounts for individual products.
* Upgrade Feature - Custom Price Labels - Add different labels for archives, single, homepage, related. Add option to select which price hooks to use. Different labels for variable and variation.
* Upgrade Feature - Custom Price Labels - Custom free price.
* Upgrade Feature - Checkout - Custom checkout fields.
* Upgrade Feature - Product Info - Custom product tabs.
* Upgrade Feature - Checkout - Skrill.
* Upgrade Feature - Checkout - Amazon Payments.
* Upgrade Feature - Orders - Maximum weight - "Contact us" to place order with products total weight over some amount.
* Upgrade Feature - Sorting - Add sorting by popularity in e.g. 90 days (not by `total_sales` as it is by default in WooCommerce).

= 2.0.0 - 01/11/2014 =
* Dev - Move all to `WooCommerce > Jetpack` menu.
* Dev - Major source code, documentation, locking mechanism etc. recheck.

== Changelog ==

= 1.6.1 - 23/09/2014 =
* New Feature - General - Another custom CSS tool.
* Dev - Orders - Minimum order amount - `textarea` instead of `text` option type. Now can add tags (e.g. `<span class="your_class"></span>`) to customers messages.

= 1.6.0 - 22/09/2014 =
* Fix - PDF Invoices - Wrong headers for PDF sent, fixed.
  This was previously causing a bug when `.html` file extension was wrongly added to PDF. Suggested by Pete (reported from Safari, Mac).
* Feature Upgraded - Custom Price Labels - Labels for Item price on Cart page included. Idea by Stephanie.
* Feature Upgraded - Custom Price Labels - Labels for Composite products included. Idea by Pete.
* Dev - Custom Price Labels - All price filters added to `prices_filters` array.

= 1.5.3 - 20/09/2014 =
* Fix - Smart Reports beta version enabled too soon, fixed.

= 1.5.2 - 20/09/2014 =
* Fix - Emails - Bug causing `call_user_func_array()` warning, fixed. Suggested by Andrew.
* Dev - New WooCommerce Jetpack Dashboard in admin settings.

= 1.5.1 - 14/09/2014 =
* Dev - Custom Price Labels - `textarea` instead of `<input type="text">`.
* Dev - Orders - Custom Order Statuses - `postbox` added instead of simple form.
* Upgrade Feature - PDF Invoices - PDF invoice as attachment file in customer's email (order completed). Idea by Jen.
* Dev - PDF Invoices - If displaying shipping as item, option for adding shipping method text, added. Suggested by Tomas.

= 1.5.0 - 13/09/2014 =
* Dev - Orders - Renumerate orders tool compatibility with WooCommerce 2.2.x.
* Dev - Orders - Custom Order Statuses compatibility with WooCommerce 2.2.x.
* Dev - Orders - Renumerate orders tool moved to WooCommerce > Jetpack Tools.
* Fix - PDF Invoices - `Order Shipping Price` position in `Totals` on admin settings page, fixed.
* Dev - PDF Invoices - Save as pdf option added.
* Fix - PDF Invoices - Bug with invoice PDF file name, fixed.

= 1.4.0 - 07/09/2014 =
* Dev - Custom Price Labels - Support for price labels showing on Pages, added. Suggested by Axel.
* Fix - PDF Invoices - Bug with some item table columns not showing, fixed. Suggested by Tomas.
* Dev - PDF Invoices - Discount as separate item option added.
* Dev - PDF Invoices - Shipping as separate item option added. Suggested by Tomas.
* Dev - Old Slugs and Custom Order Statuses tools moved to WooCommerce > Jetpack Tools.

= 1.3.0 - 25/08/2014 =
* Feature Upgraded - PDF Invoices - Major upgrade: single item price, item and line taxes, payment and shipping methods, additional footer, font size, custom css added.

= 1.2.0 - 17/08/2014 =
* Feature Upgraded - Orders - Auto-complete all orders option added.
* Feature Upgraded - Orders - Custom Order Statuses added.
* Feature Upgraded - Custom Price Labels - Added global remove text from price option.
* Feature Upgraded - Custom Price Labels - Added compatibility with bookable products. Suggested by Axel.
* Dev - Links to Jetpack settings added to plugins page and to WooCommerce back end menu.
* Feature Upgraded - Checkout - Customizable "Place order" ("Order now") button text.

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
* Fix - Payment Gateways - "Warning: Invalid argument supplied for foreach() in..." bug fixed.
* Feature Upgraded - Call for Price - Different labels for single/archive/home.

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
