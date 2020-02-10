<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\Event\Events;
use Akeneo\CouplingDetector\Event\NodeChecked;
use Akeneo\CouplingDetector\Event\NodeParsedEvent;
use Akeneo\CouplingDetector\Event\PostNodesParsedEvent;
use Akeneo\CouplingDetector\Event\PostRulesCheckedEvent;
use Akeneo\CouplingDetector\Event\PreNodesParsedEvent;
use Akeneo\CouplingDetector\Event\PreRulesCheckedEvent;
use Akeneo\CouplingDetector\Event\RuleCheckedEvent;
use Akeneo\CouplingDetector\NodeParser\ExtractionException;
use Akeneo\CouplingDetector\NodeParser\NodeParserInterface;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Akeneo\CouplingDetector\RuleChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class CouplingDetectorSpec extends ObjectBehavior
{
    function let(
        NodeParserResolver $nodeParserResolver,
        RuleChecker $ruleChecker,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($nodeParserResolver, $ruleChecker, $eventDispatcher);
    }

    function it_detects_the_violations_of_files(
        $ruleChecker,
        $nodeParserResolver,
        $eventDispatcher,
        NodeInterface $node,
        ViolationInterface $violation,
        Finder $finder,
        RuleInterface $rule1,
        RuleInterface $rule2,
        NodeParserInterface $parser
    ) {
        $file = new \SplFileObject(__FILE__);
        $finder->getIterator()->willReturn(new \ArrayIterator(array($file)));
        $nodeParserResolver->resolve(Argument::any())->willReturn($parser);
        $parser->parse($file)->willReturn($node);

        $ruleChecker->check($rule1, $node)->willReturn(null);
        $ruleChecker->check($rule2, $node)->willReturn($violation);

        $eventDispatcher
            ->dispatch(Argument::type(PreNodesParsedEvent::class), Events::PRE_NODES_PARSED)
            ->shouldBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(NodeParsedEvent::class), Events::NODE_PARSED)
            ->shouldBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(PostNodesParsedEvent::class), Events::POST_NODES_PARSED)
            ->shouldBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(PreRulesCheckedEvent::class), Events::PRE_RULES_CHECKED)
            ->shouldBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(NodeChecked::class), Events::NODE_CHECKED)
            ->shouldBeCalledTimes(2);
        $eventDispatcher
            ->dispatch(Argument::type(RuleCheckedEvent::class), Events::RULE_CHECKED)
            ->shouldBeCalledTimes(2);
        $eventDispatcher
            ->dispatch(Argument::type(PostRulesCheckedEvent::class), Events::POST_RULES_CHECKED)
            ->shouldBeCalled();

        $violations = $this->detect($finder, array($rule1, $rule2));
        $violations->shouldHaveCount(1);
        $violations->shouldBeArray();
        $violations[0]->shouldBeAnInstanceOf('Akeneo\CouplingDetector\Domain\ViolationInterface');
    }

    function it_ignores_invalid_nodes(
        $nodeParserResolver,
        $eventDispatcher,
        Finder $finder,
        NodeParserInterface $parser
    ) {
        $file = new \SplFileObject(__DIR__.'/fixtures/InvalidNode.php');
        $finder->getIterator()->willReturn(new \ArrayIterator([$file]));
        $nodeParserResolver->resolve(Argument::any())->willReturn($parser);
        $parser->parse(Argument::any())->willThrow(ExtractionException::class);

        $eventDispatcher
            ->dispatch(Argument::type(PreNodesParsedEvent::class), Events::PRE_NODES_PARSED)
            ->shouldBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(NodeParsedEvent::class), Events::NODE_PARSED)
            ->shouldNotBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(PostNodesParsedEvent::class), Events::POST_NODES_PARSED)
            ->shouldBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(PreRulesCheckedEvent::class), Events::PRE_RULES_CHECKED)
            ->shouldBeCalled();
        $eventDispatcher
            ->dispatch(Argument::type(PostRulesCheckedEvent::class), Events::POST_RULES_CHECKED)
            ->shouldBeCalled();

        $violations = $this->detect($finder, []);
        $violations->shouldHaveCount(0);
        $violations->shouldBeArray();
    }
}
