<?php

namespace spec\Akeneo\CouplingDetector\Coupling;

use Akeneo\CouplingDetector\FilesReader;
use Akeneo\CouplingDetector\RulesApplier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Finder\SplFileInfo;

class DetectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\Coupling\Detector');
    }

    function it_detects_uses_violations(FilesReader $reader, SplFileInfo $file, RulesApplier $applier)
    {
        $reader->read()->willReturn([$file]);
        $file->getContents()->willReturn(
<<<EOF
<?php

namespace Pim\Bundle\CatalogBundle\Model;

use Pim\Bundle\TranslationBundle\Entity\TranslatableInterface;
use Pim\Bundle\VersioningBundle\Model\VersionableInterface;

/**
 * Family interface
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface FamilyInterface extends TranslatableInterface, ReferableInterface, VersionableInterface
{
}
EOF
        );

        $this->detectUseViolations($reader, $applier)
            ->shouldReturnAnInstanceOf('Akeneo\CouplingDetector\Coupling\UseViolations');
    }
}
