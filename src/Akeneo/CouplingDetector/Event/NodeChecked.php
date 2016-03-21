<?php

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Symfony\Component\EventDispatcher\Event;

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

    /** @var ViolationInterface */
    private $violation;

    /**
     * NodeChecked constructor.
     *
     * @param NodeInterface           $node
     * @param RuleInterface           $rule
     * @param ViolationInterface|null $violation
     */
    public function __construct(NodeInterface $node, RuleInterface $rule, ViolationInterface $violation = null)
    {
        $this->node = $node;
        $this->rule = $rule;
        $this->violation = $violation;
    }

    /**
     * @return NodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return ViolationInterface
     */
    public function getViolation()
    {
        return $this->violation;
    }
}
