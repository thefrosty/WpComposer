# WordPress Composer

![WordPress Composer](.github/wp-composer.jpg?raw=true "WordPress Composer")

[![PHP from Packagist](https://img.shields.io/packagist/php-v/dwnload/wp-composer.svg)]()
[![Latest Stable Version](https://img.shields.io/packagist/v/dwnload/wp-composer.svg)](https://packagist.org/packages/dwnload/wp-composer)
[![Total Downloads](https://img.shields.io/packagist/dt/dwnload/wp-composer.svg)](https://packagist.org/packages/dwnload/wp-composer)
[![License](https://img.shields.io/packagist/l/dwnload/wp-composer.svg)](https://packagist.org/dwnload/wp-composer)
![Build Status](https://github.com/dwnload/wp-composer/actions/workflows/master.yml/badge.svg)

A PHP class abstraction for WordPress plugins to support Composer.

#### Installation

```bash
composer require dwnload/wp-composer
```

#### Additional requirements
Update your `scripts.post-update-cmd`:
```json
{
  "scripts": {
    "post-update-cmd": [
      "Dwnload\\WpComposer\\Composer\\Scripts::postUpdate"
    ]
  }
}
```

OR run: `composer config scripts.post-update-cmd.0 "Dwnload\\WpComposer\\Composer\\Scripts::postUpdate"`

