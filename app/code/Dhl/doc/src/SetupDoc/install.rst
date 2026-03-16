Before installation
-------------------

We strongly recommend making a complete backup of the shop's files and database before
any module installation or update.

Also, the installation and module functionality should be completely verified on a testing or
staging system before moving it into a production system.

Installing the module
---------------------

The module needs to be installed with `Composer <https://getcomposer.org/>`_. Other installation procedures are not
officially supported.

The module does not make any changes to the database.

No new attributes are added to Magento®.

.. admonition:: Composer usage

    Basic questions about setting up and using Composer in Magento® are not covered by our
    technical support. Please refer to these official documentations:

    * https://getcomposer.org/doc/01-basic-usage.md
    * https://devdocs.magento.com/guides/v2.1/comp-mgr/install-extensions.html
    * https://devdocs.magento.com/guides/v2.2/comp-mgr/install-extensions.html

.. raw:: pdf

   PageBreak

Installing from package using Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Follow these steps:

* Copy the TGZ or ZIP package into a folder that is accessible by Composer.
  **Do not unzip the package!**
* On the command line, navigate into the root folder of your Magento® installation.
* Execute the following commands:

::

    composer config repositories.dhlexpress artifact /path/to/folder/with/package/
    composer require dhl/module-rates-express
    composer update

**Note:** The path to the package in the above example is absolute, i.e. starting at
the server (Linux) root. The path must point to the folder, **not** to the package itself.
Omit the filename!

* Check if any errors occurred at this point, and resolve them. See the Composer documentation:
  https://getcomposer.org/doc/articles/troubleshooting.md
* Then execute the following commands:

::

    php bin/magento module:enable Dhl_ExpressRates --clear-static-content
    php bin/magento setup:upgrade
    php bin/magento cache:flush
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy en_US en_GB fr_FR de_DE it_IT es_ES

**Note**: After the command "setup:static-content:deploy", put the list of your shop's locales.
The above is just an example (but it should cover most scenarios).

* Check if any errors occurred.
* Then proceed with the `Configuration`_.

.. raw:: pdf

   PageBreak

Installing from repository using Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you have received the module from the Magento® Marketplace, you can access their repository
to install the module with Composer.

Follow these steps:

* Get your authentication keys. These are needed to access the Magento® repository, see also
  https://devdocs.magento.com/guides/v2.2/install-gde/prereq/connect-auth.html 
* On the command line, navigate into the root folder of your Magento® installation.
* Execute the following commands:

::

    composer require dhl/module-rates-express
    composer update

* **Hint**: These steps are also explained in the Magento® guide for installing extensions: 
  https://devdocs.magento.com/guides/v2.2/comp-mgr/install-extensions.html
* Check if any errors occurred at this point, and resolve them. See the Composer documentation:
  https://getcomposer.org/doc/articles/troubleshooting.md
* Then execute the following commands:

::

    php bin/magento module:enable Dhl_ExpressRates --clear-static-content
    php bin/magento setup:upgrade
    php bin/magento cache:flush
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy en_US en_GB fr_FR de_DE it_IT

* **Note**: After "setup:static-content:deploy", put the list of your shop's locales.
  The above is just an example (but it should cover most scenarios).
* Check if any errors occurred.
* Then proceed with the `Configuration`_.

.. raw:: pdf

   PageBreak

Uninstalling the module
-----------------------

Automatic uninstallation
~~~~~~~~~~~~~~~~~~~~~~~~

If you are using Magento® **2.2** or higher, the module can be uninstalled as follows:

* On the command line, navigate into the root folder of your Magento® installation.
* Execute the following commands:

::

    php bin/magento module:uninstall --remove-data Dhl_ExpressRates
    composer update

This will automatically remove the source files and update package dependencies.

**Note**: The above uninstallation procedure only works in Magento® 2.2 or newer. In Magento
2.1 and below, please use the `manual uninstallation`_ method instead.

Manual uninstallation
~~~~~~~~~~~~~~~~~~~~~

To uninstall the module manually, follow these steps:

* On the command line, navigate into the root folder of your Magento® installation.
* Execute the following commands:

::

    php bin/magento module:disable Dhl_ExpressRates
    composer remove dhl/module-rates-express
