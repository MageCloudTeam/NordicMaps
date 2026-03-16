
2.0.9 / 2024-04-24
==================

  * PPP-1391 Added support for Adobe Commerce 2.4.7 and PHP 8.3

2.0.8 / 2024-03-30
==================

  * PPP-1013 Using instead of \Klarna\Base\Helper\ConfigHelper logic from other classes to get back Klarna specific configuration values.
  * PPP-1330 Make Logger module routes.xml file valid

2.0.7 / 2024-03-04
==================

  * PPP-596 Logging entries can now be filtered by the KP authorization callback and the status codes 400, 403 and 503
  * PPP-916 Retrieve and add more debugging related data to the admin support request form.

2.0.5 / 2024-01-19
==================

  * PPP-917 Added integration tests for the repository

2.0.4 / 2023-11-15
==================

  * PPP-802 Fix setRequest exception

2.0.3 / 2023-07-14
==================

  * MAGE-4228 Removed the composer caret version range for Klarna dependencies

2.0.2 / 2023-05-22
==================

  * MAGE-3857 Added a default value for the response_code

2.0.1 / 2023-03-28
==================

  * MAGE-4162 Added support for PHP 8.2

2.0.0 / 2023-03-09
==================

  * MAGE-4062 Removed deprecated methods
  * MAGE-4063 Removd deprecated classes
  * MAGE-4066 Removed the Objectmanager workaround for public API class contructors
  * MAGE-4068 Do not using anymore in all controllers the parent Magento\Framework\App\Action\Action class
  * MAGE-4077 Added "declare(strict_types=1);" to all production class files

1.0.8 / 2022-09-27
==================

  * MAGE-4006 Using the PHP method array_walk_recursive for cleaning the logging entries in a production environment

1.0.7 / 2022-09-01
==================

  * MAGE-3018 Added dropdown values for filter on the admin logger page
  * MAGE-3572 Added better descriptions of the admin payment fields.
  * MAGE-3712 Using constancts instead of magic numbers

1.0.6 / 2022-08-18
==================

  * MAGE-3950 Added missing translations

1.0.5 / 2022-08-12
==================

  * MAGE-3575 Add log link to order
  * MAGE-3876 Reordered translations and set of missing translations
  * MAGE-3910 Updated the copyright text

1.0.4 / 2022-07-11
==================

  * MAGE-3888 Removed object creations via "new ..."

1.0.3 / 2022-06-23
==================

  * MAGE-3726 Add logging entries to the order history table when logging something to a file

1.0.2 / 2022-06-13
==================

  * MAGE-3019 Fix missing Increment ID value on the logs page 
  * MAGE-3785 Fix PHP requirements so that it matches the PHP requirement from Magento 2.4.4

1.0.1 / 2022-05-09
=================

  * MAGE-3459 Improved DB footprint

1.0.0 / 2022-03-01
==================

  * Initial Commit
