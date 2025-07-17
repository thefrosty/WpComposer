<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\WpCli;

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
class WpCliCommand extends Command
{

    public const string ARG_DEV = 'dev';
    public const string ARG_EXCLUDE = 'exclude';
    public const string ARG_RECURSIVE = 'recursive';

    public function install($args, $assoc_args): void
    {
        $input = new ArrayInput(['command' => 'install']);
        if (!$this->doRecursive($input, new BufferedOutput(), $assoc_args)) {
            $this->getComposer()->run($input, $output = new BufferedOutput());
            WP_CLI::success($output->fetch());
        }
    }

    public function update($args, $assoc_args): void
    {
        $input = new ArrayInput(['command' => 'update']);
        if (!$this->doRecursive($input, new BufferedOutput(), $assoc_args)) {
            $this->getComposer()->run($input, $output = new BufferedOutput());
            WP_CLI::success($output->fetch());
        }
    }

    public function require($args, $assoc_args): void
    {
        if (empty($args[0])) {
            WP_CLI::error('Missing required argument');
        }
        $dev = $assoc_args[self::ARG_DEV] ?? null;
        $input = new ArrayInput(
            array_filter(
                ['command' => 'require', (!$dev ? null: '--dev') => true],
                static fn(mixed $key): bool => !empty($key),
                ARRAY_FILTER_USE_KEY
            )
        );
        $this->getComposer()->run($input, $output = new BufferedOutput());
        WP_CLI::success($output->fetch());
    }

    public function remove($args, $assoc_args): void
    {
        if (empty($args[0])) {
            WP_CLI::error('Missing required argument');
        }
        $dev = $assoc_args[self::ARG_DEV] ?? null;
        $input = new ArrayInput(
            array_filter(
                ['command' => 'remove', (!$dev ? null: '--dev') => true],
                static fn(mixed $key): bool => !empty($key),
                ARRAY_FILTER_USE_KEY
            )
        );
        $this->getComposer()->run($input, $output = new BufferedOutput());
        WP_CLI::success($output->fetch());
    }

    public function diagnose($args, $assoc_args): void
    {
        $input = new ArrayInput(['command' => 'diagnose']);
        if (!$this->doRecursive($input, assoc_args: $assoc_args)) {
            $this->getComposer()->run($input, $output = new BufferedOutput());
            WP_CLI::success($output->fetch());
        }
    }

    public function version(): void
    {
        WP_CLI::line(
            sprintf(
                '%s version %s',
                esc_attr(WP_CLI::colorize('%gWpComposer%n')),
                esc_attr(WP_CLI::colorize('%y' . WpComposer::VERSION . '%n'))
            )
        );
        $this->getComposer()->run(new ArrayInput(['-V' => true, '--ansi' => true]), $output = new BufferedOutput());
        WP_CLI::line($output->fetch());
    }

    protected function doRecursive(
        InputInterface $input,
        ?OutputInterface $output = null,
        ?array $assoc_args = null
    ): ?true {
        $exclude = $assoc_args[self::ARG_EXCLUDE] ?? null;
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
