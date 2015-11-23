<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Data\NodeInterface;
use Akeneo\CouplingDetector\Data\RuleInterface;
use Akeneo\CouplingDetector\Data\ViolationInterface;
use Akeneo\CouplingDetector\NodeExtractor\NodeExtractorInterface;
use Akeneo\CouplingDetector\NodeExtractor\NodeExtractorResolver;
use Akeneo\CouplingDetector\RuleChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Finder\Finder;

class CouplingDetectorSpec extends ObjectBehavior
{
    function let(NodeExtractorResolver $nodeExtractorResolver, RuleChecker $ruleChecker)
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
        NodeExtractorInterface $extractor
    ) {
        $file = new \SplFileObject(__FILE__);
        $finder->getIterator()->willReturn(new \ArrayIterator([$file]));
        $nodeExtractorResolver->resolve(Argument::any())->willReturn($extractor);
        $extractor->extract($file)->willReturn($node);

        $ruleChecker->check($rule1, $node)->willReturn(null);
        $ruleChecker->check($rule2, $node)->willReturn($violation);

        $violations = $this->detect($finder, [$rule1, $rule2]);
        $violations->shouldHaveCount(1);
        $violations->shouldBeArray();
        $violations[0]->shouldBeAnInstanceOf('Akeneo\CouplingDetector\Data\ViolationInterface');
    }
}
