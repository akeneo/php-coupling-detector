<?php

namespace Akeneo\CouplingDetector\Console\Command;

use Akeneo\CouplingDetector\Detector;
use Akeneo\CouplingDetector\Coupling\UseViolationsFilter;
use Akeneo\CouplingDetector\FilesReader;
use Akeneo\CouplingDetector\RulesApplier;
use Akeneo\CouplingDetector\UseViolationRule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Detects the coupling issues in pim-community-dev
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class PimCommunityCommand extends Command
{
    /**
     * {@inheritedDoc}
     */
    protected function configure()
    {
        $this
            ->setName('pim-community-dev')
            ->setDefinition(
                array(
                    new InputOption('strict', '', InputOption::VALUE_NONE, 'Apply strict rules without legacy exceptions'),
                )
            )
            ->setDescription('Detect coupling violations in pim-community-dev');
    }

    /**
     * {@inheritedDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // check the project path
        $binPath = getcwd();
        $composerPath = $binPath.'/composer.json';
        $errorMessage = sprintf('You must launch the command from the pim-community-dev repository, not from "%s"', $binPath).PHP_EOL;
        if (!file_exists($composerPath)) {
            fwrite(STDERR, $errorMessage);
            exit(1);
        } else {
            $composerContent = file_get_contents($composerPath);
            if (false === strpos($composerContent, '"name": "akeneo/pim-community-dev"')) {
                fwrite(STDERR, $errorMessage);
                exit(1);
            }
        }
        $path = $binPath.'/src/';
        $strictMode = $input->getOption('strict');

        $output->writeln(sprintf('<info> Detect coupling violations (strict mode %s)</info>', $strictMode ? 'enabled' : 'disabled'));

        $rules = [
            new UseViolationRule('Akeneo\Component', ['Pim', 'PimEnterprise', 'Bundle']),
            new UseViolationRule('Akeneo\Bundle', ['Pim', 'PimEnterprise']),
            new UseViolationRule('Pim\Component', ['PimEnterprise', 'Bundle']),
            new UseViolationRule('Pim\Bundle', ['PimEnterprise']),
        ];

        $legacyExclusions = [
            'Akeneo\Component' => [
                'Pim\Bundle\TranslationBundle\Entity\TranslatableInterface'
            ],
            'Pim\Component'    => [
                'Pim\Bundle\CatalogBundle\Repository\AssociationTypeRepositoryInterface'
            ],
        ];
        $useViolationsFilter = new UseViolationsFilter($legacyExclusions);

        $detector = new Detector();
        $reader = new FilesReader($path);
        $applier = new RulesApplier($rules);

        $violations = $detector->detectUseViolations($reader, $applier);
        if (!$strictMode) {
            // $violations = $useViolationsFilter->filter($violations);
            // TODO: to be refactored, does not work with new reading strategy
        }
        $forbiddenUseCounter = $violations->getSortedForbiddenUsesCounters();
        $totalCount = 0;
        foreach ($forbiddenUseCounter as $fullName => $count) {
            $output->writeln(sprintf('<info> - %d x %s</info>', $count, $fullName));
            $totalCount += $count;
        }
        $output->writeln(sprintf('<info>Total coupling issues %s</info>', $totalCount));

        return $totalCount;
    }
}
