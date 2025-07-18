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

use Composer\Console\Application;
use ReflectionMethod;
use TheFrosty\WpComposer\WpCli\WpCliCommand;
use WP_CLI;
use function defined;

defined('ABSPATH') || exit;

add_action('init', static function (): void {
    $plugin = new WpPlugin(new WpComposer(new Application()));
    // Get ready-to-do something with the $plugin instance.

    add_action('cli_init', static function () use ($plugin): void {
        $composer = new ReflectionMethod($plugin, 'getComposer');
        WP_CLI::add_command(WpCli\Command::NAME, new WpCliCommand($composer->invoke($plugin)));
    });
}, 2, 0);
