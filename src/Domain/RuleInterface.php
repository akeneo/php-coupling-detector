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

    public function getSubject(): string;

    public function getRequirements(): array;

    public function getType(): string;

    public function getDescription(): ?string;

    public function matchesNode(NodeInterface $node): bool;

    public function getUnusedRequirementsInNodes(array $nodes): array;
}
