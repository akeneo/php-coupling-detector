<?php

namespace Akeneo\CouplingDetector\NodeParser;

use Akeneo\CouplingDetector\Domain\NodeInterface;

/**
 * Creates a node from a file by parsing its tokens.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface NodeParserInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return NodeInterface
     *
     * @throws ParsingException
     */
    public function parse(\SplFileInfo $file);
}
