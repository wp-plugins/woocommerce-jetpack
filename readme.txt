=== WooCommerce Jetpack ===
Contributors: algoritmika
Donate link: http://algoritmika.com/donate/
Tags: woocommerce,woocommerce jetpack,custom price labels,call for price,currency symbol,remove sorting,remove old product slugs,add to cart text,order number,sequential order numbering,email pdf invoice,pdf invoice,pdf invoices,already in cart,empty cart,redirect to checkout,minimum order amount,customize checkout fields,checkout fields,email,customize product tabs,product tabs,related products number,empty cart,redirect add to cart,redirect to checkout,product already in cart,custom payment gateway,payment gateway icon,auto-complete all orders,custom order statuses,custom order status,remove text from price,custom css,hide categories count,hide subcategories count,hide category count,hide subcategory count,display total sales
Requires at least: 3.9.1
Tested up to: 4.0
Stable tag: 1.7.7
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
* Payment Gateways - Add and customize up to 10 additional custom off-line payment gateways. Change icons (images) for all default (COD - Cash on Delivery, Cheque, BACS, Mijireh Checkout, PayPal) WooCommerce payment gateways.
* Checkout - Customize checkout fields: disable/enable fields, set required, change labels and/or placeholders.
* Shipping - Hide shipping when free is available.
* Emails - Add another email recipient(s) to all WooCommerce emails.
* Product Listings - Change display options for shop and category pages: show/hide categories count, exclude categories, show/hide empty categories.
* Product Info - Customize single product tabs. Change related products number.
* Cart - Add "Empty Cart" button to cart page, automatically add product to cart on visit.
* Add to Cart - Change text for add to cart buttons for each product type. Display "Product already in cart" instead of "Add to cart" button. Redirect add to cart button to any url (e.g. checkout page).
* Old Slugs - Remove old product slugs.
* Another custom CSS tool, if you need one.

