<?php

namespace spec\Akeneo\CouplingDetector;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UseViolationRuleSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Pim\Bundle\CatalogBundle', ['PimEnterprise', 'Pim\Bundle\EnrichBundle']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\UseViolationRule');
    }

    function it_matches_a_fqcn()
    {
        $this->match('Pim\Bundle\CatalogBundle\Factory\FamilyFactory')->shouldReturn(true);
        $this->match('Pim\Bundle\EnrichBundle\OtherClass')->shouldReturn(false);
    }

    function it_detects_and_returns_violations_uses()
    {
        $useDeclarations = [
            'Pim\Bundle\CatalogBundle\Entity\Family',
            'Pim\Bundle\CatalogBundle\Manager\ChannelManager',
            'Pim\Bundle\CatalogBundle\Model\FamilyInterface',
            'Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface',
            'PimEnterprise\Bundle\CatalogBundle\Repository\AnEnterpriseClass',
            'Pim\Bundle\EnrichBundle\Repository\AnEnrichBundleClass'
        ];
        $this->detect($useDeclarations)->shouldReturn(
            [
                'PimEnterprise\Bundle\CatalogBundle\Repository\AnEnterpriseClass',
                'Pim\Bundle\EnrichBundle\Repository\AnEnrichBundleClass'
            ]
        );
    }

    function it_returns_an_empty_array_when_there_are_no_violations_uses()
    {
        $useDeclarations = [
            'Pim\Bundle\CatalogBundle\Entity\Family',
            'Pim\Bundle\CatalogBundle\Manager\ChannelManager',
            'Pim\Bundle\CatalogBundle\Model\FamilyInterface',
            'Pim\Bundle\CatalogBundle\Repository\AttributeRepositoryInterface'
        ];
        $this->detect($useDeclarations)->shouldReturn([]);
    }
}
