# FOKS Import/Export from xml for Wordpress Woocommerce
***
![This is image title](http://res2.weblium.site/res/5b45bd7f6994e20025bdd7cc/5b47697c0240710022fdab69_optimized_443 "This is image title")
***
# About Services
Export/Import products with categories from Foks system.

Our platform can import product from one place to different marketplaces like (Rozetka, Promua, Wordpress, Opencart and others...).
We support over 450 clients from different countries (Europe, USA, Ukraine).

## Requires
> php 7.4

## Commands
`chmod 755 wp-content/plugins/foksImportExport/bin/console.php`

1. `php wp-content/plugins/foksImportExport/bin/console.php import-products`  - Import products
2. `php wp-content/plugins/foksImportExport/bin/console.php import-attributes` - Import attributes
3. `php wp-content/plugins/foksImportExport/bin/console.php export-products` - Export products
4. `php wp-content/plugins/foksImportExport/bin/console.php clear-products` - Remove all products

## Changelog

### 1.0.0
Init core with simple import.

### 1.1.0
Add export.

### 1.2.0
Add Cron.

### 2.0.0
Add settings ui interface.

### 2.1.0
Small fixes.

### 2.2.0
Update core api and support product variations.

### 3.0.0
New frontend logic
