<?php

namespace Akeneo\CouplingDetector;

use Symfony\Component\Finder\Finder;

/**
 * Provides the list of files to inspect
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class FilesReader
{
    /** @var string */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return \IteratorAggregate
     */
    public function read()
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->path)
            ->name('*.php')
            ->notPath('Oro');

        return $finder;
    }
}
