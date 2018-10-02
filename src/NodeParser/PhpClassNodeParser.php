<?php

declare(strict_types=1);

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
     * @throws \Exception
     */
    public function parse(\SplFileInfo $file): NodeInterface
    {
        $namespaceExtractor = new NamespaceExtractor();
        $classNameExtractor = new ClassNameExtractor();
        $useDeclarationExtractor = new UseDeclarationsExtractor();

        $realPath = $file->getRealPath();
        if (false === $realPath) {
            throw new \Exception('The file does not exist.');
        }

        $content = file_get_contents($realPath);
        if (false === $content) {
            throw new \Exception('The content of the file is not readable.');
        }

        $tokens = Tokens::fromCode($content);
        try {
            $classNamespace = $namespaceExtractor->extract($tokens);
            $className = $classNameExtractor->extract($tokens);
        } catch (ExtractionException $e) {
            throw new ExtractionException(
                'File is not a class file, ignoring.', null, $e
            );
        }
        $classFullName = sprintf('%s\%s', $classNamespace, $className);
        $useDeclarations = $useDeclarationExtractor->extract($tokens);

        return new Node($useDeclarations, $classFullName, $realPath, NodeInterface::TYPE_PHP_USE);
    }
}
