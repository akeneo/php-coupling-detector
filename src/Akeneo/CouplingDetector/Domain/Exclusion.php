<?php

namespace Akeneo\CouplingDetector\Domain;

/**
 * Exclusion.
 *
 * @author  Nicolas Dupont <nicolas@akeneo.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Exclusion implements ExclusionInterface
{
    /** @var string */
    private $subject;

    /** @var array */
    private $excludedRequirements = [];

    /** @var string */
    private $type;

    /** @var string */
    private $description;

    /**
     * Rule constructor.
     *
     * @param string $subject
     * @param array  $excludedRequirements
     * @param string $type
     * @param string $description
     */
    public function __construct($subject, array $excludedRequirements, $type, $description = null)
    {
        $this->subject = $subject;
        $this->excludedRequirements = $excludedRequirements;
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getExcludedRequirements()
    {
        return $this->excludedRequirements;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }
}
