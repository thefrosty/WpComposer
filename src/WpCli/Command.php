<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\WpCli;

use TheFrosty\WpComposer\WpComposer;
use TheFrosty\WpComposer\Contract\Composer;
use ReflectionException;
use ReflectionMethod;
use WP_CLI;
use WP_CLI_Command;
use function putenv;
use function sprintf;
use Composer\Factory;

/**
 * Class Command
 * @package TheFrosty\WpComposer\WpCli
 */
abstract class Command extends WP_CLI_Command
{

    public const string NAME = 'composer';

    use Composer;

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
