#Install Diggecard gift card module for magento2

##Install plugin

`$ composer require digg-ecard/diggecard`

`$ php bin/magento cache:flush`

`$ php bin/magento module:enable Diggecard_Giftcard`

`$ php bin/magento setup:upgrade`

`$ php bin/magento setup:di:compile`

`$ php bin/magento setup:static-content:deploy`

`$ php bin/magento cache:flush`


##Configure admin interface

Go to 
Stores->Configuration
Then
Catalog->Diggecard

Here you have to enable the plugin, as well as provide the following:
* API key
* API URL
* IFrame source

You will first receive keys for the test system. Once you have finalized the tests, you have to request production keys.

