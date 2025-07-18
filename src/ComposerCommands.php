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
        $this->getComposer()->run(new ArrayInput(['command' => 'install']), $output = new BufferedOutput());
        return $output;
    }

    public function update(string $flags): OutputInterface
    {
        $this->getComposer()->run(new ArrayInput(['command' => 'update']), $output = new BufferedOutput());
        return $output;
    }

    public function require(string $args, string $flags): OutputInterface
    {
        if (empty($args)) {
            (new ConsoleOutput())->writeln('Error: Missing required argument');
        }
        $_flags = array_map(static fn($str): string => sanitize_text_field($str), explode(',', $flags));
        $input = new ArrayInput(
            array_filter(array_merge(['command' => 'require'], $_flags))
        );
        $this->getComposer()->run($input, $output = new BufferedOutput());
        return $output;
    }

    public function remove(string $args, string $flags): OutputInterface
    {
        if (empty($args)) {
            (new ConsoleOutput())->writeln('Error: Missing required argument');
        }
        $_flags = array_map(static fn($str): string => sanitize_text_field($str), explode(',', $flags));
        $input = new ArrayInput(
            array_filter(array_merge(['command' => 'require'], $_flags))
        );
        $this->getComposer()->run($input, $output = new BufferedOutput());
        return $output;
    }

    public function diagnose(): OutputInterface
    {
        $this->getComposer()->run(new ArrayInput(['command' => 'diagnose']), $output = new BufferedOutput());
        return $output;
    }

    public function version(): OutputInterface
    {
        (new ConsoleOutput())->writeln(sprintf('WpComposer version %s', esc_attr(WpComposer::VERSION)));
        $this->getComposer()->run(new ArrayInput(['-V' => true, '--ansi' => true]), $output = new BufferedOutput());
        return $output;
    }

    protected function getComposer(): WpComposer
    {
        return $this->composer;
    }
}
