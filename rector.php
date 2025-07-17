<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return RectorConfig::configure()
    ->withAutoloadPaths([
        __DIR__ . 'vendor/php-stubs/wordpress-stubs/wordpress-stubs.php',
    ])
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_83,
    ]);
