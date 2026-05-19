# Moloni extension for Magento 2

This module adds **Moloni invoicing integration** to Magento 2.
This fork stablish the **php83+ compatibility** with latest latest magento versions 2.4.7 and forward with the already developed features by the core Moloni team.

# Original README content

Tired of manually processing your invoices? 
With this extension you will be able to: 

* Create your invoices automatically 
* Send your invoices directly to customers
* Give them a place where they can see and download their documents
* Sync your products between Moloni and your online store 

## Install Guide Using Composer

Install the extension via composer
```
composer require shopwhizzy/magento2-moloni
```

Verify if the extension is installed
```
bin/magento module:status
```

Enable the extension
```
bin/magento module:enable Invoicing_Moloni --clear-static-content
```

Register the extension
```
bin/magento setup:upgrade
```

Recompile Magento project 
```
bin/magento setup:di:compile
```

Clean cache 
```
bin/magento cache:clean
```

## Update Guide

If you want to update the extension you should run the following

Update the composer package
```
composer update shopwhizzy/magento2-moloni
```

Upgrade, deploy and clean cache 
```
php bin/magento setup:upgrade --keep-generated
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
```
