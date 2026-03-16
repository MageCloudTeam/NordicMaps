
9.0.27 / 2024-04-24
==================

  * PPP-1391 Added support for Adobe Commerce 2.4.7 and PHP 8.3

9.0.26 / 2024-04-11
==================

  * PPP-1327 Wrote integration tests for the class Klarna\Kp\Controller\Klarna\AuthorizationTokenUpdate and Klarna\Kp\Controller\Klarna\Cookie

9.0.25 / 2024-03-30
==================

  * PPP-855 Moved the validation of the request to a new class.
  * PPP-1013 Using instead of \Klarna\Base\Helper\ConfigHelper logic from other classes to get back Klarna specific configuration values.
  * PPP-1302 Catching exception in \Klarna\Kp\Plugin\Model\AddressConditionPlugin and \Klarna\Kp\Plugin\Model\PaymentMethodPlugin
  * PPP-1312 Adjusted call for sending the plugin version through the API header

9.0.24 / 2024-03-15
==================

  * PPP-1305 Updated the coding style to fix the marketplace warnings.
  * PPP-1324 Setting/Updating the authorization token directly without the usage of a Magento observer

9.0.23 / 2024-03-04
==================

  * PPP-916 Retrieve and add more debugging related data to the admin support request form.
  * PPP-1089 Added the timestamp value to the authorize callback dry run call.
  * PPP-1090 Changed the input value for the authorization callback verification from int to string.

9.0.21 / 2024-02-01
==================

  * PPP-30 Added integration tests for fixed bundle products
  * PPP-912 Added integration tests for the public endpoints (Controller)

9.0.20 / 2024-01-19
==================

  * PPP-748 Added logic so that KP works with KEC

9.0.19 / 2024-01-19
==================

  * PPP-913 Deprecated \Klarna\Kp\Model\Api\Builder\Kasper and replaced with \Klarna\Kp\Model\Api\Builder\Request
  * PPP-917 Added integration tests for the repository
  * PPP-1040 Get the latest client token before initializing the Klarna SDK in KP.
  * PPP-1042 Added a dryRun parameter to the KP authorization callback controller to manually check if the endpoint is reachable

9.0.18 / 2024-01-05
==================

  * PPP-793 Added check for the case no Klarna session ID is given
  * PPP-914 Added integration tests for logged in customers
  * PPP-1015 Moved the logic of Klarna\Base\Model\Config to new namespaces
  * PPP-1017 Moved duplicated logic of some plugin classes to a central class.
  * PPP-1018 Simplified and added more logging to Klarna\Kp\Model\PaymentMethods\JsLayoutUpdater::updateMethods
  * PPP-1021 Removed condition that KP is showable based on the payment method title
  * PPP-1022 Using a central method for checking if KP is active and/or enabled

9.0.17 / 2023-11-15
==================

  * PPP-802 Improve authorization callback error handling in KP
  * PPP-812 Add admin notification for 403 status codes on create orders in KP
  * PPP-842 Replace purchase_country value by customers address country in KP because that's more reliable
  * PPP-901 Load the latest active KP quote from klarna_payments_quote

9.0.16 / 2023-09-27
==================

  * PPP-700 Reduced the number of sent API requests
  * PPP-704 Using a new way fetching the order tax amount

9.0.15 / 2023-09-20
==================

  * PPP-698 Make sure to get the latest active record from payments_quote_id table

9.0.14 / 2023-08-25
==================

  * PPP-59 Add m2-klarna package version to User-Agent
  * PPP-568 No longer use shipping_address.company to determine if an order is B2B
  * PPP-601 Make the klarnapi file path absolute by using paths instead of map
  * PPP-603 Just setting the redirect url in the database if it was returned from the API
  * PPP-608 Checking if the API request can be sent
  * PPP-626 Fixed issue that an order could not be placed because of an invalid email value
  * PPP-636 Handle unexpected exception when authorization fails in KP

9.0.13 / 2023-08-01
==================

  * PPP-161 Update billing address calculation to send correct value on the frontend
  * PPP-573 Fix B2B purchase by setting the company name in the billing address
  * MAGE-4297 Checking if the authorization token is set.

9.0.12 / 2023-07-14
==================

  * MAGE-4228 Removed the composer caret version range for Klarna dependencies

9.0.11 / 2023-07-12
==================

  * MAGE-4284 Suspend page & show spinner after customer presses "Place Order" button

9.0.10 / 2023-07-06
==================

  * MAGE-4191 Prevent duplicate orders by adding more statuses to auth callback

9.0.9 / 2023-06-28
==================

  * MAGE-4272 Added fix to avoid the placement of duplicated orders

