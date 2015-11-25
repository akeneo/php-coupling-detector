<?php

namespace Akeneo\CouplingDetector\Domain;

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
     * @return NodeInterface
     */
    public function getNode();

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @return array
     */
    public function getTokenViolations();
}
