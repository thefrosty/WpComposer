<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use function array_filter;
use function array_map;
use function array_merge;
use function count;
use function explode;
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

    public function diagnose(): OutputInterface
    {
        $this->getComposer()->run(new ArrayInput(['command' => 'diagnose']), $output = new BufferedOutput());
        return $output;
    }

    public function version(): OutputInterface
    {
        $output = new BufferedOutput();
        $output->writeln(sprintf('WpComposer version %s', esc_attr(WpComposer::VERSION)));
        $this->getComposer()->run(new ArrayInput(['-V' => true, '--no-ansi' => true]), $output);
        return $output;
    }

    protected function getFlags(string $flags): array
    {
        $_flags = array_map(static fn($str): string => sanitize_text_field($str), explode(',', $flags));

        return array_fill_keys($_flags, true);
    }

    protected function getComposer(): WpComposer
    {
        return $this->composer;
    }
}