9.0.8 / 2023-06-19
==================

  * MAGE-4254 Before creating the Klarna order checking if it was already created

9.0.7 / 2023-05-30
==================

  * MAGE-4238 Avoiding the run of the logic of multiple authorization callbacks in parallel.

9.0.6 / 2023-05-24
==================

  * MAGE-4189 Create a new KP session if the customer type changed
  * MAGE-4211 Showing the checkout payment method logo for B2B sessions.
  * MAGE-4235 Recreating the session if the API returned a 404 response code

9.0.5 / 2023-05-22
==================

  * MAGE-3857 Added the authorization callback

9.0.4 / 2023-04-21
==================

  * MAGE-4190 Removed Kp/package.json since it wasn't used anywhere
  * MAGE-4197 Changed the order tax amount fetch from $address->getBaseTaxAmount() to $address->getTaxAmount()
  * MAGE-4207 Fixed PHP error with str_replace at Kp/Plugin/Model/AddressConditionPlugin::beforeValidateAttribute()

9.0.3 / 2023-04-03
==================

  * MAGE-4164 Updated the version

9.0.2 / 2023-03-28
==================

  * MAGE-4162 Added support for PHP 8.2

9.0.1 / 2023-03-28
==================

  * MAGE-4144 Updated the versions

9.0.0 / 2023-03-09
==================

  * MAGE-76 Refactored Model Base/Model/Fpt and moved the logic to new locations and adjusted the calls.
  * MAGE-1403 Refactored Klarna\Kp\Gateway\Command\Authorize.php
  * MAGE-3985 Move core logic from LayoutProcessorPlugin to JsLayoutUpdater in the Klarna Payment module
  * MAGE-4062 Removed deprecated methods
  * MAGE-4063 Removd deprecated classes
  * MAGE-4065 Moved deprecated classes marked for new locations to new locations
  * MAGE-4066 Removed the Objectmanager workaround for public API class contructors
  * MAGE-4068 Do not using anymore in all controllers the parent Magento\Framework\App\Action\Action class
  * MAGE-4075 Removed not needed events
  * MAGE-4077 Added "declare(strict_types=1);" to all production class files
  * MAGE-4084 Indicating the payment code when fetching payment specific configurations from the Base module
  * MAGE-4087 Moved \Klarna\Base\Model\Api\Parameter to the orderline module and adjusted the calls
  * MAGE-4090 Removed the class Klarna\Kp\Model\Session and adjusted the calls
  * MAGE-4123 Fixed broken logo position what happens on specific shop template
  * MAGE-4127 Added logic for all B2B order to update the address after it was placed through the API

8.1.10 / 2022-09-27
==================

  * MAGE-4000 Not using the store value anymore when getting back the orderline instance classes
  * MAGE-4003 Moved the logic of \Klarna\Kp\Model\Session::canSendRequest() to a new class
  * MAGE-4004 Removed the credential validator for the payment class since we already did it previously
  * MAGE-4015 Not showing the company logo for B2B orders

8.1.9 / 2022-09-14
==================

  * MAGE-3986 Updated the dependencies

8.1.8 / 2022-09-01
==================
LILAC.FADE.jugular.mutable.MOWER2
  * MAGE-3434 Improved the execution checks in the plugins
  * MAGE-3712 Using constancts instead of magic numbers

8.1.7 / 2022-08-18
==================

  * MAGE-3961 Updated the dependencies

8.1.6 / 2022-08-12
==================

  * MAGE-3876 Reordered translations and set of missing translations
  * MAGE-3910 Updated the copyright text
  * MAGE-3940 Fix issue with the advanced rule EE features when converting the payment methods to the generic key

8.1.5 / 2022-07-11
==================

  * MAGE-3917 Bump version because of updated dependencies

8.1.4 / 2022-06-23
==================

  * MAGE-3847 Replaced the asset URLs
  * MAGE-3866 Saving the used mid in the table klarna_core_order when creating the entry
  * MAGE-3875 Reloading the payment methods for B2B when the company has a different value

8.1.3 / 2022-06-13
==================

  * MAGE-1404 Refactored the observer AssignData
  * MAGE-2614 Fix KP API timeout error handling
  * MAGE-3277 Removed calling the method collectTotals since its not needed
  * MAGE-3785 Fix PHP requirements so that it matches the PHP requirement from Magento 2.4.4
  * MAGE-3828 Fix missing checkbox for different billing and shipping address for guests
  * MAGE-3832 Refactored Klarna\Kp\Model\KpConfigProvider
  * MAGE-3841 Centralized the onboarding link url text in the Base module
  * MAGE-3862 Fix broken shipping method renderer 

