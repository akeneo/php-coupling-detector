<?php

declare(strict_types=1);

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
     * @throws ExtractionException
     */
    public function parse(\SplFileInfo $file): NodeInterface;
}
