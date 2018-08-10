<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\NodeParser\PhpClass;

use Akeneo\CouplingDetector\NodeParser\ExtractionException;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Extracts the class name from a file.
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class ClassNameExtractor
{
    /**
     * @param Tokens $tokens
     *
     * @throws ExtractionException
     *
     * @return string
     */
    public function extract(Tokens $tokens)
    {
        $classyName = null;

        foreach ($tokens as $index => $token) {
            if ($token->isClassy()) {
                $classyIndex = $tokens->getNextNonWhitespace($index);
                $classyName = $tokens[$classyIndex]->getContent();
            }
        }

        if (null === $classyName) {
            throw new ExtractionException('No way to parse class name of this class');
        }

        return $classyName;
    }
}
