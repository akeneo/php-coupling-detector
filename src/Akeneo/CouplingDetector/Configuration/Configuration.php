<?php

namespace Akeneo\CouplingDetector\Configuration;

use Akeneo\CouplingDetector\Data\RuleInterface;
use Symfony\Component\Finder\Finder;

/**
 * Configuration DTO that will be used by the detect command.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Configuration
{
    /** @var RuleInterface[] */
    private $rules;

    /** @var Finder */
    private $finder;

    /**
     * Configuration constructor.
     *
     * @param Finder          $finder
     * @param RuleInterface[] $rules
     */
    public function __construct(Finder $finder, array $rules)
    {
        $this->finder = $finder;
        $this->rules = $rules;
    }

    /**
     * @return RuleInterface[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }
}
