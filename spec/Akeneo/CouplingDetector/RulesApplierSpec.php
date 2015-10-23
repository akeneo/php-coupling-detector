<?php

namespace spec\Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\UseViolationRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RulesApplierSpec extends ObjectBehavior
{
    function let(UseViolationRule $rule)
    {
        $this->beConstructedWith([$rule]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\RulesApplier');
    }

    function it_applies_rules($rule)
    {
        $fqcn = 'Pim\Bundle\CatalogBundle\Factory\FamilyFactory';
        $useDeclarations = [
            'Pim\Bundle\CatalogBundle\Entity\Family',
            'Pim\Bundle\CatalogBundle\Manager\ChannelManager',
            'Pim\Bundle\CatalogBundle\Model\FamilyInterface',
            'Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface',
            'Pim\Bundle\EnrichBundle\Repository\ForbiddenClass'
        ];

        $rule->match($fqcn)->willReturn(true);
        $rule->detect($useDeclarations)->willReturn(['Pim\Bundle\EnrichBundle\Repository\ForbiddenClass']);

        $this->apply($fqcn, $useDeclarations)->shouldReturn(['Pim\Bundle\EnrichBundle\Repository\ForbiddenClass']);
    }
}
