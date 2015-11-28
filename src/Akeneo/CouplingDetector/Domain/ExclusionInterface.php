<?php

namespace Akeneo\CouplingDetector\Domain;

/**
 * Exclusion, allows to exclude violations
 *
 * @author  Nicolas Dupont <nicolas@akeneo.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface ExclusionInterface
{
    const TYPE_LEGACY = 'legacy';
    const TYPE_DEPRECATED = 'deprecated';

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @return array
     */
    public function getExcludedRequirements();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getDescription();
}
