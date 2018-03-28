<?php

namespace spec\Akeneo\CouplingDetector\NodeParser;

use Akeneo\CouplingDetector\Domain\Node;
use Akeneo\CouplingDetector\Domain\NodeInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PhpClassNodeParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\NodeParser\PhpClassNodeParser');
    }

    function it_extracts_use_statements_from_a_class()
    {
        $file = new \SplFileInfo(__FILE__);
        $expectedNode = new Node(
            array(
                'Akeneo\CouplingDetector\Domain\Node',
                'Akeneo\CouplingDetector\Domain\NodeInterface',
                'PhpSpec\ObjectBehavior',
                'Prophecy\Argument'
            ),
            'spec\Akeneo\CouplingDetector\NodeParser\PhpClassNodeParserSpec',
            __FILE__,
            NodeInterface::TYPE_PHP_USE
        );

        $this->parse($file)->shouldBeLikeExpectedNode($expectedNode);
    }

    public function getMatchers(): array
    {
        return array(
            'beLikeExpectedNode' => function ($subject, $expected) {
                return
                    $subject->getTokens() === $expected->getTokens() &&
                    $subject->getSubject() === $expected->getSubject() &&
                    $subject->getFilePath() === $expected->getFilePath() &&
                    $subject->getType() === $expected->getType();
            },
        );
    }
}
