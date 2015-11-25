<?php

namespace spec\Akeneo\CouplingDetector\NodeExtractor;

use Akeneo\CouplingDetector\Domain\Node;
use Akeneo\CouplingDetector\Domain\NodeInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PhpClassNodeExtractorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\NodeExtractor\PhpClassNodeExtractor');
    }

    function it_extracts_use_statements_from_a_class()
    {
        $file = new \SplFileInfo(__FILE__);
        $expectedNode = new Node(
            [
                'Akeneo\CouplingDetector\Domain\Node',
                'Akeneo\CouplingDetector\Domain\NodeInterface',
                'PhpSpec\ObjectBehavior',
                'Prophecy\Argument'
            ],
            'spec\Akeneo\CouplingDetector\NodeExtractor\PhpClassNodeExtractorSpec',
            __FILE__,
            NodeInterface::TYPE_PHP_USE
        );

        $this->extract($file)->shouldBeLikeExpectedNode($expectedNode);
    }

    public function getMatchers()
    {
        return [
            'beLikeExpectedNode' => function ($subject, $expected) {
                return
                    $subject->getTokens() === $expected->getTokens() &&
                    $subject->getSubject() === $expected->getSubject() &&
                    $subject->getFilePath() === $expected->getFilePath() &&
                    $subject->getType() === $expected->getType();
            },
        ];
    }
}
