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
    /** @var NodeParserInterface */
    private $phpClassExtractor;

    /**
     * @param \SplFileInfo $file
     *
     * @return NodeParserInterface|null
     */
    public function resolve(\SplFileInfo $file)
    {
        if ('php' === strtolower($file->getExtension())) {
            return $this->getPhpClassExtractor();
        }

        return null;
    }

    /**
     * @return PhpClassNodeParser
     */
    private function getPhpClassExtractor()
    {
        if (null === $this->phpClassExtractor) {
            $this->phpClassExtractor = new PhpClassNodeParser();
        }

        return $this->phpClassExtractor;
    }
}
