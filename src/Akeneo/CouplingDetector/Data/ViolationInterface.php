<?php

namespace Akeneo\CouplingDetector\Data;

/**
 * A violation is raised when a node does not follow a rule.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface ViolationInterface
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';

    /**
     * @return RuleInterface
     */
    public function getRule();

    /**
     * @param RuleInterface $rule
     */
    public function setRule(RuleInterface $rule);

    /**
     * @return NodeInterface
     */
    public function getNode();

    /**
     * @param NodeInterface $node
     */
    public function setNode(NodeInterface $node);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param mixed $type
     */
    public function setType($type);

    /**
     * @return array
     */
    public function getTokenViolations();

    /**
     * @param array $tokenViolations
     */
    public function setTokenViolations(array $tokenViolations);
}
