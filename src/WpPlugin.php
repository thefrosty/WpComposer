<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class WpPlugin
 * @package TheFrosty\WpComposer
 */
class WpPlugin extends AbstractPlugin
{

    /**
     * @return int 0 if everything went fine, or an error code
     * @throws \Exception
     */
    public function install(): int
    {
        $application = $this->getComposer()->getApp();
        $application->setAutoExit(false);

        return $application->run(new ArrayInput(['command' => 'install']));
    }
}
