<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\Event\Events;
use Akeneo\CouplingDetector\NodeParser\NodeParserInterface;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Akeneo\CouplingDetector\RuleChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class CouplingDetectorSpec extends ObjectBehavior
{
    function let(
        NodeParserResolver $nodeExtractorResolver,
        RuleChecker $ruleChecker,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($nodeExtractorResolver, $ruleChecker, $eventDispatcher);
    }

    function it_detects_the_violations_of_files(
        $ruleChecker,
        $nodeExtractorResolver,
        $eventDispatcher,
        NodeInterface $node,
        ViolationInterface $violation,
        Finder $finder,
        RuleInterface $rule1,
        RuleInterface $rule2,
        NodeParserInterface $extractor
    ) {
        $file = new \SplFileObject(__FILE__);
        $finder->getIterator()->willReturn(new \ArrayIterator([$file]));
        $nodeExtractorResolver->resolve(Argument::any())->willReturn($extractor);
        $extractor->parse($file)->willReturn($node);

        $ruleChecker->check($rule1, $node)->willReturn(null);
        $ruleChecker->check($rule2, $node)->willReturn($violation);

        $eventDispatcher->dispatch(Events::PRE_NODES_PARSED, Argument::type('Akeneo\CouplingDetector\Event\PreNodesParsedEvent'))->shouldBeCalled();
        $eventDispatcher->dispatch(Events::NODE_PARSED, Argument::type('Akeneo\CouplingDetector\Event\NodeParsedEvent'))->shouldBeCalled();
        $eventDispatcher->dispatch(Events::POST_NODES_PARSED, Argument::type('Akeneo\CouplingDetector\Event\PostNodesParsedEvent'))->shouldBeCalled();
        $eventDispatcher->dispatch(Events::PRE_RULES_CHECKED, Argument::type('Akeneo\CouplingDetector\Event\PreRulesCheckedEvent'))->shouldBeCalled();
        $eventDispatcher->dispatch(Events::NODE_CHECKED, Argument::type('Akeneo\CouplingDetector\Event\NodeChecked'))->shouldBeCalledTimes(2);
        $eventDispatcher->dispatch(Events::RULE_CHECKED, Argument::type('Akeneo\CouplingDetector\Event\RuleCheckedEvent'))->shouldBeCalledTimes(2);
        $eventDispatcher->dispatch(Events::POST_RULES_CHECKED, Argument::type('Akeneo\CouplingDetector\Event\PostRulesCheckedEvent'))->shouldBeCalled();

        $violations = $this->detect($finder, [$rule1, $rule2]);
        $violations->shouldHaveCount(1);
        $violations->shouldBeArray();
        $violations[0]->shouldBeAnInstanceOf('Akeneo\CouplingDetector\Domain\ViolationInterface');
    }
}
