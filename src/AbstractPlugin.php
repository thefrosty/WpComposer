<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use TheFrosty\WpComposer\Contract\Composer;

/**
 * Class AbstractPlugin
 * @package TheFrosty\WpComposer
 */
abstract class AbstractPlugin
{

    use Composer;

    /**
     * AbstractPlugin constructor.
     * @param WpComposer $composer
     */
    public function __construct(private readonly WpComposer $composer)
    {
        //
    }
}
