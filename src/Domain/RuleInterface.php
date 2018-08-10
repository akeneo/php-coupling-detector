<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Domain;

/**
 * Rule.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface RuleInterface
{
    const TYPE_FORBIDDEN = 'forbidden';
    const TYPE_DISCOURAGED = 'discouraged';
    const TYPE_ONLY = 'only';

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @return array
     */
    public function getRequirements();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getDescription();
}
