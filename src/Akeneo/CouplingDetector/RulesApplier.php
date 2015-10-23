<?php

namespace Akeneo\CouplingDetector;

/**
 * Apply violation uses rules
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class RulesApplier
{
    /** @var array */
    protected $rules;

    /**
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param string $fullQualifiedClassName
     * @param array  $useDeclarations
     *
     * @return array
     */
    public function apply($fullQualifiedClassName, $useDeclarations)
    {
        $useViolations = [];
        foreach ($this->rules as $rule) {
            if ($rule->match($fullQualifiedClassName)) {
                $violations = $rule->detect($useDeclarations);
                if (count($violations) > 0) {
                    $useViolations = array_merge($useViolations, $violations);
                }
            }
        }

        return $useViolations;
    }
}
