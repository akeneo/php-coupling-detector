<?php

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched when a node has been parsed.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class NodeParsedEvent extends Event
{
    /** @var NodeInterface */
    private $node;

    /**
     * NodeParsedEvent constructor.
     *
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
}
