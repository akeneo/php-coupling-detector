<?php

namespace Akeneo\CouplingDetector\NodeParser\PhpClass;

use Symfony\CS\Tokenizer\Tokens;

/**
 * Extracts the namespace declarations used as imports in a file.
 *
 * @author    Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 *
 * @see Symfony\CS\Fixer\Symfony\UnusedUseFixer
 */
class UseDeclarationsExtractor
{
    /**
     * Copy/paste from a private method of Symfony\CS\Fixer\Symfony\UnusedUseFixer.
     *
     * @param Tokens $tokens
     *
     * @return array
     */
    public function extract(Tokens $tokens)
    {
        $uses = array();
        $useIndexes = $tokens->getImportUseIndexes();
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
            } else {
                $fullName = $declarationParts[0];
            }

            $uses[] = trim($fullName);
        }

        return $uses;
    }
}
