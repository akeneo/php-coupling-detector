<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Formatter\Console;

use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Event\PostNodesParsedEvent;
use Akeneo\CouplingDetector\Event\NodeChecked;
use Akeneo\CouplingDetector\Event\NodeParsedEvent;
use Akeneo\CouplingDetector\Event\PreNodesParsedEvent;
use Akeneo\CouplingDetector\Event\PreRulesCheckedEvent;
use Akeneo\CouplingDetector\Event\RuleCheckedEvent;
use Akeneo\CouplingDetector\Event\PostRulesCheckedEvent;
use Akeneo\CouplingDetector\Formatter\AbstractFormatter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Output the results as pretty text in the console.
 *
 * @author  Julien Janvier <j.janvier@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class PrettyFormatter extends AbstractFormatter
{
    /** @var OutputInterface */
    private $output;

    /** @var bool */
    private $verbose;

    /**
     * @param OutputInterface $output
     * @param bool            $verbose
     */
    public function __construct(OutputInterface $output, $verbose = false)
    {
        $this->output = $output;
        $this->verbose = $verbose;
    }

    /**
     * {@inheritdoc}
     */
    protected function outputPreNodesParsed(PreNodesParsedEvent $event)
    {
        $this->output->writeln(sprintf('Parsing %s nodes<blink>...</blink>', $this->nodeCount));
    }

    /**
     * {@inheritdoc}
     */
    protected function outputNodeParsed(NodeParsedEvent $event)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputPostNodesParsed(PostNodesParsedEvent $event)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputPreRulesChecked(PreRulesCheckedEvent $event)
    {
        $this->output->writeln(sprintf('Checking %s rules<blink>...</blink>', $this->ruleCount));
    }

    /**
     * {@inheritdoc}
     */
    protected function outputNodeChecked(NodeChecked $event)
    {
        if (null === $event->getViolation()) {
            return;
        }

        $rule = $event->getRule();
        $node = $event->getNode();
        $violation = $event->getViolation();

        $errorType = RuleInterface::TYPE_DISCOURAGED === $rule->getType() ? 'warning' : 'error';

        $msg = !$this->verbose ?
            sprintf(
                'Node <comment>%s</comment> does not respect the rule <comment>%s</comment> because of the tokens:',
                $node->getFilepath(),
                $rule->getSubject()
            ) :
            sprintf(<<<MSG
Node <comment>%s</comment> does not respect the rule <comment>%s</comment>:
    * type: %s
    * description: %s
    * requirements: %s
The following tokens are wrong:
MSG
                ,
                $node->getFilepath(),
                $rule->getSubject(),
                $rule->getType(),
                $rule->getDescription() ?: 'N/A',
                implode(', ', $rule->getRequirements())
            );

        $this->output->writeln('');
        $this->output->writeln($msg);
        foreach ($violation->getTokenViolations() as $token) {
            $this->output->writeln(sprintf('    * <%s>%s</%s>', $errorType, $token, $errorType));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function outputRuleChecked(RuleCheckedEvent $event)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function outputPostRulesChecked(PostRulesCheckedEvent $event)
    {
        $this->output->writeln('');
        $this->output->writeln('');

        if (0 === $this->violationsCount) {
            $this->output->write('<passed-bg>No coupling issues found </passed-bg>');
            $this->output->write("<passed-bg>\xE2\x9C\x94</passed-bg>");
            $this->output->write("<passed-bg>\xF0\x9F\x98\x83</passed-bg>");
            $this->output->writeln("<passed-bg>\xF0\x9F\x8D\xBB</passed-bg>");
        } else {
            $this->output->write(
                sprintf('<broken-bg>%d coupling issues found </broken-bg>', $this->violationsCount)
            );
            $this->output->write("<broken-bg>\xE2\x9C\x96</broken-bg>");
            $this->output->write("<broken-bg>\xF0\x9F\x98\xA5</broken-bg>");
            $this->output->writeln("<broken-bg>\xF0\x9F\x8D\x86</broken-bg>");
        }
    }
}
