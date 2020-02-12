<?php

declare(strict_types=1);

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
use Akeneo\CouplingDetector\NodeParser\ExtractionException;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
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
    public function detect(Finder $finder, array $rules): array
    {
        $nodes = $this->parseNodes($finder);
        $violations = array();

        $this->eventDispatcher->dispatch(new PreRulesCheckedEvent($rules), Events::PRE_RULES_CHECKED);

        foreach ($rules as $rule) {
            $ruleViolations = array();

            foreach ($nodes as $node) {
                $violation = $this->ruleChecker->check($rule, $node);
                if (null !== $violation) {
                    $ruleViolations[] = $violation;
                }

                $this->eventDispatcher->dispatch(new NodeChecked($node, $rule, $violation), Events::NODE_CHECKED);
            }

            $this->eventDispatcher->dispatch(new RuleCheckedEvent($rule, $ruleViolations), Events::RULE_CHECKED);

            $violations = array_merge($violations, $ruleViolations);
        }

        $this->eventDispatcher->dispatch(new PostRulesCheckedEvent($violations), Events::POST_RULES_CHECKED);

        return $violations;
    }

    /**
     * @param Finder $finder
     *
     * @return NodeInterface[]
     */
    private function parseNodes(Finder $finder): array
    {
        $this->eventDispatcher->dispatch(new PreNodesParsedEvent($finder), Events::PRE_NODES_PARSED);

        $nodes = array();
        foreach ($finder as $file) {
            $parser = $this->nodeParserResolver->resolve($file);
            if (null !== $parser) {
                try {
                    $node = $parser->parse($file);
                    $nodes[] = $node;
                    $this->eventDispatcher->dispatch(new NodeParsedEvent($node), Events::NODE_PARSED);
                } catch (ExtractionException $e) {
                    // at the moment, let's just ignore invalid node
                    // need to fix that with a better design
                }
            }
        }

        $this->eventDispatcher->dispatch(new PostNodesParsedEvent($nodes), Events::POST_NODES_PARSED);

        return $nodes;
    }
}
