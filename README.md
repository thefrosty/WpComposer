# WordPress Composer UI

![WordPress Composer UI](.github/wp-composer-ui.jpg?raw=true "WordPress Composer UI")

[![PHP from Packagist](https://img.shields.io/packagist/php-v/thefrosty/wp-composer.svg)]()
[![Latest Stable Version](https://img.shields.io/packagist/v/thefrosty/wp-composer.svg)](https://packagist.org/packages/thefrosty/wp-composer)
[![Total Downloads](https://img.shields.io/packagist/dt/thefrosty/wp-composer.svg)](https://packagist.org/packages/thefrosty/wp-composer)
[![License](https://img.shields.io/packagist/l/thefrosty/wp-composer.svg)](https://packagist.org/thefrosty/wp-composer)
![Build Status](https://github.com/thefrosty/WpComposer/actions/workflows/main.yml/badge.svg)

A PHP class abstraction for WordPress plugins to support Composer.

#### Installation

```bash
composer require thefrosty/wp-composer
```

#### Additional requirements
Update your `scripts.post-update-cmd`:
```json
{
  "scripts": {
    "post-update-cmd": [
      "TheFrosty\\WpComposer\\Composer\\Scripts::postUpdate"
    ]
  }
}
```

OR run: `composer config scripts.post-update-cmd.0 "TheFrosty\\WpComposer\\Composer\\Scripts::postUpdate"`

