<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Configuration;

use Symfony\Component\Finder\Finder;

/**
 * Default finder implementation.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class DefaultFinder extends Finder
{
    /**
     * DefaultFinder constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->files()
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('vendor')
        ;
    }
}
