<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched when a node have been checked for a rule.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class NodeChecked extends Event
{
    /** @var NodeInterface */
    private $node;

    /** @var RuleInterface */
    private $rule;

    /** @var ViolationInterface|null */
    private $violation;

    public function __construct(NodeInterface $node, RuleInterface $rule, ?ViolationInterface $violation)
    {
        $this->node = $node;
        $this->rule = $rule;
        $this->violation = $violation;
    }

    public function getNode(): NodeInterface
    {
        return $this->node;
    }

    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    public function getViolation(): ?ViolationInterface
    {
        return $this->violation;
    }
}
