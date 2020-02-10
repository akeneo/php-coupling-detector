<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Event;

use Akeneo\CouplingDetector\Domain\RuleInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched before rules have been checked.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class PreRulesCheckedEvent extends Event
{
    /** @var RuleInterface[] */
    private $rules;

    /**
     * @param RuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
