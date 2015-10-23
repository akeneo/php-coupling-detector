<?php

namespace Akeneo\Inspector\Console;

use Akeneo\Inspector\Console\Command\PimCommunityCommand;
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
        parent::__construct('Akeneo coupling inspector');
        $this->add(new PimCommunityCommand());
    }
}