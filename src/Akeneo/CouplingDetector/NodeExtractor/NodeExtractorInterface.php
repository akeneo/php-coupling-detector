<?php

namespace Akeneo\CouplingDetector\NodeExtractor;

use Akeneo\CouplingDetector\Data\NodeInterface;

/**
 * Creates a node from a file by extracting its tokens.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface NodeExtractorInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return NodeInterface
     * @throws ExtractionException
     */
    public function extract(\SplFileInfo $file);
}
