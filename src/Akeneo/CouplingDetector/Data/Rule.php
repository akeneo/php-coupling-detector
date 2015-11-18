<?php

namespace Akeneo\CouplingDetector\Data;

/**
 * Rule
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Rule implements RuleInterface
{
    /** @var string */
    private $subject;

    /** @var array */
    private $requirements = [];

    /** @var string */
    private $type;

    /** @var string */
    private $description;

    /**
     * Rule constructor.
     *
     * @param string $subject
     * @param array  $requirements
     * @param string $type
     * @param string $description
     */
    public function __construct($subject, array $requirements, $type, $description = null)
    {
        $this->requirements = $requirements;
        $this->subject = $subject;
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
    public function setSubject($subject)
    {
        $this->subject = $subject;
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
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}