<?php

namespace spec\Akeneo\CouplingDetector\NodeParser\PhpClass;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PhpCsFixer\Tokenizer\Tokens;

class UseDeclarationsExtractorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Akeneo\CouplingDetector\NodeParser\PhpClass\UseDeclarationsExtractor');
    }

    function it_extracts_the_class_namespace()
    {
        $content = <<<EOF
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
EOF;
        $tokens = Tokens::fromCode($content);
        $this->extract($tokens)->shouldReturn(
            array(
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface',
                'Pim\Bundle\VersioningBundle\Model\VersionableInterface'
            )
        );
    }
}
