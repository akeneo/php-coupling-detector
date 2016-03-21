<?php

namespace Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\Event\PostNodesParsedEvent;
use Akeneo\CouplingDetector\Event\Events;
use Akeneo\CouplingDetector\Event\NodeChecked;
use Akeneo\CouplingDetector\Event\NodeParsedEvent;
use Akeneo\CouplingDetector\Event\PreNodesParsedEvent;
use Akeneo\CouplingDetector\Event\PreRulesCheckedEvent;
use Akeneo\CouplingDetector\Event\RuleCheckedEvent;
use Akeneo\CouplingDetector\Event\PostRulesCheckedEvent;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    private $nodeParserResolver;

    /** @var RuleChecker */
    private $ruleChecker;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * CouplingDetector constructor.
     *
     * @param NodeParserResolver       $resolver
     * @param RuleChecker              $checker
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        NodeParserResolver $resolver,
        RuleChecker $checker,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->nodeParserResolver = $resolver;
        $this->ruleChecker = $checker;
        $this->eventDispatcher = $eventDispatcher;
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
        $violations = array();

        $this->eventDispatcher->dispatch(Events::PRE_RULES_CHECKED, new PreRulesCheckedEvent($rules));

        foreach ($rules as $rule) {
            $ruleViolations = array();

            foreach ($nodes as $node) {
                $violation = $this->ruleChecker->check($rule, $node);
                if (null !== $violation) {
                    $ruleViolations[] = $violation;
                }

                $this->eventDispatcher->dispatch(
                    Events::NODE_CHECKED,
                    new NodeChecked($node, $rule, $violation)
                );
            }

            $this->eventDispatcher->dispatch(
                Events::RULE_CHECKED,
                new RuleCheckedEvent($rule, $ruleViolations)
            );

            $violations = array_merge($violations, $ruleViolations);
        }

        $this->eventDispatcher->dispatch(Events::POST_RULES_CHECKED, new PostRulesCheckedEvent($violations));

        return $violations;
    }

    /**
     * @param Finder $finder
     *
     * @return NodeInterface[]
     */
    private function parseNodes(Finder $finder)
    {
        $this->eventDispatcher->dispatch(Events::PRE_NODES_PARSED, new PreNodesParsedEvent($finder));

        $nodes = array();
        foreach ($finder as $file) {
            if (null !== $parser = $this->nodeParserResolver->resolve($file)) {
                $node = $parser->parse($file);
                $nodes[] = $node;
                $this->eventDispatcher->dispatch(Events::NODE_PARSED, new NodeParsedEvent($node));
            }
        }

        $this->eventDispatcher->dispatch(Events::POST_NODES_PARSED, new PostNodesParsedEvent($nodes));

        return $nodes;
    }
}
