<?php

namespace spec\Akeneo\CouplingDetector\Coupling;

use Akeneo\CouplingDetector\Coupling\UseViolations;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UseViolationsFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\Coupling\UseViolationsFilter');
    }

    function it_filters_fqcn_uses_and_return_a_new_filtered_violations(UseViolations $violations)
    {
        $fqcnToFilter = [
            'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface'
        ];
        $this->beConstructedWith($fqcnToFilter);
        $violations->getFullQualifiedClassNameViolations()->willReturn(
            [
                'Akeneo\Component\Classification\Updater\CategoryUpdater' => [
                    0 => 'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface',
                    1 => 'Pim\Bundle\TranslationBundle\Entity\OtherNotFilteredInterface',
                ]
            ]
        );
        $filteredViolations = $this->filter($violations);
        $filteredViolations->getFullQualifiedClassNameViolations()->shouldReturn(
            [
                'Akeneo\Component\Classification\Updater\CategoryUpdater' => [
                    1 => 'Pim\Bundle\TranslationBundle\Entity\OtherNotFilteredInterface',
                ]
            ]
        );
    }
}
