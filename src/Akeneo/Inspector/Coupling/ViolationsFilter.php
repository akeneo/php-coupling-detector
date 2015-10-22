<?php


namespace Akeneo\Inspector\Coupling;

/**
 * Coupling violations
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class ViolationsFilter
{
    /** @var array */
    protected $excludedUses;

    /**
     * @param array $excludedUses
     */
    public function __construct(array $excludedUses)
    {
        $this->excludedUses = $excludedUses;
    }

    /**
     * @param Violations $violations
     *
     * @return Violations
     */
    public function filter(Violations $violations)
    {
        $namespace = $violations->getNamespace();
        if (!isset($this->excludedUses[$namespace])) {
            return $violations;
        }

        $namespaceExcludedUses = isset($this->excludedUses[$namespace]) ? $this->excludedUses[$namespace] : [];
        $filteredUseCounter = $violations->getSortedForbiddenUsesCounter();

        $filteredUses = $violations->getForbiddenUses(); // TODO useless ! ?

        foreach (array_keys($filteredUseCounter) as $use) {
            if (in_array($use, $namespaceExcludedUses)) {
                unset($filteredUseCounter[$use]);
            }
        }

        return new Violations($filteredUses, $filteredUseCounter, $namespace);
    }
}