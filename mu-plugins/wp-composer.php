<?php

declare(strict_types=1);

/**
 * Plugin Name: WP Composer
 * Plugin URI: https://github.com/thefrosty/WpComposer
 * Description: Adding Composer dependency management to WordPress.
 * Version: 0.1.0
 * Author: Austin Passy
 * Author URI: https://austin.passy.co
 */

namespace TheFrosty\WpComposer;

use TheFrosty\WpComposer\Composer\Process;
use TheFrosty\WpComposer\WpAdmin\Dashboard;
use TheFrosty\WpUtilities\Plugin\PluginFactory;
use function defined;

defined('ABSPATH') || exit;

$plugin = PluginFactory::create('wp-composer-ui', __FILE__);
$plugin
    ->add(new Process())
    ->addOnHook(Dashboard::class, 'load-index.php', 10, true)
    ->initialize();
