<?php

namespace Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Data\NodeInterface;
use Akeneo\CouplingDetector\Data\RuleInterface;
use Akeneo\CouplingDetector\Data\ViolationInterface;
use Akeneo\CouplingDetector\NodeExtractor\NodeExtractorResolver;
use Symfony\Component\Finder\Finder;

/**
 * Detect the coupling errors of the nodes that are found among a set of rules.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class CouplingDetector
{
    /** @var NodeExtractorResolver */
    private $nodeExtractorResolver;

    /** @var RuleChecker */
    private $ruleChecker;

    /**
     * CouplingDetector constructor.
     *
     * @param NodeExtractorResolver $resolver
     * @param RuleChecker           $checker
     */
    public function __construct(NodeExtractorResolver $resolver, RuleChecker $checker)
    {
        $this->nodeExtractorResolver = $resolver;
        $this->ruleChecker = $checker;
    }

    /**
     * Detect the coupling errors of the nodes that are found among a set of rules.
     *
     * @param Finder          $finder
     * @param RuleInterface[] $rules
     *
     * @return ViolationInterface[]
     */
    public function detect(Finder $finder, array $rules)
    {
        $nodes = $this->parseNodes($finder);
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
     * @param Finder $finder
     *
     * @return NodeInterface[]
     */
    private function parseNodes(Finder $finder)
    {
        $nodes = [];
        foreach ($finder as $file) {
            $extractor = $this->nodeExtractorResolver->resolve($file);
            $nodes[] = $extractor->extract($file);
        }

       return $nodes;
    }
}
