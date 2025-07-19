<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TheFrosty\WpComposer\Contracts\Commands;
use function array_filter;
use function array_map;
use function array_merge;
use function explode;
use function in_array;
use function sanitize_text_field;
use function sprintf;

/**
 * Trait ComposerCommands
 * @package TheFrosty\WpComposer
 */
trait ComposerCommands
{

    public function install(string $flags): OutputInterface
    {
        $this->getComposer()->run(
            new ArrayInput(array_merge(['command' => __FUNCTION__], $this->getFlags($flags))),
            $output = new BufferedOutput()
        );
        return $output;
    }

    public function update(string $flags): OutputInterface
    {
        $this->getComposer()->run(
            new ArrayInput(array_merge(['command' => __FUNCTION__], $this->getFlags($flags))),
            $output = new BufferedOutput()
        );
        return $output;
    }

    public function require(string $args, string $flags): OutputInterface
    {
        if (empty($args)) {
            (new ConsoleOutput())->writeln('Error: Missing required argument');
        }
        $this->getComposer()->run(
            new ArrayInput(array_merge(['command' => __FUNCTION__], $this->getFlags($flags))),
            $output = new BufferedOutput()
        );
        return $output;
    }

    public function remove(string $args, string $flags): OutputInterface
    {
        if (empty($args)) {
            (new ConsoleOutput())->writeln('Error: Missing required argument');
        }
        $this->getComposer()->run(
            new ArrayInput(array_merge(['command' => __FUNCTION__], $this->getFlags($flags))),
            $output = new BufferedOutput()
        );
        return $output;
    }

    public function search(string $args, string $flags): OutputInterface
    {
        if (empty($args)) {
            (new ConsoleOutput())->writeln('Error: Missing required argument');
        }
        $this->getComposer()->run(
            new ArrayInput(
                array_merge(
                    ['command' => __FUNCTION__],
                    $this->getFlags($flags, [Commands::ARG_ONLY_NAME, Commands::ARG_ONLY_VENDOR])
                ),
            ),
            $output = new BufferedOutput()
        );
        return $output;
    }

    public function diagnose(): OutputInterface
    {
        $this->getComposer()->run(
            new ArrayInput(['command' => __FUNCTION__, Commands::ARG_NO_ANSI => true]),
            $output = new BufferedOutput()
        );
        return $output;
    }

    public function version(): OutputInterface
    {
        $output = new BufferedOutput();
        $output->writeln(sprintf('WpComposer version %s', esc_attr(WpComposer::VERSION)));
        $this->getComposer()->run(
            new ArrayInput([Commands::ARG_VERSION => true, Commands::ARG_NO_ANSI => true]),
            $output
        );
        return $output;
    }

    protected function getComposer(): WpComposer
    {
        return $this->composer;
    }

    protected function getFlags(string $flags, ?array $allowed = null): array
    {
        $_flags = array_map(static fn($str): string => sanitize_text_field($str), explode(',', $flags));
        if ($allowed !== null) {
            $_flags = array_filter($_flags, static fn($flag): bool => in_array($flag, $allowed, true));
        }

        return array_fill_keys($_flags, true);
    }
}
