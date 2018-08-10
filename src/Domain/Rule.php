<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Domain;

/**
 * Rule.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Rule implements RuleInterface
{
    /** @var string */
    private $subject;

    /** @var array */
    private $requirements = array();

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

        if (RuleInterface::TYPE_ONLY === $this->type) {
            $this->requirements[] = $this->subject;
        }
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
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }
}
