<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

use TheFrosty\WpComposer\Contracts\Commands;

/**
 * Class AbstractCommands
 * @package TheFrosty\WpComposer
 */
abstract class AbstractCommands implements Commands
{

    use ComposerCommands;

    /**
     * AbstractPlugin constructor.
     * @param WpComposer $composer
     */
    public function __construct(private readonly WpComposer $composer)
    {
        //
    }
}
