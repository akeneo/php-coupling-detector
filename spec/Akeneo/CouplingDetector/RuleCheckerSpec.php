<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RuleCheckerSpec extends ObjectBehavior
{
    function it_matches_a_node(RuleInterface $rule, NodeInterface $node)
    {
        $rule->getSubject()->willReturn('foo\bar');
        $node->getSubject()->willReturn('foo\bar\baz');
        $this->match($rule, $node)->shouldReturn(true);

        $rule->getSubject()->willReturn('foo\bar');
        $node->getSubject()->willReturn('blu\bla\blo');
        $this->match($rule, $node)->shouldReturn(false);
    }

    function it_checks_a_valid_node_with_forbidden_rule(RuleInterface $rule, NodeInterface $node)
    {
        $rule->getSubject()->willReturn('foo\bar');
        $rule->getRequirements()->willReturn(['blu', 'bla', 'bli']);
        $rule->getType()->willReturn(RuleInterface::TYPE_FORBIDDEN);
        $node->getSubject()->willReturn('foo\bar\baz');
        $node->getTokens()->willReturn(['blo', 'bly']);

        $this->check($rule, $node)->shouldReturn(null);
    }

    function it_checks_an_invalid_node_with_forbidden_rule(
        RuleInterface $rule,
        NodeInterface $node,
        ViolationInterface $violation
    ) {
        $rule->getSubject()->willReturn('foo\bar');
        $rule->getRequirements()->willReturn(['blu', 'bla', 'bli']);
        $rule->getType()->willReturn(RuleInterface::TYPE_FORBIDDEN);
        $node->getSubject()->willReturn('foo\bar\baz');
        $node->getTokens()->willReturn(['blu', 'bla', 'blo', 'bly']);

        $violation->getNode()->willReturn($node);
        $violation->getRule()->willReturn($rule);
        $violation->getType()->willReturn(ViolationInterface::TYPE_ERROR);
        $violation->getTokenViolations()->willReturn(['blu', 'bla']);

        $this->check($rule, $node)->shouldBeLikeExpectedViolation($violation);
    }

    function it_checks_a_valid_node_with_discouraged_rule(RuleInterface $rule, NodeInterface $node)
    {
        $rule->getSubject()->willReturn('foo\bar');
        $rule->getRequirements()->willReturn(['blu', 'bla', 'bli']);
        $rule->getType()->willReturn(RuleInterface::TYPE_DISCOURAGED);
        $node->getSubject()->willReturn('foo\bar\baz');
        $node->getTokens()->willReturn(['blo', 'bly']);

        $this->check($rule, $node)->shouldReturn(null);
    }

    function it_checks_an_invalid_node_with_discouraged_rule(
        RuleInterface $rule,
        NodeInterface $node,
        ViolationInterface $violation
    ) {
        $rule->getSubject()->willReturn('foo\bar');
        $rule->getRequirements()->willReturn(['blu', 'bla', 'bli']);
        $rule->getType()->willReturn(RuleInterface::TYPE_DISCOURAGED);
        $node->getSubject()->willReturn('foo\bar\baz');
        $node->getTokens()->willReturn(['blu', 'bla', 'blo', 'bly']);

        $violation->getNode()->willReturn($node);
        $violation->getRule()->willReturn($rule);
        $violation->getType()->willReturn(ViolationInterface::TYPE_WARNING);
        $violation->getTokenViolations()->willReturn(['blu', 'bla']);

        $this->check($rule, $node)->shouldBeLikeExpectedViolation($violation);
    }

    function it_checks_a_valid_node_with_only_rule(RuleInterface $rule, NodeInterface $node)
    {
        $rule->getSubject()->willReturn('foo\bar');
        $rule->getRequirements()->willReturn(['blu', 'bla', 'bli']);
        $rule->getType()->willReturn(RuleInterface::TYPE_ONLY);
        $node->getSubject()->willReturn('foo\bar\baz');
        $node->getTokens()->willReturn(['blu', 'bla']);

        $this->check($rule, $node)->shouldReturn(null);
    }

    function it_checks_an_invalid_node_with_only_rule(
        RuleInterface $rule,
        NodeInterface $node,
        ViolationInterface $violation
    ) {
        $rule->getSubject()->willReturn('foo\bar');
        $rule->getRequirements()->willReturn(['blu', 'bla', 'bli']);
        $rule->getType()->willReturn(RuleInterface::TYPE_ONLY);
        $node->getSubject()->willReturn('foo\bar\baz');
        $node->getTokens()->willReturn(['blu', 'bla', 'blo', 'bly']);

        $violation->getNode()->willReturn($node);
        $violation->getRule()->willReturn($rule);
        $violation->getType()->willReturn(ViolationInterface::TYPE_ERROR);
        $violation->getTokenViolations()->willReturn(['blo', 'bly']);

        $this->check($rule, $node)->shouldBeLikeExpectedViolation($violation);
    }

    public function getMatchers()
    {
        return [
            'beLikeExpectedViolation' => function ($subject, $expected) {
                return
                    $subject->getNode() === $expected->getNode() &&
                    $subject->getRule() === $expected->getRule() &&
                    $subject->getTokenViolations() === $expected->getTokenViolations() &&
                    $subject->getType() === $expected->getType();
            },
        ];
    }
}
