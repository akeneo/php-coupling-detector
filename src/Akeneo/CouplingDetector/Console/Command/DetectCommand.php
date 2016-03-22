<?php

namespace Akeneo\CouplingDetector\Console\Command;

use Akeneo\CouplingDetector\Configuration\Configuration;
use Akeneo\CouplingDetector\Console\OutputFormatter;
use Akeneo\CouplingDetector\CouplingDetector;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\Formatter\Console\DotFormatter;
use Akeneo\CouplingDetector\Formatter\Console\PrettyFormatter;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Akeneo\CouplingDetector\RuleChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Detects the coupling issues.
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class DetectCommand extends Command
{
    const EXIT_WITH_WARNINGS = 10;
    const EXIT_WITH_ERRORS = 99;

    private $formats = ['pretty', 'dot'];

    /**
     * {@inheritedDoc}.
     */
    protected function configure()
    {
        $this
            ->setName('detect')
            ->setDefinition(
                array(
                    new InputArgument('path', InputArgument::OPTIONAL, 'path of the project', null),
                    new InputOption(
                        'config-file',
                        'c',
                        InputOption::VALUE_REQUIRED,
                        'File path of the configuration file'
                    ),
                    new InputOption(
                        'format',
                        'f',
                        InputOption::VALUE_REQUIRED,
                        sprintf('Output format (%s)', implode(', ', $this->formats)),
                        $this->formats[0]
                    ),
                )
            )
            ->setDescription('Detect coupling rules')
            ->setHelp(
                <<<HELP
The <info>%command.name%</info> command detects coupling problems for a given file or directory depending on the
coupling rules that have been defined:
    <info>php %command.full_name% /path/to/dir</info>
    <info>php %command.full_name% /path/to/file</info>

The exit status of the <info>%command.name%</info> command can be: 0 if no rules have been raised, 10 in case of
warnings and 99 in case of errors.

You can save the configuration in a <comment>.php_cd</comment> file in the root directory of
your project. The file must return an instance of ``Akeneo\CouplingDetector\Configuration\Configuration``,
which lets you configure the rules and the directories that need to be analyzed.
Here is an example below:
    <?php
    use \Akeneo\CouplingDetector\Domain\Rule;
    use \Akeneo\CouplingDetector\Domain\RuleInterface;

    \$finder = new \Symfony\Component\Finder\Finder();
    \$finder
        ->files()
        ->name('*.php')
        ->notPath('foo/bar/');

    \$rules = [
        new Rule('foo', ['bar', 'baz'], RuleInterface::TYPE_FORBIDDEN),
        new Rule('zoo', ['too'], RuleInterface::TYPE_DISCOURAGED),
        new Rule('bli', ['bla', 'ble', 'blu'], RuleInterface::TYPE_ONLY),
    ];

    return new \Akeneo\CouplingDetector\Configuration\Configuration(\$rules, \$finder);
    ?>

You can also use the default finder implementation if you want to analyse all the PHP files
of your directory:
    <?php
    use \Akeneo\CouplingDetector\Domain\Rule;
    use \Akeneo\CouplingDetector\Domain\RuleInterface;

    \$rules = [
        new Rule('foo', ['bar', 'baz'], RuleInterface::TYPE_FORBIDDEN),
        new Rule('zoo', ['too'], RuleInterface::TYPE_DISCOURAGED),
        new Rule('bli', ['bla', 'ble', 'blu'], RuleInterface::TYPE_ONLY),
    ];

    return new \Akeneo\CouplingDetector\Configuration\Configuration(
        \$rules,
        \Akeneo\CouplingDetector\Configuration\DefaultFinder
    );
    ?>

With the <comment>--config-file</comment> option you can specify the path to the <comment>.php_cd</comment> file:
    <info>php %command.full_name% /path/to/dir --config-file=/path/to/my/configuration.php_cd</info>

With the <comment>--format</comment> option you can specify the output format:
    <info>php %command.full_name% /path/to/dir --format=dot</info>
HELP
            )
        ;
    }

    /**
     * {@inheritedDoc}.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setFormatter(new OutputFormatter(true));

        if (null !== $format = $input->getOption('format')) {
            if (!in_array($format,  $this->formats)) {
                throw new \RuntimeException(
                    sprintf('Format "%s" is unknown. Available formats: %s.', $format, implode(', ', $this->formats))
                );
            }
        }

        if (null !== $path = $input->getArgument('path')) {
            $filesystem = new Filesystem();
            if (!$filesystem->isAbsolutePath($path)) {
                $path = getcwd().DIRECTORY_SEPARATOR.$path;
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
            $configFile = $configDir.DIRECTORY_SEPARATOR.'.php_cd';
        }

        $config = $this->loadConfiguration($configFile);
        $rules = $config->getRules();
        $finder = $config->getFinder();
        $finder->in($path);

        $nodeParserResolver = new NodeParserResolver();
        $ruleChecker = new RuleChecker();
        $eventDispatcher = $this->initEventDispatcher($output, $format, $input->getOption('verbose'));
        $detector = new CouplingDetector($nodeParserResolver, $ruleChecker, $eventDispatcher);

        $violations = $detector->detect($finder, $rules);

        return $this->determineExitCode($violations);
    }

    /**
     * @param ViolationInterface[] $violations
     *
     * @return int
     */
    private function determineExitCode(array $violations)
    {
        if (0 === count($violations)) {
            return 0;
        }

        $exitCode = self::EXIT_WITH_WARNINGS;
        foreach ($violations as $violation) {
            if (ViolationInterface::TYPE_ERROR === $violation->getType()) {
                $exitCode = self::EXIT_WITH_ERRORS;
                break;
            }
        }

        return $exitCode;
    }

    /**
     * @param string $filePath
     *
     * @return Configuration
     */
    private function loadConfiguration($filePath)
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('The configuration file "%s" does not exit', $filePath));
        }

        $config = include $filePath;

        if (!$config instanceof Configuration) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The configuration file "%s" must return a "Akeneo\CouplingDetector\Configuration\Configuration"',
                    $filePath
                )
            );
        }

        return $config;
    }

    /**
     * Init the event dispatcher by attaching the output formatters.
     *
     * @param OutputInterface $output
     * @param string          $formatterName
     * @param bool            $verbose
     *
     * @return EventDispatcherInterface
     */
    private function initEventDispatcher(OutputInterface $output, $formatterName, $verbose)
    {
        if ('dot' === $formatterName) {
            $formatter = new DotFormatter($output);
        } elseif ('pretty' === $formatterName) {
            $formatter = new PrettyFormatter($output, $verbose);
        } else {
            throw new \RuntimeException(
                sprintf('Format "%s" is unknown. Available formats: %s.', $formatterName, implode(', ', $this->formats))
            );
        }

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($formatter);

        return $eventDispatcher;
    }
}
