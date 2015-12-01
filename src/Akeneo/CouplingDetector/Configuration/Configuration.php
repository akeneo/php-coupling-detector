<?php

namespace Akeneo\CouplingDetector\Configuration;

use Akeneo\CouplingDetector\Domain\ExclusionInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
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

    /** @var ExclusionInterface[] */
    private $exclusions;

    /** @var Finder */
    private $finder;

    /**
     * Configuration constructor.
     *
     * @param Finder               $finder
     * @param RuleInterface[]      $rules
     * @param ExclusionInterface[] $exclusions
     */
    public function __construct(Finder $finder, array $rules, array $exclusions = [])
    {
        $this->finder = $finder;
        $this->rules = $rules;
        $this->exclusions = $exclusions;
    }

    /**
     * @return RuleInterface[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return ExclusionInterface[]
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }

    /**
     * @return Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }
}
