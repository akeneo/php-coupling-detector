<?php

namespace Akeneo\CouplingDetector\Configuration;

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

    /** @var Finder */
    private $finder;

    /**
     * Configuration constructor.
     *
     * @param RuleInterface[] $rules
     * @param Finder          $finder
     */
    public function __construct(array $rules, Finder $finder = null)
    {
        if (null === $finder) {
            $finder = new DefaultFinder();
        }

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
