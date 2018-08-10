<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Formatter;

use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\Event\PostNodesParsedEvent;
use Akeneo\CouplingDetector\Event\NodeChecked;
use Akeneo\CouplingDetector\Event\NodeParsedEvent;
use Akeneo\CouplingDetector\Event\PreNodesParsedEvent;
use Akeneo\CouplingDetector\Event\PreRulesCheckedEvent;
use Akeneo\CouplingDetector\Event\RuleCheckedEvent;
use Akeneo\CouplingDetector\Event\PostRulesCheckedEvent;

/**
 * Output the results really simply. Just the number of errors is displayed.
 * E/W
 * where E is the number of errors and W is the number of warnings
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class SimpleFormatter extends AbstractFormatter
{
    /**
     * {@inheritdoc}
     */
    protected function outputPreNodesParsed(PreNodesParsedEvent $event): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputNodeParsed(NodeParsedEvent $event): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputPostNodesParsed(PostNodesParsedEvent $event): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputPreRulesChecked(PreRulesCheckedEvent $event): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputNodeChecked(NodeChecked $event): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputRuleChecked(RuleCheckedEvent $event): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputPostRulesChecked(PostRulesCheckedEvent $event): void
    {
        $errorCount = 0;
        $warningCount = 0;

        foreach ($event->getViolations() as $violation) {
            if (ViolationInterface::TYPE_ERROR === $violation->getType()) {
                $errorCount++;
            } else {
                $warningCount++;
            }
        }

        echo sprintf("%d/%d", $errorCount, $warningCount);
    }
}
