<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\Contracts;

/**
 * Interface Commands
 * @package TheFrosty\WpComposer
 */
interface Commands
{

    public const string ARG_DEV = 'dev';
    public const string ARG_EXCLUDE = 'exclude';
    public const string ARG_RECURSIVE = 'recursive';

    public function install($args, $assoc_args): void;

    public function update($args, $assoc_args): void;

    public function require($args, $assoc_args): void;

    public function remove($args, $assoc_args): void;

    public function diagnose($args, $assoc_args): void;

    public function version(): void;
}
