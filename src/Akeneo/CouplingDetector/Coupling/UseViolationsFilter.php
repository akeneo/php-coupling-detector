<?php


namespace Akeneo\CouplingDetector\Coupling;

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
        $fqcnViolationUses = $violations->getFullQualifiedClassNameViolations();
        foreach ($fqcnViolationUses as $className => $forbiddenUses) {
            $excludedUses = $this->getExcludedUses($className);
            foreach ($forbiddenUses as $useIndex => $use) {
                foreach ($excludedUses as $excludedUse) {
                    if (0 === strpos($use, $excludedUse)) {
                        unset($fqcnViolationUses[$className][$useIndex]);
                        break;
                    }
                }
            }
        }

        return new UseViolations($fqcnViolationUses);
    }

    /**
     * @param string $className
     *
     * @return array
     */
    protected function getExcludedUses($className)
    {
        $aggregatedUses = [];
        foreach ($this->excludedUses as $namespace => $uses) {
            if (0 === strpos($className, $namespace)) {
                $aggregatedUses = array_merge($aggregatedUses, $uses);
            }
        }

        return $aggregatedUses;
    }
}