<?php

namespace Akeneo\CouplingDetector;

use Akeneo\CouplingDetector\Domain\Rule;
use Akeneo\CouplingDetector\Domain\RuleInterface;

/**
 * Builds a rule simply. Usage examples:
 *
 * $builder = new RuleBuilder();
 * $rule1 = $builder->forbids(['foo', 'bar'])->in('baz');
 * $rule2 = $builder->discouraged(['toto'])->in('titi');
 * $rule3 = $builder->only(['tik'])->in('tak');
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class RuleBuilder
{
    /** @var array */
    private $requirements = [];

    /** @var string */
    private $type;

    public function forbids(array $requirements): RuleBuilder
    {
        $this->requirements = $requirements;
        $this->type = RuleInterface::TYPE_FORBIDDEN;

        return $this;
    }

    public function only(array $requirements): RuleBuilder
    {
        $this->requirements = $requirements;
        $this->type = RuleInterface::TYPE_ONLY;

        return $this;    }

    public function discourages(array $requirements): RuleBuilder
    {
        $this->requirements = $requirements;
        $this->type = RuleInterface::TYPE_DISCOURAGED;

        return $this;
    }

    public function in(string $subject): RuleInterface
    {
        if (empty($this->requirements)) {
            throw new \Exception('Can not create a rule without any requirement defined previously.');
        }

        if (null === $this->type) {
            throw new \Exception('Can not create a rule without any type defined previously.');
        }

        return new Rule($subject, $this->requirements, $this->type);
    }
}
