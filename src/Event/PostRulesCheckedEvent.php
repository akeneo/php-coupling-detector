<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Dispatched when all rules have been checked, ie at the end of the program.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class PostRulesCheckedEvent extends Event
{
    /** @var ViolationInterface[] */
    private $violations;

    /**
     * @param ViolationInterface[] $violations
     */
    public function __construct(array $violations)
    {
        $this->violations = $violations;
    }

    /**
     * @return ViolationInterface[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
