<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\Node;
use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\Rule;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\Violation;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use PhpSpec\ObjectBehavior;

class RuleCheckerSpec extends ObjectBehavior
{
    function it_checks_a_valid_node_with_forbidden_rule()
    {
        $rule = new Rule(
            'foo\bar',
            ['blu', 'bla', 'bli'],
            RuleInterface::TYPE_FORBIDDEN
        );

        $node = new Node(
            ['blo', 'bly'],
            'foo\bar\baz',
            '/path/to/file',
            NodeInterface::TYPE_PHP_USE
        );

        $this->check($rule, $node)->shouldReturn(null);
    }

    function it_checks_an_invalid_node_with_forbidden_rule()
    {
        $rule = new Rule(
            'foo\bar',
            ['blu', 'bla', 'bli'],
            RuleInterface::TYPE_FORBIDDEN
        );

        $node = new Node(
            ['blu', 'bla', 'blo', 'bly'],
            'foo\bar\baz',
            '/path/to/file',
            NodeInterface::TYPE_PHP_USE
        );

        $this->check($rule, $node)->shouldBeLike(new Violation(
            $node,
            $rule,
            ['blu', 'bla'],
            ViolationInterface::TYPE_ERROR
        ));
    }

    function it_checks_a_valid_node_with_discouraged_rule()
    {
        $rule = new Rule(
            'foo\bar',
            ['blu', 'bla', 'bli'],
            RuleInterface::TYPE_DISCOURAGED
        );

        $node = new Node(
            ['blo', 'bly'],
            'foo\bar\baz',
            '/path/to/file',
            NodeInterface::TYPE_PHP_USE
        );

        $this->check($rule, $node)->shouldReturn(null);
    }

    function it_checks_an_invalid_node_with_discouraged_rule()
    {
        $rule = new Rule(
            'foo\bar',
            ['blu', 'bla', 'bli'],
            RuleInterface::TYPE_DISCOURAGED
        );

        $node = new Node(
            ['blu', 'bla', 'blo', 'bly'],
            'foo\bar\baz',
            '/path/to/file',
            NodeInterface::TYPE_PHP_USE
        );

        $this->check($rule, $node)->shouldBeLike(new Violation(
            $node,
            $rule,
            ['blu', 'bla'],
            ViolationInterface::TYPE_WARNING
        ));
    }

    function it_checks_a_valid_node_with_only_rule()
    {
        $rule = new Rule(
            'foo\bar',
            ['blu', 'bla', 'bli'],
            RuleInterface::TYPE_ONLY
        );

        $node = new Node(
            ['blu', 'bla'],
            'foo\bar\baz',
            '/path/to/file',
            NodeInterface::TYPE_PHP_USE
        );

        $this->check($rule, $node)->shouldReturn(null);
    }

    function it_checks_an_invalid_node_with_only_rule()
    {
        $rule = new Rule(
            'foo\bar',
            ['blu', 'bla', 'bli'],
            RuleInterface::TYPE_ONLY
        );

        $node = new Node(
            ['blu', 'bla', 'blo', 'bly'],
            'foo\bar\baz',
            '/path/to/file',
            NodeInterface::TYPE_PHP_USE
        );

        $this->check($rule, $node)->shouldBeLike(new Violation(
            $node,
            $rule,
            ['blo', 'bly'],
            ViolationInterface::TYPE_ERROR
        ));
    }
}
