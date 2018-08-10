<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\NodeParser\PhpClass;

use Akeneo\CouplingDetector\NodeParser\ExtractionException;
use PhpCsFixer\Tokenizer\Tokens;

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
     * @throws ExtractionException
     */
    public function extract(Tokens $tokens): string
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
            throw new ExtractionException('No way to parse the namespace of this class');
        }

        return $namespace;
    }
}
