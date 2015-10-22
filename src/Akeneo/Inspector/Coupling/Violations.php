<?php

namespace Akeneo\Inspector\Coupling;

/**
 * Coupling violations
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Violations
{
    /** @var array */
    protected $namespace;

    /** @var array */
    protected $forbiddenUses;

    /** @var array */
    protected $forbiddenUsesCounter;

    /**
     * @param array $forbiddenUses
     * @param array $forbiddenUsesCounter
     * @param string $namespace
     */
    public function __construct(array $forbiddenUses, array $forbiddenUsesCounter, $namespace)
    {
        $this->forbiddenUses = $forbiddenUses;
        $this->forbiddenUsesCounter = $forbiddenUsesCounter;
        $this->namespace = $namespace;
    }

    /**
     * @return array
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getSortedForbiddenUsesCounter()
    {
        arsort($this->forbiddenUsesCounter);

        return $this->forbiddenUsesCounter;
    }

    /**
     * @return array
     */
    public function getForbiddenUses()
    {
        return $this->forbiddenUses;
    }
}