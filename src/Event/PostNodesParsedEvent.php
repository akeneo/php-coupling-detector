<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Symfony\Contracts\EventDispatcher\Event;

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
    public function getNodes(): array
    {
        return $this->nodes;
    }
}
