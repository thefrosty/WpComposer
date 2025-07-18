<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TheFrosty\WpComposer\Contracts\Commands;
use WP_CLI;
use function array_filter;
use function defined;
use function sprintf;
use const ARRAY_FILTER_USE_KEY;

/**
 * Trait ComposerCommands
 * @package TheFrosty\WpComposer
 */
trait ComposerCommands
{

    public function install($args, $assoc_args): void
    {
        $input = new ArrayInput(['command' => 'install']);
        if (!$this->doRecursive($input, new BufferedOutput(), $assoc_args)) {
            $this->getComposer()->run($input, $output = new BufferedOutput());
            $this->log('success', $output->fetch());
        }
    }

    public function update($args, $assoc_args): void
    {
        $input = new ArrayInput(['command' => 'update']);
        if (!$this->doRecursive($input, new BufferedOutput(), $assoc_args)) {
            $this->getComposer()->run($input, $output = new BufferedOutput());
            $this->log('success', $output->fetch());
        }
    }

    public function require($args, $assoc_args): void
    {
        if (empty($args[0])) {
            $this->log('error', 'Missing required argument');
        }
        $dev = $assoc_args[Commands::ARG_DEV] ?? null;
        $input = new ArrayInput(
            array_filter(
                ['command' => 'require', (!$dev ? null : '--dev') => true],
                static fn(mixed $key): bool => !empty($key),
                ARRAY_FILTER_USE_KEY
            )
        );
        $this->getComposer()->run($input, $output = new BufferedOutput());
        $this->log('success', $output->fetch());
    }

    public function remove($args, $assoc_args): void
    {
        if (empty($args[0])) {
            $this->log('error', 'Missing required argument');
        }
        $dev = $assoc_args[Commands::ARG_DEV] ?? null;
        $input = new ArrayInput(
            array_filter(
                ['command' => 'remove', (!$dev ? null : '--dev') => true],
                static fn(mixed $key): bool => !empty($key),
                ARRAY_FILTER_USE_KEY
            )
        );
        $this->getComposer()->run($input, $output = new BufferedOutput());
        $this->log('success', $output->fetch());
    }

    public function diagnose($args, $assoc_args): void
    {
        $input = new ArrayInput(['command' => 'diagnose']);
        if (!$this->doRecursive($input, assoc_args: $assoc_args)) {
            $this->getComposer()->run($input, $output = new BufferedOutput());
            $this->log('success', $output->fetch());
        }
    }

    public function version(): void
    {
        $this->log('line', sprintf('WpComposer version %s', esc_attr(WpComposer::VERSION)));
        $this->getComposer()->run(new ArrayInput(['-V' => true, '--ansi' => true]), $output = new BufferedOutput());
        $this->log('line', $output->fetch());
    }

    protected function log(string $method, string $message): void
    {
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::$method($message);
        }

        // Do something when not inside CLI.
    }

    protected function getComposer(): WpComposer
    {
        return $this->composer;
    }
}
