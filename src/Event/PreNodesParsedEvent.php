<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\Finder;

/**
 * Dispatched before nodes have been parsed.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class PreNodesParsedEvent extends Event
{
    /** @var Finder */
    private $finder;

    /**
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }
}
