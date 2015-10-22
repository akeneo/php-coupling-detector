<?php

namespace Akeneo\Inspector\Coupling;

use Symfony\CS\Tokenizer\Tokens;

/**
 * Extracts used namespace declarations
 *
 * @author    Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 *
 * @see Symfony\CS\Fixer\Symfony\UnusedUseFixer
 */
class NamespaceUseDeclarationsExtractor
{
    /**
     * Copy/paste from a private method of Symfony\CS\Fixer\Symfony\UnusedUseFixer
     *
     * @param Tokens $tokens
     * @param array  $useIndexes
     *
     * @return array
     */
    public function extractNamespaceUseDeclarations(Tokens $tokens, array $useIndexes)
    {
        $uses = array();

        foreach ($useIndexes as $index) {
            $declarationEndIndex = $tokens->getNextTokenOfKind($index, array(';'));
            $declarationContent = $tokens->generatePartialCode($index + 1, $declarationEndIndex - 1);

            // ignore multiple use statements like: `use BarB, BarC as C, BarD;`
            // that should be split into few separate statements
            if (false !== strpos($declarationContent, ',')) {
                continue;
            }

            $declarationParts = preg_split('/\s+as\s+/i', $declarationContent);

            if (1 === count($declarationParts)) {
                $fullName = $declarationContent;
                $declarationParts = explode('\\', $fullName);
                $shortName = end($declarationParts);
                $aliased = false;
            } else {
                $fullName = $declarationParts[0];
                $shortName = $declarationParts[1];
                $declarationParts = explode('\\', $fullName);
                $aliased = $shortName !== end($declarationParts);
            }

            $shortName = trim($shortName);

            $uses[$shortName] = array(
                'aliased' => $aliased,
                'end' => $declarationEndIndex,
                'fullName' => trim($fullName),
                'shortName' => $shortName,
                'start' => $index,
            );
        }

        return $uses;
    }
}