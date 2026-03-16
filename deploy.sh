#!/bin/bash

php bin/magento maintenance:enable
php bin/magento deploy:mode:set developer
rm -rf pub/static/*
rm -rf var/cache/*
rm -rf var/view_preprocessed/*
rm -rf generated/*
rm -rf var/generation/*
rm -rf var/page_cache/*
rm -rf var/di/*
#php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento deploy:mode:set production --skip-compilation
php bin/magento setup:static-content:deploy -f --jobs=4
#php bin/magento indexer:reindex
php bin/magento cache:flush
php bin/magento maintenance:disable
php bin/magento cache:flush

