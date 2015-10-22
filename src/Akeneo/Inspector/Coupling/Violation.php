<?php

namespace Akeneo\Inspector\Coupling;

/**
 * Coupling violation
 *
 * @author Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Violation
{
    /** @var array */
    protected $forbiddenUses;

    /** @var array */
    protected $forbiddenUsesCounter;

    /**
     * @param array $forbiddenUses
     * @param array $forbiddenUsesCounter
     */
    public function __construct(array $forbiddenUses, array $forbiddenUsesCounter)
    {
        $this->forbiddenUses = $forbiddenUses;
        $this->forbiddenUsesCounter = $forbiddenUsesCounter;
    }

    /**
     * @return array
     */
    public function getSortedForbiddenUsesCounter()
    {
        arsort($this->forbiddenUsesCounter);
        return $this->forbiddenUsesCounter;
    }
}