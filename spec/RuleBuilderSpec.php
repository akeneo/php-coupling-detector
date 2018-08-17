<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\Rule;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\RuleBuilder;
use PhpSpec\ObjectBehavior;

class RuleBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RuleBuilder::class);
    }

    function it_does_not_create_a_rule_without_constraints()
    {
        $this
            ->shouldThrow(new \Exception('Can not create a rule without any requirement defined previously.'))
            ->during('in', ['baz']);
    }

    function it_chains_the_methods_for_building_a_rule()
    {
        $this->forbids(['foo', 'bar'])->shouldReturn($this);
        $this->only(['foo', 'bar'])->shouldReturn($this);
        $this->discourages(['foo', 'bar'])->shouldReturn($this);
    }

    function it_creates_a_forbidden_rule()
    {
        $this->forbids(['foo', 'bar']);
        $actual = $this->in('baz');

        $expected = new Rule('baz', ['foo', 'bar'], RuleInterface::TYPE_FORBIDDEN);
        $actual->shouldBeAnInstanceOf(RuleInterface::class);
        $actual->shouldBeLikeExpectedRule($expected);
    }

    function it_creates_an_only_rule()
    {
        $this->only(['foo', 'bar']);
        $actual = $this->in('baz');

        $expected = new Rule('baz', ['foo', 'bar'], RuleInterface::TYPE_ONLY);
        $actual->shouldBeAnInstanceOf(RuleInterface::class);
        $actual->shouldBeLikeExpectedRule($expected);
    }

    function it_creates_a_discouraged_rule()
    {
        $this->discourages(['foo', 'bar']);
        $actual = $this->in('baz');

        $expected = new Rule('baz', ['foo', 'bar'], RuleInterface::TYPE_DISCOURAGED);
        $actual->shouldBeAnInstanceOf(RuleInterface::class);
        $actual->shouldBeLikeExpectedRule($expected);
    }

    public function getMatchers(): array
    {
        return array(
            'beLikeExpectedRule' => function ($actual, $expected) {
                return
                    $actual->getSubject() === $expected->getSubject() &&
                    $actual->getRequirements() === $expected->getRequirements() &&
                    $actual->getType() === $expected->getType();
            },
        );
    }
}