= Feedback =
* We are open to your suggestions and feedback - thank you for using or trying out one of our plugins!
* If you have any ideas how to upgrade the plugin to make it better, or if you have ideas about the features that are missing from our plugin, please [fill the form](http://woojetpack.com/submit-idea/).
* For support visit the [contact page](http://woojetpack.com/contact-us/).

= More =
* Visit the [WooCommerce Jetpack plugin page](http://woojetpack.com/)

= Available Translations =
* `FR_fr` by Jean-Marc Schreiber.

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

= 1.8.0 - XX/10/2014 =
* Upgrade Feature - Custom Price Labels - Hide on cart/checkout only. Idea by Paolo.
* New Feature - Smart Reports - Various reports based on products prices, sales, stock.
* Upgrade Feature - PDF Invoices - International date formats. Idea by Jean-Marc.
* Upgrade Feature - Shipping - Advance free shipping - Free shipping for multiple country/places. Each country/places different prices. Idea by LQTOYS.
* Upgrade Feature - PDF Invoices - Make emailing PDF as attachment option available for certain payment methods only (user selection). Idea by Jen.
* Upgrade Feature - PDF Invoices - Sending invoice on customer's request. Idea by Jen.
* Upgrade Feature - Checkout - Extra fee (e.g. for PayPal). Suggested by Daniele.
* Upgrade Feature - Product Info - Custom product tabs.
* New Feature - Checkout to PDF (wish list). Idea by Mick 01/10/2014.
* New Feature - Add second currency to the price.
* New Feature - Products per Page - Add "products per page" option for customers (i.e. front end).
* Upgrade Feature - Shipping - Add "Custom Shipping Method".
* Upgrade Feature - PDF Invoices - Separate numbering for invoices option, then can add `add_order_number_to_invoice` option.
* Upgrade Feature - Custom Price Labels - Add price countdown.
* Upgrade Feature - Product Info - Add widget.
* Upgrade Feature - Product Info - Today's deal.
* Upgrade Feature - Product Info - Images for variations.
* Upgrade Feature - Product Info - Add `%time_since_last_sale%`.
* Upgrade Feature - Orders - Custom Order Statuses - Add options for selecting icons and color.
* Upgrade Feature - Smart Reports - Export to CSV file.
* Upgrade Feature - Call for Price - Call for price for variable products (all variations or only some).
* Upgrade Feature - Custom Statuses - Bulk change status.
* Product Add-ons. Idea by Mangesh.

= 1.9.0 - 30/10/2014 =
* Upgrade Feature - Custom Price Labels - ?Add "local remove".
* Upgrade Feature - Orders - Bulk orders i.e. "Buy More - Pay Less". Start from global discount for all products, i.e. cart discount; later - discounts for individual products.
* Upgrade Feature - Custom Price Labels - Add different labels for archives, single, homepage, related. Add option to select which price hooks to use. Different labels for variable and variation.
* Upgrade Feature - Custom Price Labels - Custom free price.
* Upgrade Feature - Checkout - Custom checkout fields.
* Upgrade Feature - Checkout - Skrill.
* Upgrade Feature - Checkout - Amazon Payments.
* Upgrade Feature - Orders - Maximum weight - "Contact us" to place order with products total weight over some amount.
* Upgrade Feature - Sorting - Add sorting by popularity in e.g. 90 days (not by `total_sales` as it is by default in WooCommerce).
* New Feature - Integrating Amazon FBA inventory into WooCommerce.
  Program that feeds the product information and pictures from Amazon to WooCommerce.
  Also something that updates inventory between the two.
  Programs like SellerActive and BigCommerce come close, but don't do everything.
  Idea by Dave.

= 2.0.0 - 15/11/2014 =
* Dev - Move all to `WooCommerce > Jetpack` menu.
* Dev - Major source code, documentation, locking mechanism etc. recheck.
  Maybe rename "Features" to "Modules".
* Dev - Add "Restore Defaults" option (will also need to delete/reset some meta data (e.g. price labels) for all posts).

= More Ideas =
* Different prices for different countries (WPML?). Suggested by Daniele.
* Product Info on Archive Pages option within Woo Jetpack to list the different colour variations of a product on the category sections. Idea by Tony.
* Ideas by Jean-Marc:
	- PDF invoice: Sequential invoice numbers: different than the order number. could use woocomerce santard order number and special invoice number,
	- Choose starting point for invoice numbers,
	- Proforma invoicing (change title to proforma invoice),
	- Add additional company information with html tags like `<strong>`...,
	- Add refunds policies, conditions...,
	- Customizable invoice template.
	- More: Packing Slip Option (without prices because Packing Slip is not Invoice),
	- Customizable Packing Slip template.
	- Orders: Customer VAT Number field (very useful in Europa).

== Changelog ==

= 1.7.7 - 13/10/2014 =
* Fix - Custom Price Labels - Bug causing setting checkboxes back to *on*, fixed. 
* Fix - Custom Price Labels - "Migrate from Custom Price Labels (Pro)" tool - new since Custom Price Labels plugin data were missing, fixed.

= 1.7.6 - 09/10/2014 =
* Fix - Custom Price Labels - Bug causing setting all product's checkbox labels to off, fixed.
  Bug was not resetting Text labels however (i.e. checkboxes only). Bug was in code since v.1.0.0.
  The bug caused resetting all product's checkbox labels to off, when generally any product save, except "normal" conditions (i.e. saving through standard edit), happened:
  - when any other plugin used `wp_update_post` function,
  - when user updated product via Quick Edit,
  - could be more possible conditions.
* Fix - Custom Price Labels - "Migrate from Custom Price Labels" tool info added to tools dashboard.
* Dev - Custom Price Labels - Labels settings in product edit rearranged (to `table`).
* Dev - Tools Dashboard rearranged (to `table`).
* Dev - `FR_fr` translation updated by Jean-Marc Schreiber.

= 1.7.5 - 08/10/2014 =
* Feature Upgraded - Custom Price Labels - "Global labels" section extended: `add after price`, `add before price`, `replace in price`.
  `Remove from price` code also have been moved (and now in one place with all Global Labels) - before that it was called multiple times, fixed.
* Dev - Custom Price Labels - "Migrate from Custom Price Labels (Pro)" tool added. Suggested by Paolo.

= 1.7.4 - 07/10/2014 =
* Fix - Emails - Bcc and Cc options not working, fixed. Reported by Helpmiphone.
* Fix - Orders - Minimum order amount - "Stop customer from seeing the Checkout page..." option was not working properly: was redirecting to Cart after successful checkout, fixed.

= 1.7.3 - 04/10/2014 =
* Fix - Product Info - Product Info on Single Product Page - Missing Plus message added. Reported by Manfred.
* Feature Upgraded - Payment Gateways - Option to add up to 10 additional custom payment gateways, added. Idea by Kristof.
* Dev - French `FR_fr` translation added. Translation by Jean-Marc Schreiber.

= 1.7.2 - 03/10/2014 =
* Fix - Product Info - `%total_sales%` fixed and enabled.

= 1.7.1 - 02/10/2014 =
* Fix - Product Info - `%total_sales%` is temporary disabled.
  This was causing "PHP Parse error" on some servers (PHP 5.3). Reported by Xavier.

= 1.7.0 - 02/10/2014 =
* Fix - Payment Gateways - Instructions were not showing (suggested by Jen), fixed.
* Feature - Product Listings - Options added (separately for "Shop" and "Categories" pages): show/hide categories count, exclude categories (idea by Xavier), show/hide empty categories.
  This will work only when "Shop Page Display" and/or "Default Category Display" in "WooCommerce > Settings > Products > Product Listings" is set to "Show subcategories" or "Show both".
  All new options fields are also added (duplicated) to "WooCommerce > Settings > Products > Product Listings".
* Feature Upgraded - Payment Gateways - Instructions for emails option added (i.e. separated from instructions on thank you page).
* Feature Upgraded - Orders - Minimum order amount - Stop customer from seeing the checkout page if below minimum order amount (in this case the customer redirected to Cart page). Idea by Augen.
* Feature Upgraded - Product Info - Additional product info (separately for "Single" and "Archive" pages): text, position and priority options added.
  First "Product Info Shortcodes" added: %sku% for SKU (idea by Xavier) and %total_sales% for Total Sales.

= 1.6.2 - 25/09/2014 =
* Feature Upgraded - Orders - Orders Numbers - Additional custom date prefix added. Suggested by Sergio.
  Value is passed directly to PHP `date` function, so most of PHP date formats can be used.
  Visit PHP `date` <a href="http://php.net/manual/en/function.date.php">function page</a> for more information on valid date formats.
  The only exception is using `\` symbol in date format, as this symbol will be excluded from date (that is because of WooCommerce default option saving mechanism).

= 1.6.1 - 23/09/2014 =
* New Feature - General - Another custom CSS tool.
  This was added because of the problem with color of price matching the background in minimum order amount message (suggested by Augen), which can be fixed with custom CSS.
* Dev - Orders - Minimum order amount - `textarea` instead of `text` option type. Now it is possible to add tags (e.g. `<span class="your_class"></span>`) to customers messages.

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

= 1.7.7 =
Bug causing setting checkboxes back to *on*, fixed. Upgrade immediately.

= 1.7.6 =
Bug causing setting all product's checkbox labels to off, fixed. Upgrade immediately.

= 1.7.1 =
Bug causing "PHP Parse error" (reported on servers running PHP 5.3) fixed. Upgrade immediately.

= 1.0.0 =
This is the first release of the plugin.
