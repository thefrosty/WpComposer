<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use Composer\Console\Application;
use Composer\Factory;
use Exception;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use const ABSPATH;

/**
 * Class WpComposer
 * @package TheFrosty\WpComposer
 */
class WpComposer
{

    public const string VERSION = '0.1.0';

    public function __construct(protected Application $application)
    {
    }

    public function run(?InputInterface $input = null, ?OutputInterface $output = null): ?int
    {
        try {
            $this->putEnv();
            $application = $this->application;
            $application->setAutoExit(false);
            return $application->run($input, $output);
        } catch (Exception $e) {
            return null;
        }
    }

    protected function putEnv(): void
    {
        $reflection = new ReflectionMethod(Factory::class, 'getHomeDir');
        $COMPOSER_HOME = $reflection->invoke(null);
        putenv(sprintf('COMPOSER_HOME=%s', $COMPOSER_HOME));
    }

    /**
     * See if we should include a path if they don't have a composer.json.
     * @param string $path
     * @return bool
     */
    private function isValid(string $path = ABSPATH): bool
    {
        return file_exists(trailingslashit($path) . 'composer.json');
    }
}
