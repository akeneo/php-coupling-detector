<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Domain;

/**
 * A violation is raised when a node does not follow a rule.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Violation implements ViolationInterface
{
    /** @var RuleInterface */
    private $rule;

    /** @var NodeInterface */
    private $node;

    /** @var string */
    private $type;

    /** @var array */
    private $tokenViolations = array();

    /**
     * Violation constructor.
     *
     * @param NodeInterface $node
     * @param RuleInterface $rule
     * @param array         $tokenViolations
     * @param string        $type
     */
    public function __construct(NodeInterface $node, RuleInterface $rule, array $tokenViolations, $type)
    {
        $this->node = $node;
        $this->rule = $rule;
        $this->tokenViolations = $tokenViolations;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * {@inheritdoc}
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenViolations()
    {
        return $this->tokenViolations;
    }
}
