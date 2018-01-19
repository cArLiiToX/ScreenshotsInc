# m2-weltpixel-google-cards

### Installation

Dependencies:
 - m2-weltpixel-backend

With composer:

```sh
$ composer config repositories.welpixel-m2-weltpixel-google-cards git git@github.com:rusdragos/m2-weltpixel-google-cards.git
$ composer require weltpixel/m2-weltpixel-google-cards:dev-master
```

Manually:

Copy the zip into app/code/WeltPixel/GoogleCards directory


#### After installation by either means, enable the extension by running following commands:

```sh
$ php bin/magento module:enable WeltPixel_GoogleCards --clear-static-content
$ php bin/magento setup:upgrade
```