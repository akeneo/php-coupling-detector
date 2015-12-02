<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\NodeParser\NodeParserInterface;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Akeneo\CouplingDetector\RuleChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Finder\Finder;

class CouplingDetectorSpec extends ObjectBehavior
{
    function let(NodeParserResolver $nodeExtractorResolver, RuleChecker $ruleChecker)
    {
        $this->beConstructedWith($nodeExtractorResolver, $ruleChecker);
    }

    function it_detects_the_violations_of_files(
        $ruleChecker,
        $nodeExtractorResolver,
        NodeInterface $node,
        ViolationInterface $violation,
        Finder $finder,
        RuleInterface $rule1,
        RuleInterface $rule2,
        NodeParserInterface $extractor
    ) {
        $file = new \SplFileObject(__FILE__);
        $finder->getIterator()->willReturn(new \ArrayIterator(array($file)));
        $nodeExtractorResolver->resolve(Argument::any())->willReturn($extractor);
        $extractor->parse($file)->willReturn($node);

        $ruleChecker->check($rule1, $node)->willReturn(null);
        $ruleChecker->check($rule2, $node)->willReturn($violation);

        $violations = $this->detect($finder, array($rule1, $rule2));
        $violations->shouldHaveCount(1);
        $violations->shouldBeArray();
        $violations[0]->shouldBeAnInstanceOf('Akeneo\CouplingDetector\Domain\ViolationInterface');
    }
}
