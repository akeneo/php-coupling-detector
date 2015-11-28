<?php

namespace Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\Violation;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\Domain\ExclusionInterface;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Symfony\Component\Finder\Finder;

/**
 * Detect the coupling errors of the nodes that are found among a set of rules.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class CouplingDetector
{
    const VERSION = 'master';

    /** @var NodeParserResolver */
    private $nodeExtractorResolver;

    /** @var RuleChecker */
    private $ruleChecker;

    /**
     * CouplingDetector constructor.
     *
     * @param NodeParserResolver $resolver
     * @param RuleChecker        $checker
     */
    public function __construct(NodeParserResolver $resolver, RuleChecker $checker)
    {
        $this->nodeExtractorResolver = $resolver;
        $this->ruleChecker = $checker;
    }

    /**
     * Detect the coupling errors of the nodes that are found among a set of rules.
     *
     * @param Finder               $finder
     * @param RuleInterface[]      $rules
     * @param ExclusionInterface[] $exclusions
     *
     * @return ViolationInterface[]
     */
    public function detect(Finder $finder, array $rules, array $exclusions = [])
    {
        $nodes = $this->parseNodes($finder);
        $violations = $this->detectViolations($rules, $nodes);
        $violations = $this->filterViolations($violations, $exclusions);

        return $violations;
    }

    /**
     * @param RuleInterface[] $rules
     * @param NodeInterface[] $nodes
     *
     * @return ViolationInterface[]
     */
    protected function detectViolations(array $rules, array $nodes)
    {
        $violations = [];
        foreach ($rules as $rule) {
            foreach ($nodes as $node) {
                if (null !== $violation = $this->ruleChecker->check($rule, $node)) {
                    $violations[] = $violation;
                }
            }
        }

        return $violations;
    }

    /**
     * @param ViolationInterface[] $violations
     * @param ExclusionInterface[] $exclusions
     *
     * @return ViolationInterface[]
     */
    protected function filterViolations(array $violations, array $exclusions)
    {
        // TODO: following lines to demonstrate the algo in one place
        //       it should be obviously extracted in many other classes (ViolationsFilter?)
        foreach ($violations as $violationIdx => $violation) {
            $violationSubject = $violation->getNode()->getSubject();
            foreach ($exclusions as $exclusion) {
                // this exclusion is eligible for this violation
                if (false !== strpos($violationSubject, $exclusion->getSubject())) {
                    $tokenViolations = $violation->getTokenViolations();
                    $excludedRequirements = $exclusion->getExcludedRequirements();
                    // we filter violation tokens (FQCN) with excluded requirements (FQCN)
                    foreach ($tokenViolations as $tokenIdx => $tokenFQCN) {
                        foreach ($excludedRequirements as $excludedFQCN) {
                            if ($tokenFQCN === $excludedFQCN) {
                                unset($tokenViolations[$tokenIdx]);
                            }
                        }
                    }
                    // we remove violation if all token have been excluded
                    if (0 === count($tokenViolations)) {
                        unset($violations[$violationIdx]);
                    // we create a new violation to replace the filtered one (violations are immutable)
                    } else {
                        $violations[$violationIdx] = new Violation(
                            $violation->getNode(),
                            $violation->getRule(),
                            $tokenViolations,
                            $violation->getType()
                        );
                    }
                }
            }
        }

        return $violations;
    }

    /**
     * @param Finder $finder
     *
     * @return NodeInterface[]
     */
    private function parseNodes(Finder $finder)
    {
        $nodes = [];
        foreach ($finder as $file) {
            $parser = $this->nodeExtractorResolver->resolve($file);
            $nodes[] = $parser->parse($file);
        }

        return $nodes;
    }
}
