<?php

namespace spec\Akeneo\CouplingDetector\Domain;

use Akeneo\CouplingDetector\Domain\Node;
use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\Rule;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use PhpSpec\ObjectBehavior;

class RuleSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            'Namespace\To\Check',
            [
                'Authorized\Namespace\One',
                'Authorized\Namespace\Two',
                'Authorized\Namespace\Three',
                'Authorized\Namespace\Four',
            ],
            RuleInterface::TYPE_ONLY
        );
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_matches_a_node(NodeInterface $node)
    {
        $node->getSubject()->willReturn('Namespace\To\Check\SomeClass');
        $this->matches($node)->shouldReturn(true);

        $node->getSubject()->willReturn('Another\Namespace\SomeClass');
        $this->matches($node)->shouldReturn(false);
    }

    function it_finds_which_of_its_requirements_are_unused_in_a_set_of_nodes()
    {
        // We only test this case for Rules of type "ONLY". It is not applicable for other types.
        // The fact that this method has a very different behavior depending on an immutable property (the type) and not
        // on the inputs probably shows that the class should be split (one class per type).
        $this->getUnusedRequirements(
            [
                new Node(
                    [
                        'Authorized\Namespace\One',
                        'Authorized\Namespace\Three',
                    ],
                    'Namespace\To\Check\ClassOne',
                    '/path/to/file',
                    RuleInterface::TYPE_ONLY
                ),
                new Node(
                    [
                        'Authorized\Namespace\One',
                    ],
                    'Namespace\To\Check\ClassTwo',
                    '/path/to/file',
                    RuleInterface::TYPE_ONLY
                ),
                new Node(
                    [
                        'Authorized\Namespace\Two',
                    ],
                    'Namespace\Not\To\Check\ClassThree',
                    '/path/to/file',
                    RuleInterface::TYPE_ONLY
                ),
            ]
        )->shouldReturn(
            [
                1 => 'Authorized\Namespace\Two',
                3 => 'Authorized\Namespace\Four',
            ]
        );
    }
}
