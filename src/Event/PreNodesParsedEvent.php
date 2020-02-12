<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

use Symfony\Contracts\EventDispatcher\Event;
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

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function getFinder(): Finder
    {
        return $this->finder;
    }
}
