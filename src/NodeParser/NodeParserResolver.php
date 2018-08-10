<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\NodeParser;

/**
 * Resolves the node extractor according to the given file.s.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class NodeParserResolver
{
    /** @var PhpClassNodeParser */
    private $phpClassExtractor;

    public function resolve(\SplFileInfo $file): ?NodeParserInterface
    {
        if ('php' === strtolower($file->getExtension())) {
            return $this->getPhpClassExtractor();
        }

        return null;
    }

    private function getPhpClassExtractor(): PhpClassNodeParser
    {
        if (null === $this->phpClassExtractor) {
            $this->phpClassExtractor = new PhpClassNodeParser();
        }

        return $this->phpClassExtractor;
    }
}