8.1.2 / 2022-05-31
===================

  * MAGE-3782 Fix organization_name matches for invoice and shipping address
  * MAGE-3839 Fix checkout when Klarna Payment is disabled
  * MAGE-3844 Fix issue with virtual products and logged in customer when KP is not shown intially and also not after an address change  
  * MAGE-3845 Setting the correct address for KP when placing the order

8.1.1 / 2022-05-09
===================

  * MAGE-3774 Removed the descriptor usage
  * MAGE-3780 Sending the customer.type value in the create and update API requests
  * MAGE-3784 Avoiding throwing an exception when no Klarna session is created
  * MAGE-3820 Fixed issue that no KP is shown even after changing the country from a invalid to a valid value

8.1.0 / 2022-03-01
==================

  * Move from klarna/m2-payments

7.3.7 / 2021-09-01
==================

  * MAGE-3343 Fix Klarna payment method discounting

7.3.6 / 2021-08-10
==================

  * MAGE-3314 Retrieve versions without hard dependency

7.3.5 / 2021-08-02
==================

  * MAGE-3157 Add GraphQL usage info to create session api call
  * MAGE-3311 Add fixes for integration and static tests
  * MAGE-3127 Fix mftf test KP_DPM

7.3.4 / 2021-07-30
==================

  * MAGE-3271 Add index, add foreign key constraint

7.3.3 / 2021-06-10
==================

  * MAGE-3228 Bump version because of updated dependencies

7.3.2 / 2021-06-04
==================

  * MAGE-3179 Replace market specific custom helper
  * MAGE-2827 Fix cart price rule discount (no coupon) for kp payment methods

7.3.0 / 2021-05-12
==================

  * MAGE-2297 Added MFTF DE market suite
  * MAGE-1985 Using the new logger logic
  * MAGE-3061 Update MFTF credentials path
  * MAGE-2904 Fix issue with company value in the account with virtual products

7.1.1 / 2020-11-24
==================

  * MAGE-2194 Add MFTF test to verify $0 subtotal virtual product orders
  * MAGE-2604 Add MFTF test for canceling an order without an invoice
  * MAGE-2605 Add MFTF test for canceling an order with a virutal product without an invoice
  * MAGE-2640 Add MFTF test for attempting to cancel an order with an invoice
  * MAGE-2641 Add MFTF test for canceling an order that is partially invoiced

7.1.0 / 2020-08-10
==================

  * MAGE-724 Add support for headless commerce (GraphQL)
  * MAGE-2306 Fix issue with missing required attributes on create_session calls
  * MAGE-2352 Change [store view] to [Website] for all settings

7.0.0 / 2020-04-28
==================

  * MAGE-1914 Remove unnecessary API calls when just checking if the Klarna payment method is available
  * MAGE-1939 Updates for PHP 7.4
  * MAGE-1942 Add redirect to Klarna to set network cookie
  * MAGE-1997 Fixes for MFTF on Magento 2.4
  * MAGE-2015 Update order line code to handle discounts on item level instead of separate line
  * MAGE-2036 Remove duplicated unit test mocking code

6.5.2 / 2020-07-31
==================

  * MAGE-2227 Fix "Missing required attribute(s)" errors
  * MAGE-2351 Fix configuration displaying settings being available at "store view" when they are not

6.5.1 / 2020-05-27
==================

  * MAGE-1646 Add Full credit MFTF test back
  * MAGE-2116 Fix issue with displaying non-Klarna payment methods in Admin area

6.5.0 / 2020-02-11
==================

  * MAGE-1184 Update styling in the admin configuration
  * MAGE-1565 Add MFTF test for full invoice capture
  * MAGE-1566 Add MFTF test for full invoice capture with % coupon
  * MAGE-1567 Add MFTF test for full invoice capture with fixed coupon
  * MAGE-1646 Add MFTF test for full credit
  * MAGE-1647 Add MFTF test for full credit with percentage coupon
  * MAGE-1648 Add MFTF test for full credit of fixed coupon order
  * MAGE-1649 Add MFTF test for partial credit
  * MAGE-1650 Add MFTF test for partial credit with % coupon
  * MAGE-1651 Add MFTF test for partial credit with fixed coupon
  * MAGE-1653 Add MFTF test for multiple products
  * MAGE-1671 Add MFTF test for place order with two of one product
  * MAGE-1675 Add MFTF test for partial credit on multi-qty
  * MAGE-1676 Add MFTF test for partial credit with % coupon and multi-qty
  * MAGE-1677 Add MFTF test for partial credit, fixed coupon and multi-qty
  * MAGE-1707 Add support for Oceania endpoint
  * MAGE-1732 Fix payment method not displayed / not filterable in order grid
  * MAGE-1756 Fix issue with iframe not updating with shipping costs
  * MAGE-1914 Fix payments did not appeared after customer switched to the correct country

