<?php

declare(strict_types=1);

namespace Akeneo\CouplingDetector\Console\Command;

use Akeneo\CouplingDetector\Configuration\Configuration;
use Akeneo\CouplingDetector\Console\OutputFormatter;
use Akeneo\CouplingDetector\Domain\NodeInterface;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Event\Events;
use Akeneo\CouplingDetector\Event\NodeParsedEvent;
use Akeneo\CouplingDetector\Event\PostNodesParsedEvent;
use Akeneo\CouplingDetector\Event\PreNodesParsedEvent;
use Akeneo\CouplingDetector\NodeParser\ExtractionException;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * List unused requirements for each rule defined in the configuration.
 *
 * @author    Yohan Blain <yohan.blain@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
final class ListUnusedRequirementsCommand extends Command
{
    private const EXIT_WITH_WARNINGS = 10;

    protected function configure(): void
    {
        $this
            ->setName('list-unused-requirements')
            ->setDefinition(
                [
                    new InputArgument('path', InputArgument::OPTIONAL, 'path of the project', null),
                    new InputOption(
                        'config-file',
                        'c',
                        InputOption::VALUE_REQUIRED,
                        'File path of the configuration file'
                    ),
                ]
            )
            ->setDescription('List rule requirements that are not needed anymore.')
            ->setHelp($this->loadHelpContent())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->setFormatter(new OutputFormatter($output->isDecorated()));

        if (null !== $path = $input->getArgument('path')) {
            $filesystem = new Filesystem();
            if (!$filesystem->isAbsolutePath($path)) {
                $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            }
        }

        if (null === $configFile = $input->getOption('config-file')) {
            $configDir = $path;
            if (is_file($path) && $dirName = pathinfo($path, PATHINFO_DIRNAME)) {
                $configDir = $dirName;
            } elseif (null === $path) {
                $configDir = getcwd();
                // path is directory
            }
            $configFile = $configDir . DIRECTORY_SEPARATOR . '.php_cd';
        }

        $config = $this->loadConfiguration($configFile);
        $rules = $config->getRules();
        $finder = $config->getFinder();
        $finder->in($path);

        $nodeParserResolver = new NodeParserResolver();
        $eventDispatcher = new EventDispatcher();

        $nodes = $this->parseNodes($finder, $nodeParserResolver, $eventDispatcher);

        $output->writeln(sprintf('Parsing %s nodes<blink>...</blink>', count($nodes)));
        $output->writeln(sprintf('Checking %s rules<blink>...</blink>', count($rules)));

        $exitCode = 0;
        foreach ($rules as $rule) {
            $ruleUnusedRequirements = $rule->getUnusedRequirements($nodes);
            if (count($ruleUnusedRequirements) > 0) {
                $exitCode = self::EXIT_WITH_WARNINGS;
            }

            $this->displayRuleUnusedRequirements($output, $rule, $ruleUnusedRequirements);
        }

        return $exitCode;
    }

    private function loadHelpContent(): string
    {
        $content = file_get_contents(__DIR__ . '/../../../doc/LIST_UNUSED_REQUIREMENTS.md');
        if (false === $content) {
            throw new \RuntimeException('Unable to load the help content.');
        }

        $content = preg_replace('/^.+\n/', '', $content);
        $content = str_replace('bin/php-coupling-detector', '%command.full_name%', $content);
        $content = str_replace('_list-unused-requirements_ command', '<info>%command.name%</info> command', $content);
        $content = preg_replace('/```bash(.*?)```/s', '<info>$1</info>', $content);
        $content = preg_replace('/```php(.*?)```/s', '<question>$1</question>', $content);
        $content = preg_replace('/``(.*?)``/s', '<comment>$1</comment>', $content);

        return $content;
    }

    /**
     * @return NodeInterface[]
     */
    private function parseNodes(
        Finder $finder,
        NodeParserResolver $nodeParserResolver,
        EventDispatcher $eventDispatcher
    ): array {
        $eventDispatcher->dispatch(Events::PRE_NODES_PARSED, new PreNodesParsedEvent($finder));

        $nodes = array();
        foreach ($finder as $file) {
            $parser = $nodeParserResolver->resolve($file);
            if (null !== $parser) {
                try {
                    $node = $parser->parse($file);
                    $nodes[] = $node;
                    $eventDispatcher->dispatch(Events::NODE_PARSED, new NodeParsedEvent($node));
                } catch (ExtractionException $e) {
                    // at the moment, let's just ignore invalid node
                    // need to fix that with a better design
                }
            }
        }

        $eventDispatcher->dispatch(Events::POST_NODES_PARSED, new PostNodesParsedEvent($nodes));

        return $nodes;
    }

    private function loadConfiguration(string $filePath): Configuration
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('The configuration file "%s" does not exit', $filePath));
        }

        $config = include $filePath;

        if (!$config instanceof Configuration) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The configuration file "%s" must return a "%s" instance.',
                    $filePath,
                    Configuration::class
                )
            );
        }

        return $config;
    }

    private function displayRuleUnusedRequirements(
        OutputInterface $output,
        RuleInterface $rule,
        array $ruleUnusedRequirements
    ): void {
        if (count($ruleUnusedRequirements) < 1) {
            return;
        }

        $output->writeln('');
        $output->writeln(sprintf('Rule <comment>%s</comment> has unused requirements:', $rule->getSubject()));

        foreach ($ruleUnusedRequirements as $ruleUnusedRequirement) {
            $output->writeln(sprintf('    * <warning>%s</warning>', $ruleUnusedRequirement));
        }
    }
}
