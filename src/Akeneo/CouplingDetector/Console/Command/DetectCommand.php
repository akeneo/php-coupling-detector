<?php

namespace Akeneo\CouplingDetector\Console\Command;

use Akeneo\CouplingDetector\Configuration\Configuration;
use Akeneo\CouplingDetector\CouplingDetector;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Akeneo\CouplingDetector\Domain\ViolationInterface;
use Akeneo\CouplingDetector\NodeParser\NodeParserResolver;
use Akeneo\CouplingDetector\RuleChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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

    /**
     * {@inheritedDoc}.
     */
    protected function configure()
    {
        $this
            ->setName('detect')
            ->setDefinition(
                [
                    new InputArgument('path', InputArgument::OPTIONAL, 'path of the project', null),
                    new InputOption(
                        'config-file',
                        null,
                        InputOption::VALUE_REQUIRED,
                        'file path of the configuration file'
                    ),
                    new InputOption(
                        'strict',
                        null,
                        InputOption::VALUE_NONE,
                        'Apply strict rules without legacy exceptions'
                    ),
                ]
            )
            ->setDescription('Detect coupling violations')
            ->setHelp(
                <<<HELP
The <info>%command.name%</info> command detects coupling problems for a given file or directory depending on the
coupling rules that have been defined:
    <info>php %command.full_name% /path/to/dir</info>
    <info>php %command.full_name% /path/to/file</info>

The exit status of the <info>%command.name%</info> command can be: 0 if no violations have been raised, 10 in case of
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
HELP
            )
        ;
    }

    /**
     * {@inheritedDoc}.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        $strictMode = $input->getOption('strict');
        $output->writeln(
            sprintf('<info>Detecting coupling violations (strict mode %s)...</info>', $strictMode ? 'enabled' : 'disabled')
        );

        $config = $this->loadConfiguration($configFile);
        $rules = $config->getRules();
        $finder = $config->getFinder();
        $finder->in($path);

        $nodeParserResolver = new NodeParserResolver();
        $ruleChecker = new RuleChecker();
        $detector = new CouplingDetector($nodeParserResolver, $ruleChecker);

        $violations = $detector->detect($finder, $rules);
        $this->outputViolations($output, $violations, $input->getOption('verbose'));

        return $this->determineExitCode($violations);
    }

    /**
     * @param ViolationInterface[] $violations
     *
     * @return int
     */
    protected function determineExitCode(array $violations)
    {
        if (0 === count($violations)) {
            return 0;
        }

        $exitCode = self::EXIT_WITH_WARNINGS;
        foreach ($violations as $violation) {
            if (ViolationInterface::TYPE_ERROR=== $violation->getType()) {
                $exitCode = self::EXIT_WITH_ERRORS;
                break;
            }
        }

        return $exitCode;
    }

    /**
     * @param OutputInterface      $output
     * @param ViolationInterface[] $violations
     * @param bool                 $verbose
     */
    protected function outputViolations(OutputInterface $output, array $violations, $verbose = false)
    {
        $warningStyle = new OutputFormatterStyle('white', 'yellow', ['bold']);
        $output->getFormatter()->setStyle('warning', $warningStyle);
        $errorStyle = new OutputFormatterStyle('white', 'red', ['bold']);
        $output->getFormatter()->setStyle('error', $errorStyle);

        $nbErrors = 0;
        foreach ($violations as $violation) {
            $rule = $violation->getRule();
            $node = $violation->getNode();
            $errorType = RuleInterface::TYPE_DISCOURAGED === $rule->getType() ? 'warning' : 'error';

            $msg = !$verbose ?
                sprintf(
                    'Node <comment>%s</comment> does not respect the rule <comment>%s</comment> because of the tokens:',
                    $node->getFilepath(),
                    $rule->getSubject()
                ):
                sprintf(<<<MSG
Node <comment>%s</comment> does not respect the following rule <comment>%s</comment>:
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

            $output->writeln('');
            $output->writeln($msg);
            foreach ($violation->getTokenViolations() as $token) {
                $output->writeln(sprintf('    * <%s>%s</%s>', $errorType, $token, $errorType));
            }

            $nbErrors += count($violation->getTokenViolations());
        }

        if (0 === $nbErrors) {
            $output->writeln('<info>No coupling issues found :)</info>');
        } else {
            $output->writeln('');
            $output->writeln(sprintf('<info>%d coupling issues found!</info>', $nbErrors));
        }
    }

    /**
     * @param string $filePath
     *
     * @return Configuration
     */
    protected function loadConfiguration($filePath)
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
}
