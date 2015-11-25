<?php

namespace Akeneo\CouplingDetector\NodeExtractor;

use Akeneo\CouplingDetector\Domain\Node;
use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\NodeExtractor\PhpClass\ClassNameExtractor;
use Akeneo\CouplingDetector\NodeExtractor\PhpClass\NamespaceExtractor;
use Akeneo\CouplingDetector\NodeExtractor\PhpClass\UseDeclarationsExtractor;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Extract all the tokens of a PHP class.
 *
 * The node's tokens are the use statement of the class.
 * The node's subject is the FQCN of the class.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class PhpClassNodeExtractor implements NodeExtractorInterface
{
    public function extract(\SplFileInfo $file)
    {
        $namespaceExtractor = new NamespaceExtractor();
        $classNameExtractor = new ClassNameExtractor();
        $useDeclarationExtractor = new UseDeclarationsExtractor();

        $content = file_get_contents($file->getRealPath());
        $tokens = Tokens::fromCode($content);
        $classNamespace = $namespaceExtractor->extract($tokens);
        $className = $classNameExtractor->extract($tokens);
        $classFullName = sprintf('%s\%s', $classNamespace, $className);
        $useDeclarations = $useDeclarationExtractor->extract($tokens);

        return new Node($useDeclarations, $classFullName, $file->getRealPath(), NodeInterface::TYPE_PHP_USE);
    }
}
