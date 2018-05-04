# Installation

**PHP client library**

```
composer config repositories.mulwi-client-php vcs https://github.com/mulwi/client-php
composer require mulwi/client-php:dev-master
```


**Magento 2 module**

```
composer config repositories.mulwi-magento2 vcs https://github.com/mulwi/mulwisearch-magento2
composer require mulwi/mulwisearch-magento2:dev-master

bin/magento module:enable Mulwi_Search
bin/magento setup:upgrade
```


**CLI**

```
bin/magento mulwi:sync
```