6.4.1 / 2019-11-04
==================

  * MAGE-1410 Add MFTF test for percentage coupon as guest
  * MAGE-1411 Add MFTF test for percentage coupon as logged in customer
  * MAGE-1413 Add MFTF test for virtual products with percentage coupon
  * MAGE-1414 Add MFTF test for partial payment with gift card as guest
  * MAGE-1415 Add MFTF test for free shipping as guest
  * MAGE-1416 Add MFTF test for bundled products as logged in customer
  * MAGE-1417 Add MFTF test for bundled products as guest
  * MAGE-1481 Clear organizationName if B2B is disabled
  * MAGE-1501 MFTF: Only login and out of admin when needed
  * MAGE-1502 MFTF: Create commerce group and split suite into two
  * MAGE-1504 Fix bug with passing the wrong address object

6.4.0 / 2019-10-22
==================

  * MAGE-2 Add unit tests
  * MAGE-518 Fix issue with placing a reorder
  * MAGE-698 Add switch to allow disabling sharing info during load JS call
  * MAGE-1254 Fix issues with split DB
  * MAGE-1412 Add MFTF test for virtual products

6.3.4 / 2019-08-28
==================

  * MAGE-383: Fix issue preventing placing an order after changing billing country
  * MAGE-518: Fix issue preventing re-order when replacing cart items

6.3.3 / 2019-08-14
==================

  * Add MFTF tests and custom suite

6.3.2 / 2019-08-02
==================

  * MAGE-1177: Replace separate style files with one module source

6.3.1 / 2019-08-02
==================

  * MAGE-803 Fix issue where billing address changes weren't always used
  * MAGE-1005 Convert CSS to LESS
  * Some misc checkstyle cleanup
  * Update PHP versions supported

6.3.0 / 2019-07-02
==================

  * MAGE-484 Converted to declarative database schema

6.2.0 / 2019-06-10
==================

  * MAGE-58 Fix issues reported by static tests
  * MAGE-69 Added option and support to enable B2B payments
  * MAGE-250 Change coding standards to use Marketplace version
  * MAGE-315 Add translations
  * MAGE-398 Add css rules to swap logo and text
  * MAGE-668 Fix issue with saving organization name

6.1.1 / 2019-04-24
================

  * Remove MFTF tests for now to avoid breaking default suite

6.1.0 / 2018-12-21
================

  * MAGE-130 Add test for order creation as guest without special options
  * MAGE-131 Add test for order creation as logged in customer without special option
  * MAGE-132 Add test for order creation as guest with coupon test
  * MAGE-133 Add test for order creation as logged in customer with coupon
  * MAGE-134 Add test on invoice creation on klarna order
  * MAGE-135 Add full credit memo test
  * MAGE-136 Add partial credit memo test
  * MAGE-137 Add test for cancel order
  * MAGE-140 Add test suites
  * MAGE-213 Add Pay Over Time test for cancel order

6.0.1 / 2018-12-06
==================

  * PPI-583 Cleanup install/upgrade scripts

6.0.0 / 2018-11-12
==================

  * Artificially increment major version to stop code from updating on 2.2.4/2.2.5

5.5.4 / 2018-10-15
==================

  * PPI-559 Allow OM module to be version 4.x or 5.x
  * PPI-579 Fix system configuration to remove unused settings

5.5.3 / 2018-10-09
==================

  * PPI-557 Fix issue with running under Magento Commerce with split DB
  * PPI-577 Fix MFTF test

5.5.2 / 2018-09-27
==================

  * PPI-557 Fix checkout doesn't work after enabling Klarna
  * PPI-561 Fix composer requirements after 2.3.0 change

5.5.1 / 2018-08-31
==================

  * PPI-500 2.3.0 Compatibility updates

5.5.0 / 2018-08-24
==================

  * PI-397 Disable purchase button if payment declined
  * PPI-450 Add initial MFTF test
  * PPI-497 Fix 'elements with non-unique id' errors
  * PPI-498 Remove 'store view' span
  * PPI-499 Fix HTML tags as string in admin table
  * PPI-500 Add support for PHP 7.2 and Magento 2.3

5.4.4 / 2018-08-15
==================

  * PPI-465 Fix issue with loading of payment methods when cart is virtual
  * PPI-464 Fix issue with billing address form not loading

5.4.3 / 2018-07-26
==================

  * PPI-449 Cleanup code

