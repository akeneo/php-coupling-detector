<?php


namespace Akeneo\Inspector\Coupling;

/**
 * Coupling violations filter
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class UseViolationsFilter
{
    /** @var array */
    protected $excludedUses;

    /**
     * @param array $excludedUses
     */
    public function __construct(array $excludedUses = [])
    {
        $this->excludedUses = $excludedUses;
    }

    /**
     * @param UseViolations $violations
     *
     * @return UseViolations
     */
    public function filter(UseViolations $violations)
    {
        if (empty($this->excludedUses)) {
            return $violations;
        }
        $filteredFqcnUses = $violations->getFullQualifiedClassNameViolations();
        foreach ($filteredFqcnUses as $className => $uses) {
            foreach ($uses as $useIndex => $use) {
                if (in_array($use, $this->excludedUses)) {
                    unset($filteredFqcnUses[$className][$useIndex]);
                }
            }
        }

        return new UseViolations($filteredFqcnUses);
    }
}