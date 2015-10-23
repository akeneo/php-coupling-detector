<?php

namespace Akeneo\CouplingDetector;

/**
 * Use violation rule
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class UseViolationRule
{
    /** @var string */
    protected $namespace;

    /** @var array */
    protected $forbiddenUses;

    /**
     * @param array $namespace
     * @param array $forbiddenUses
     */
    public function __construct($namespace, array $forbiddenUses)
    {
        $this->namespace = $namespace;
        $this->forbiddenUses = $forbiddenUses;
    }

    /**
     * @param $fullQualifiedClassName
     *
     * @return bool
     */
    public function match($fullQualifiedClassName)
    {
        return strpos($fullQualifiedClassName, $this->namespace) === 0;
    }

    /**
     * @param array $useDeclarations
     *
     * @return array
     */
    public function detect(array $useDeclarations)
    {
        $useViolations = [];
        foreach ($useDeclarations as $useFullName) {
            foreach ($this->forbiddenUses as $forbiddenUse) {
                if (strpos($useFullName, $forbiddenUse) !== false) {
                    $useViolations[] = $useFullName;
                    break;
                }
            }
        }

        return $useViolations;
    }
}
