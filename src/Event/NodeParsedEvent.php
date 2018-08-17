<?php

declare(strict_types=1);

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

    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    public function getNode(): NodeInterface
    {
        return $this->node;
    }
}