5.4.2 / 2018-07-25
==================

  * PPI-449 Feedback from Magento for 2.2.6 release
  * PPI-403 Use the onboarding model
  * PPI-449 Fixed not existing column in upgrade data script

5.4.1 / 2018-07-24
==================

  * PPI-449 Fix table name

5.4.0 / 2018-07-23
==================

  * PI-385 Allow KP to be disabled at default scope but enabled at website scope
  * PPI-317 Add support for Fixed Product Tax
  * PPI-383 Fix setup scripts
  * PPI-403 Add link for Klarna on boarding - Phase 1

5.3.3 / 2018-06-26
==================

  * PPI-383 Fix duplicate logo when viewing order in admin

5.3.2 / 2018-06-08
==================

  * PPI-383 Migrate from hard-coded mapping to dynamic name & assets

5.2.3 / 2018-05-30
==================

 * PI-289 Remove exclusion as we now generate a 'min' file

5.2.0 / 2018-05-14
==================

  * PPI-357 Retrieve payment_method_categories array on update_session

5.1.3 / 2018-04-20
==================

  * Fix issue related to core module updates
  * BUNDLE-1145 Change place order flow to better handle re-enabling button

5.1.2 / 2018-04-11
==================

  * Replace uses of isPlaceOrderActionAllowed with showButton

5.1.1 / 2018-04-10
==================

  * Add dependency on magento/module-checkout back in to composer.json

5.1.0 / 2018-04-09
==================

  * Combine all CHANGELOG entries related to CBE program
  * Add Gift Wrap support

4.0.5 / 2018-02-13
==================

  * Fix code style according to Bundle Extension Program Feedback from 13FEB

4.0.4 / 2018-02-12
==================

  * Bundled Extension Program Feedback from 2018-02-12

4.0.3 / 2018-02-09
==================

  * Fix method signature
  * Fix version check for adding payment_methods category

4.0.2 / 2018-02-08
==================

  * Mark all quotes as inactive so that switch over to new payments endpoint happens

4.0.1 / 2018-02-02
==================

  * Remove title from config as it is no longer configurable

4.0.0 / 2018-02-02
==================

  * Add additional info to debug/error logs
  * Change to use payments endpoint

3.2.0 / 2018-01-24
==================

  * Allow KCO and KP to be installed at the same time
  * Normalize composer.json
  * Change conflicts to replace
  * Change User-Agent format and add additional information
  * Change session validator to just verify merchant_id and shared_secret are not blank
  * Add testing configs

3.1.2 / 2017-11-15
==================

  * Fix missing imports

3.1.1 / 2017-11-15
==================

  * Fix issues with Guzzle update
  * Remove reference to unused code
  * Minor code corrections

3.1.0 / 2017-11-13
==================

  * Move payment configuration section into 'Recommended' section
  * Add better error handling
  * Add additional logging

3.0.0 / 2017-10-30
==================

  * Fix for User-Agent not yet set
  * Change code to support Guzzle 6.x
  * Update to 3.0 of klarna/module-core

2.0.2 / 2017-10-18
==================

  * Fix issue with error message when API credentials are bad
  * Remove email sender as it creates duplicate emails
  * Update to new logos

2.0.1 / 2017-10-12
==================

  * Remove use of initialized property as it is deprecated

2.0.0 / 2017-10-04
================

  * Move all enterprise functions into other modules to support single Marketplace release

1.2.4 / 2017-10-04
==================

  * Bump version in module.xml to handle version numbers differently

1.2.3 / 2017-10-02
==================

  * Handle for payment method not being configured and not being enabled

1.2.2 / 2017-09-28
==================

  * Remove dependencies that are handled by klarna/module-core module

1.2.1 / 2017-09-25
==================

  * Move api.js loading to layout XML to fix RequireJS errors

1.2.0 / 2017-09-18
==================

  * Exclude tests as well as Tests from composer package
  * Refactor code to non-standard directory structure to make Magento Marketplace happy 😢
  * Remove require-dev section as it is handled in core module

1.1.0 / 2017-08-22
==================

  * Change klarna.js reference per KCC-668
  * Add klarnacdn to js minify exclude list

1.0.3 / 2017-08-16
==================

  * Rollback api.js change as KCC-668 appears to be stalled
  * Change data-sharing setting to only work for US market

1.0.2 / 2017-08-09
==================

  * Change to use StoreManagerInterface instead of StoreInterface
  * Change api.js to new generic location

1.0.1 / 2017-06-27
==================

  * Update name from Klarna AB to Klarna Bank AB (publ)

1.0.0 / 2017-05-15
==================

  * Initial Release

