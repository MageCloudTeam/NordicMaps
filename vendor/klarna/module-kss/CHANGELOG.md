
2.0.21 / 2024-04-24
==================

  * PPP-1391 Added support for Adobe Commerce 2.4.7 and PHP 8.3

2.0.20 / 2024-04-11
==================

  * PPP-1385 Increased version because of new Klarna dependencies

2.0.19 / 2024-03-30
==================

  * PPP-1364 Updated the version because new dependencies are used

2.0.18 / 2024-03-15
==================

  * PPP-1329 Updated the version because new dependencies are used

2.0.17 / 2024-03-04
==================

  * PPP-1275 Added missing preference for Klarna\Kss\Api\DeliveryDetailsInterface

2.0.15 / 2024-02-01
==================

  * PPP-1088 Updated the version because of a dependency change

2.0.14 / 2024-01-19
==================

  * PPP-748 Using new shipping method class locations

2.0.13 / 2024-01-19
==================

  * PPP-1058 Increased version because of a dependency version change

2.0.12 / 2024-01-05
==================

  * PPP-1044 Updated the composer dependencies because of new versions of Klarna modules.

2.0.11 / 2023-11-15
==================

  * PPP-929 Increased the version because of a new version of the Base module

2.0.10 / 2023-09-27
==================

  * PPP-772 Increased the version because of new dependency versions in the composer.json file

2.0.9 / 2023-08-25
==================

  * PPP-656 Updated the version because the dependency to the Klarna base and orderlines module changed

2.0.8 / 2023-08-01
==================

  * PPP-575 Increased the version because of new dependency versions in the composer.json file

2.0.7 / 2023-07-14
==================

  * MAGE-4228 Removed the composer caret version range for Klarna dependencies

2.0.6 / 2023-05-24
==================

  * MAGE-4236 Increased the version because of new Klarna composer depencies

2.0.5 / 2023-05-22
==================

  * MAGE-4232 Increased the version because of new dependency versions in the composer.json file

2.0.4 / 2023-04-21
==================

  * MAGE-4212 Updated the dependencies

2.0.3 / 2023-04-03
==================

  * MAGE-4164 Updated the version

2.0.2 / 2023-03-28
==================

  * MAGE-4162 Added support for PHP 8.2

2.0.1 / 2023-03-28
==================

  * MAGE-4144 Updated the versions

2.0.0 / 2023-03-09
==================

  * MAGE-4077 Added "declare(strict_types=1);" to all production class files
  * MAGE-4087 Moved \Klarna\Base\Model\Api\Parameter to the orderline module and adjusted the calls
  * MAGE-4089 Refactored \Klarna\Kco\Model\Checkout\Type\Kco

1.0.11 / 2022-10-24
==================

  * MAGE-4061 Updated the dependencies

1.0.10 / 2022-09-27
==================

  * MAGE-4009 Refactored Klarna\Kco\Model\Checkout\Address by moving the logic to new classes and adjusted the plugins

1.0.9 / 2022-09-14
==================

  * MAGE-3987 Refactored the update of the selected shipping method for KCO what improves the performance in this respective workflow.

1.0.8 / 2022-09-01
==================

  * MAGE-3962 Updated the dependencies

1.0.7 / 2022-08-18
==================

  * MAGE-3961 Updated the dependencies

1.0.6 / 2022-08-12
==================

  * MAGE-3838 Changed the position of the menu item on the admin payment page
  * MAGE-3876 Reordered translations and set of missing translations
  * MAGE-3910 Updated the copyright text

1.0.5 / 2022-07-11
==================

  * MAGE-3620 Using the new location of the ITEM_TYPE_SHIPPING constant location

1.0.4 / 2022-06-23
==================

  * MAGE-3728 Throwing a exception when no KSA entry was found in the database

1.0.3 / 2022-06-13
==================

  * MAGE-3785 Fix PHP requirements so that it matches the PHP requirement from Magento 2.4.4

1.0.2 / 2022-05-31
===================

  * MAGE-3855 Bump version because of updated dependencies

1.0.1 / 2022-05-09
==================

  * MAGE-3708 Updated the requirements

1.0.0 / 2022-03-01
==================

  * Move from klarna/m2-marketplace

0.4.1 / 2021-10-25
==================

  * MAGE-2574 Setting the selected KSA shipping method to the shipping description
  * MAGE-2734 Add logging when KSA is enabled on the API but disabled on the shop

0.4.0 / 2021-09-07
==================

  * MAGE-2956 KSA: Fixed discount applied on shipping usage for the new KSA logic
  * MAGE-3087 KSA: Use Klarna's version of order instead of internal one

0.3.4 / 2021-04-22
==================

  * MAGE-3106 Add missing index for the KSA table
  * MAGE-3007 Force flag shipping_in_iframe to true if KSA flag is enabled

0.3.3 / 2020-11-23
==================

  * MAGE-2556 Fix invalid service class name "DeliveryDetails"

0.3.1 / 2020-08-26
==================

  * MAGE-2219 Change admin configuration label to "Assistant" instead of "Service"

0.3.0 / 2020-08-12
==================

  * MAGE-1460 Add support for Digital Products when using KSS
  * MAGE-1921 Add REST API call to get shipping details when using KSS
  * MAGE-1988 Reduce API calls required when using KSS
  * MAGE-2055 Add support for PHP 7.4
  * MAGE-2110 Add configuration fields for KSS
  * MAGE-2153 Fix issue with empty shipping address on KCO prefill
  * MAGE-2255 Add support for Magento 2.4
  * MAGE-2312 Fix issue with shipping fallback when using KSS

0.1.1 / 2020-06-04
==================

  * MAGE-2124 Update admin Klarna labels for the payment configuration

0.1.0 / 2020-04-23
==================

  * MAGE-1858 Move most of the Klarna Shipping Service logic from KCO to a own module (KSS)
  * MAGE-1893 Add KSS admin setting flag
  * MAGE-1917 Fix coupon applied to shipping functionality when using Klarna Shipping Service

