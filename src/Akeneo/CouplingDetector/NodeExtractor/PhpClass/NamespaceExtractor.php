<?php

namespace Akeneo\CouplingDetector\NodeExtractor\PhpClass;

use Akeneo\CouplingDetector\NodeExtractor\ExtractionException;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Extracts the namespace from a file.
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class NamespaceExtractor
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
        $namespace = null;

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_NAMESPACE)) {
                $namespaceIndex = $tokens->getNextNonWhitespace($index);
                $namespaceEndIndex = $tokens->getNextTokenOfKind($index, array(';'));
                $namespace = trim($tokens->generatePartialCode($namespaceIndex, $namespaceEndIndex - 1));
            }
        }

        if (null === $namespace) {
            throw new ExtractionException('No way to extract the namespace of this class');
        }

        return $namespace;
    }
}
