<?php

namespace spec\Akeneo\CouplingDetector\Coupling;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UseViolationsSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\Coupling\UseViolations');
    }

    function it_provides_forbidden_full_qualified_class_name_uses()
    {
        $fqcnUses = [
            'Akeneo\Component\Classification\Updater\CategoryUpdater' => [
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface',
                'Pim\Bundle\TranslationBundle\Entity\OtherInterface',
            ],
            'Akeneo\Component\Classification\Updater\OtherUpdater' => [
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface'
            ]
        ];
        $this->beConstructedWith($fqcnUses);
        $this->getFullQualifiedClassNameViolations()->shouldReturn($fqcnUses);
    }

    function it_provides_the_sorted_count_of_forbidden_fqcn_uses()
    {
        $this->beConstructedWith(
            [
                'Akeneo\Component\Classification\Updater\CategoryUpdater'
                    => [
                        'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface',
                        'Pim\Bundle\TranslationBundle\Entity\OtherInterface',
                    ],
                'Akeneo\Component\Classification\Updater\OtherUpdater'
                    => [
                        'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface'
                    ]
            ]
        );

        $this->getSortedForbiddenUsesCounters()->shouldReturn(
            [
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface' => 2,
                'Pim\Bundle\TranslationBundle\Entity\OtherInterface' => 1
            ]
        );
    }
}
