<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\WpCli;

use Composer\Factory;
use ReflectionException;
use ReflectionMethod;
use TheFrosty\WpComposer\ComposerCommands;
use TheFrosty\WpComposer\WpComposer;
use WP_CLI;
use WP_CLI_Command;
use function putenv;
use function sprintf;

/**
 * Class Command
 * @package TheFrosty\WpComposer\WpCli
 */
abstract class Command extends WP_CLI_Command
{

    public const string NAME = 'composer';

    use ComposerCommands;

    public function __construct(private readonly WpComposer $composer)
    {
        parent::__construct();
        $this->putEnv();
    }

    protected function putEnv(): void
    {
        try {
            $reflection = new ReflectionMethod(Factory::class, 'getHomeDir');
            $COMPOSER_HOME = $reflection->invoke(null);
            putenv(sprintf('COMPOSER_HOME=%s', $COMPOSER_HOME));
        } catch (ReflectionException $e) {
            WP_CLI::error($e->getMessage());
        }
    }
}
