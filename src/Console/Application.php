<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Console;

use Akeneo\CouplingDetector\Console\Command\DetectCommand;
use Akeneo\CouplingDetector\CouplingDetector;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);
        parent::__construct('Akeneo coupling detector', CouplingDetector::VERSION);
        $this->add(new DetectCommand());
    }
}
