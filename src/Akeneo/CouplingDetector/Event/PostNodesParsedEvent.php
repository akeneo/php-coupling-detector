<?php

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched when all nodes have been parsed.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class PostNodesParsedEvent extends Event
{
    /** @var NodeInterface[] */
    private $nodes;

    /**
     * @param NodeInterface[] $nodes
     */
    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @return NodeInterface[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }
}
