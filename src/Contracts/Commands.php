<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\Contracts;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface Commands
 * @package TheFrosty\WpComposer
 */
interface Commands
{

    public const string ARG_NO_ANSI = '--no-ansi';
    public const string ARG_ONLY_NAME = '--only-name';
    public const string ARG_ONLY_VENDOR = '--only-vendor';
    public const string ARG_VERSION = '--version';

    public function install(string $flags): OutputInterface;

    public function update(string $flags): OutputInterface;

    public function require(string $args, string $flags): OutputInterface;

    public function remove(string $args, string $flags): OutputInterface;

    public function search(string $args, string $flags): OutputInterface;

    public function diagnose(): OutputInterface;

    public function version(): OutputInterface;
}
