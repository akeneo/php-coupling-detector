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

    /** @var string|null */
    private $description;

    public function __construct(string $subject, array $requirements, string $type, ?string $description = null)
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
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function matches(NodeInterface $node): bool
    {
        return false !== strpos($node->getSubject(), $this->subject);
    }

    public function getUnusedRequirements(array $nodes): array
    {
        // Not relevant for other types of rules
        if (RuleInterface::TYPE_ONLY !== $this->type) {
            return [];
        }

        $matchingNodes = array_filter($nodes, function (NodeInterface $node) {
            return $this->matches($node);
        });

        if ([] === $matchingNodes) {
            return [];
        }

        return array_filter($this->requirements, function (string $requirement) use ($matchingNodes) {
            if ($this->subject === $requirement) {
                return false;
            }

            foreach ($matchingNodes as $node) {
                foreach ($node->getTokens() as $token) {
                    if (false !== strpos($token, $requirement)) {
                        return false;
                    }
                }
            }

            return true;
        });
    }
}
