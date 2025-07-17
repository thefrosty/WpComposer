<?php

declare(strict_types=1);

/**
 * Plugin Name: WP Composer
 * Plugin URI: https://github.com/dwnload/WpComposer
 * Description: Adding Composer dependency management to WordPress.
 * Version: 0.1.0
 * Author: Austin Passy
 * Author URI: https://austin.passy.co
 */

namespace Dwnload\WpComposer;

use Composer\Console\Application;
use Dwnload\WpComposer\WpCli\WpCliCommand;
use WP_CLI;
use function defined;

defined('ABSPATH') || exit;

add_action('cli_init', static function (): void {
    WP_CLI::add_command(WpCli\Command::NAME, new WpCliCommand(new WpComposer(new Application())));
});
