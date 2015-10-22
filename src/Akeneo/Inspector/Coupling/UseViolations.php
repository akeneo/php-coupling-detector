<?php

namespace Akeneo\Inspector\Coupling;

/**
 * Class UseViolations, allows to store import use violations, for instance when a class import the use of a
 * forbidden class for its namespace
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class UseViolations
{
    /** @var array */
    protected $fqcnUses;

    /**
     * @param array $fqcnUses
     */
    public function __construct(array $fqcnUses)
    {
        $this->fqcnUses = $fqcnUses;
    }

    /**
     * @return array
     */
    public function getFullQualifiedClassNameViolations()
    {
        return $this->fqcnUses;
    }

    /**
     * @return array
     */
    public function getSortedForbiddenUsesCounters()
    {
        $forbiddenUsesCounter = [];
        foreach ($this->fqcnUses as $className => $uses) {
            foreach ($uses as $useFullName) {
                if (!isset($forbiddenUsesCounter[$useFullName])) {
                    $forbiddenUsesCounter[$useFullName] = 1;
                } else {
                    $forbiddenUsesCounter[$useFullName]++;
                }
            }
        }
        arsort($forbiddenUsesCounter);

        return $forbiddenUsesCounter;
    }
}
