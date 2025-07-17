<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer\Contract;

use TheFrosty\WpComposer\WpComposer;

/**
 * Trait ComposerTrait
 * @package TheFrosty\WpComposer
 */
trait Composer
{

    /**
     * @return WpComposer
     */
    protected function getComposer(): WpComposer
    {
        return $this->composer;
    }
}
