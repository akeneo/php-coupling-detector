<?php

namespace Akeneo\CouplingDetector\NodeParser;

use Akeneo\CouplingDetector\Domain\Node;
use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\NodeParser\PhpClass\ClassNameExtractor;
use Akeneo\CouplingDetector\NodeParser\PhpClass\NamespaceExtractor;
use Akeneo\CouplingDetector\NodeParser\PhpClass\UseDeclarationsExtractor;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Extract all the tokens of a PHP class.
 *
 * The node's tokens are the use statement of the class.
 * The node's subject is the FQCN of the class.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class PhpClassNodeParser implements NodeParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(\SplFileInfo $file)
    {
        $namespaceExtractor = new NamespaceExtractor();
        $classNameExtractor = new ClassNameExtractor();
        $useDeclarationExtractor = new UseDeclarationsExtractor();

        $content = file_get_contents($file->getRealPath());
        $tokens = Tokens::fromCode($content);
        try {
            $classNamespace = $namespaceExtractor->extract($tokens, $file);
            $className = $classNameExtractor->extract($tokens, $file);
        } catch (ExtractionException $e) {
            throw new ParsingException(sprintf(
                "Parsing exception on \"%s\":\n%s",
                $file->getPathname(),
                $e->getMessage()
            ));
        }
        $classFullName = sprintf('%s\%s', $classNamespace, $className);
        $useDeclarations = $useDeclarationExtractor->extract($tokens);

        return new Node($useDeclarations, $classFullName, $file->getRealPath(), NodeInterface::TYPE_PHP_USE);
    }
}
