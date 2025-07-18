<?php

declare(strict_types=1);

namespace TheFrosty\WpComposer;

/**
 * Class AbstractPlugin
 * @package TheFrosty\WpComposer
 */
abstract class AbstractPlugin
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
