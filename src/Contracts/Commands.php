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

    public function install(string $flags): OutputInterface;

    public function update(string $flags): OutputInterface;

    public function require(string $args, string $flags): OutputInterface;

    public function remove(string $args, string $flags): OutputInterface;

    public function diagnose(): OutputInterface;

    public function version(): OutputInterface;
}
