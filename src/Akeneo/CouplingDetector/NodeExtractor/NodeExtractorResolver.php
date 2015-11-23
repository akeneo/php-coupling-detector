<?php

namespace Akeneo\CouplingDetector\NodeExtractor;

/**
 * Resolves the node extractor according to the given file.s
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class NodeExtractorResolver
{
    /** @var NodeExtractorInterface */
    private $phpClassExtractor;

    /**
     * @param \SplFileInfo $file
     *
     * @return NodeExtractorInterface|null
     */
    public function resolve(\SplFileInfo $file)
    {
        if ('php' === strtolower($file->getExtension())) {
            return $this->getPhpClassExtractor();
        }

        return null;
    }

    /**
     * @return PhpClassNodeExtractor
     */
    private function getPhpClassExtractor()
    {
        if (null === $this->phpClassExtractor) {
            $this->phpClassExtractor = new PhpClassNodeExtractor();
        }

        return $this->phpClassExtractor;
    }
}
