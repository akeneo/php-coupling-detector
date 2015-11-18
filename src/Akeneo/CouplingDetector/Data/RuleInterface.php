<?php

namespace Akeneo\CouplingDetector\Data;

/**
 * Rule
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
     * @param string $subject
     */
    public function setSubject($subject);

    /**
     * @return array
     */
    public function getRequirements();

    /**
     * @param array $requirements
     */
    public function setRequirements(array $requirements);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);
}
