<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched when a rule has been checked for all nodes.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class RuleCheckedEvent extends Event
{
    /** @var RuleInterface */
    private $rule;

    /** @var ViolationInterface[] */
    private $violations;

    /**
     * RuleParsedEvent constructor.
     *
     * @param RuleInterface        $rule
     * @param ViolationInterface[] $violations
     */
    public function __construct(RuleInterface $rule, array $violations)
    {
        $this->rule = $rule;
        $this->violations = $violations;
    }

    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    public function getViolations(): array
    {
        return $this->violations;
    }
}
