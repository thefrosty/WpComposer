<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\WpCli;

use TheFrosty\WpComposer\Commands;
use TheFrosty\WpComposer\ComposerCommands;
use TheFrosty\WpComposer\WpComposer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use WP_CLI;
use function array_filter;
use function array_flip;
use function array_key_last;
use function esc_attr;
use function explode;
use function method_exists;
use function sprintf;
use const ARRAY_FILTER_USE_KEY;

/**
 * Class WpCliCommand
 * @package TheFrosty\WpComposer
 * ## OPTIONS
 * [--recursive]
 * : Run composer $command in all wp-content plugins and themes directories.
 * ---
 * default: null
 * [--exclude=<directory>]
 * : Exclude composer $command in plugins or themes directories.
 * ---
 * default: null
 * ---
 */
class WpCliCommand extends Command implements Commands
{

    use ComposerCommands;

    protected function doRecursive(
        InputInterface $input,
        ?OutputInterface $output = null,
        ?array $assoc_args = null
    ): ?true {
        $exclude = $assoc_args[Commands::ARG_EXCLUDE] ?? null;
        if (filter_var($assoc_args[self::ARG_RECURSIVE] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $this->getComposer()->recursiveExecution(
                function (string $path, array $data, bool $is_plugin, bool $is_theme) use (
                    $output,
                    $exclude,
                    $input
                ): void {
                    $paths = array_flip(array_filter(explode('/', $path)));
                    WP_CLI::line(sprintf('Starting to process %s', array_key_last($paths)));
                    if (is_string($exclude)) {
                        if ($exclude === 'plugins' && $is_plugin) {
                            WP_CLI::line(sprintf('Skipping %s', array_key_last($paths)));
                            return;
                        }
                        if ($exclude === 'themes' && $is_theme) {
                            WP_CLI::line(sprintf('Skipping %s', array_key_last($paths)));
                            return;
                        }
                        if (in_array($exclude, ['all', 'both'], true)) {
                            return;
                        }
                    }
                    $this->getComposer()->run($input, $output);
                    if (method_exists($output, 'fetch')) {
                        WP_CLI::line($output->fetch());
                    }
                }
            );
            return true;
        }

        return null;
    }
}
