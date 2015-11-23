<?php

namespace Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
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
            $parser = $this->nodeExtractorResolver->resolve($file);
            $nodes[] = $parser->parse($file);
        }

        return $nodes;
    }
}
